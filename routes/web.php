<?php
Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware('auth', 'access:Template')->prefix('component')->group(function () {
    Route::get('/create','ComponentController@create')->name("Component.Create");
    Route::get('/edit/{name}','ComponentController@edit')->name("Component.Edit");
    Route::get('/add_basic','ComponentController@addBasic')->name("Component.AddBasic");
    Route::post('/add','ComponentController@add')->name("Component.Add");
    Route::get('/component/load/{name?}', 'ComponentController@loadComponent')->name("LoadComponent");
    Route::get('/components/load', 'ComponentController@loadComponents')->name("LoadComponents");
    Route::post('/component/save', 'ComponentController@saveComponent')->name("Component.Save");
    Route::post('/component/edit', 'ComponentController@editComponent')->name("Component.Update");
});

Route::middleware('auth', 'access:Data')->prefix('data')->group(function () {
    Route::get('/', 'DataEntryController@index')->name('DataEntry.Home');
    
    Route::post('/image/get', 'DataEntryController@image_get')->name('Image.get');
    Route::post('/image/upload', 'DataEntryController@image_upload')->name('Image.upload');
    
    Route::get('/geolocation', 'DataEntryController@geolocation')->name('Geolocation');
    Route::post('/geolocation/get', 'DataEntryController@geolocation_get')->name('Geolocation.get');
    Route::post('/geolocation/add', 'DataEntryController@geolocation_add')->name('Geolocation.add');

    Route::post('/hotel/get', 'DataEntryController@hotel_get')->name('Hotel.get');

    Route::get('/facilities/{operation?}/{id?}', 'DataEntryController@facilities')->name('DataEntry.Facilities');
    Route::post('/facilities', 'DataEntryController@facilities_add')->name('DataEntry.Facilities.add');
    Route::put('/facilities', 'DataEntryController@facilities_edit')->name('DataEntry.Facilities.edit');
    Route::get('/delete/facility/{id}', 'DataEntryController@facilities_delete')->name('DataEntry.Facilities.delete')->middleware('canDelete');
    
    Route::get('/markers/{operation?}/{id?}', 'DataEntryController@markers')->name('DataEntry.Markers');
    Route::post('/markers', 'DataEntryController@markers_add')->name('DataEntry.Markers.add');
    Route::put('/markers', 'DataEntryController@markers_edit')->name('DataEntry.Markers.edit');
    Route::get('/delete/marker/{id}', 'DataEntryController@markers_delete')->name('DataEntry.Markers.delete')->middleware('canDelete');

    Route::get('/locations/{operation?}/{id?}', 'DataEntryController@locations')->name('DataEntry.Locations');
    Route::post('/locations', 'DataEntryController@locations_add')->name('DataEntry.Locations.add');
    Route::put('/locations', 'DataEntryController@locations_edit')->name('DataEntry.Locations.edit');
    Route::get('/delete/location/{id}', 'DataEntryController@locations_delete')->name('DataEntry.Locations.delete')->middleware('canDelete');

    Route::get('/transports/{operation?}/{id?}', 'DataEntryController@transports')->name('DataEntry.Transports');
    Route::post('/transports', 'DataEntryController@transports_add')->name('DataEntry.Transports.add');
    Route::put('/transports', 'DataEntryController@transports_edit')->name('DataEntry.Transports.edit');
    Route::get('/delete/transport/{id}', 'DataEntryController@transports_delete')->name('DataEntry.Transports.delete')->middleware('canDelete');

    Route::get('/package/itineraries/{package_id}/{package_detail_id}/{operation?}/{id?}', 'DataEntryController@package_itineraries')->name('DataEntry.Package.Itineraries');
    Route::post('/package/itineraries', 'DataEntryController@package_itineraries_add')->name('DataEntry.Package.Itineraries.add');
    Route::put('/package/itineraries', 'DataEntryController@package_itineraries_edit')->name('DataEntry.Package.Itineraries.edit');
    Route::get('/delete/package/itineraries/{id}', 'DataEntryController@package_itineraries_delete')->name('DataEntry.Package.Itineraries.delete')->middleware('canDelete');

    Route::get('/package/price/{package_id}/{package_detail_id}/{operation?}/{id?}', 'DataEntryController@package_price')->name('DataEntry.Package.Price');
    Route::post('/package/price', 'DataEntryController@package_price_add')->name('DataEntry.Package.Price.add');
    Route::put('/package/price', 'DataEntryController@package_price_edit')->name('DataEntry.Package.Price.edit');
    Route::get('/delete/package/price/{id}', 'DataEntryController@package_price_delete')->name('DataEntry.Package.Price.delete')->middleware('canDelete');

    Route::get('/package/marker/{package_id}/{package_detail_id}/{operation?}/{id?}', 'DataEntryController@package_marker')->name('DataEntry.Package.Marker');
    Route::post('/package/marker', 'DataEntryController@package_markers')->name('DataEntry.Package.Marker.options');

    Route::get('/package/detail/{package_id}/{operation?}/{id?}/{tab?}', 'DataEntryController@package_detail')->name('DataEntry.Package.Detail');
    Route::post('/package/detail', 'DataEntryController@package_detail_add')->name('DataEntry.Package.Detail.add');
    Route::put('/package/detail', 'DataEntryController@package_detail_edit')->name('DataEntry.Package.Detail.edit');
    Route::get('/delete/package/detail/{id}', 'DataEntryController@package_detail_delete')->name('DataEntry.Package.Detail.delete')->middleware('canDelete');

    Route::get('/package/{operation?}/{id?}', 'DataEntryController@package')->name('DataEntry.Package');
    Route::post('/package', 'DataEntryController@package_add')->name('DataEntry.Package.add');
    Route::put('/package', 'DataEntryController@package_edit')->name('DataEntry.Package.edit');
    Route::get('/delete/package/{id}', 'DataEntryController@package_delete')->name('DataEntry.Package.delete')->middleware('canDelete');
    
    Route::get('/hotel/contact/{hotel_id}/{operation?}/{id?}', 'DataEntryController@hotel_contact')->name('DataEntry.Hotel.Contact');
    Route::post('/hotel/contact', 'DataEntryController@hotel_contact_add')->name('DataEntry.Hotel.Contact.add');
    Route::put('/hotel/contact', 'DataEntryController@hotel_contact_edit')->name('DataEntry.Hotel.Contact.edit');
    Route::get('/delete/hotel/contact/{id}', 'DataEntryController@hotel_contact_delete')->name('DataEntry.Hotel.Contact.delete')->middleware('canDelete');
    
    Route::get('/hotel/facility/{hotel_id}/{operation?}/{hotel_room_id?}', 'DataEntryController@hotel_facility')->name('DataEntry.Hotel.Facility');
    Route::post('/hotel/facility', 'DataEntryController@hotel_facilities')->name('DataEntry.Hotel.Facility.option');
    
    Route::get('/hotel/marker/{hotel_id}', 'DataEntryController@hotel_marker')->name('DataEntry.Hotel.Marker');
    Route::post('/hotel/marker', 'DataEntryController@hotel_markers')->name('DataEntry.Hotel.Markers');
    
    Route::get('/hotel/room/{hotel_id}/{operation?}/{id?}', 'DataEntryController@hotel_room')->name('DataEntry.Hotel.Room');
    Route::post('/hotel/room', 'DataEntryController@hotel_room_add')->name('DataEntry.Hotel.Room.add');
    Route::put('/hotel/room', 'DataEntryController@hotel_room_edit')->name('DataEntry.Hotel.Room.edit');
    Route::get('/delete/hotel/room/{id}', 'DataEntryController@hotel_room_delete')->name('DataEntry.Hotel.Room.delete')->middleware('canDelete');
    
    Route::get('/hotel/{operation?}/{id?}/{tab?}', 'DataEntryController@hotel')->name('DataEntry.Hotel');
    Route::post('/hotel', 'DataEntryController@hotel_add')->name('DataEntry.Hotel.add');
    Route::put('/hotel', 'DataEntryController@hotel_edit')->name('DataEntry.Hotel.edit');
    Route::get('/delete/hotel/{id}', 'DataEntryController@hotel_delete')->name('DataEntry.Hotel.delete')->middleware('canDelete');
    
});

Route::get('/home', 'HomeController@index')->name('home');