<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    protected function wantsJson(Request $r): bool
    {
        return $r->wantsJson() || $r->is('api/*');
    }

    /**
     * List locations.
     */
    public function index(Request $request)
    {
        $locations = Location::orderBy('name')->paginate(50);
        if ($this->wantsJson($request)) return $locations;

        return view('dashboard.locations', [
            'title'     => 'Địa điểm',
            'locations' => $locations,
        ]);
    }

    /**
     * Form create location.
     */
    public function create()
    {
        return view('dashboard.locations.form', [
            'title' => 'Thêm địa điểm',
        ]);
    }

    /**
     * Store new location.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255|unique:locations,name',
            'address' => 'nullable|string|max:255',
            'link'    => 'nullable|string|max:500',
        ]);
        $loc = Location::create($data);

        if ($this->wantsJson($request)) return response()->json(['message'=>'Location created','location'=>$loc],201);
        return redirect()->route('locations.index')->with('success','Địa điểm đã được thêm.');
    }

    /**
     * Show location (redirect to edit).
     */
    public function show(Request $request, Location $location)
    {
        if ($this->wantsJson($request)) return $location;
        return redirect()->route('locations.edit',$location);
    }

    /**
     * Form edit location.
     */
    public function edit(Location $location)
    {
        return view('dashboard.locations.form', [
            'title'    => 'Chỉnh sửa địa điểm',
            'location' => $location,
        ]);
    }

    /**
     * Update location.
     */
    public function update(Request $request, Location $location)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255|unique:locations,name,' . $location->id,
            'address' => 'nullable|string|max:255',
            'link'    => 'nullable|string|max:500',
        ]);
        $location->update($data);

        if ($this->wantsJson($request)) return response()->json(['message'=>'Location updated','location'=>$location]);
        return redirect()->route('locations.index')->with('success','Cập nhật địa điểm thành công.');
    }

    /**
     * Delete location.
     */
    public function destroy(Request $request, Location $location)
    {
        $location->delete();
        if ($this->wantsJson($request)) return response()->json(['message'=>'Location deleted']);
        return redirect()->route('locations.index')->with('success','Đã xóa địa điểm.');
    }
}