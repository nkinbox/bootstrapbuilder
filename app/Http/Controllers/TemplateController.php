<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Template;
use App\Models\Page;
use App\Models\Content;
use App\Models\Components;
use App\Models\PageContent;
use App\Models\PageComponent;
use App\Models\LoopSource;
use App\Models\DatabaseVariable;
use App\Models\Variables;
use App\Models\WebUrl;
use App\Rules\alpha_dash_space;
use Auth;
use Cookie;
use View;

class TemplateController extends Controller
{
    private $response;
    private $propertyResolver;
    function __construct() {
        $this->propertyResolver = function($toresolve, &$loops, $debug = false) {
            //$database_variables = "@@database.1.2.3|(first/last)@@"
            $temp = explode("|", $toresolve."|last");
            $database_variables = explode(".", $temp[0]);
            $relation = $temp[1];
            $eval = "";
            $hasOneRelation = false;
            foreach($database_variables as $variable) {
                if($variable && $db_var = \App\Models\DatabaseVariable::find($variable)) {
                    $isRelation = false;
                    if($relation == "first") {
                        for($i = 0; $i < count($loops); $i++) {
                            if(isset($loops[$i]['related'][$db_var->object])) {
                                $isRelation = true;
                                if($loops[$i]['isArray'])
                                $eval = "\$value".$i;
                                else
                                $eval = $loops[$i]['model_var'];
                                break;
                            }
                        }
                    } else {
                        for($i = (count($loops) - 1); $i >= 0; $i--) {
                            if(isset($loops[$i]['related'][$db_var->object])) {
                                $isRelation = true;
                                if($loops[$i]['isArray'])
                                $eval = "\$value".$i;
                                else
                                $eval = $loops[$i]['model_var'];
                                break;
                            }
                        }
                    }
                    if(!$isRelation) {
                        $isSet = false;
                        if($relation == "first") {
                            for($i = 0; $i < count($loops); $i++) {
                                if(isset($loops[$i]['loaded'][$db_var->object])) {
                                    $isSet = true;
                                    if($loops[$i]['isArray'])
                                    $eval = "\$value".$i;
                                    else
                                    $eval = $loops[$i]['model_var'];
                                    break;
                                }
                            }
                        } else {
                            for($i = (count($loops) - 1); $i >= 0; $i--) {
                                if(isset($loops[$i]['loaded'][$db_var->object])) {
                                    $isSet = true;
                                    if($loops[$i]['isArray'])
                                    $eval = "\$value".$i;
                                    else
                                    $eval = $loops[$i]['model_var'];
                                    break;
                                }
                            }
                        }
                        if($db_var->property) {
                            if($isSet) {
                                $eval .= "->".$db_var->property;
                                if(!$db_var->is_array && $db_var->related_to) {
                                    $hasOneRelation = $db_var->related_to;
                                }
                            }
                        }
                    } else {
                        $eval .= "->".$db_var->property;
                        if(!$db_var->is_array && $db_var->related_to) {
                            $hasOneRelation = $db_var->related_to;
                        }
                    }
                    if($eval && $hasOneRelation && $hasOneRelation != $db_var->related_to) {
                        $eval .= "->".$db_var->property;
                        $hasOneRelation = false;
                    }
                }
            }
            $result = str_replace("@","",$toresolve);
            if($eval) {
                if($debug)
                $result = "\".((isset(".$eval."))?".$eval.":'').\"<!--".str_replace("$", "\\$", $eval)."-->";
                else
                $result = "\".((isset(".$eval."))?".$eval.":'').\"";
            }
            return $result;
        };
    }
    private function DBComponent(&$request, $id, $name = "") {
        if($name) {
            $component = new Components;
            $component->name = $name;
        }
        else
        $component = Components::find($id);
        $component->template_id = $request->template_id;
        $component->visibility_id = $request->visibility_id[$id];
        $component->geolocation = $request->geolocation[$id];
        $component->type = $request->type[$id];
        $component->category = $request->category[$id];
        $component->node = $request->node[$id];
        $component->visibility = $request->visibility[$id];
        $component->content_type = $request->content_type[$id];
        $component->child_order = $request->child_order[$id];
        $component->loop_source = $request->loop_source[$id];
        $component->start_tag = $request->start_tag[$id];
        $component->end_tag = $request->end_tag[$id];
        $component->attributes = $request->attribute[$id];
        // $component->var_attributes = $request->var_attribute[$id];
        $component->classes = $request->classes[$id];
        $component->style = $request->style[$id];
        $component->script = $request->script[$id];
        $component->content = ((isset($request->content[$id]))?$request->content[$id]:null);
        $component->save();
        return $component->id;
    }
    private function HTMLTouchUp($template_id, $url, $html, &$variables, $geolocation) {
        $propertyResolver = $this->propertyResolver;
        $html = preg_replace_callback('/@@component\.(.*?)@@/', function($match_) use ($url, $propertyResolver) {
            $component = \App\Models\Components::find($match_[1]);
            if($component) {
                $view = \Illuminate\Support\Facades\View::make('Website.element', [
                    'id' => 0,
                    'element' => $component,
                    'url' => $url,
                    'loop_count' => -1,
                    'loops' => [],
                    'propertyResolver' => $propertyResolver
                ]);
                // dd($view->render());
                return $view->render();
            } else return "";
        }, $html);
        $html = preg_replace_callback('/@@currency\.(.*?)@@/', function($match_) use ($geolocation) {
            $temp = explode(".", $match_[1]);
            if(count($temp) == 2) {
                dd(round($geolocation['conversion']*$temp[1]));
                return (($geolocation['currency'])?$geolocation['currency']:$temp[0]). " " .(($geolocation['conversion'])?round($geolocation['conversion']*$temp[1]):$temp[1]);
            }
            return "";
        }, $html);
        //sets variable to $variables[] @@variable.key.value@@
        $html = preg_replace_callback('/@@variable\.(.*?)@@/', function($match_) use (&$variables) {
            $temp = explode(".", $match_[1]);
            if(count($temp) == 2) {
                if(array_key_exists($temp[0], $variables)) {
                    if(is_array($variables[$temp[0]])) {
                        $variables[$temp[0]][] = $temp[1];
                    } else {
                        $variables[$temp[0]] = [$variables[$temp[0]], $temp[1]];
                    }
                } else {
                    $variables[$temp[0]] = $temp[1];
                }
            }
            return "";
        }, $html);
        $html = preg_replace_callback('/id=@@image\.(.*?)@@/', function($match_) {
            $image = \App\Models\Images::find($match_[1]);
            return 'src="' .(($image)?asset('storage/'.$image->file_name):'#'). '" title="' .(($image)?$image->image_title:'Image'). '"';
        }, $html);
        $html = preg_replace_callback('/@@image\.(.*?)@@/', function($match_) {
            $image = \App\Models\Images::find($match_[1]);
            return (($image)?asset('storage/'.$image->file_name):'#');
        }, $html);
        //replaces Evaluates Variable
        $html = preg_replace_callback('/@@evaluate\.(.*?)@@/', function($match_) use ($template_id, &$variables) {
            $variable = \App\Models\Variables::where(["template_id" => $template_id, "variable_name" => $match_[1]])->first();
            if($variable) {
                if($variable->is_php){
                    eval($variable->php_code);
                    return $evaluate;
                } else return $variable->evaluate;
            } else return "";
        }, $html);
        $auth = Auth::user();
        if($auth) {
            $html = preg_replace_callback('/@@auth\.(.*?)@@/', function($match_) use ($auth) {
                return $auth->{$match_[1]};
            }, $html);
        }
        $html = preg_replace_callback('/@@php(.*?)phpend@@/', function($match_) use ($variables) {
            $evaluated = "";
            if($match_[1]) {
                $eval = str_replace("@", "$", $match_[1]);
                eval($eval);
            }
            return $evaluated;
        }, $html);
        $html = preg_replace_callback('/@@url\.(.*?)@@/', function($match_) use ($variables) {
            return strtolower(str_replace(" ", "-", $match_[1]));
        }, $html);
        //replaces Variables
        $html = preg_replace_callback('/@@(.*?)@@/', function($match_) use ($variables, $url) {
            $evaluated = "";
            if($match_[1]) {
                $temp = explode(".", $match_[1]);
                if(array_key_exists($temp[0], $variables)) {
                    if(count($temp) == 2) {
                        $evaluated = $variables[$temp[0]][$temp[1]];
                    } else {
                        $evaluated = $variables[$temp[0]];
                    }
                } elseif(array_key_exists($temp[0], $url)) {
                    $evaluated = $url[$temp[0]];
                }
            }
            return $evaluated;
        }, $html);
        $html = str_replace("\\/", "/", $html);
        return $html;
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
            $this->response["templates"] = Template::orderBy('is_website')->orderBy('title')->paginate(100);
        }
        return view('Template.index', $this->response);
    }
    public function template_add(Request $request) {
        $request->validate([
            "title" => "required|string|max:50|unique:templates,title",
            "is_website" => "required|boolean",
            "js_content" => "nullable|string|max:65500",
            "css_content" => "nullable|string|max:65500"
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
        $template->is_website = $request->is_website;
        $template->title = $request->title;
        $template->script_id = $script_id;
        $template->css_id = $css_id;
        $template->user_id = Auth::id();
        $template->save();
        if($request->has('template_id') && $request->template_id && $parentTemplate = Template::find($request->template_id)) {
            $save = false;
            if($parentTemplate->script_id) {
                $save = true;
                if($template->script_id) {
                    $content = Content::find($template->script_id);
                } else {
                    $content = new Content;
                }
                $content->content_type = "text";
                $content->content = $parentTemplate->getScript->content;
                $content->user_id = Auth::id();
                $content->save();
                $template->script_id = $content->id;
            }
            if($parentTemplate->css_id) {
                $save = true;
                if($template->css_id) {
                    $content = Content::find($template->css_id);
                } else {
                    $content = new Content;
                }
                $content->content_type = "text";
                $content->content = $parentTemplate->getCSS->content;
                $content->user_id = Auth::id();
                $content->save();
                $template->css_id = $content->id;
            }
            if($save)
            $template->save();
            $componentIndex = [];
            if($parentTemplate->AllComponents) {
                $nested = [];
                foreach($parentTemplate->AllComponents as $components) {
                    $component = new Components;
                    $component->name = $components->name ."_".$template->id;
                    $component->template_id = $template->id;
                    $component->visibility_id = $components->visibility_id;
                    $component->geolocation = $components->geolocation;
                    $component->type = $components->type;
                    $component->category = $components->category;
                    $component->node = $components->node;
                    $component->visibility = $components->visibility;
                    $component->content_type = $components->content_type;
                    $component->child_order = $components->child_order;
                    $component->loop_source = $components->loop_source;
                    $component->start_tag = $components->start_tag;
                    $component->end_tag = $components->end_tag;
                    $component->attributes = $components->attributes;
                    // $component->var_attributes = $components->var_attributes;
                    $component->classes = $components->classes;
                    $component->style = $components->style;
                    $component->script = $components->script;
                    $component->content = $components->content;
                    $component->save();
                    if($components->nested_component)
                    $nested[$components->nested_component] = $components->id;
                    $componentIndex[$components->id] = $component->id;
                }
                foreach($nested as $k => $val) {
                    Components::where('id', $componentIndex[$val])->update(['nested_component' => $componentIndex[$k]]);
                }
            }
            if($parentTemplate->Pages) {
                foreach($parentTemplate->Pages as $pages) {
                    $script_id = 0;
                    $css_id = 0;
                    if($pages->script_id) {
                        $content = new Content;
                        $content->content_type = "text";
                        $content->content = $pages->getScript->content;
                        $content->user_id = Auth::id();
                        $content->save();
                        $script_id = $content->id;
                    }
                    if($pages->css_id) {
                        $content = new Content;
                        $content->content_type = "text";
                        $content->content = $pages->getCSS->content;
                        $content->user_id = Auth::id();
                        $content->save();
                        $css_id = $content->id;
                    }
                    $page = new Page;
                    $page->template_id = $template->id;
                    $page->title = $pages->title;
                    $page->script_id = $script_id;
                    $page->css_id = $css_id;
                    $page->user_id = Auth::id();
                    $page->save();
                    if(count($pages->AllComponents)) {
                        foreach($pages->AllComponents as $pageComponents){
                            $pageComponent = new PageComponent;
                            $pageComponent->page_id = $page->id;
                            $pageComponent->component_id = $componentIndex[$pageComponents->component_id];
                            $pageComponent->order = $pageComponents->order;
                            $pageComponent->save();
                        }
                    }
                    if(count($pages->URLs)) {
                        foreach($pages->URLs as $weburl) {
                            $url = new WebUrl;
                            $url->template_id = $template->id;
                            $url->page_id = $page->id;
                            $url->url = $weburl->url;
                            $url->geolocation = $weburl->geolocation;
                            $url->regex = $weburl->regex;
                            $url->matches = $weburl->matches;
                            $url->url_variables = $weburl->url_variables;
                            $url->url_builder = $weburl->url_builder;
                            $url->user_id = Auth::id();
                            $url->save();
                        }
                    }
                }
            }
            if(count($parentTemplate->GlobalVariables)) {
                foreach($parentTemplate->GlobalVariables as $variables) {
                    $variable = new Variables;
                    $variable->template_id = $template->id;
                    $variable->variable_name = $variables->variable_name;
                    $variable->is_php = $variables->is_php;
                    $variable->evaluate = $variables->evaluate;
                    $variable->php_code = $variables->php_code;
                    $variable->save();
                }
            }
        }
        return redirect()->route('Template.index')->with("message", $request->title. " Created Successfully.");
    }
    public function template_edit(Request $request) {
        $request->validate([
            "id" => "required|exists:templates",
            "is_website" => "required|boolean",
            "title" => "required|string|max:50",
            "js_content" => "nullable|string|max:65500",
            "css_content" => "nullable|string|max:65500"
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
        $template->is_website = $request->is_website;
        $template->script_id = $script_id;
        $template->css_id = $css_id;
        $template->user_id = Auth::id();
        $template->save();
        if($request->has('template_id') && $request->template_id && $parentTemplate = Template::find($request->template_id)) {
            $save = false;
            /*if($parentTemplate->script_id) {
                $save = true;
                if($template->script_id) {
                    $content = Content::find($template->script_id);
                } else {
                    $content = new Content;
                }
                $content->content_type = "text";
                $content->content = $parentTemplate->getScript->content;
                $content->user_id = Auth::id();
                $content->save();
                $template->script_id = $content->id;
            }
            if($parentTemplate->css_id) {
                $save = true;
                if($template->css_id) {
                    $content = Content::find($template->css_id);
                } else {
                    $content = new Content;
                }
                $content->content_type = "text";
                $content->content = $parentTemplate->getCSS->content;
                $content->user_id = Auth::id();
                $content->save();
                $template->css_id = $content->id;
            }
            if($save)
            $template->save();*/
            $componentIndex = [];
            if($parentTemplate->AllComponents) {
                $nested = [];
                foreach($parentTemplate->AllComponents as $components) {
                    $exists = Components::where(['name' => $components->name ."_".$template->id, "node" => $components->node, "child_order" => $components->child_order])->first();
                    if($exists == null) {
                        $component = new Components;
                        $component->name = $components->name ."_".$template->id;
                        $component->template_id = $template->id;
                        $component->visibility_id = $components->visibility_id;
                        $component->geolocation = $components->geolocation;
                        $component->type = $components->type;
                        $component->category = $components->category;
                        $component->node = $components->node;
                        $component->visibility = $components->visibility;
                        $component->content_type = $components->content_type;
                        $component->child_order = $components->child_order;
                        $component->loop_source = $components->loop_source;
                        $component->start_tag = $components->start_tag;
                        $component->end_tag = $components->end_tag;
                        $component->attributes = $components->attributes;
                        // $component->var_attributes = $components->var_attributes;
                        $component->classes = $components->classes;
                        $component->style = $components->style;
                        $component->script = $components->script;
                        $component->content = $components->content;
                        $component->save();
                        if($components->nested_component)
                        $nested[$components->nested_component] = $components->id;
                        $componentIndex[$components->id] = $component->id;
                    } else {
                        $componentIndex[$components->id] = $exists->id;
                    }
                }
                foreach($nested as $k => $val) {
                    Components::where('id', $componentIndex[$val])->update(['nested_component' => $componentIndex[$k]]);
                }
            }
            if($parentTemplate->Pages) {
                foreach($parentTemplate->Pages as $pages) {
                    if(Page::where(["template_id" => $template->id, "title" => $pages->title])->first() == null) {
                        $script_id = 0;
                        $css_id = 0;
                        if($pages->script_id) {
                            $content = new Content;
                            $content->content_type = "text";
                            $content->content = $pages->getScript->content;
                            $content->user_id = Auth::id();
                            $content->save();
                            $script_id = $content->id;
                        }
                        if($pages->css_id) {
                            $content = new Content;
                            $content->content_type = "text";
                            $content->content = $pages->getCSS->content;
                            $content->user_id = Auth::id();
                            $content->save();
                            $css_id = $content->id;
                        }
                        $page = new Page;
                        $page->template_id = $template->id;
                        $page->title = $pages->title;
                        $page->script_id = $script_id;
                        $page->css_id = $css_id;
                        $page->user_id = Auth::id();
                        $page->save();
                        if(count($pages->AllComponents)) {
                            foreach($pages->AllComponents as $pageComponents){
                                $existspageComponent = Components::find($pageComponents->component_id);
                                $existing_component = Components::where(["template_id" => $template->id, "name" => ($existspageComponent->name . "_" . $template->id), "node" => "self"])->first();
                                if($existing_component) {
                                    if(PageComponent::where(["page_id" => $page->id, "component_id" => $existing_component->id, "order" => $pageComponents->order])->first() == null) {
                                        $pageComponent = new PageComponent;
                                        $pageComponent->page_id = $page->id;
                                        $pageComponent->component_id = $existing_component->id;
                                        $pageComponent->order = $pageComponents->order;
                                        $pageComponent->save();
                                    }
                                }
                            }
                        }
                        if(count($pages->URLs)) {
                            foreach($pages->URLs as $weburl) {
                                if(WebUrl::where(["template_id" => $template->id, "page_id" => $page->id, "url" => $weburl->url, "geolocation" => $weburl->geolocation])->first() == null) {
                                    $url = new WebUrl;
                                    $url->template_id = $template->id;
                                    $url->page_id = $page->id;
                                    $url->url = $weburl->url;
                                    $url->geolocation = $weburl->geolocation;
                                    $url->regex = $weburl->regex;
                                    $url->matches = $weburl->matches;
                                    $url->url_variables = $weburl->url_variables;
                                    $url->url_builder = $weburl->url_builder;
                                    $url->user_id = Auth::id();
                                    $url->save();
                                }
                            }
                        }
                    }
                }
            }
            if(count($parentTemplate->GlobalVariables)) {
                foreach($parentTemplate->GlobalVariables as $variables) {
                    if(Variables::where(["template_id" => $template->id, "variable_name" => $variables->variable_name])->first() == null) {
                        $variable = new Variables;
                        $variable->template_id = $template->id;
                        $variable->variable_name = $variables->variable_name;
                        $variable->is_php = $variables->is_php;
                        $variable->evaluate = $variables->evaluate;
                        $variable->php_code = $variables->php_code;
                        $variable->save();
                    }
                }
            }
        }
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
            "meta_content" => "nullable|string|max:65500",
            "js_content" => "nullable|string|max:65500",
            "css_content" => "nullable|string|max:65500"
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
            "meta_content" => "nullable|string|max:65500",
            "js_content" => "nullable|string|max:65500",
            "css_content" => "nullable|string|max:65500"
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
                return view('Template.Forms.page_component', $this->response);
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
        return view('Template.page_component', $this->response);
    }
    public function page_component_add(Request $request) {
        $request->validate([
            "component_id" => "required|exists:components,id",
            "page_id" => "required|exists:pages,id",
        ]);
        $component = Components::find($request->component_id);
        if($component->type == "main")
        PageContent::where('page_id', $request->page_id)->update(["broked" => 1]);
        $pageComponents = PageComponent::where('page_id', $request->page_id)->get();
        $order = 1 + count($pageComponents);
        $pageComponent = new PageComponent;
        $pageComponent->page_id = $request->page_id;
        $pageComponent->component_id = $request->component_id;
        $pageComponent->order = $order;
        $pageComponent->save();
        return redirect()->route('Template.Page.Component', ['page_id'=>$request->page_id])->with("message", $component->name." Component Added Successfully!");
    }
    public function page_component_order(Request $request) {
        $request->validate([
            "page_id" => "required|exists:pages,id",
            "order" => "required|array"
        ]);
        $error = false;
        $components = PageComponent::where(["page_id" => $request->page_id])->orderBy('order')->get();
        if(count($request->order) == count($components)) {
            $change = true;
            $i = 0;
            foreach($request->order as $id => $order) {
                if(!(isset($components[$i]) && $components[$i]->component_id == explode("_",$id)[0]))
                $change = false;
                $i++;
            }
            if(!$change)
            $error = true;
            else {
                $i = 0;
                foreach($request->order as $id => $order) {
                    $components[$i]->order = $order;
                    $components[$i]->save();
                    $i++;
                }
            }
        } else $error = true;
        if($error)
        return redirect()->back()->with("error", "An error occured.");
        return redirect()->back()->with("message", "Component order changed successfully.");
    }
    public function page_component_delete($page_id, $id, $order) {
        $components = PageComponent::where(["page_id" => $page_id])->orderBy('order')->get();
        $broked = 0;
        $change = false;
        foreach($components as $component) {
            if($component->component_id == $id && $component->order == $order) {
                if($component->Component->type == "main")
                $broked = 1;
                $change = true;
                $component->delete();
            } elseif($change) {
                $component->order = $component->order - 1;
                $component->save();
            }
        }
        if($broked)
        PageContent::where('page_id', $page_id)->update(["broked" => 1]);
        return redirect()->route('Template.Page.Component', ['page_id'=>$page_id])->with("message", "Component Deleted Successfully!");
    }
    public function component($template_id, $operation = null, $id = null) {
        $this->response = [
            "breadcrumbs" => [[
                "route" => "Template.index",
                "routePar" => [],
                "name" => '<i class="fa fa-home"></i>'
            ]]
        ];
        $this->response["template"] = Template::find($template_id);
        $this->response["breadcrumbs"][] = [
            "route" => "Template.Component",
            "routePar" => ["template_id" => $this->response["template"]->id],
            "name" => $this->response["template"]->title
        ];
        if($operation) {
            $this->response["template_id"] = $template_id;
            $this->response["operation"] = $operation;
            $this->response["component"] = null;
            if($id) {
                $this->response["component"] = Components::find($id);
            }
            if($operation == "add" || $operation == "edit") {
                // $this->response["redirectTo"] = url()->previous();
                $this->response["breadcrumbs"][] = [
                    "route" => null,
                    "routePar" => [],
                    "name" => (($operation == "add")?'Add Component':'Edit Component')
                ];
                return view('Template.Forms.component', $this->response);
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
    public function component_add(Request $request) {
        $request->validate([
            "id" => "required|exists:components",
            "template_id" => "required|exists:templates,id",
            "visibility_id.*" => "required|integer",
            "name" => "required|string|max:45|unique:components,name",
            "type.*" => "required|in:body,header,footer,main",
            "category.*" => "required|in:basic,element,component,web",
            "node.*" => "required|in:self,parent,child",
            "geolocation.*" => "nullable|string|max:250",
            "visibility.*" => "required|in:auth,guest,show,none",
            "content_type.*" => "required|in:static,variable,element",
            "loop_source.*" => "nullable|integer",
            "child_order.*" => "required|numeric",
            "nested_component.*" => "nullable|integer",
            "start_tag.*" => "required|string|max:10",
            "end_tag.*" => "required|nullable|string|max:10",
            "attribute.*" => "required|json|max:500",
            // "var_attribute.*" => "nullable|json|max:500",
            "classes.*" => "required|json|max:500",
            "style.*" => "required|json|max:500",
            "script.*" => "nullable|string|max:2000",
            "content.*" => "nullable|string|max:65500"
        ]);
        $ides = [];
        foreach($request->type as $key => $val) {
            $ides[$key] = $this->DBComponent($request, $key, $request->name);
        }
        $component = Components::find($ides[$request->id]);
        $component->category = "web";
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
        return redirect()->route('Template.Component', ['template_id'=>$request->template_id])->with("message", $request->name." Added Successfully!");
    }
    public function component_edit(Request $request) {
        $request->validate([
            "id" => "required|exists:components",
            "template_id" => "required|exists:templates,id",
            "visibility_id.*" => "required|integer",
            "name" => "required|string|max:45|exists:components,name",
            "type.*" => "required|in:body,header,footer,main",
            "category.*" => "required|in:basic,element,component,web",
            "node.*" => "required|in:self,parent,child",
            "geolocation.*" => "nullable|string|max:250",
            "visibility.*" => "required|in:auth,guest,show,none",
            "content_type.*" => "required|in:static,variable,element",
            "loop_source.*" => "nullable|integer",
            "child_order.*" => "required|numeric",
            "nested_component.*" => "nullable|integer",
            "start_tag.*" => "required|string|max:10",
            "end_tag.*" => "required|nullable|string|max:10",
            "attribute.*" => "required|json|max:500",
            // "var_attribute.*" => "nullable|json|max:500",
            "classes.*" => "required|json|max:500",
            "style.*" => "required|json|max:500",
            "script.*" => "nullable|string|max:2000",
            "content.*" => "nullable|string|max:65500"
        ]);
        foreach($request->type as $key => $val) {
            $this->DBComponent($request, $key);
        }
        return redirect()->back()->with("message", $request->name." Edited Successfully!");
        // if(url()->previous() == $request->redirectTo)
        // return redirect()->route('Template.Component', ['template_id' => $request->template_id])->with("message", $request->name." Edited Successfully!");
        // return redirect($request->redirectTo)->with("message", $request->name." Edited Successfully!");
    }
    public function all_components_view($template_id) {
        $template = Template::find($template_id);
        if($template)
        return view('Template.Content.index', ['template' => $template]);
        return redirect()->route('home');
    }
    public function all_components_content(Request $request) {
        $response = [
            "success" => 0
        ];
        $request->validate([
            "template_id" => "required|exists:templates,id",
            "content" => "required|array",
            "content.*" => "nullable|string|max:65500"
        ]);
        $template = Template::find($request->template_id);
        if($template) {
            foreach($template->AllComponents as $component) {
                if(array_key_exists($component->id, $request->content)) {
                    $text = str_replace("\"@@","@@", str_replace("@@\"","@@", $request->content[$component->id]));
                    $text = preg_replace('/ src="(.*?)"/s', "", $text);
                    $text = html_entity_decode($text);
                    $component->content = $text;
                    $component->save();
                }
            }
            $response = [
                "success" => 1
            ];
        }
        return response()->json($response);
    }
    public function data_variable($operation = null, $id = null) {
        $this->response = [
            "breadcrumbs" => [[
                "route" => "Template.index",
                "routePar" => [],
                "name" => '<i class="fa fa-home"></i>'
            ]]
        ];
        $this->response["breadcrumbs"][] = [
            "route" => "Database",
            "routePar" => null,
            "name" => "Database Variable"
        ];
        $this->response["operation"] = $operation;
        if($operation && ($operation == "add" || $operation == "edit")) {
            $this->response["database"] = null;
            $this->response["breadcrumbs"][] = [
                "route" => null,
                "routePar" => null,
                "name" => ucwords($operation)
            ];
            if($operation == "edit") {
            $this->response["database"] = DatabaseVariable::find($id);
            }
            return view('Template.Forms.database', $this->response);
        } else {
            $this->response["database"] = DatabaseVariable::orderBy('object')->get();
        }
        return view('Template.database', $this->response);
    }
    public function data_variable_add(Request $request) {
        $request->validate([
            "object" => "required|string|max:50",
            "property" => "nullable|string|max:50",
            "is_array" => "required|boolean",
            "related_to" => "required|integer"
        ]);
        $database = new DatabaseVariable;
        $database->object = $request->object;
        $database->property = $request->property;
        $database->is_array = $request->is_array;
        $database->related_to = $request->related_to;
        $database->save();
        return redirect()->back()->with('message', 'Variable Added Successfully');
    }
    public function data_variable_edit(Request $request) {
        $request->validate([
            "id" => "required|exists:database_variables",
            "object" => "required|string|max:50",
            "property" => "nullable|string|max:50",
            "is_array" => "required|boolean",
            "related_to" => "required|integer"
        ]);
        $database = DatabaseVariable::find($request->id);
        $database->object = $request->object;
        $database->property = $request->property;
        $database->is_array = $request->is_array;
        $database->related_to = $request->related_to;
        $database->save();
        return redirect()->route('Database')->with('message', 'Variable Edited Successfully');
    }
    public function data_variable_delete($id) {
        DatabaseVariable::destroy($id);
        return redirect()->route('Database')->with('message', 'Variable Deleted Successfully');
    }
    public function variable($template_id, $operation = null, $id = null) {
        $this->response = [
            "breadcrumbs" => [[
                "route" => "Template.index",
                "routePar" => [],
                "name" => '<i class="fa fa-home"></i>'
            ]]
        ];
        $this->response["operation"] = $operation;
        $this->response["template"] = Template::find($template_id);
        $this->response["breadcrumbs"][] = [
            "route" => "Template.Page",
            "routePar" => ["template_id" => $this->response["template"]->id],
            "name" => $this->response["template"]->title
        ];
        $this->response["breadcrumbs"][] = [
            "route" => "Variable",
            "routePar" => ['template_id' => $template_id],
            "name" => "Global Variable"
        ];
        if($operation && ($operation == "add" || $operation == "edit")) {
            $this->response["variable"] = null;
            $this->response["breadcrumbs"][] = [
                "route" => null,
                "routePar" => null,
                "name" => ucwords($operation)
            ];
            if($operation == "edit") {
            $this->response["variable"] = Variables::find($id);
            }
            return view('Template.Forms.variable', $this->response);
        } else {
            $this->response["variable"] = Variables::where('template_id', $template_id)->get();
        }
        return view('Template.variable', $this->response);
    }
    public function variable_add(Request $request) {
        $request->validate([
            "template_id" => "required|exists:templates,id",
            "variable_name" => "required|alpha_dash|max:100",
            "is_php" => "required|boolean",
            "evaluate" => "nullable|string|max:200",
            "php_code" => "nullable|string|max:65500"
        ]);
        $variable = new Variables;
        $variable->template_id = $request->template_id;
        $variable->variable_name = $request->variable_name;
        $variable->is_php = $request->is_php;
        $variable->evaluate = $request->evaluate;
        $variable->php_code = $request->php_code;
        $variable->save();
        return redirect()->back()->with('message', 'Global Variable Added Successfully');
    }
    public function variable_edit(Request $request) {
        $request->validate([
            "id" => "required|exists:variables",
            "template_id" => "required|exists:templates,id",
            "variable_name" => "required|alpha_dash|max:100",
            "is_php" => "required|boolean",
            "evaluate" => "nullable|string|max:200",
            "php_code" => "nullable|string|max:65500"
        ]);
        $variable = Variables::find($request->id);
        $variable->template_id = $request->template_id;
        $variable->variable_name = $request->variable_name;
        $variable->is_php = $request->is_php;
        $variable->evaluate = $request->evaluate;
        $variable->php_code = $request->php_code;
        $variable->save();
        return redirect()->route('Variable', ['template_id' => $request->template_id])->with('message', 'Global Variable Edited Successfully');
    }
    public function variable_delete($template_id, $id) {
        Variables::destroy($id);
        return redirect()->route('Variable', ['template_id' => $template_id])->with('message', 'Global Variable Deleted Successfully');
    }
    public function loopsource($operation = null, $id = null) {
        $this->response = [
            "breadcrumbs" => [[
                "route" => "Template.index",
                "routePar" => [],
                "name" => '<i class="fa fa-home"></i>'
            ]]
        ];
        $this->response["breadcrumbs"][] = [
            "route" => "Loopsource",
            "routePar" => null,
            "name" => "Loop Source"
        ];
        $this->response["operation"] = $operation;
        if($operation && ($operation == "add" || $operation == "edit")) {
            $this->response["loopsource"] = null;
            $this->response["breadcrumbs"][] = [
                "route" => null,
                "routePar" => null,
                "name" => ucwords($operation)
            ];
            if($operation == "edit") {
            $this->response["loopsource"] = LoopSource::find($id);
            }
            return view('Template.Forms.loopsource', $this->response);
        } else {
            $this->response["loopsource"] = LoopSource::all();
        }
        return view('Template.loopsource', $this->response);
    }
    public function loopsource_add(Request $request) {
        $request->validate([
            "database_variables" => "required|string|max:50",
            "title" => "required|string|max:300",
            "object_query" => "nullable|string|max:200",
            "property_query" => "nullable|string|max:200",
            "variables" => "nullable|json|max:500",
            "relation" => "required_without:object_query|nullable|boolean"
        ]);
        $ls = new LoopSource;
        $ls->database_variables = $request->database_variables;
        $ls->title = $request->title;
        $ls->object_query = $request->object_query;
        $ls->property_query = $request->property_query;
        $ls->variables = $request->variables;
        $ls->relation = $request->relation;
        $ls->save();
        return redirect()->back()->with('message', 'Loop Source Added Successfully');
    }
    public function loopsource_edit(Request $request) {
        $request->validate([
            "id" => "required|exists:loop_sources",
            "database_variables" => "required|string|max:50",
            "title" => "required|string|max:300",
            "object_query" => "nullable|string|max:200",
            "property_query" => "nullable|string|max:200",
            "variables" => "nullable|json|max:500",
            "relation" => "required_without:object_query|nullable|boolean"
        ]);
        $ls = LoopSource::find($request->id);
        $ls->database_variables = $request->database_variables;
        $ls->title = $request->title;
        $ls->object_query = $request->object_query;
        $ls->property_query = $request->property_query;
        $ls->variables = $request->variables;
        $ls->relation = $request->relation;
        $ls->save();
        return redirect()->route('Loopsource')->with('message', 'Loop Source Edited Successfully');
    }
    public function loopsource_delete($id) {
        LoopSource::destroy($id);
        return redirect()->route('Loopsource')->with('message', 'Loop Source Deleted Successfully');
    }
    public function weburl($page_id, $operation = null, $id = null) {
        $this->response = [
            "breadcrumbs" => [[
                "route" => "Template.index",
                "routePar" => [],
                "name" => '<i class="fa fa-home"></i>'
            ]]
        ];
        $this->response["operation"] = $operation;
        $this->response["page"] = Page::find($page_id);
        $this->response["template"] = $this->response["page"]->Template;
        $this->response["breadcrumbs"][] = [
            "route" => "Template.Page",
            "routePar" => ["template_id" => $this->response["template"]->id],
            "name" => $this->response["template"]->title
        ];
        $this->response["breadcrumbs"][] = [
            "route" => "Template.Page",
            "routePar" => ["template_id" => $this->response["template"]->id],
            "name" => $this->response["page"]->title
        ];
        $this->response["breadcrumbs"][] = [
            "route" => "WebUrl",
            "routePar" => ['page_id' => $page_id],
            "name" => "Web URLs"
        ];
        if($operation && ($operation == "add" || $operation == "edit")) {
            $this->response["weburl"] = null;
            $this->response["breadcrumbs"][] = [
                "route" => null,
                "routePar" => null,
                "name" => ucwords($operation)
            ];
            if($operation == "edit") {
            $this->response["weburl"] = WebUrl::find($id);
            }
            return view('Template.Forms.weburl', $this->response);
        } else {
            $this->response["weburl"] = WebUrl::where('page_id', $page_id)->orderBy('regex', 'desc')->orderBy('url')->paginate(50);
        }
        return view('Template.weburl', $this->response);
    }
    public function weburl_add(Request $request) {
        $request->validate([
            "template_id" => "required|exists:templates,id",
            "page_id" => "required|exists:pages,id",
            "url" => "required|string|max:1000",
            "geolocation" => "nullable|string|max:45",
            "regex" => "nullable|string|max:200",
            "matches" => "nullable|json|max:500",
            "url_variables" => "nullable|json|max:500",
            "url_builder" => "nullable|json|max:500",
            "meta_content" => "nullable|string|max:65500",
            "js_content" => "nullable|string|max:65500",
            "css_content" => "nullable|string|max:65500"
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
        $url = new WebUrl;
        $url->template_id = $request->template_id;
        $url->page_id = $request->page_id;
        $url->url = $request->url;
        $url->geolocation = $request->geolocation;
        $url->regex = $request->regex;
        $url->matches = $request->matches;
        $url->url_variables = $request->url_variables;
        $url->url_builder = $request->url_builder;
        $url->meta_id = $meta_id;
        $url->script_id = $script_id;
        $url->css_id = $css_id;
        $url->user_id = Auth::id();
        $url->save();
        return redirect()->route('WebUrl', ['page_id' => $request->page_id])->with('message', $request->url.' Added Successfully');
    }
    public function weburl_edit(Request $request) {
        $request->validate([
            "id" => "required|exists:web_urls",
            "template_id" => "required|exists:templates,id",
            "page_id" => "required|exists:pages,id",
            "url" => "required|string|max:1000",
            "geolocation" => "nullable|string|max:45",
            "regex" => "nullable|string|max:200",
            "matches" => "nullable|json|max:500",
            "url_variables" => "nullable|json|max:500",
            "url_builder" => "nullable|json|max:500",
            "meta_content" => "nullable|string|max:65500",
            "js_content" => "nullable|string|max:65500",
            "css_content" => "nullable|string|max:65500"
        ]);
        $url = WebUrl::find($request->id);
        $meta_id = 0;
        $script_id = 0;
        $css_id = 0;
        if($request->has('meta_content') && $request->meta_content) {
            if($url->meta_id)
            $content = $url->getMetadata;
            else
            $content = new Content;
            $content->content_type = "text";
            $content->content = $request->meta_content;
            $content->user_id = Auth::id();
            $content->save();
            $meta_id = $content->id;
        } else {
            if($url->meta_id)
            $url->getMetadata->delete();
        }
        if($request->has('js_content') && $request->js_content) {
            if($url->script_id)
            $content = $url->getScript;
            else
            $content = new Content;
            $content->content_type = "text";
            $content->content = $request->js_content;
            $content->user_id = Auth::id();
            $content->save();
            $script_id = $content->id;
        } else {
            if($url->script_id)
            $url->getScript->delete();
        }
        if($request->has('css_content') && $request->css_content) {
            if($url->css_id)
            $content = $url->getCSS;
            else
            $content = new Content;
            $content->content_type = "text";
            $content->content = $request->css_content;
            $content->user_id = Auth::id();
            $content->save();
            $css_id = $content->id;
        } else {
            if($url->css_id)
            $url->getCSS->delete();
        }
        $url->template_id = $request->template_id;
        $url->page_id = $request->page_id;
        $url->url = $request->url;
        $url->geolocation = $request->geolocation;
        $url->regex = $request->regex;
        $url->matches = $request->matches;
        $url->url_variables = $request->url_variables;
        $url->url_builder = $request->url_builder;
        $url->meta_id = $meta_id;
        $url->script_id = $script_id;
        $url->css_id = $css_id;
        $url->user_id = Auth::id();
        $url->save();
        return redirect()->route('WebUrl', ['page_id' => $request->page_id])->with('message', $request->url.' Edited Successfully');
    }
    public function weburl_delete($page_id, $id) {
        Weburl::destroy($id);
        return redirect()->route('WebUrl', ['page_id' => $page_id])->with('message', 'URL Deleted Successfully');
    }
    public function view($template_id, $id) {
        $url = [];
        $page = Page::find($id);
        if($page) {
            $variables = [];
            $mode = Cookie::get('mode');
            $geolocation = ["country" => strtolower(Cookie::get('country')), "currency" => "USD", "conversion" => "65"];
            $view = View::make('Page.index', [
                'url' => $url,
                'page' => $page,
                'template_id' => $template_id,
                'mode' => $mode,
                'propertyResolver' => $this->propertyResolver
            ]);
            $html = $view->render();
            $html = $this->HTMLTouchUp($template_id, $url, $html, $variables, $geolocation);
            if(Auth::user()->admin) {
                $html .= "<!--".json_encode($variables)."-->";
                $pageVar = "";
                foreach($variables as $key => $val) {
                    $pageVar .= '<kbd>'.$key.'</kbd>';
                    if(is_array($val)) {
                        $pageVar .= ' <code>Array</code>';
                    }
                    $pageVar .= '<br>';
                }
                if($pageVar) {
                    $page->variables = $pageVar;
                    $page->save();
                }
            }
            return $html;
        }
        return redirect()->route('home');
    }
    public function WebView($pageURL) {
        $url = ["current" => '/'.$pageURL];
        $pageURL = str_replace(".html", "", $pageURL);
        $url["title"] = ucwords(str_replace("-", " ", $pageURL));
        $page = 0;
        $content_id = 0;
        if(Cookie::get('template_id') == null || Cookie::get('country') == null)
        return redirect()->route('home')->with("error", "Template or Country Missing");
        $template_id = Cookie::get('template_id');
        $geolocation = ["country" => strtolower(Cookie::get('country')), "currency" => "USD", "conversion" => "65"];
        $webURL = null;
        if($pageURL == "index") {
            $page = Page::where(['template_id' => $template_id, "title" => "index"])->first();
        } else {
            $isStaticPage = WebUrl::where(['template_id' => $template_id, "url" => $pageURL])->whereNull('regex')->get();
            if($count = count($isStaticPage)) {
                if($count == 1) {
                    if($isStaticPage[0]->page_content_id) {
                        $page = $isStaticPage[0]->PageContent->Page;
                        $content_id = $isStaticPage[0]->PageContent->content_id;
                        $url["title"] = $isStaticPage[0]->PageContent->title;
                        $url["group_title"] = $isStaticPage[0]->PageContent->group_title;
                    }
                    $webURL = $isStaticPage[0];
                } else {
                    foreach($isStaticPage as $found) {
                        if($found->geolocation == null) {
                            $webURL = $found;
                            if($found->page_content_id) {
                                $page = $found->PageContent->Page;
                                $content_id = $found->PageContent->content_id;
                                $url["title"] = $found->PageContent->title;
                                $url["group_title"] = $found->PageContent->group_title;
                            }
                        } elseif(strtolower($found->geolocation) == $country) {
                            $webURL = $found;
                            if($found->page_content_id) {
                                $page = $found->PageContent->Page;
                                $content_id = $found->PageContent->content_id;
                                $url["title"] = $found->PageContent->title;
                                $url["group_title"] = $found->PageContent->group_title;
                            }
                        }
                    }
                }
            } else {
                foreach(\App\Models\WebUrl::where('template_id', $template_id)->whereNotNull('regex')->cursor() as $weburl) {
                    $matches = [];
                    if(preg_match($weburl->regex, $pageURL, $matches)) {
                        $webURL = $weburl;
                        $match_var = json_decode($weburl->matches, true);
                        foreach($match_var as $match_group => $var_name) {
                            $url[$var_name] = ucwords(str_replace("-", " ", $matches[intval($match_group)]));
                        }
                        break;
                    }
                }
            }
            if($webURL && $webURL->url_variables) {
                $url_variables = json_decode($webURL->url_variables, true);
                if(isset($url_variables["url"])) {
                    foreach($url_variables["url"] as $key => $val) {
                        $url[$key] = $val;
                    }
                }
            }
            if(!$page) {
                if($webURL)
                $page = $webURL->Page;
            }
        }
        if($page) {
            $variables = [];
            $view = View::make('Website.index', [
                'url' => $url,
                'webURL' => $webURL,
                'page' => $page,
                'template_id' => $template_id,
                'content_id' => $content_id,
                'propertyResolver' => $this->propertyResolver
            ]);
            $html = $view->render();
            $html = $this->HTMLTouchUp($template_id, $url, $html, $variables, $geolocation);
            return $html;
        }
        return redirect()->route('home')->with("error", "Web URL could not be resolved.");
    }
    public function mode(Request $request) {
        $request->validate([
            "template_id" => "required|exists:templates,id",
            "mode" => "required|in:guest,auth",
            "country" => "nullable|string|exists:geo_locations,country"
        ]);
        Cookie::queue('mode', $request->mode, 0);
        Cookie::queue('template_id', $request->template_id, 0);
        if($request->country)
        Cookie::queue('country', $request->country, 0);
        return redirect()->back()->with("message", "Mode: ".$request->mode." ".$request->country);
    }
}
