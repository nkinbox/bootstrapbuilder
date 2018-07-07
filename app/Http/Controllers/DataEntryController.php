<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Content;
use App\Models\DataMarker;
use App\Models\DataFacility;
use App\Models\Location;
use App\Models\GeoLocation;
use App\Models\PublicTransport;
use App\Models\Images;
use App\Models\Package;
use App\Models\PackageDetail;
use App\Models\PackageItinerary;
use App\Models\PackageMarker;
use App\Models\PackagePrice;
use App\Models\Hotel;
use App\Models\HotelContact;
use App\Models\HotelFacility;
use App\Models\HotelMarker;
use App\Models\HotelRoom;
use App\Rules\alpha_dash_space;
use Auth;
use Cookie;
use Storage;

class DataEntryController extends Controller
{   
    private $response;
    public function __construct() {
        $this->response = [
            "breadcrumbs" => [[
                "route" => "DataEntry.Home",
                "routePar" => [],
                "name" => "Home"
            ]]
        ];
    }
    public function index() {
        $this->response['breadcrumbs'][] = [
            "route" => "",
            "routePar" => [],
            "name" => ucwords(Auth::user()->name)
        ];
        return view('DataEntry.index', $this->response);
    }
    public function geolocation() {
        if(!Auth::user()->admin)
        return redirect()->back()->with("error", "You must be Administrator to add geolocation");
        $this->response['breadcrumbs'][] = [
            "route" => "",
            "routePar" => [],
            "name" => "Add GeoLocation"
        ];
        return view('DataEntry.Forms.newgeolocation', $this->response);
    }
    public function geolocation_add(Request $request) {
        if(!Auth::user()->admin)
        return redirect()->back()->with("error", "You must be Administrator to add geolocation");
        $request->validate([
            "continent" => ['required',new alpha_dash_space,'max:15'],
            "country" => ['required',new alpha_dash_space,'max:45'],
            "division" => ['nullable',new alpha_dash_space,'max:50'],
            "subdivision" => ['nullable',new alpha_dash_space,'max:50'],
            "city" => ['nullable',new alpha_dash_space,'max:50'],
            "time_zone" => ['required','string','max:50'],
            "is_in_european_union" => "required|boolean"
        ]);
        $geoLocation = new GeoLocation;
        $geoLocation->continent = ucwords($request->continent);
        $geoLocation->country = ucwords($request->country);
        $geoLocation->division = ($request->division)?ucwords($request->division):"";
        $geoLocation->subdivision = ($request->subdivision)?ucwords($request->subdivision):"";
        $geoLocation->city = ($request->city)?ucwords($request->city):"";
        $geoLocation->time_zone = $request->time_zone;
        $geoLocation->is_in_european_union = $request->is_in_european_union;
        $geoLocation->save();
        $result = (($geoLocation && $geoLocation->city)?$geoLocation->city.", ":'').(($geoLocation && $geoLocation->subdivision)?$geoLocation->subdivision.", ":'').(($geoLocation && $geoLocation->division)?$geoLocation->division.", ":'').(($geoLocation && $geoLocation->country)?$geoLocation->country:'');
        return redirect()->back()->with("message", "(".$result.") Added Successfully!");
    }
    public function geolocation_get(Request $request) {
        $response = ["countries" => "", "divisions" => "", "subdivisions" => "", "cities" => ""];
        $continent = ($request->continent)?$request->continent:'';
        $country = ($request->country)?$request->country:'';
        $division = ($request->division)?$request->division:'';
        $subdivision = ($request->subdivision)?$request->subdivision:'';
        $geolocation_id = null;
        $city = ($request->city)?$request->city:'';
        if($continent) {
            if($country) {
                $geolocation_id = GeoLocation::where(["continent" => $continent, "country" => $country])->first();
                if($geolocation_id) {
                    $countries = [["selected"=>$country]];
                    Cookie::queue('geolocation_id', $geolocation_id->id, 0);
                    if($division) {
                        $geolocation_id = GeoLocation::where(["continent" => $continent, "country" => $country, "division" => $division])->first();
                        if($geolocation_id) {
                            $divisions = [["selected"=>$division]];
                            Cookie::queue('geolocation_id', $geolocation_id->id, 0);
                            if($subdivision) {
                                $geolocation_id = GeoLocation::where(["continent" => $continent, "country" => $country, "division" => $division, "subdivision" => $subdivision])->first();
                                if($geolocation_id) {
                                    $subdivisions = [["selected"=>$subdivision]];
                                    Cookie::queue('geolocation_id', $geolocation_id->id, 0);
                                    if($city) {
                                        $geolocation_id = GeoLocation::where(["continent" => $continent, "country" => $country, "division" => $division, "subdivision" => "", "city" => $city])->first();
                                        if($geolocation_id) {
                                            $cities = [["selected"=>$city]];
                                            Cookie::queue('geolocation_id', $geolocation_id->id, 0);
                                        }
                                    } else {
                                        $cities = GeoLocation::select("city")->where(["continent" => $continent, "country" => $country, "division" => $division, "subdivision" => $subdivision])->get();
                                    }
                                }
                            } else {
                                $subdivisions = GeoLocation::select("subdivision")->where(["continent" => $continent, "country" => $country, "division" => $division])->groupBy("subdivision")->get();
                                if($subdivisions[0]->subdivision == "") {
                                    $subdivisions = "";
                                    if($city) {
                                        $geolocation_id = GeoLocation::where(["continent" => $continent, "country" => $country, "division" => $division, "subdivision" => "", "city" => $city])->first();
                                        if($geolocation_id) {
                                            $cities = [["selected"=>$city]];
                                            Cookie::queue('geolocation_id', $geolocation_id->id, 0);
                                        }
                                    } else {
                                        $cities = GeoLocation::select("city")->where(["continent" => $continent, "country" => $country, "division" => $division, "subdivision" => ""])->get();
                                    }
                                }
                            }
                        }
                    } else {
                        $divisions = GeoLocation::select("division")->where(["continent" => $continent, "country" => $country])->groupBy("division")->get();
                    }
                }
            } else {
                $countries = GeoLocation::select("country")->where(["continent" => $continent])->groupBy("country")->get();
            }
        }
        foreach($response as $key => $val) {
            if(isset($$key)) {
                $response[$key] = $$key;
            }
        }

        $response["current"] = (($request->route && $geolocation_id)?'<a href='.route($request->route, ['operation' => 'geography', 'id' => $geolocation_id]).' class="text-dark" tabindex="1">':'').
        (($geolocation_id)?"<ol class='breadcrumb bg-warning'>":'').
        (($geolocation_id && $geolocation_id->country)?"<li class='breadcrumb-item'><i class='fa fa-map-marker'></i> ".$geolocation_id->country.'</li>':'').
        (($geolocation_id && $geolocation_id->division)?"<li class='breadcrumb-item'>".$geolocation_id->division."</li>":'').
        (($geolocation_id && $geolocation_id->subdivision)?"<li class='breadcrumb-item'>".$geolocation_id->subdivision."</li>":'').
        (($geolocation_id && $geolocation_id->city)?"<li class='breadcrumb-item'>".$geolocation_id->city."</li>":'').
        (($geolocation_id)?"</ol>":'').
        (($request->route && $geolocation_id)?'</a>':'');
        return response()->json($response);
    }
    private function image_management($content, $type, $id) {
        $matches = [];
        preg_match_all('/id=@@image\.(.*?)@@/' , $content, $matches);
        foreach($matches[1] as $m) {
            $image = Images::find($m);
            $image->type = $type;
            $image->belongs_to = $id;
            $image->save();
        }
    }
    public function image_upload(Request $request) {
        $request->validate([
            "image_title" => ['required',new alpha_dash_space,'max:60'],
            "image" => "required|image"
        ]);
        $id = 0;
        if ($request->hasFile('image')) {
            $filename = $request->image->store($request->type, 'public');
            $image = new Images;
            $image->type = "asset";
            $image->belongs_to = 0;
            $image->image_title = $request->image_title;
            $image->file_name = $filename;
            $image->user_id = Auth::id();
            $image->save();
            $id = $image->id;
        }
        return response()->json([
            "success" => (($id)?1:0),
            "image_id" => $id
        ]);
    }
    public function image_get(Request $request) {
        $request->validate([
            "id" => "required|exists:images"
        ]);
        $image = Images::find($request->id);
        $response = [
            "success" => 1,
            "id" => $request->id,
            "src" => (($image)?asset('storage/'.$image->file_name):'#')
        ];
        return response()->json($response);
    }
    public function transports($operation = null, $id = null) {
        $this->response['breadcrumbs'][] = [
            "route" => "DataEntry.Transports",
            "routePar" => [],
            "name" => "Transports"
        ];
        $this->response["operation"] = $operation;
        if($operation) {
            if($operation != "add" && $operation != "edit") {
                $this->response['breadcrumbs'][] = [
                    "route" => "DataEntry.Transports",
                    "routePar" => ["operation" => $operation, "id" => $id],
                    "name" => ucwords($operation)
                ];
            } else {
                $this->response['breadcrumbs'][] = [
                    "route" => "DataEntry.Transports",
                    "routePar" => ["operation" => $operation],
                    "name" => ucwords($operation)
                ];
            }
            $this->response["operation"] = $operation;
            if($operation == "edit") {
                $this->response["transport"] = PublicTransport::find($id);
                if(Cookie::get('geolocation_id') == null) {
                    Cookie::queue('geolocation_id', $this->response['transport']->geolocation_id, 0);
                    $this->response['reload'] = true;
                } elseif(Cookie::get('geolocation_id') != $this->response['transport']->geolocation_id) {
                    Cookie::queue('geolocation_id', $this->response['transport']->geolocation_id, 0);
                    $this->response['reload'] = true;
                }
            } elseif($operation == "geography") {
                $this->response["transports"] = PublicTransport::where("geolocation_id", $id)->orderBy('id', 'desc')->paginate(100);
                if(Cookie::get('geolocation_id') == null) {
                    Cookie::queue('geolocation_id', $id, 0);
                    $this->response['reload'] = true;
                } elseif(Cookie::get('geolocation_id') != $id) {
                    Cookie::queue('geolocation_id', $id, 0);
                    $this->response['reload'] = true;
                }
            }
        } else {
            $this->response["transports"] = PublicTransport::orderBy('id', 'desc')->paginate(100);
        }
        return view('DataEntry.transports', $this->response);
    }
    public function transports_add(Request $request) {
        if(Cookie::get('geolocation_id') == null) {
            return redirect()->back()->with("error", "GeoLocation Missing!");
        }
        $request->validate([
            "latitude" => "nullable|regex:/^-?[0-9]{1,3}(?:\.[0-9]{1,8})?$/",
            "longitude" => "nullable|regex:/^-?[0-9]{1,3}(?:\.[0-9]{1,8})?$/",
            "title" => ['required',new alpha_dash_space,'max:80'],
            "type" => "required|in:Airport,Busstand,Railwaystation,Taxistand",
            "category" => "required|string|max:50",
            "content_type" => "sometimes|required|in:html,text,blade",
            "content" => "required_with:content_type|string|max:65000"
        ]);
        $content_id = 0;
        if($request->has("content_type")) {
            $content = new Content;
            $content->content_type = $request->content_type;
            $content->content = $request->content;
            $content->user_id = Auth::id();
            $content->save();
            $content_id = $content->id;
        }
        $transport = new PublicTransport;
        $transport->geolocation_id = Cookie::get('geolocation_id');
        $transport->type = $request->type;
        $transport->title = $request->title;
        $transport->category = $request->category;
        $transport->latitude = $request->latitude;
        $transport->longitude = $request->longitude;
        $transport->content_id = $content_id;
        $transport->user_id = Auth::id();
        $transport->save();
        return redirect()->back()->with("message", $request->title." Added Successfully!");
    }
    public function transports_edit(Request $request) {
        if(Cookie::get('geolocation_id') == null) {
            return redirect()->back()->with("error", "GeoLocation Missing!");
        }
        $request->validate([
            "id" => "required|exists:public_transports",
            "latitude" => "nullable|regex:/^-?[0-9]{1,3}(?:\.[0-9]{1,8})?$/",
            "longitude" => "nullable|regex:/^-?[0-9]{1,3}(?:\.[0-9]{1,8})?$/",
            "title" => ['required',new alpha_dash_space,'max:80'],
            "type" => "required|in:Airport,Busstand,Railwaystation,Taxistand",
            "category" => "required|string|max:50",
            "content_type" => "sometimes|required|in:html,text,blade",
            "content" => "required_with:content_type|string|max:65000"
        ]);
        $transport = PublicTransport::find($request->id);
        $content_id = 0;
        if($request->has("content_type")) {
            if($transport->content_id)
            $content = Content::find($transport->content_id);
            else
            $content = new Content;
            $content->content_type = $request->content_type;
            $content->content = $request->content;
            $content->user_id = Auth::id();
            $content->save();
            $content_id = $content->id;
        } else {
            if($transport->content_id)
            Content::destroy($transport->content_id);
        }
        $transport->geolocation_id = Cookie::get('geolocation_id');
        $transport->type = $request->type;
        $transport->title = $request->title;
        $transport->category = $request->category;
        $transport->latitude = $request->latitude;
        $transport->longitude = $request->longitude;
        $transport->content_id = $content_id;
        $transport->user_id = Auth::id();
        $transport->save();
        return redirect()->route('DataEntry.Transports')->with("message", $request->title." Edited Successfully!");
    }
    public function transports_delete($id) {
        $transport = transport::find($id);
        if($transport) {
            $transport->delete();
        }
        return redirect()->back()->with("message", "transport Deleted Successfully!");
    }
    public function facilities($operation = null, $id = null) {
        $this->response['breadcrumbs'][] = [
            "route" => "DataEntry.Facilities",
            "routePar" => [],
            "name" => "Facilities"
        ];
        $this->response["operation"] = $operation;
        if($operation) {
            $this->response['breadcrumbs'][] = [
                "route" => "DataEntry.Facilities",
                "routePar" => ["operation" => $operation],
                "name" => ucwords($operation)
            ];
            $this->response["operation"] = $operation;
            if($operation == "edit") {
                $this->response["facility"] = DataFacility::find($id);
            }
        } else {
            $this->response["facilities"] = DataFacility::all();
        }
        return view('DataEntry.facilities', $this->response);
    }
    public function facilities_add(Request $request) {
        $request->validate([
            "title" => ['required',new alpha_dash_space,'max:250'],
            "facility_type" => "required|in:hotel,room",
            "facility_content" => "nullable|string|max:500",
            "content_type" => "sometimes|required|in:html,text,blade",
            "content" => "required_with:content_type|string|max:65000"
        ]);
        $content_id = 0;
        if($request->has("content_type")) {
            $content = new Content;
            $content->content_type = $request->content_type;
            $content->content = $request->content;
            $content->user_id = Auth::id();
            $content->save();
            $content_id = $content->id;
        }
        $facility = new DataFacility;
        $facility->title = $request->title;
        $facility->type = $request->facility_type;
        $facility->content = $request->facility_content;
        $facility->content_id = $content_id;
        $facility->user_id = Auth::id();
        $facility->save();
        return redirect()->back()->with("message", "Facility Added Successfully!");
    }
    public function facilities_edit(Request $request) {
        $request->validate([
            "id" => "required|exists:data_facilities",
            "title" => ['required',new alpha_dash_space,'max:250'],
            "facility_type" => "required|in:hotel,room",
            "facility_content" => "nullable|string|max:500",
            "content_type" => "sometimes|required|in:html,text,blade",
            "content" => "required_with:content_type|string|max:65000",
        ]);
        $facility = DataFacility::find($request->id);
        $content_id = 0;
        if($request->has("content_type")) {
            if($facility->content_id)
            $content = Content::find($facility->content_id);
            else
            $content = new Content;
            $content->content_type = $request->content_type;
            $content->content = $request->content;
            $content->user_id = Auth::id();
            $content->save();
            $content_id = $content->id;
        } else {
            if($facility->content_id)
            Content::destroy($facility->content_id);
        }
        $facility->title = $request->title;
        $facility->type = $request->facility_type;
        $facility->content = $request->facility_content;
        $facility->user_id = Auth::id();
        $facility->content_id = $content_id;
        $facility->save();

        return redirect()->route('DataEntry.Facilities')->with("message", "Facility Edited Successfully!");
    }
    public function facilities_delete($id) {
        $facility = DataFacility::find($id);
        if($facility) {
            $facility->delete();
        }
        return redirect()->back()->with("message", "Facility Deleted Successfully!");
    }
    public function markers($operation = null, $id = null) {
        $this->response['breadcrumbs'][] = [
            "route" => "DataEntry.Markers",
            "routePar" => [],
            "name" => "Labels-Tags-Categories"
        ];
        $this->response["operation"] = $operation;
        if($operation) {
            if($operation != "add" && $operation != "edit") {
                $this->response['breadcrumbs'][] = [
                    "route" => "DataEntry.Markers",
                    "routePar" => ["operation" => $operation, "id" => $id],
                    "name" => ucwords($operation." ".$id)
                ];
            } else {
                $this->response['breadcrumbs'][] = [
                    "route" => "DataEntry.Markers",
                    "routePar" => ["operation" => $operation],
                    "name" => ucwords($operation)
                ];
            }
            $this->response["operation"] = $operation;
            if($operation == "edit") {
                $this->response["marker"] = DataMarker::find($id);
            } else if($operation == "type") {
                $this->response["markers"] = DataMarker::where("type", $id)->get();
            } else if($operation == "category") {
                $this->response["markers"] = DataMarker::where("category", $id)->get();
            }
        } else {
            $this->response["markers"] = DataMarker::all();
        }
        return view('DataEntry.markers', $this->response);
    }
    public function markers_add(Request $request) {
        $request->validate([
            "title" => ['required',new alpha_dash_space,'max:50'],
            "category" => "required|in:hotel,packg",
            "type" => "required|in:label,category,tag,inclusions,exclusions,activity,theme",
            "marker_content" => "nullable|string|max:500",
            "content_type" => "sometimes|required|in:html,text,blade",
            "content" => "required_with:content_type|string|max:65000"
        ]);
        $content_id = 0;
        if($request->has("content_type")) {
            $content = new Content;
            $content->content_type = $request->content_type;
            $content->content = $request->content;
            $content->user_id = Auth::id();
            $content->save();
            $content_id = $content->id;
        }
        $marker = new DataMarker;
        $marker->title = $request->title;
        $marker->category = $request->category;
        $marker->type = $request->type;
        $marker->content = $request->marker_content;
        $marker->content_id = $content_id;
        $marker->user_id = Auth::id();
        $marker->save();
        return redirect()->back()->with("message", $marker->type." Added Successfully!");
    }
    public function markers_edit(Request $request) {
        $request->validate([
            "id" => "required|exists:data_markers",
            "title" => ['required',new alpha_dash_space,'max:50'],
            "category" => "required|in:hotel,packg",
            "type" => "required|in:label,category,tag,inclusions,exclusions,activity,theme",
            "marker_content" => "nullable|string|max:500",
            "content_type" => "sometimes|required|in:html,text,blade",
            "content" => "required_with:content_type|string|max:65000"
        ]);
        $marker = DataMarker::find($request->id);
        $content_id = 0;
        if($request->has("content_type")) {
            if($marker->content_id)
            $content = Content::find($marker->content_id);
            else
            $content = new Content;
            $content->content_type = $request->content_type;
            $content->content = $request->content;
            $content->user_id = Auth::id();
            $content->save();
            $content_id = $content->id;
        } else {
            if($marker->content_id)
            Content::destroy($marker->content_id);
        }
        $marker->title = $request->title;
        $marker->category = $request->category;
        $marker->type = $request->type;
        $marker->content = $request->marker_content;
        $marker->content_id = $content_id;
        $marker->user_id = Auth::id();
        $marker->save();
        return redirect()->route('DataEntry.Markers')->with("message", $marker->type." Edited Successfully!");
    }
    public function markers_delete($id) {
        $marker = DataMarker::find($id);
        $type = "Marker";
        if($marker) {
            $type = $marker->type;
            $marker->delete();
        }
        return redirect()->back()->with("message", $type." Deleted Successfully!");
    }
    public function locations($operation = null, $id = null) {
        $this->response['breadcrumbs'][] = [
            "route" => "DataEntry.Locations",
            "routePar" => [],
            "name" => "Locations"
        ];
        $this->response["operation"] = $operation;
        if($operation) {
            if($operation != "add" && $operation != "edit") {
                $this->response['breadcrumbs'][] = [
                    "route" => "DataEntry.Locations",
                    "routePar" => ["operation" => $operation, "id" => $id],
                    "name" => ucwords($operation)
                ];
            } else {
                $this->response['breadcrumbs'][] = [
                    "route" => "DataEntry.Locations",
                    "routePar" => ["operation" => $operation],
                    "name" => ucwords($operation)
                ];
            }
            $this->response["operation"] = $operation;
            if($operation == "edit") {
                $this->response["location"] = Location::find($id);
                if(Cookie::get('geolocation_id') == null) {
                    Cookie::queue('geolocation_id', $this->response['location']->geolocation_id, 0);
                    $this->response['reload'] = true;
                } elseif(Cookie::get('geolocation_id') != $this->response['location']->geolocation_id) {
                    Cookie::queue('geolocation_id', $this->response['location']->geolocation_id, 0);
                    $this->response['reload'] = true;
                }
            } elseif($operation == "geography") {
                $this->response["locations"] = Location::where("geolocation_id", $id)->orderBy('id', 'desc')->paginate(100);
                if(Cookie::get('geolocation_id') == null) {
                    Cookie::queue('geolocation_id', $id, 0);
                    $this->response['reload'] = true;
                } elseif(Cookie::get('geolocation_id') != $id) {
                    Cookie::queue('geolocation_id', $id, 0);
                    $this->response['reload'] = true;
                }
            }
        } else {
            $this->response["locations"] = Location::orderBy('id', 'desc')->paginate(100);
        }
        return view('DataEntry.locations', $this->response);
    }
    public function locations_get() {
        $response = null;
        if(Cookie::get('geolocation_id') == null) {
            $response = ["message" => "Missing GeoLocation."];
        }
        $locations = Location::select('id', 'title')->where(['geolocation_id' => Cookie::get('geolocation_id'), 'type' => 'locality'])->get();
        foreach($locations as $location) {
            $response[$location->id] = $location->title;
        }
        if(!$response) {
            $response = ["message" => "No Locality Found."];
        }
        return response()->json($response);
    }
    public function locations_add(Request $request) {
        if(Cookie::get('geolocation_id') == null) {
            return redirect()->back()->with("error", "GeoLocation Missing!");
        }
        $request->validate([
            "latitude" => "nullable|regex:/^-?[0-9]{1,3}(?:\.[0-9]{1,8})?$/",
            "longitude" => "nullable|regex:/^-?[0-9]{1,3}(?:\.[0-9]{1,8})?$/",
            "title" => ['required',new alpha_dash_space,'max:100'],
            "type" => "required|in:landmark,attraction,locality",
            "content_type" => "sometimes|required|in:html,text,blade",
            "content" => "required_with:content_type|string|max:65000"
        ]);
        $content_id = 0;
        if($request->has("content_type")) {
            $content = new Content;
            $content->content_type = $request->content_type;
            $content->content = $request->content;
            $content->user_id = Auth::id();
            $content->save();
            $content_id = $content->id;
        }
        $location = new Location;
        $location->geolocation_id = Cookie::get('geolocation_id');
        $location->type = $request->type;
        $location->title = $request->title;
        $location->latitude = $request->latitude;
        $location->longitude = $request->longitude;
        $location->content_id = $content_id;
        $location->user_id = Auth::id();
        $location->save();
        return redirect()->back()->with("message", "Location Added Successfully!");
    }
    public function locations_edit(Request $request) {
        if(Cookie::get('geolocation_id') == null) {
            return redirect()->back()->with("error", "GeoLocation Missing!");
        }
        $request->validate([
            "id" => "required|exists:locations",
            "latitude" => "nullable|regex:/^-?[0-9]{1,3}(?:\.[0-9]{1,8})?$/",
            "longitude" => "nullable|regex:/^-?[0-9]{1,3}(?:\.[0-9]{1,8})?$/",
            "title" => ['required',new alpha_dash_space,'max:100'],
            "type" => "required|in:landmark,attraction,locality",
            "content_type" => "sometimes|required|in:html,text,blade",
            "content" => "required_with:content_type|string|max:65000"
        ]);
        $location = Location::find($request->id);
        $content_id = 0;
        if($request->has("content_type")) {
            if($location->content_id)
            $content = Content::find($location->content_id);
            else
            $content = new Content;
            $content->content_type = $request->content_type;
            $content->content = $request->content;
            $content->user_id = Auth::id();
            $content->save();
            $content_id = $content->id;
        } else {
            if($location->content_id)
            Content::destroy($location->content_id);
        }
        $location->geolocation_id = Cookie::get('geolocation_id');
        $location->type = $request->type;
        $location->title = $request->title;
        $location->latitude = $request->latitude;
        $location->longitude = $request->longitude;
        $location->content_id = $content_id;
        $location->user_id = Auth::id();
        $location->save();
        return redirect()->route('DataEntry.Locations')->with("message", "Location Edited Successfully!");
    }
    public function locations_delete($id) {
        $location = Location::find($id);
        if($location) {
            $location->delete();
        }
        return redirect()->back()->with("message", "Location Deleted Successfully!");
    }
    public function package($operation = null, $id = null) {
        $this->response['breadcrumbs'][] = [
            "route" => "DataEntry.Package",
            "routePar" => [],
            "name" => "Package"
        ];
        $this->response["operation"] = $operation;
        if($operation) {
            if($operation == "edit") {
                $this->response["package"] = Package::find($id);
                $this->response['breadcrumbs'][] = [
                    "route" => "DataEntry.Package",
                    "routePar" => ["operation" => $operation, "id" => $id],
                    "name" => ucwords($this->response["package"]->title)
                ];
            } else {
                $this->response['breadcrumbs'][] = [
                    "route" => "DataEntry.Package",
                    "routePar" => ["operation" => $operation],
                    "name" => ucwords($operation)
                ];
            }
            if($operation == "geography") {
                $this->response["packages"] = Package::whereHas('PackageItinerary', function($query)use($id){
                    $query->where("geolocation_id", $id);
                })->orderBy('id', 'desc')->paginate(100);
                if(Cookie::get('geolocation_id') == null) {
                    Cookie::queue('geolocation_id', $id, 0);
                    $this->response['reload'] = true;
                } elseif(Cookie::get('geolocation_id') != $id) {
                    Cookie::queue('geolocation_id', $id, 0);
                    $this->response['reload'] = true;
                }
            }
        } else {
            $this->response["packages"] = Package::orderBy('id', 'desc')->paginate(100);
        }
        return view('DataEntry.Package.index', $this->response);
    }
    public function package_add(Request $request) {
        $request->validate([
            "title" => ['required',new alpha_dash_space,'max:250'],
            "package_content" => "required|string|max:500",
            "content_type" => "sometimes|required|in:html,text,blade",
            "content" => "required_with:content_type|string|max:65000"
        ]);
        $content_id = 0;
        if($request->has("content_type")) {
            $content = new Content;
            $content->content_type = $request->content_type;
            $content->content = $request->content;
            $content->user_id = Auth::id();
            $content->save();
            $content_id = $content->id;
        }
        $package = new Package;
        $package->title = $request->title;
        $package->content = $request->package_content;
        $package->content_id = $content_id;
        $package->user_id = Auth::id();
        $package->save();
        if($request->has("content_type") && $request->content) {
            $this->image_management($request->content, "package", $package->id);
        }
        if($request->package_content) {
            $this->image_management($request->package_content, "package", $package->id);
        }
        return redirect()->route('DataEntry.Package.Detail', ["package_id"=>$package->id])->with("message", "Package '" .$request->title. "' Added Successfully!");
    }
    public function package_edit(Request $request) {
        $request->validate([
            "id" => "required|exists:packages",
            "title" => ['required',new alpha_dash_space,'max:250'],
            "package_content" => "required|string|max:500",
            "content_type" => "sometimes|required|in:html,text,blade",
            "content" => "required_with:content_type|string|max:65000"
        ]);
        $package = Package::find($request->id);
        $content_id = 0;
        if($request->has("content_type")) {
            if($package->content_id)
            $content = Content::find($package->content_id);
            else
            $content = new Content;
            $content->content_type = $request->content_type;
            $content->content = $request->content;
            $content->user_id = Auth::id();
            $content->save();
            $content_id = $content->id;
        } else {
            if($package->content_id)
            Content::destroy($package->content_id);
        }
        $package->title = $request->title;
        $package->content = $request->package_content;
        $package->content_id = $content_id;
        $package->user_id = Auth::id();
        $package->save();
        if($request->has("content_type") && $request->content) {
            $this->image_management($request->content, "package", $package->id);
        }
        if($request->package_content) {
            $this->image_management($request->package_content, "package", $package->id);
        }
        return redirect()->route('DataEntry.Package')->with("message", "Package '" .$request->title. "' Edited Successfully!");
    }
    public function package_delete($id = null) {
        $package = Package::find($id);
        if(!$package) {
            return redirect()->back()->with("error", "No Package Found.");
        }
        if(count($package->PackageDetail)) {
            if(count($package->PackageItinerary)){
                $package->PackageItinerary()->delete();
            }
            if(count($package->PackageMarker)) {
                $package->PackageMarker()->delete();
            }
            if(count($package->PackagePrice)) {
                $package->PackagePrice()->delete();
            }
            if(count($package->Images)) {
                $images = [];
                foreach($package->Images as $image) {
                    $images[] = 'public/'.$image->file_name;
                }
                Storage::delete($images);
                $package->Images()->delete();
            }
            $package->PackageDetail()->delete();
        }
        $package->delete();
        return redirect()->back()->with("message", $package->title." deleted successfully!");
    }
    public function package_detail($package_id, $operation = null, $id = null, $tab = null) {
        $package = Package::find($package_id);
        $this->response['tab'] = (($tab)?$tab:'itineraries');
        $this->response['breadcrumbs'][] = [
            "route" => "DataEntry.Package",
            "routePar" => [],
            "name" => "Package"
        ];
        $this->response['breadcrumbs'][] = [
            "route" => "DataEntry.Package.Detail",
            "routePar" => ["package_id" => $package_id],
            "name" => $package->title
        ];
        $this->response["package"] = $package;
        $this->response["operation"] = $operation;
        if($operation) {
            if($operation == "edit" || $operation == "show") {
                $this->response["packageDetail"] = PackageDetail::find($id);
            }
            if($operation == "show") {
                $this->response['breadcrumbs'][] = [
                    "route" => "DataEntry.Package.Detail",
                    "routePar" => ["operation" => $operation, "package_id" => $package_id],
                    "name" => ucwords((($this->response["packageDetail"]->days)?($this->response["packageDetail"]->days > 1)?$this->response["packageDetail"]->days.' Days':'1 Day':'Only')." ".(($this->response["packageDetail"]->nights)?($this->response["packageDetail"]->nights > 1)?$this->response["packageDetail"]->nights.' Nights':'1 Night':'Only'))
                ];
            } else {
                $this->response['breadcrumbs'][] = [
                    "route" => "DataEntry.Package.Detail",
                    "routePar" => ["operation" => $operation, "package_id" => $package_id],
                    "name" => ucwords(($operation))
                ];
            }
            $this->response["operation"] = $operation;
        } else {
            $this->response["packageDetails"] = $package->PackageDetail;
        }
        if($operation == "add" || $operation == "edit")
        return view('DataEntry.Forms.packageDetail', $this->response);
        if($operation == "show")
        return view('DataEntry.Package.completePackage', $this->response);
        return view('DataEntry.Package.packageDetail', $this->response);
    }
    public function package_detail_add(Request $request) {
        $request->validate([
            "package_id" => "required|exists:packages,id",
            "days" => "required|integer",
            "nights" => "required|integer",
            "content_type" => "sometimes|required|in:html,text,blade",
            "content" => "required_with:content_type|string|max:65000"
        ]);
        $content_id = 0;
        if($request->has("content_type")) {
            $content = new Content;
            $content->content_type = $request->content_type;
            $content->content = $request->content;
            $content->user_id = Auth::id();
            $content->save();
            $content_id = $content->id;
            $this->image_management($request->content, "package", $request->package_id);
        }
        $packageDetail = new PackageDetail;
        $packageDetail->package_id = $request->package_id;
        $packageDetail->days = $request->days;
        $packageDetail->nights = $request->nights;
        $packageDetail->content_id = $content_id;
        $packageDetail->user_id = Auth::id();
        $packageDetail->save();
        $package = Package::find($request->package_id);
        return redirect()->route('DataEntry.Package.Detail',['package_id' => $package->id, 'operation' => 'show', 'id' => $packageDetail->id])->with("message", "Added to " .$package->title. " Successfully!");

    }
    public function package_detail_edit(Request $request) {
        $request->validate([
            "id" => "required|exists:package_details",
            "package_id" => "required|exists:packages,id",
            "days" => "required|integer",
            "nights" => "required|integer",
            "content_type" => "sometimes|required|in:html,text,blade",
            "content" => "required_with:content_type|string|max:65000"
        ]);        
        $packageDetail = PackageDetail::find($request->id);
        $content_id = 0;
        if($request->has("content_type")) {
            if($packageDetail->content_id)
            $content = Content::find($packageDetail->content_id);
            else
            $content = new Content;
            $content->content_type = $request->content_type;
            $content->content = $request->content;
            $content->user_id = Auth::id();
            $content->save();
            $content_id = $content->id;
            $this->image_management($request->content, "package", $request->package_id);
        } else {
            if($packageDetail->content_id)
            Content::destroy($packageDetail->content_id);
        }
        $packageDetail->package_id = $request->package_id;
        $packageDetail->days = $request->days;
        $packageDetail->nights = $request->nights;
        $packageDetail->content_id = $content_id;
        $packageDetail->user_id = Auth::id();
        $packageDetail->save();
        return redirect()->route('DataEntry.Package.Detail', ["package_id" => $request->package_id])->with("message", "Edited " .(($packageDetail->days)?($packageDetail->days > 1)?$packageDetail->days.' Days':'1 Day':'Only')." ".(($packageDetail->nights)?($packageDetail->nights > 1)?$packageDetail->nights.' Nights':'1 Night':'Only')." Successfully!");
    }
    public function package_detail_delete($id) {
        $packageDetail = PackageDetail::find($id);
        if(!$packageDetail) {
            return redirect()->back()->with("error", "No Package Detail Found.");
        }
        if(count($packageDetail->PackageItinerary)){
            $packageDetail->PackageItinerary()->delete();
        }
        if(count($packageDetail->PackageMarker)) {
            $packageDetail->PackageMarker()->delete();
        }
        if(count($packageDetail->PackagePrice)) {
            $packageDetail->PackagePrice()->delete();
        }
        $packageDetail->delete();
        return redirect()->back()->with("message", $packageDetail->title." deleted successfully!");
    }
    public function package_marker($package_id, $package_detail_id, $operation = null, $id = null) {
        $package = Package::find($package_id);
        $this->response['package'] = $package;
        $packageDetail = PackageDetail::find($package_detail_id);
        $this->response['packageDetail'] = $packageDetail;
        $this->response['breadcrumbs'][] = [
            "route" => "DataEntry.Package",
            "routePar" => [],
            "name" => "Package"
        ];
        $this->response['breadcrumbs'][] = [
            "route" => "DataEntry.Package.Detail",
            "routePar" => ["package_id" => $package_id],
            "name" => $package->title
        ];
        $this->response['breadcrumbs'][] = [
            "route" => "DataEntry.Package.Detail",
            "routePar" => ["operation" => "show", "package_id" => $package_id, "id" => $packageDetail->id],
            "name" => ucwords((($packageDetail->days)?($packageDetail->days > 1)?$packageDetail->days.' Days':'1 Day':'Only')." ".(($packageDetail->nights)?($packageDetail->nights > 1)?$packageDetail->nights.' Nights':'1 Night':'Only'))
        ];
        if($operation) {
            $this->response['breadcrumbs'][] = [
                "route" => "DataEntry.Package.Marker",
                "routePar" => ['package_id' => $package->id, 'package_detail_id' => $packageDetail->id, 'operation' => $operation],
                "name" => "Markers"
            ];
            $this->response["operation"] = $operation;
        }
        return view('DataEntry.Forms.packageMarker', $this->response);
    }
    public function package_markers(Request $request) {
        $request->validate([
            "package_id" => "required|exists:packages,id",
            "package_detail_id" => "required|exists:package_details,id",
            "marker" => "required|array",
            "marker.*" => "required|exists:data_markers,id",
            "order" => "required|array",
            "order.*" => "required|integer",
            "primary.*" => "required|in:0,1",
        ]);
        $alreadyMarked = PackageMarker::where("package_detail_id",$request->package_detail_id)->get();
        $delete = [];
        foreach($alreadyMarked as $marked) {
            $delete[$marked->id] = 1;
        }
        $markers = DataMarker::whereIn("id", $request->marker)->get();
        foreach($markers as $marker) {
            $packageMarker = null;
            foreach($alreadyMarked as $marked) {
                if($marked->type == $marker->type && $marked->title == $marker->title) {
                    unset($delete[$marked->id]);
                    $packageMarker = $marked;
                    break;
                }
            }
            if(!$packageMarker)
            $packageMarker = new PackageMarker;
            $packageMarker->package_detail_id = $request->package_detail_id;
            $packageMarker->type = $marker->type;
            $packageMarker->primary_marker = $request->primary[$marker->id];
            $packageMarker->order = $request->order[$marker->id];
            $packageMarker->title = $marker->title;
            $packageMarker->content = $marker->content;
            $packageMarker->content_id = $marker->content_id;
            $packageMarker->user_id = Auth::id();
            $packageMarker->save();
        }
        foreach($delete as $key => $val) {
            PackageMarker::destroy($key);
        }
        return redirect()->route('DataEntry.Package.Detail', ['package_id' => $request->package_id, 'operation' => 'show', 'id' => $request->package_detail_id, 'tab' => 'markers'])->with("message", "Markers Added Successfully!");
    }
    public function package_price($package_id, $package_detail_id, $operation = null, $id = null) {
        $package = Package::find($package_id);
        $this->response['package'] = $package;
        $packageDetail = PackageDetail::find($package_detail_id);
        $this->response['packageDetail'] = $packageDetail;
        $this->response['breadcrumbs'][] = [
            "route" => "DataEntry.Package",
            "routePar" => [],
            "name" => "Package"
        ];
        $this->response['breadcrumbs'][] = [
            "route" => "DataEntry.Package.Detail",
            "routePar" => ["package_id" => $package_id],
            "name" => $package->title
        ];
        $this->response['breadcrumbs'][] = [
            "route" => "DataEntry.Package.Detail",
            "routePar" => ["operation" => "show", "package_id" => $package_id, "id" => $packageDetail->id],
            "name" => ucwords((($packageDetail->days)?($packageDetail->days > 1)?$packageDetail->days.' Days':'1 Day':'Only')." ".(($packageDetail->nights)?($packageDetail->nights > 1)?$packageDetail->nights.' Nights':'1 Night':'Only'))
        ];
        if($operation) {
            if($operation == "edit") {
                $this->response["price"] = PackagePrice::find($id);
            }
            $this->response['breadcrumbs'][] = [
                "route" => "DataEntry.Package.Price",
                "routePar" => ['package_id' => $package->id, 'package_detail_id' => $packageDetail->id, 'operation' => $operation],
                "name" => ucwords(($operation)). " Price"
            ];
            $this->response["operation"] = $operation;
        }
        return view('DataEntry.Forms.packagePrice', $this->response);
    }
    public function package_price_add(Request $request) {
        $request->validate([
            "package_id" => "required|exists:packages,id",
            "package_detail_id" => "required|exists:package_details,id",
            "title" => ['required',new alpha_dash_space,'max:250'],
            "currency" => "required|alpha|max:3",
            "price_start" => "required|numeric",
            "price_end" => "required|nullable|numeric",
            "discount_percent" => "required|nullable|numeric",
            "person" => "required|integer"
        ]);
        $price = new PackagePrice;
        $price->package_detail_id = $request->package_detail_id;
        $price->title = $request->title;
        $price->currency = $request->currency;
        $price->price_start = $request->price_start;
        $price->price_end = $request->price_end;
        $price->discount_percent = $request->discount_percent;
        $price->person = $request->person;
        $price->user_id = Auth::id();
        $price->save();
        return redirect()->back()->with("message", "Price Added Successfully!");
    }
    public function package_price_edit(Request $request) {
        $request->validate([
            "id" => "required|exists:package_prices",
            "package_id" => "required|exists:packages,id",
            "package_detail_id" => "required|exists:package_details,id",
            "title" => ['required',new alpha_dash_space,'max:250'],
            "currency" => "required|alpha|max:3",
            "price_start" => "required|numeric",
            "price_end" => "required|nullable|numeric",
            "discount_percent" => "required|nullable|numeric",
            "person" => "required|integer"
        ]);
        $price = PackagePrice::find($request->id);
        $price->package_detail_id = $request->package_detail_id;
        $price->title = $request->title;
        $price->currency = $request->currency;
        $price->price_start = $request->price_start;
        $price->price_end = $request->price_end;
        $price->discount_percent = $request->discount_percent;
        $price->person = $request->person;
        $price->user_id = Auth::id();
        $price->save();
        return redirect()->route('DataEntry.Package.Detail', ['package_id' => $request->package_id, "operation" => "show", "id" => $request->package_detail_id, 'tab' => 'prices'])->with("message", "Price '" .$request->title. "' Edited Successfully!");
    }
    public function package_price_delete($id) {
        $price = PackagePrice::find($id);
        if(!$price) {
            return redirect()->back()->with("message", "Price not found!");
        }
        $price->delete();
        return redirect()->back()->with("message", "Price Deleted Successfully!");
    }
    public function package_itineraries($package_id, $package_detail_id, $operation = null, $id = null) {
        $package = Package::find($package_id);
        $this->response['package'] = $package;
        $packageDetail = PackageDetail::find($package_detail_id);
        $this->response['packageDetail'] = $packageDetail;
        $this->response['breadcrumbs'][] = [
            "route" => "DataEntry.Package",
            "routePar" => [],
            "name" => "Package"
        ];
        $this->response['breadcrumbs'][] = [
            "route" => "DataEntry.Package.Detail",
            "routePar" => ["package_id" => $package_id],
            "name" => $package->title
        ];
        $this->response['breadcrumbs'][] = [
            "route" => "DataEntry.Package.Detail",
            "routePar" => ["operation" => "show", "package_id" => $package_id, "id" => $packageDetail->id],
            "name" => ucwords((($packageDetail->days)?($packageDetail->days > 1)?$packageDetail->days.' Days':'1 Day':'Only')." ".(($packageDetail->nights)?($packageDetail->nights > 1)?$packageDetail->nights.' Nights':'1 Night':'Only'))
        ];
        if($operation) {
            if($operation == "edit") {
                $this->response["itinerary"] = PackageItinerary::find($id);
            }
            $this->response['breadcrumbs'][] = [
                "route" => "DataEntry.Package.Itinerary",
                "routePar" => ['package_id' => $package->id, 'package_detail_id' => $packageDetail->id, 'operation' => $operation],
                "name" => ucwords(($operation)). " Itinerary"
            ];
            $this->response["operation"] = $operation;
        }
        return view('DataEntry.Forms.packageItinerary', $this->response);
    }
    public function package_itineraries_add(Request $request) {
        $request->validate([
            "package_id" => "required|exists:packages,id",
            "package_detail_id" => "required|exists:package_details,id",
            "title" => ['required',new alpha_dash_space,'max:250'],
            "itinerary_type" => "nullable|in:geolocation,geo_hotel",
            "hotel_name" => "required_if:itinerary_type,geo_hotel|nullable|exists:hotels",          
            "content_type" => "required|in:html,text,blade",
            "content" => "required|string|max:65000"
        ]);
        $geolocation_id = 0;
        $hotel_id = 0;
        $location_id = 0;
        if($request->itinerary_type) {
            if(Cookie::get('geolocation_id') == null) {
                return redirect()->back()->with("error", "GeoLocation not found.");
            }
            $geolocation_id = Cookie::get('geolocation_id');
            if($request->itinerary_type == "geo_hotel") {
                $hotel = Hotel::where([
                    "geolocation_id" => $geolocation_id,
                    "visibility" => 1,
                    "hotel_name" => $request->hotel_name
                ])->first();
                if(!$hotel)
                return redirect()->back()->with("error", "Hotel '" .$request->hotel_name. "' could not be found");
                $hotel_id = $hotel->id;
            }
            if($request->has('location_name') && $request->location_name) {
                $location = Location::where([
                    "geolocation_id" => $geolocation_id,
                    "type" => 'locality',
                    "title" => $request->location_name
                ])->first();
                if(!$location)
                return redirect()->back()->with("error", "Location '" .$request->location_name. "' could not be found");
                $location_id = $location->id;
            }
        }
        $content = new Content;
        $content->content_type = $request->content_type;
        $content->content = $request->content;
        $content->user_id = Auth::id();
        $content->save();
        $itinerary = new PackageItinerary;
        $itinerary->package_detail_id = $request->package_detail_id;
        $itinerary->title = $request->title;
        $itinerary->geolocation_id = $geolocation_id;
        $itinerary->location_id = $location_id;
        $itinerary->hotel_id = $hotel_id;
        $itinerary->content_id = $content->id;
        $itinerary->save();
        $this->image_management($request->content, "package", $request->package_id);
        return redirect()->back()->with("message", "Itinerary '" .$request->title. "' Added Successfully!");
    }
    public function package_itineraries_edit(Request $request) {
        $request->validate([
            "id" => "required|exists:package_itineraries",
            "package_id" => "required|exists:packages,id",
            "package_detail_id" => "required|exists:package_details,id",
            "title" => ['required',new alpha_dash_space,'max:250'],
            "itinerary_type" => "nullable|in:geolocation,geo_hotel",
            "hotel_name" => "required_if:itinerary_type,geo_hotel|nullable|exists:hotels",          
            "content_type" => "required|in:html,text,blade",
            "content" => "required|string|max:65000"
        ]);
        $itinerary = PackageItinerary::find($request->id);
        $geolocation_id = 0;
        $hotel_id = 0;
        $location_id = 0;
        if($request->itinerary_type) {
            if(Cookie::get('geolocation_id') == null) {
                return redirect()->back()->with("error", "GeoLocation not found.");
            }
            $geolocation_id = Cookie::get('geolocation_id');
            if($request->itinerary_type == "geo_hotel") {
                $hotel = Hotel::where([
                    "geolocation_id" => $geolocation_id,
                    "visibility" => 1,
                    "hotel_name" => $request->hotel_name
                ])->first();
                if(!$hotel)
                return redirect()->back()->with("error", "Hotel '" .$request->hotel_name. "' does not exist in selected GeoLocation.");
                $hotel_id = $hotel->id;
            }
            if($request->has('location_name') && $request->location_name) {
                $location = Location::where([
                    "geolocation_id" => $geolocation_id,
                    "type" => 'locality',
                    "title" => $request->location_name
                ])->first();
                if(!$location)
                return redirect()->back()->with("error", "Location '" .$request->location_name. "' could not be found");
                $location_id = $location->id;
            }
        }
        $content = Content::find($itinerary->content_id);
        $content->content_type = $request->content_type;
        $content->content = $request->content;
        $content->user_id = Auth::id();
        $content->save();
        $itinerary->package_detail_id = $request->package_detail_id;
        $itinerary->title = $request->title;
        $itinerary->geolocation_id = $geolocation_id;
        $itinerary->location_id = $location_id;
        $itinerary->hotel_id = $hotel_id;
        $itinerary->save();
        $this->image_management($request->content, "package", $request->package_id);
        return redirect()->route('DataEntry.Package.Detail', ['package_id' =>$request->package_id, "operation" => "show", "id" => $request->package_detail_id])->with("message", "Itinerary '" .$request->title. "' Edited Successfully!");
    }
    public function package_itineraries_delete($id) {
        $itinerary = PackageItinerary::find($id);
        if($itinerary) {
            $itinerary->delete();
        }
        return redirect()->back()->with("message", "Itinerary Deleted Successfully!");
    }
    public function hotel($operation = null, $id = null, $tab = null) {
        $this->response['breadcrumbs'][] = [
            "route" => "DataEntry.Hotel",
            "routePar" => [],
            "name" => "Hotels"
        ];
        $this->response['operation'] = $operation;
        if($operation) {
            $this->response['hotel'] = null;
            if($operation == "edit" || $operation == "show") {
                $this->response['hotel'] = Hotel::find($id);
                if($operation == "edit" && $this->response['hotel'] && $this->response['hotel']->geolocation_id) {
                    if(Cookie::get('geolocation_id') == null) {
                        Cookie::queue('geolocation_id', $this->response['hotel']->geolocation_id, 0);
                        $this->response['reload'] = true;
                    } elseif(Cookie::get('geolocation_id') != $this->response['hotel']->geolocation_id) {
                        Cookie::queue('geolocation_id', $this->response['hotel']->geolocation_id, 0);
                        $this->response['reload'] = true;
                    }
                }
            }
            $this->response['breadcrumbs'][] = [
                "route" => "DataEntry.Hotel",
                "routePar" => ["operation" => $operation],
                "name" => (($operation != "show")?ucwords($operation).' ':'').(($this->response['hotel'])?$this->response['hotel']->hotel_name:'')
            ];
            if($operation == "show") {
                $this->response["tab"] = (($tab)?$tab:"hotel_contact");
                return view('DataEntry.Hotel.hotel', $this->response);
            } else if($operation == "geography") {
                $this->response['hotels'] = Hotel::where("geolocation_id", $id)->orderBy('id', 'desc')->paginate(100);
                if(Cookie::get('geolocation_id') == null) {
                    Cookie::queue('geolocation_id', $id, 0);
                    $this->response['reload'] = true;
                } elseif(Cookie::get('geolocation_id') != $id) {
                    Cookie::queue('geolocation_id', $id, 0);
                    $this->response['reload'] = true;
                }
                return view('DataEntry.Hotel.index', $this->response);
            }
            return view('DataEntry.Forms.hotel', $this->response);
        } else {
            $this->response['hotels'] = Hotel::orderBy('id', 'desc')->paginate(100);
        }
        return view('DataEntry.Hotel.index', $this->response);
    }
    public function hotel_add(Request $request) {
        if(Cookie::get('geolocation_id') == null) {
            return redirect()->back()->with("error", "Geolocation Missing.");
        }
        $request->validate([
            "location_name" => "nullable|exists:locations,title",
            "latitude" => "nullable|regex:/^-?[0-9]{1,3}(?:\.[0-9]{1,8})?$/",
            "longitude" => "nullable|regex:/^-?[0-9]{1,3}(?:\.[0-9]{1,8})?$/",
            "hotel_name" => ['required',new alpha_dash_space,'max:100'],
            "no_of_rooms" => "nullable|integer",
            "visibility" => "required|boolean",
            "address" => "required|string|max:100",
            "hotel_content_type" => "nullable|in:html,text,blade",
            "hotel_content" => "nullable|string|max:65000",
            "policy_content_type" => "nullable|in:html,text,blade",
            "policy_content" => "nullable|string|max:65000",
            "content" => "nullable|string|max:65000"
        ]);
        $policy_id = 0;
        $content_id = 0;
        $location_id = 0;
        if($request->has('location_name') && $request->location_name) {
            $location = Location::where([
                "geolocation_id" => Cookie::get('geolocation_id'),
                "type" => 'locality',
                "title" => $request->location_name
            ])->first();
            if(!$location)
            return redirect()->back()->with("error", "Location '" .$request->location_name. "' could not be found");
            $location_id = $location->id;
        }
        if($request->has("hotel_content") && $request->hotel_content) {
            $content = new Content;
            $content->content_type = $request->hotel_content_type;
            $content->content = $request->hotel_content;
            $content->user_id = Auth::id();
            $content->save();
            $content_id = $content->id;
        }
        if($request->has("policy_content") && $request->policy_content) {
            $content = new Content;
            $content->content_type = $request->policy_content_type;
            $content->content = $request->policy_content;
            $content->user_id = Auth::id();
            $content->save();
            $policy_id = $content->id;
        }
        $hotel = new Hotel;
        $hotel->geolocation_id = Cookie::get('geolocation_id');
        $hotel->visibility = $request->visibility;
        $hotel->location_id = $location_id;
        $hotel->hotel_name = $request->hotel_name;
        $hotel->address = $request->address;
        $hotel->no_of_rooms = $request->no_of_rooms;
        $hotel->content_id = $content_id;
        $hotel->policy_id = $policy_id;
        $hotel->latitude = $request->latitude;
        $hotel->longitude = $request->longitude;
        $hotel->user_id = Auth::id();
        $hotel->save();
        if($request->has("hotel_content") && $request->hotel_content) {
            $this->image_management($request->hotel_content, "hotel", $hotel->id);
        }
        if($request->has("policy_content") && $request->policy_content) {
            $this->image_management($request->policy_content, "hotel", $hotel->id);
        }
        if($request->has("content") && $request->content) {
            $this->image_management($request->content, "hotelIM", $hotel->id);
        }
        return redirect()->route('DataEntry.Hotel', ["operation" => "show", "id" => $hotel->id])->with("message", $request->hotel_name." Added Successfully!");
    }
    public function hotel_edit(Request $request) {
        if(Cookie::get('geolocation_id') == null) {
            return redirect()->back()->with("error", "Geolocation Missing.");
        }
        $request->validate([
            "id" => "required|exists:hotels",
            "location_name" => "nullable|exists:locations,title",
            "latitude" => "nullable|regex:/^-?[0-9]{1,3}(?:\.[0-9]{1,8})?$/",
            "longitude" => "nullable|regex:/^-?[0-9]{1,3}(?:\.[0-9]{1,8})?$/",
            "hotel_name" => ['required',new alpha_dash_space,'max:100'],
            "no_of_rooms" => "nullable|integer",
            "visibility" => "required|boolean",
            "address" => "required|string|max:100",
            "hotel_content_type" => "nullable|in:html,text,blade",
            "hotel_content" => "nullable|string|max:65000",
            "policy_content_type" => "nullable|in:html,text,blade",
            "policy_content" => "nullable|string|max:65000",
            "content" => "nullable|string|max:65000"
        ]);
        $hotel = Hotel::find($request->id);
        $policy_id = 0;
        $content_id = 0;
        $location_id = 0;
        if($request->has('location_name') && $request->location_name) {
            $location = Location::where([
                "geolocation_id" => Cookie::get('geolocation_id'),
                "type" => 'locality',
                "title" => $request->location_name
            ])->first();
            if(!$location)
            return redirect()->back()->with("error", "Location '" .$request->location_name. "' could not be found");
            $location_id = $location->id;
        }
        if($request->has("hotel_content") && $request->hotel_content) {
            if($hotel->content_id)
            $content = Content::find($hotel->content_id);
            else
            $content = new Content;
            $content->content_type = $request->hotel_content_type;
            $content->content = $request->hotel_content;
            $content->user_id = Auth::id();
            $content->save();
            $content_id = $content->id;
        } else {
            if($hotel->content_id)
            Content::destroy($hotel->content_id);
        }
        if($request->has("policy_content") && $request->policy_content) {
            if($hotel->policy_id)
            $content = Content::find($hotel->policy_id);
            else
            $content = new Content;
            $content->content_type = $request->policy_content_type;
            $content->content = $request->policy_content;
            $content->user_id = Auth::id();
            $content->save();
            $policy_id = $content->id;
        } else {
            if($hotel->policy_id)
            Content::destroy($hotel->policy_id);
        }
        $hotel->geolocation_id = Cookie::get('geolocation_id');
        $hotel->visibility = $request->visibility;
        $hotel->location_id = $location_id;
        $hotel->hotel_name = $request->hotel_name;
        $hotel->address = $request->address;
        $hotel->no_of_rooms = $request->no_of_rooms;
        $hotel->content_id = $content_id;
        $hotel->policy_id = $policy_id;
        $hotel->latitude = $request->latitude;
        $hotel->longitude = $request->longitude;
        $hotel->user_id = Auth::id();
        $hotel->save();
        if($request->has("hotel_content") && $request->hotel_content) {
            $this->image_management($request->hotel_content, "hotel", $hotel->id);
        }
        if($request->has("policy_content") && $request->policy_content) {
            $this->image_management($request->policy_content, "hotel", $hotel->id);
        }
        if($request->has("content") && $request->content) {
            $this->image_management($request->content, "hotelIM", $hotel->id);
        }
        return redirect()->route('DataEntry.Hotel')->with("message", $request->hotel_name." Edited Successfully!");
    }
    public function hotel_delete($id) {
        $hotel = Hotel::find($id);
        if(count($hotel->HotelContact))
        $hotel->HotelContact()->delete();
        if(count($hotel->AllHotelFacility))
        $hotel->AllHotelFacility()->delete();
        if(count($hotel->HotelMarker))
        $hotel->HotelMarker()->delete();
        if(count($hotel->HotelRoom))
        $hotel->HotelRoom()->delete();
        if(count($hotel->Images)) {
            $images = [];
            foreach($hotel->Images as $image) {
                $images[] = 'public/'.$image->file_name;
            }
            Storage::delete($images);
            $hotel->Images()->delete();
        }
        $hotel->delete();
        return redirect()->back()->with("message", $hotel->hotel_name." Deleted Successfully!");
    }
    public function hotel_get() {
        $response = null;
        if(Cookie::get('geolocation_id') == null) {
            $response = ["message" => "Missing GeoLocation."];
        }
        $hotels = Hotel::select('id', 'hotel_name')->where(['geolocation_id' => Cookie::get('geolocation_id'), 'visibility' => 1])->get();
        foreach($hotels as $hotel) {
            $response[$hotel->id] = $hotel->hotel_name;
        }
        if(!$response) {
            $response = ["message" => "No Hotel Found."];
        }
        return response()->json($response);
    }
    public function hotel_contact($hotel_id, $operation = null, $id = null) {
        $this->response['breadcrumbs'][] = [
            "route" => "DataEntry.Hotel",
            "routePar" => [],
            "name" => "Hotels"
        ];
        $this->response['operation'] = $operation;
        $this->response['hotel'] = Hotel::find($hotel_id);
        $this->response['breadcrumbs'][] = [
            "route" => "DataEntry.Hotel",
            "routePar" => ["operation" => "show", "hotel_id" => $this->response['hotel']->id, "tab" => "hotel_contact"],
            "name" => $this->response['hotel']->hotel_name
        ];
        $this->response['breadcrumbs'][] = [
            "route" => "",
            "routePar" => [],
            "name" => ucwords($operation)." Contact"
        ];
        if($operation == "edit")
        $this->response['contact'] = HotelContact::find($id);
        else
        $this->response['contact'] = null;
        return view('DataEntry.Forms.hotelContact', $this->response);
    }
    public function hotel_contact_add(Request $request) {
        $request->validate([
            "hotel_id" => "required|exists:hotels,id",
            "type" => "required|in:email,mobile,landline,website",
            "content" => "required|string|max:200"
        ]);
        $contact = new HotelContact;
        $contact->hotel_id = $request->hotel_id;
        $contact->type = $request->type;
        $contact->content = $request->content;
        $contact->user_id = Auth::id();
        $contact->save();
        return redirect()->back()->with("message", ucwords($request->type)." Added Successfully.");
    }
    public function hotel_contact_edit(Request $request) {
        $request->validate([
            "id" => "required|exists:hotel_contacts",
            "hotel_id" => "required|exists:hotels,id",
            "type" => "required|in:email,mobile,landline,website",
            "content" => "required|string|max:200"
        ]);
        $contact = HotelContact::find($request->id);
        $contact->hotel_id = $request->hotel_id;
        $contact->type = $request->type;
        $contact->content = $request->content;
        $contact->user_id = Auth::id();
        $contact->save();
        return redirect()->route('DataEntry.Hotel', ['operation' => 'show', 'id' => $request->hotel_id, 'tab' => "hotel_contact"])->with("message", ucwords($request->type)." Edited Successfully.");
    }
    public function hotel_contact_delete($id) {
        $contact = HotelContact::find($id);
        if($contact) {
            $contact->delete();
            return redirect()->route('DataEntry.Hotel', ['operation' => 'show', 'id' => $contact->hotel_id, 'tab' => "hotel_contact"])->with("message", ucwords($contact->type)." Deleted Successfully.");
        }
        return redirect()->back()->with("error", "No Contact Found.");
    }
    public function hotel_room($hotel_id, $operation = null, $id = null) {
        $this->response['breadcrumbs'][] = [
            "route" => "DataEntry.Hotel",
            "routePar" => [],
            "name" => "Hotels"
        ];
        $this->response['operation'] = $operation;
        $this->response['hotel'] = Hotel::find($hotel_id);
        $this->response['breadcrumbs'][] = [
            "route" => "DataEntry.Hotel",
            "routePar" => ["operation" => "show", "hotel_id" => $this->response['hotel']->id, "tab" => "hotel_rooms"],
            "name" => $this->response['hotel']->hotel_name
        ];
        $this->response['breadcrumbs'][] = [
            "route" => "",
            "routePar" => [],
            "name" => ucwords($operation)." Room"
        ];
        if($operation == "edit")
        $this->response['hotelRoom'] = HotelRoom::find($id);
        else
        $this->response['hotelRoom'] = null;
        return view('DataEntry.Forms.hotelRoom', $this->response);
    }
    public function hotel_room_add(Request $request) {
        $request->validate([
            "hotel_id" => "required|exists:hotels,id",
            "title" => ['required',new alpha_dash_space,'max:250'],
            "currency" => "required|alpha|max:3",
            "price_start" => "required|numeric",
            "price_end" => "required|nullable|numeric",
            "discount_percent" => "required|nullable|numeric",
            "person" => "required|integer"
        ]);
        $hotelRoom = new HotelRoom;
        $hotelRoom->hotel_id = $request->hotel_id;
        $hotelRoom->title = $request->title;
        $hotelRoom->currency = $request->currency;
        $hotelRoom->price_start = $request->price_start;
        $hotelRoom->price_end = $request->price_end;
        $hotelRoom->discount_percent = $request->discount_percent;
        $hotelRoom->person = $request->person;
        $hotelRoom->user_id = Auth::id();
        $hotelRoom->save();
        return redirect()->back()->with("message", $request->title." Room Added Successfully!");
    }
    public function hotel_room_edit(Request $request) {
        $request->validate([
            "id" => "required|exists:hotel_rooms",
            "hotel_id" => "required|exists:hotels,id",
            "title" => ['required',new alpha_dash_space,'max:250'],
            "currency" => "required|alpha|max:3",
            "price_start" => "required|numeric",
            "price_end" => "required|nullable|numeric",
            "discount_percent" => "required|nullable|numeric",
            "person" => "required|integer"
        ]);
        $hotelRoom = HotelRoom::find($request->id);
        $hotelRoom->hotel_id = $request->hotel_id;
        $hotelRoom->title = $request->title;
        $hotelRoom->currency = $request->currency;
        $hotelRoom->price_start = $request->price_start;
        $hotelRoom->price_end = $request->price_end;
        $hotelRoom->discount_percent = $request->discount_percent;
        $hotelRoom->person = $request->person;
        $hotelRoom->user_id = Auth::id();
        $hotelRoom->save();
        return redirect()->route('DataEntry.Hotel', ['operation' => 'show', 'id' => $request->hotel_id, 'tab' => "hotel_rooms"])->with("message", $request->title." Room Edited Successfully.");
    }
    public function hotel_room_delete(Request $request) {
        $room = HotelRoom::find($id);
        if($room) {
            $room->delete();
            return redirect()->route('DataEntry.Hotel', ['operation' => 'show', 'id' => $room->hotel_id, 'tab' => "hotel_rooms"])->with("message", ucwords($room->title)." Deleted Successfully.");
        }
        return redirect()->back()->with("error", "No Room Found.");
    }
    public function hotel_facility($hotel_id, $operation = null, $hotel_room_id = null) {
        $this->response['breadcrumbs'][] = [
            "route" => "DataEntry.Hotel",
            "routePar" => [],
            "name" => "Hotels"
        ];
        $this->response['operation'] = $operation;
        $this->response['hotel'] = Hotel::find($hotel_id);
        $this->response['breadcrumbs'][] = [
            "route" => "DataEntry.Hotel",
            "routePar" => ["operation" => "show", "hotel_id" => $this->response['hotel']->id, "tab" => "hotel_facilities"],
            "name" => $this->response['hotel']->hotel_name
        ];
        if($hotel_room_id) {
            $this->response['hotelRoom'] = HotelRoom::find($hotel_room_id);
            $this->response['breadcrumbs'][] = [
                "route" => "DataEntry.Hotel",
                "routePar" => ["operation" => "show", "hotel_id" => $this->response['hotel']->id, "tab" => "hotel_rooms"],
                "name" => $this->response['hotelRoom']->title
            ];
            $this->response['breadcrumbs'][] = [
                "route" => "",
                "routePar" => [],
                "name" => "Hotel Room Facilities"
            ];
        } else {
            $this->response['breadcrumbs'][] = [
                "route" => "",
                "routePar" => [],
                "name" => "Hotel Facilities"
            ];
        }
        return view('DataEntry.Forms.hotelFacility', $this->response);
    }
    public function hotel_facilities(Request $request) {
        $request->validate([
            "hotel_id" => "required|exists:hotels,id",
            "hotel_room_id" => "sometimes|required|exists:hotel_rooms,id",
            "operation" => "required|in:hotel,room",
            "facility" => "required|array",
            "facility.*" => "required|exists:data_facilities,id"
        ]);
        $where = ["hotel_id" => $request->hotel_id];
        $hotel_room_id = 0;
        if($request->operation == "room") {
            $hotel_room_id = $request->hotel_room_id;
            $where["hotel_room_id"] = $request->hotel_room_id;
        }
        $alreadyMarked = HotelFacility::where($where)->get();
        $delete = [];
        foreach($alreadyMarked as $marked) {
            $delete[$marked->id] = 1;
        }
        $facilities = DataFacility::whereIn("id", $request->facility)->get();
        foreach($facilities as $facility) {
            $HotelFacility = null;
            foreach($alreadyMarked as $marked) {
                if($marked->type == $facility->type && $marked->title == $facility->title) {
                    unset($delete[$marked->id]);
                    $HotelFacility = $marked;
                    break;
                }
            }
            if(!$HotelFacility)
            $HotelFacility = new HotelFacility;
            $HotelFacility->hotel_id = $request->hotel_id;
            $HotelFacility->title = $facility->title;
            $HotelFacility->type = $facility->type;
            $HotelFacility->hotel_room_id = $hotel_room_id;
            $HotelFacility->content = $facility->content;
            $HotelFacility->content_id = $facility->content_id;
            $HotelFacility->user_id = Auth::id();
            $HotelFacility->save();
        }
        foreach($delete as $key => $val) {
            HotelFacility::destroy($key);
        }
        if($request->operation == "room")
        $tab = "hotel_rooms";
        else
        $tab = "hotel_facilities";
        return redirect()->route('DataEntry.Hotel', ['operation' => 'show', 'id' => $request->hotel_id, 'tab' => $tab])->with("message", "Facilities Added Successfully!");
    }
    public function hotel_marker($hotel_id) {
        $this->response['breadcrumbs'][] = [
            "route" => "DataEntry.Hotel",
            "routePar" => [],
            "name" => "Hotels"
        ];
        $this->response['hotel'] = Hotel::find($hotel_id);
        $this->response['breadcrumbs'][] = [
            "route" => "DataEntry.Hotel",
            "routePar" => ["operation" => "show", "hotel_id" => $this->response['hotel']->id, "tab" => "hotel_markers"],
            "name" => $this->response['hotel']->hotel_name
        ];
        $this->response['breadcrumbs'][] = [
            "route" => "",
            "routePar" => [],
            "name" => "Labels Tags Categories"
        ];
        return view('DataEntry.Forms.hotelMarker', $this->response);
    }
    public function hotel_markers(Request $request) {
        $request->validate([
            "hotel_id" => "required|exists:packages,id",
            "marker" => "required|array",
            "marker.*" => "required|exists:data_markers,id",
            "order" => "required|array",
            "order.*" => "required|integer",
            "primary.*" => "required|boolean",
        ]);
        $alreadyMarked = HotelMarker::where("hotel_id", $request->hotel_id)->get();
        $delete = [];
        foreach($alreadyMarked as $marked) {
            $delete[$marked->id] = 1;
        }
        $markers = DataMarker::whereIn("id", $request->marker)->get();
        foreach($markers as $marker) {
            $HotelMarker = null;
            foreach($alreadyMarked as $marked) {
                if($marked->type == $marker->type && $marked->title == $marker->title) {
                    unset($delete[$marked->id]);
                    $HotelMarker = $marked;
                    break;
                }
            }
            if(!$HotelMarker)
            $HotelMarker = new HotelMarker;
            $HotelMarker->hotel_id = $request->hotel_id;
            $HotelMarker->type = $marker->type;
            $HotelMarker->order = $request->order[$marker->id];
            $HotelMarker->primary_marker = $request->primary[$marker->id];
            $HotelMarker->title = $marker->title;
            $HotelMarker->content = $marker->content;
            $HotelMarker->content_id = $marker->content_id;
            $HotelMarker->user_id = Auth::id();
            $HotelMarker->save();
        }
        foreach($delete as $key => $val) {
            HotelMarker::destroy($key);
        }
        return redirect()->route('DataEntry.Hotel', ['operation' => 'show', 'id' => $request->hotel_id, 'tab' => 'hotel_markers'])->with("message", "Markers Added Successfully!");
    }
}
