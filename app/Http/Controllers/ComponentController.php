<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Components;
use App\Models\PageComponent;
use App\Models\PageContent;
use Validator;
use Storage;
class ComponentController extends Controller
{
    private $stack;
    private $template_id;
    private function replaceNULL($value) {
        array_walk_recursive($value, function (&$item, $key) {
            $item = null === $item ? '' : $item;
        });
        return $value;
    }
    private function validateComponent() {
        $error = false;
        $rules = [
            "name" => "required|string|max:45|unique:components,name",
            "category" => "required|in:basic,element,component,web",
            "node" => "required|in:self,parent,child",
            "visibility" => "required|in:auth,guest,show,none",
            "content_type" => "required|in:static,variable,element",
            "child_order" => "required_if:node,child|numeric",
            "nested_component" => "nullable|string",
            "start_tag" => "required|string|max:10",
            "end_tag" => "required|nullable|string|max:10",
            "attributes" => "required|json|max:500",
            "var_attributes" => "nullable|json|max:500",
            "classes" => "required|json|max:500",
            "style" => "required|json|max:500",
            "content" => "required_if:content_type,static,variable|nullable|string|max:65500"
        ];        
        foreach($this->stack as $elements) {
            foreach($elements as $element) {
                $validator = Validator::make($element, $rules);
                if ($validator->fails()) {
                    $error = $validator->errors()->first();
                    break 2;
                }
            }
        }
        return $error;
    }
    private function component(&$stack, $component, $pointer, $name, $index) {
        if(empty($component))
        return;
        if($pointer == "component" || $pointer == "basic" || $pointer == "web") {
            $category = $pointer;
            $pointer = "base";
        } else $category = "element";
        if(isset($component["child"])) {
            $i = 1;
            foreach($component["child"] as $child) {
                $component["child"]["node"] = "child";
                $component["child"]["category"] = "element";
                $this->element($stack, $child, $pointer.'_'.($i++), $name, $index);
            }
        }
        if(isset($component["parent"])) {
            $component["parent"]["node"] = "parent";
            $component["parent"]["category"] = "element";
            $this->element($stack, $component["parent"], '', $name, $index);
        }
        if(isset($component["self"])) {
            $this->template_id = ((isset($component["self"]["template_id"]))?$component["self"]["template_id"]:0);
            $component["self"]["node"] = "self";
            $component["self"]["category"] = $category;            
            $this->element($stack,$component["self"], $pointer.'_self', $name, $index);
        }
    }
    private function element(&$stack, $element, $pointer, $name, $index) {
        if(empty($element))
        return;
        $nextComponent = null;
        if($index)
        $element["name"] = $name.$index;
        else
        $element["name"] = $name;
        $element["nested_component"] = null;
        $element["var_attributes"] = ((isset($element["var_attributes"]))?json_encode(array_unique($this->replaceNULL($element["var_attributes"]))):"[]");
        $element["attributes"] = json_encode(array_unique($this->replaceNULL($element["attributes"])));
        $element["classes"] = json_encode(array_unique($this->replaceNULL($element["classes"])));
        $element["style"] = json_encode($this->replaceNULL($element["style"]));
        if($element["content_type"] == "element" && !empty($element["content"])) {
            $nextComponent = $element["content"];
            $element["content"] = null;
            if($nextComponent && $pointer != '')
            $element['nested_component'] = $pointer;
        }
        $stack[] = $element;
        if($nextComponent && $pointer != '') {
            $this->component($this->stack[$pointer], $nextComponent, $pointer, $name, ++$index);
        }
    }
    private function insertComponent($elements) {
        $id = null;
        foreach($elements as $element) {
            $return = $this->insertElement($element);
            if($element["node"] == "self")
            $id = $return;
        }
        return $id;
    }
    private function insertElement($element) {
        $id = null;
        if($element['nested_component']) {
            $id = $this->insertComponent($this->stack[$element['nested_component']]);
        }
        $ele = new Components;
        $ele->template_id = $this->template_id;
        $ele->visibility_id = ((isset($element['visibility_id']))?$element['visibility_id']:0);
        $ele->type = ((isset($element['type']))?$element['type']:'body');
        $ele->geolocation = ((isset($element['geolocation']))?$element['geolocation']:0);
        $ele->name = $element['name'];
        $ele->category = $element['category'];
        $ele->node = $element['node'];
        $ele->visibility = $element['visibility'];
        $ele->content_type = $element['content_type'];
        $ele->child_order = $element['child_order'];
        $ele->nested_component = $id;
        $ele->loop_source = ((isset($element['loop_source']))?$element['loop_source']:null);
        $ele->start_tag = $element['start_tag'];
        $ele->end_tag = $element['end_tag'];
        $ele->attributes = $element['attributes'];
        $ele->var_attributes = $element['var_attributes'];
        $ele->classes = $element['classes'];
        $ele->style = $element['style'];
        $ele->content = $element['content'];
        $ele->save();
        if($element['node'] == "self")
        return $ele->id;
        else return 0;
    }
    private function fetchComponent($name) {
        $components = Components::where("name", $name)->orderBy("child_order")->get();
        $response = [];
        foreach($components as $key => $component) {
            if($component->nested_component != null) {
                $nested = Components::find($component->nested_component);
                $components[$key]->content = $this->fetchComponent($nested->name);
            }
            $components[$key]->var_attributes = json_decode($component->var_attributes);
            $components[$key]->classes = json_decode($component->classes);
            $components[$key]->attributes = json_decode($component->attributes);
            $components[$key]->style = json_decode($component->style);
            if($component->node == "child")
            $response[$component->node][] = $components[$key];
            else
            $response[$component->node] = $components[$key];
        }
        return $response;
    }
    private function fetchComponentID($name, &$id) {
        $components = Components::where("name", $name)->orderBy("child_order")->get();
        foreach($components as $key => $component) {
            $id[] = $component->id;
            if($component->nested_component != null) {
                $nested = Components::find($component->nested_component);
                $this->fetchComponentID($nested->name, $id);
            }
        }
    }
    public function create() {
        return view('Component.editor', ["edit" => false]);
    }
    public function edit($name) {
        $stack = $this->fetchComponent($name);
        if(empty($stack))
        abort(404);
        return view('Component.editor', ["edit" => true, "name" => $name, "stack" => $stack]);
    }
    public function addBasic() {
        return view('Component.addBasic');
    }
    public function add(Request $request) {
        $request->validate([
            "self.name" => "required|string|max:50|unique:components,name",
            "self.category" => "required|in:basic,component",
            "self.content" => "required_if:self.content_type,static,variable|nullable|string|max:65500",
            "self.node" => "required|in:self",
            "self.var_attributes" => "nullable|json|max:500",
            "self.start_tag" => "required|string|max:10",
            "self.end_tag" => "required|nullable|string|max:10",
            "self.content_type" => "required|in:static,variable,component,element",
            "self.classes" => "required|json|max:500",
            "self.attributes" => "required|json|max:500",
            "self.style" => "required|json|max:500",
            "parent.node" => "sometimes|required|in:parent",
            "parent.category" => "required_with:parent.node|in:element",
            "parent.content_type" => "required_with:parent.node|in:element",
            "parent.var_attributes" => "required_with:parent.node|json|max:500",
            "parent.start_tag" => "required_with:parent.node|string|max:10",
            "parent.end_tag" => "required_with:parent.node|nullable|string|max:10",
            "parent.classes" => "required_with:parent.node|json|max:500",
            "parent.attributes" => "required_with:parent.node|json|max:500",
            "parent.style" => "required_with:parent.node|json|max:500",
            "child.*.node" => "sometimes|required|in:child",
            "child.*.category" => "required_with:child.*.node|in:element",
            "child.*.child_order" => "required_with:child.*.node|numeric",
            "child.*.content_type" => "required_with:child.*.node|in:static,variable,component",
            "child.*.content" => "required_if:child.*.content_type,static,variable|nullable|string|max:65500",
            "child.*.var_attributes" => "required_with:child.*.node|json|max:500",
            "child.*.start_tag" => "required_with:child.*.node|string|max:10",
            "child.*.end_tag" => "required_with:child.*.node|nullable|string|max:10",
            "child.*.classes" => "required_with:child.*.node|json|max:500",
            "child.*.attributes" => "required_with:child.*.node|json|max:500",
            "child.*.style" => "required_with:child.*.node|json|max:500"
        ]);
        $self = new Components;
        $self->name = $request->self['name'];
        $self->category = $request->self['category'];
        $self->node = $request->self['node'];
        $self->content_type = $request->self['content_type'];
        //$self->child_order = $request->self['child_order'];
        //$self->nested_component = $request->self['nested_component'];
        //$self->loop_source = $request->self['loop_source'];
        $self->start_tag = $request->self['start_tag'];
        $self->end_tag = $request->self['end_tag'];
        $self->attributes = $request->self['attributes'];
        $self->var_attributes = $request->self['var_attributes'];
        $self->classes = $request->self['classes'];
        $self->style = $request->self['style'];
        if(in_array($request->self['content_type'], ["static", "variable"]))
        $self->content = $request->self['content'];
        $self->save();
        if($request->has('parent')) {
            $parent = new Components;
            $parent->name = $request->self['name'];
            $parent->category = $request->parent['category'];
            $parent->node = $request->parent['node'];
            $parent->content_type = $request->parent['content_type'];
            //$parent->child_order = $request->parent['child_order'];
            //$parent->nested_component = $request->parent['nested_component'];
            //$parent->loop_source = $request->parent['loop_source'];
            $parent->start_tag = $request->parent['start_tag'];
            $parent->end_tag = $request->parent['end_tag'];
            $parent->attributes = $request->parent['attributes'];
            $parent->var_attributes = $request->parent['var_attributes'];
            $parent->classes = $request->parent['classes'];
            $parent->style = $request->parent['style'];
            if(in_array($request->parent['content_type'], ["static", "variable"]))
            $parent->content = $request->parent['content'];
            $parent->save();
        }
        if($request->has('child')) {
            foreach($request->child as $req) {
                $child = new Components;
                $child->name = $request->self['name'];
                $child->category = $req['category'];
                $child->node = $req['node'];
                $child->content_type = $req['content_type'];
                $child->child_order = $req['child_order'];
                //$child->nested_component = $req['nested_component'];
                //$child->loop_source = $req['loop_source'];
                $child->start_tag = $req['start_tag'];
                $child->end_tag = $req['end_tag'];
                $child->attributes = $req['attributes'];
                $child->var_attributes = $req['var_attributes'];
                $child->classes = $req['classes'];
                $child->style = $req['style'];
                if(in_array($req['content_type'], ["static", "variable"]))
                $child->content = $req['content'];
                $child->save();
            }            
        }
        return redirect()->back()->with("message", "Component Added Successfully.");
    }
    public function loadComponent($name = null) {
        return response()->json($this->fetchComponent($name));
    }
    public function loadComponents() {
        $basiccomponents = Components::where("category", "basic")->select("id", "name")->get();
        $response_ = [];
        foreach($basiccomponents as $key => $basiccomponent) {
                $response = [];
                $components = Components::where("name", $basiccomponent->name)->orderBy("child_order")->get();
                foreach($components as $key => $component) {
                if($component->nested_component != null) {
                    $nested = Components::find($component->nested_component);
                    $components[$key]->content = $this->fetchComponent($nested->name);
                }
                $components[$key]->var_attributes = json_decode($component->var_attributes);
                $components[$key]->classes = json_decode($component->classes);
                $components[$key]->attributes = json_decode($component->attributes);
                $components[$key]->style = json_decode($component->style);
                if($component->node == "child")
                $response[$component->node][] = $components[$key];
                else
                $response[$component->node] = $components[$key];
            }
            $response_[] = $response;
        }
        return response()->json($response_);
    }
    public function load_Components() {
        $basiccomponents = Components::where("category", "component")->select("id", "name")->get();
        $response_ = [];
        foreach($basiccomponents as $key => $basiccomponent) {
                $response = [];
                $components = Components::where("name", $basiccomponent->name)->orderBy("child_order")->get();
                foreach($components as $key => $component) {
                if($component->nested_component != null) {
                    $nested = Components::find($component->nested_component);
                    $components[$key]->content = $this->fetchComponent($nested->name);
                }
                $components[$key]->var_attributes = json_decode($component->var_attributes);
                $components[$key]->classes = json_decode($component->classes);
                $components[$key]->attributes = json_decode($component->attributes);
                $components[$key]->style = json_decode($component->style);
                if($component->node == "child")
                $response[$component->node][] = $components[$key];
                else
                $response[$component->node] = $components[$key];
            }
            $response_[] = $response;
        }
        return response()->json($response_);
    }
    public function load_template_Components($template_id) {
        $basiccomponents = Components::where("category", "web")->where("template_id", $template_id)->select("id", "name")->get();
        $response_ = [];
        foreach($basiccomponents as $key => $basiccomponent) {
                $response = [];
                $components = Components::where("name", $basiccomponent->name)->orderBy("child_order")->get();
                foreach($components as $key => $component) {
                if($component->nested_component != null) {
                    $nested = Components::find($component->nested_component);
                    $components[$key]->content = $this->fetchComponent($nested->name);
                }
                $components[$key]->var_attributes = json_decode($component->var_attributes);
                $components[$key]->classes = json_decode($component->classes);
                $components[$key]->attributes = json_decode($component->attributes);
                $components[$key]->style = json_decode($component->style);
                if($component->node == "child")
                $response[$component->node][] = $components[$key];
                else
                $response[$component->node] = $components[$key];
            }
            $response_[] = $response;
        }
        return response()->json($response_);
    }
    public function saveComponent(Request $request) {
        // $request = name category stack
        $component = $request->component;
        $this->stack = [];
        $this->component($this->stack['base'], $component, (($request->category)?$request->category:'component'), (($request->name)?$request->name:''), 0);
        $error = $this->validateComponent();
        if($error) {
            return response()->json(["success" => 0, "error"=> $error, "stack"=>$this->stack]);
        } else {
            $this->insertComponent($this->stack['base']);
        }
        return response()->json(["success" => 1]);
    }
    public function editComponent(Request $request) {
        if($request->name) {
            $component = Components::where("name", $request->name)->whereIn("category", ["component", "basic", "web"])->first();
            if(!$component)
            return response()->json(["success" => 0, "error"=> "Invalid Component Name", "stack"=>[]]);
            if($component->category == "web") {
                $oldID = $component->id;
            }
            $component = $request->component;
            $this->stack = [];
            $this->component($this->stack['base'], $component, (($request->category)?$request->category:'component'), "temp_name", 0);
            $error = $this->validateComponent();
            if($error) {
                return response()->json(["success" => 0, "error"=> $error, "stack"=>$this->stack]);
            } else {
                $delete = [];
                $this->fetchComponentID($request->name, $delete);
                Components::destroy($delete);
                $this->stack = [];
                $this->component($this->stack['base'], $component, (($request->category)?$request->category:'component'), (($request->name)?$request->name:''), 0);
                $this->insertComponent($this->stack['base']);
                if($request->category == "web") {
                    $component = Components::where("name", $request->name)->where("category", "web")->first();
                    PageComponent::where('component_id', $oldID)->update(["component_id" => $component->id]);
                    if($component->type == "main") {
                        $component_pages = PageComponent::select('page_id')->where('component_id', $component->id)->groupBy('page_id')->get();
                        foreach($component_pages as $component_page) {
                            PageContent::where('page_id', $component_page->page_id)->update(["broked" => 1]);
                        }
                    }
                }
            }
        }
        return response()->json(["success" => 1]);
    }
    public function deleteComponent($name = null) {
        if($name) {
            $component = Components::where("name", $name)->whereIn("category", ["component", "basic", "web"])->first();
            if(!$component)
            return redirect()->back()->with("error", "Component does not exists!");
        }
        $delete = [];
        $this->fetchComponentID($name, $delete);
        Components::destroy($delete);
        if($component->category == "web") {
            $component_pages = PageComponent::select('page_id')->where('component_id', $component->id)->groupBy('page_id')->get();
            PageComponent::where('component_id', $component->id)->delete();
            foreach($component_pages as $component_page) {
                $components = PageComponent::where(["page_id" => $component_page->page_id])->orderBy('order')->get();
                if($component->type == "main")
                PageContent::where('page_id', $component_page->page_id)->update(["broked" => 1]);
                $i = 1;
                foreach($components as $component_) {
                    $component_->order = $i++;
                    $component_->save();
                }
            }
        }
        return redirect()->back()->with("message", "Component " .$name. " deleted successfully!");
    }
}