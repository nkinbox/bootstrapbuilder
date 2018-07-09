<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Template;
use App\Models\Page;
use App\Models\Content;
use App\Rules\alpha_dash_space;
use Auth;

class TemplateController extends Controller
{
    private $response;
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
}
