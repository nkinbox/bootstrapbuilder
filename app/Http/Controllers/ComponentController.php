<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Components;

class ComponentController extends Controller
{
    public function index() {
        $basicComponents = Components::select("id", "name")->where("category", "basic")->get();
        return view('Component.create',["basicComponents" => $basicComponents]);
    }
    public function addBasic() {
        return view('Component.addBasic');
    }
    public function add(Request $request) {
        $request->validate([
            "self.name" => "required|string|max:50",
            "self.category" => "required|in:basic,component",
            "self.content" => "required_if:self.content_type,static,variable|nullable|string|max:1000",
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
            "child.*.content" => "required_if:child.*.content_type,static,variable|nullable|string|max:1000",
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
        $components = Components::where("name", $name)->orderBy("child_order")->get();
        $response = [];
        $response["basic"] = 0;
        foreach($components as $key => $component) {
            if($component->node == "self" && $component->category == "basic")
            $response["basic"] = 1;
            $components[$key]->var_attributes = json_decode($component->var_attributes);
            $components[$key]->classes = json_decode($component->classes);
            $components[$key]->attributes = json_decode($component->attributes);
            $components[$key]->style = json_decode($component->style);
            if($component->node == "child")
            $response[$component->node][] = $components[$key];
            else
            $response[$component->node] = $components[$key];
        }
        return response()->json($response);
    }
}
