<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use App\Http\Resources\DataResource;
use Illuminate\Support\Facades\Validator;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $search  = $request->get('search');

        $query = Device::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('device_id', 'like', "%$search%")
                ->orWhere('bio', 'like', "%$search%");
            });
        }

        $rfids = $query->paginate($perPage);

        return new DataResource(true, 'List Data RFID Cards', $rfids);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string|max:255|unique:devices,device_id',
            'bio'       => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 422);
        }

        $device = Device::create([
            'device_id' => $request->device_id,
            'bio'       => $request->bio,
            'is_active' => $request->is_active ?? false,
        ]);

        return new DataResource(true, 'Device Created Successfully', $device);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $device = Device::find($id);

        if (!$device) {
            return response()->json(['success' => false, 'message' => 'Device not found'], 404);
        }

        return new DataResource(true, 'Device Found', $device);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Device $device)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $device = Device::find($id);

        if (!$device) {
            return response()->json(['success' => false, 'message' => 'Device not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string|max:255|unique:devices,device_id,' . $id,
            'bio'       => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 422);
        }

        $device->device_id = $request->device_id;
        $device->bio       = $request->bio;
        $device->is_active = $request->is_active ?? $device->is_active;
        $device->save();

        return new DataResource(true, 'Device Updated Successfully', $device);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $device = Device::find($id);

        if (!$device) {
            return response()->json(['success' => false, 'message' => 'Device not found'], 404);
        }

        $device->delete();

        return new DataResource(true, 'Device Deleted Successfully', null);
    }
}
