<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Content;
use App\Models\DataMarker;
use App\Models\DataFacility;
use App\Models\Location;
use App\Models\GeoLocation;
use App\Models\Images;
use App\Models\Package;
use App\Models\PackageDetail;
use App\Models\PackageItinerary;
use App\Models\PackageMarker;
use App\Models\PackagePrice;
use App\Models\Hotel;
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
    public function hotel_contact($hotel_id, $operation = null, $id = null) {}
    public function hotel_contact_add(Request $request) {}
    public function hotel_contact_edit(Request $request) {}
    public function hotel_contact_delete(Request $request) {}
    public function hotel_facility(){}


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
    public function index() {
        return view('DataEntry.dataEntry', $this->response);
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
                $countries = [["selected"=>$country]];
                $geolocation_id = GeoLocation::where(["continent" => $continent, "country" => $country])->first();
                if($geolocation_id)
                Cookie::queue('geolocation_id', $geolocation_id->id, 0);
                if($division) {
                    $divisions = [["selected"=>$division]];
                    $geolocation_id = GeoLocation::where(["continent" => $continent, "country" => $country, "division" => $division])->first();
                    if($geolocation_id)
                    Cookie::queue('geolocation_id', $geolocation_id->id, 0);
                    if($subdivision) {
                        $subdivisions = [["selected"=>$subdivision]];
                        $geolocation_id = GeoLocation::where(["continent" => $continent, "country" => $country, "division" => $division, "subdivision" => $subdivision])->first();
                        if($geolocation_id)
                        Cookie::queue('geolocation_id', $geolocation_id->id, 0);
                        if($city) {
                            $cities = [["selected"=>$city]];
                            $geolocation_id = GeoLocation::where(["continent" => $continent, "country" => $country, "division" => $division, "subdivision" => "", "city" => $city])->first();
                            if($geolocation_id)
                            Cookie::queue('geolocation_id', $geolocation_id->id, 0);
                        } else {
                            $cities = GeoLocation::select("city")->where(["continent" => $continent, "country" => $country, "division" => $division, "subdivision" => $subdivision])->get();
                        }
                    } else {
                        $subdivisions = GeoLocation::select("subdivision")->where(["continent" => $continent, "country" => $country, "division" => $division])->groupBy("subdivision")->get();
                        if($subdivisions[0]->subdivision == "") {
                            $subdivisions = "";
                            if($city) {
                                $cities = [["selected"=>$city]];
                                $geolocation_id = GeoLocation::where(["continent" => $continent, "country" => $country, "division" => $division, "subdivision" => "", "city" => $city])->first();
                                if($geolocation_id)
                                Cookie::queue('geolocation_id', $geolocation_id->id, 0);
                            } else {
                                $cities = GeoLocation::select("city")->where(["continent" => $continent, "country" => $country, "division" => $division, "subdivision" => ""])->get();
                            }
                        }
                    }
                } else {
                    $divisions = GeoLocation::select("division")->where(["continent" => $continent, "country" => $country])->groupBy("division")->get();
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

        $response["current"] = (($geolocation_id)?"<ol class='breadcrumb bg-warning'>":'').
        (($geolocation_id && $geolocation_id->country)?"<li class='breadcrumb-item'><i class='fa fa-map-marker'></i> ".$geolocation_id->country:'</li>').
        (($geolocation_id && $geolocation_id->division)?"<li class='breadcrumb-item'>".$geolocation_id->division."</li>":'').
        (($geolocation_id && $geolocation_id->subdivision)?"<li class='breadcrumb-item'>".$geolocation_id->subdivision."</li>":'').
        (($geolocation_id && $geolocation_id->city)?"<li class='breadcrumb-item'>".$geolocation_id->city."</li>":'').
        (($geolocation_id)?"</ol>":'');
        return response()->json($response);
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
            if($facility->content_id)
            $facility->getcontent->delete();
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
            if($marker->content_id)
            $marker->getcontent->delete();
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
            } elseif($operation == "geography") {
                $this->response["locations"] = Location::where("geolocation_id", $id)->get();
            }
        } else {
            $this->response["locations"] = Location::all();
        }
        return view('DataEntry.locations', $this->response);
    }
    public function locations_add(Request $request) {
        if(Cookie::get('geolocation_id') == null) {
            return redirect()->route("Geolocation");
        }
        $request->validate([
            "latitude" => "nullable|regex:/^-?[0-9]{1,3}(?:\.[0-9]{1,8})?$/",
            "longitude" => "nullable|regex:/^-?[0-9]{1,3}(?:\.[0-9]{1,8})?$/",
            "title" => ['required',new alpha_dash_space,'max:100'],
            "type" => "required|in:landmark,attraction",
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
            return redirect()->route("Geolocation");
        }
        $request->validate([
            "id" => "required|exists:locations",
            "latitude" => "nullable|regex:/^-?[0-9]{1,3}(?:\.[0-9]{1,8})?$/",
            "longitude" => "nullable|regex:/^-?[0-9]{1,3}(?:\.[0-9]{1,8})?$/",
            "title" => ['required',new alpha_dash_space,'max:100'],
            "type" => "required|in:landmark,attraction",
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
            if($location->content_id)
            $location->getcontent->delete();
            $location->delete();
        }
        return redirect()->back()->with("message", "Location Deleted Successfully!");
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
            "src" => asset('storage/'.$image->file_name)
        ];
        return response()->json($response);
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
            $this->response["operation"] = $operation;
        } else {
            $this->response["packages"] = Package::all();
        }
        return view('DataEntry.Package.index', $this->response);
    }
    public function package_add(Request $request) {
        $request->validate([
            "title" => ['required',new alpha_dash_space,'max:250'],
            "package_content" => "nullable|string|max:500",
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
        return redirect()->back()->with("message", "Package '" .$request->title. "' Added Successfully!");
    }
    public function package_edit(Request $request) {
        $request->validate([
            "id" => "required|exists:packages",
            "title" => ['required',new alpha_dash_space,'max:250'],
            "package_content" => "nullable|string|max:500",
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
        return redirect()->back()->with("message", "Added to " .$package->title. " Successfully!");

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
        }
        $content = Content::find($itinerary->content_id);
        $content->content_type = $request->content_type;
        $content->content = $request->content;
        $content->user_id = Auth::id();
        $content->save();
        $itinerary->package_detail_id = $request->package_detail_id;
        $itinerary->title = $request->title;
        $itinerary->geolocation_id = $geolocation_id;
        $itinerary->hotel_id = $hotel_id;
        $itinerary->save();
        $this->image_management($request->content, "package", $request->package_id);
        return redirect()->route('DataEntry.Package.Detail', ['package_id' =>$request->package_id, "operation" => "show", "id" => $request->package_detail_id])->with("message", "Itinerary '" .$request->title. "' Edited Successfully!");
    }
    public function package_itineraries_delete($id) {
        $itinerary = PackageItinerary::find($id);
        if($itinerary) {
            if($itinerary->content_id)
            $itinerary->getcontent->delete();
            $itinerary->delete();
        }
        return redirect()->back()->with("message", "Itinerary Deleted Successfully!");
    }
}
