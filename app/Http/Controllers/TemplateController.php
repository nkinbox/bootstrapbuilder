<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Template;
use App\Models\Page;
use App\Models\Content;
use App\Models\Components;
use App\Rules\alpha_dash_space;
use Auth;

class TemplateController extends Controller
{
    private $response;
    private function DBComponent(&$request, $id, $name = "") {
        if($name) {
            $component = new Components;
            $component->name = $name;
        }
        else
        $component = Components::find($id);
        $component->page_id = $request->page_id;
        $component->type = $request->type[$id];
        $component->order = $request->order[$id];
        $component->geolocation = $request->geolocation[$id];
        $component->category = $request->category[$id];
        $component->node = $request->node[$id];
        $component->visibility = $request->visibility[$id];
        $component->content_type = $request->content_type[$id];
        $component->child_order = $request->child_order[$id];
        $component->loop_source = $request->loop_source[$id];
        $component->start_tag = $request->start_tag[$id];
        $component->end_tag = $request->end_tag[$id];
        $component->attributes = $request->attribute[$id];
        $component->var_attributes = $request->var_attribute[$id];
        $component->classes = $request->classes[$id];
        $component->style = $request->style[$id];
        if($request->content_type[$id] != "element")
        $component->content = ((isset($request->content[$id]))?$request->content[$id]:null);
        $component->save();
        return $component->id;
    }
    public function index($operation = null, $id = null) {
        $this->response = [
            "breadcrumbs" => [[
                "route" => "Template.index",
                "routePar" => [],
                "name" => '<i class="fa fa-home"></i>'
            ]]
        ];
        if($operation) {
            $this->response["operation"] = $operation;
            $this->response["template"] = null;
            if($id) {
                $this->response["template"] = Template::find($id);
            }
            if($operation == "add" || $operation == "edit") {
                $this->response["breadcrumbs"][] = [
                    "route" => null,
                    "routePar" => [],
                    "name" => (($operation == "add")?'Create Template':'Edit ' .$this->response["template"]->title)
                ];
                return view('Template.Forms.template', $this->response);
            }
        } else {
            $this->response["templates"] = Template::paginate(100);
        }
        return view('Template.index', $this->response);
    }
    public function template_add(Request $request) {
        $request->validate([
            "title" => ['required',new alpha_dash_space,'max:50'],
            "js_content" => "nullable|string|max:65000",
            "css_content" => "nullable|string|max:65000"
        ]);
        $script_id = 0;
        $css_id = 0;
        if($request->has('js_content') && $request->js_content) {
            $content = new Content;
            $content->content_type = "text";
            $content->content = $request->js_content;
            $content->user_id = Auth::id();
            $content->save();
            $script_id = $content->id;
        }
        if($request->has('css_content') && $request->css_content) {
            $content = new Content;
            $content->content_type = "text";
            $content->content = $request->css_content;
            $content->user_id = Auth::id();
            $content->save();
            $css_id = $content->id;
        }
        $template = new Template;
        $template->title = $request->title;
        $template->script_id = $script_id;
        $template->css_id = $css_id;
        $template->user_id = Auth::id();
        $template->save();
        return redirect()->route('Template.index')->with("message", $request->title. " Created Successfully.");
    }
    public function template_edit(Request $request) {
        $request->validate([
            "id" => "required|exists:templates",
            "title" => ['required',new alpha_dash_space,'max:50'],
            "js_content" => "nullable|string|max:65000",
            "css_content" => "nullable|string|max:65000"
        ]);
        $template = Template::find($request->id);
        $script_id = 0;
        $css_id = 0;
        if($request->has('js_content') && $request->js_content) {
            if($template->script_id)
            $content = $template->getScript;
            else
            $content = new Content;
            $content->content_type = "text";
            $content->content = $request->js_content;
            $content->user_id = Auth::id();
            $content->save();
            $script_id = $content->id;
        } else {
            if($template->script_id)
            $template->getScript->delete();
        }
        if($request->has('css_content') && $request->css_content) {
            if($template->css_id)
            $content = $template->getCSS;
            else
            $content = new Content;
            $content->content_type = "text";
            $content->content = $request->css_content;
            $content->user_id = Auth::id();
            $content->save();
            $css_id = $content->id;
        } else {
            if($template->css_id)
            $template->getCSS->delete();
        }
        $template->title = $request->title;
        $template->script_id = $script_id;
        $template->css_id = $css_id;
        $template->user_id = Auth::id();
        $template->save();
        return redirect()->route('Template.index')->with("message", $request->title. " Edited Successfully.");
    }
    public function template_delete($id) {
        Template::destroy($id);
        return redirect()->route('Template.index')->with("message", "Template Deleted Successfully.");
    }
    public function page($template_id, $operation = null, $id = null) {
        $this->response = [
            "breadcrumbs" => [[
                "route" => "Template.index",
                "routePar" => [],
                "name" => '<i class="fa fa-home"></i>'
            ]]
        ];
        $this->response["template"] = Template::find($template_id);
        $this->response["breadcrumbs"][] = [
            "route" => "Template.Page",
            "routePar" => ["template_id" => $template_id],
            "name" => $this->response["template"]->title
        ];
        if($operation) {
            $this->response["operation"] = $operation;
            $this->response["page"] = null;
            if($id) {
                $this->response["page"] = Page::find($id);
            }
            if($operation == "add" || $operation == "edit") {
                $this->response["breadcrumbs"][] = [
                    "route" => null,
                    "routePar" => [],
                    "name" => (($operation == "add")?'Create Page':'Edit ' .$this->response["page"]->title)
                ];
                return view('Template.Forms.page', $this->response);
            } else if($operation == "show") {
                $this->response["breadcrumbs"][] = [
                    "route" => "Template.index",
                    "routePar" => [],
                    "name" => $this->response["template"]->title
                ];
                return view('Template.page', $this->response);
            }
        }
        return view('Template.page', $this->response);
    }
    public function page_add(Request $request) {
        $request->validate([
            "template_id" => "required|exists:templates,id",
            "title" => ['required',new alpha_dash_space,'max:50'],
            "url" => "required|string|max:250",
            "meta_content" => "nullable|string|max:65000",
            "js_content" => "nullable|string|max:65000",
            "css_content" => "nullable|string|max:65000"
        ]);
        $meta_id = 0;
        $script_id = 0;
        $css_id = 0;
        if($request->has('meta_content') && $request->meta_content) {
            $content = new Content;
            $content->content_type = "text";
            $content->content = $request->meta_content;
            $content->user_id = Auth::id();
            $content->save();
            $meta_id = $content->id;
        }
        if($request->has('js_content') && $request->js_content) {
            $content = new Content;
            $content->content_type = "text";
            $content->content = $request->js_content;
            $content->user_id = Auth::id();
            $content->save();
            $script_id = $content->id;
        }
        if($request->has('css_content') && $request->css_content) {
            $content = new Content;
            $content->content_type = "text";
            $content->content = $request->css_content;
            $content->user_id = Auth::id();
            $content->save();
            $css_id = $content->id;
        }
        $page = new Page;
        $page->template_id = $request->template_id;
        $page->url = $request->url;
        $page->title = $request->title;
        $page->meta_id = $meta_id;
        $page->script_id = $script_id;
        $page->css_id = $css_id;
        $page->user_id = Auth::id();
        $page->save();
        return redirect()->route('Template.Page', ['template_id' => $request->template_id])->with("message", $request->title. " Created Successfully.");
    }
    public function page_edit(Request $request) {
        $request->validate([
            "id" => "required|exists:pages",
            "template_id" => "required|exists:templates,id",
            "title" => ['required',new alpha_dash_space,'max:50'],
            "url" => "required|string|max:250",
            "meta_content" => "nullable|string|max:65000",
            "js_content" => "nullable|string|max:65000",
            "css_content" => "nullable|string|max:65000"
        ]);
        $page = Page::find($request->id);
        $meta_id = 0;
        $script_id = 0;
        $css_id = 0;
        if($request->has('meta_content') && $request->meta_content) {
            if($page->meta_id)
            $content = $page->getMetadata;
            else
            $content = new Content;
            $content->content_type = "text";
            $content->content = $request->meta_content;
            $content->user_id = Auth::id();
            $content->save();
            $meta_id = $content->id;
        } else {
            if($page->meta_id)
            $page->getMetadata->delete();
        }
        if($request->has('js_content') && $request->js_content) {
            if($page->script_id)
            $content = $page->getScript;
            else
            $content = new Content;
            $content->content_type = "text";
            $content->content = $request->js_content;
            $content->user_id = Auth::id();
            $content->save();
            $script_id = $content->id;
        } else {
            if($page->script_id)
            $page->getScript->delete();
        }
        if($request->has('css_content') && $request->css_content) {
            if($page->css_id)
            $content = $page->getCSS;
            else
            $content = new Content;
            $content->content_type = "text";
            $content->content = $request->css_content;
            $content->user_id = Auth::id();
            $content->save();
            $css_id = $content->id;
        } else {
            if($page->css_id)
            $page->getCSS->delete();
        }
        $page->template_id = $request->template_id;
        $page->url = $request->url;
        $page->title = $request->title;
        $page->meta_id = $meta_id;
        $page->script_id = $script_id;
        $page->css_id = $css_id;
        $page->user_id = Auth::id();
        $page->save();
        return redirect()->route('Template.Page', ['template_id' => $request->template_id])->with("message", $request->title. " Edited Successfully.");
    }
    public function page_delete($template_id, $id) {
        Page::destroy($id);
        return redirect()->route('Template.Page', ['template_id' => $template_id])->with("message", "Page Deleted Successfully.");
    }
    public function page_component($page_id, $operation = null, $id = null) {
        $this->response = [
            "breadcrumbs" => [[
                "route" => "Template.index",
                "routePar" => [],
                "name" => '<i class="fa fa-home"></i>'
            ]]
        ];
        $this->response["page"] = Page::find($page_id);
        $this->response["breadcrumbs"][] = [
            "route" => "Template.Page",
            "routePar" => ["template_id" => $this->response["page"]->Template->id],
            "name" => $this->response["page"]->Template->title
        ];
        $this->response["breadcrumbs"][] = [
            "route" => "Template.Page.Component",
            "routePar" => ["page_id" => $this->response["page"]->id],
            "name" => $this->response["page"]->title
        ];
        if($operation) {
            $this->response["page_id"] = $page_id;
            $this->response["operation"] = $operation;
            $this->response["component"] = null;
            if($id) {
                $this->response["component"] = Components::find($id);
            }
            if($operation == "add" || $operation == "edit") {
                $this->response["breadcrumbs"][] = [
                    "route" => null,
                    "routePar" => [],
                    "name" => (($operation == "add")?'Add Component':'Edit Component')
                ];
                return view('Template.Forms.component', $this->response);
            } else if($operation == "show") {
                $this->response["breadcrumbs"][] = [
                    "route" => "Template.index",
                    "routePar" => [],
                    "name" => $this->response["template"]->title
                ];
                return view('Template.page', $this->response);
            }
        } else {
            $this->response["breadcrumbs"][] = [
                "route" => null,
                "routePar" => [],
                "name" => "Components"
            ];
            $gloc = function($component, &$geolocation) use (&$gloc) {
                if(!$geolocation) {
                    if($component->Parent && $component->Parent->geolocation) {
                        $geolocation = true;
                    } elseif($component->geolocation) {
                        $geolocation = true;
                    } elseif(count($component->Children)) {
                        foreach ($component->Children as $key => $value) {
                            if($value->geolocation) {
                                $geolocation = true;
                                break;
                            }
                        }
                        if(!$geolocation) {
                            foreach ($component->Children as $key => $value) {
                                if($value->nested_component) {
                                    $gloc($value->nestedComponent, $geolocation);
                                }
                                if($geolocation)
                                break;
                            }
                        }
                    } elseif($component->nested_component) {
                        $gloc($component->nestedComponent, $geolocation);
                    }
                }
            };
            $this->response["checkGeolocation"] = $gloc;
        }
        return view('Template.component', $this->response);
    }
    public function page_component_add(Request $request) {
        $request->validate([
            "id" => "required|exists:components",
            "page_id" => "required|exists:pages,id",
            "name" => "required|string|max:45|unique:components,name",
            "type.*" => "required|in:body,header,footer",
            "category.*" => "required|in:basic,element,component,web",
            "node.*" => "required|in:self,parent,child",
            "geolocation.*" => "required|integer",
            "visibility.*" => "required|in:auth,guest,show,none",
            "content_type.*" => "required|in:static,variable,element",
            "loop_source.*" => "nullable|string|max:200",
            "child_order.*" => "required|numeric",
            "nested_component.*" => "nullable|integer",
            "start_tag.*" => "required|string|max:10",
            "end_tag.*" => "required|nullable|string|max:10",
            "attribute.*" => "required|json|max:500",
            "var_attribute.*" => "nullable|json|max:500",
            "classes.*" => "required|json|max:500",
            "style.*" => "required|json|max:500",
            "content.*" => "nullable|string|max:1000"
        ]);
        $ides = [];
        foreach($request->type as $key => $val) {
            $ides[$key] = $this->DBComponent($request, $key, $request->name);
        }
        $page = Page::find($request->page_id);
        $order = 1 + $page->Components->count();
        $component = Components::find($ides[$request->id]);
        $component->category = "web";
        $component->order = $order;
        $component->save();
        $i = 1;
        if($request->has('nested_component')) {
            foreach($request->nested_component as $key => $nested) {
                $whereIn = [$ides[$nested]];
                $component = Components::find($ides[$key]);
                $component->nested_component = $ides[$nested];
                $component->save();
                if(isset($request->children[$nested])) {
                    foreach($request->children[$nested] as $child)
                    $whereIn[] = $ides[$child];
                }
                if(isset($request->parent[$nested])) {
                    $whereIn[] = $ides[$request->parent[$nested]];
                }
                $name = $request->name . $i;
                Components::whereIn('id', $whereIn)->update(['name' => $name]);
                $i++;
            }
        }
        return redirect()->route('Template.Page.Component', ['page_id'=>$request->page_id])->with("message", $request->name." Added Successfully!");
    }
    public function page_component_edit(Request $request) {
        $request->validate([
            "id" => "required|exists:components",
            "page_id" => "required|exists:pages,id",
            "name" => "required|string|max:45|exists:components,name",
            "type.*" => "required|in:body,header,footer",
            "category.*" => "required|in:basic,element,component,web",
            "node.*" => "required|in:self,parent,child",
            "geolocation.*" => "required|integer",
            "visibility.*" => "required|in:auth,guest,show,none",
            "content_type.*" => "required|in:static,variable,element",
            "loop_source.*" => "nullable|string|max:200",
            "child_order.*" => "required|numeric",
            "nested_component.*" => "nullable|integer",
            "start_tag.*" => "required|string|max:10",
            "end_tag.*" => "required|nullable|string|max:10",
            "attribute.*" => "required|json|max:500",
            "var_attribute.*" => "nullable|json|max:500",
            "classes.*" => "required|json|max:500",
            "style.*" => "required|json|max:500"
        ]);
        foreach($request->type as $key => $val) {
            $this->DBComponent($request, $key);
        }
        return redirect()->route('Template.Page.Component', ['page_id'=>$request->page_id])->with("message", $request->name." Edited Successfully!");
    }
    public function page_component_order(Request $request) {
        $request->validate([
            "page_id" => "required|exists:pages,id",
            "order" => "required|array"
        ]);
        foreach($request->order as $id => $order){
            $component = Components::find($id) ;
            $component->order = $order;
            $component->save();
        }
        return redirect()->back()->with("message", "Component order changed successfully.");
    }
    public function page_component_delete($id) {
        $ides = [];
        $find = function($component, &$ides) use (&$find) {
            if($component->Parent)
            $ides[] = $component->Parent->id;
            $ides[] = $component->id;
            if(count($component->Children)) {
                foreach($component->Children as $child) {
                    $ides[] = $child->id;
                    if($child->nested_component)
                    $find($child->nestedComponent, $ides);
                }
            }
            if($component->nested_component)
            $find($component->nestedComponent, $ides);
        };
        $component = Components::find($id);
        $page = Page::find($component->page_id);
        $change = false;
        foreach($page->Components as $component_) {
            if($change) {
                $component_->order = $component_->order - 1;
                $component_->save();
            }
            if($component_->id == $component->id)
            $change = true;
        }
        if($component) {
            $find($component, $ides);
            Components::destroy($ides);
            return redirect()->route('Template.Page.Component', ['page_id'=>$component->page_id])->with("message", $component->name." Deleted Successfully!");
        }
        return redirect()->back()->with("error", "An Error Occured");
    }
    public function view($id, $mode, $country = null) {
        $page = Page::find($id);
        if($page)
        return view('Page.index', ['page' => $page, 'mode' => $mode, 'country' => $country]);
        return redirect()->route('home');
    }
}
