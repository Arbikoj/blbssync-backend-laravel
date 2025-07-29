<?php

namespace App\Http\Controllers;

use App\Models\Major;
use Illuminate\Http\Request;
use App\Http\Resources\DataResource;
use Illuminate\Support\Facades\Validator;

class MajorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $sortBy = $request->get('sort_by', 'name');
        $sortDir = $request->get('sort_dir', 'asc');
        $search = $request->get('search');

        $query = Major::query();

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $majors = $query->orderBy($sortBy, $sortDir)->paginate($perPage);

        return new DataResource(true, 'List of Majors', $majors);
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
            'name' => 'required|string|max:255|unique:majors,name',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 422);
        }

        $major = Major::create([
            'name' => $request->name,
        ]);

        return new DataResource(true, 'Major Created Successfully', $major);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $major = Major::find($id);

        if (!$major) {
            return response()->json(['success' => false, 'message' => 'Major not found'], 404);
        }

        return new DataResource(true, 'Major Found', $major);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Major $major)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $major = Major::find($id);

        if (!$major) {
            return response()->json(['success' => false, 'message' => 'Major not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:majors,name,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 422);
        }

        $major->name = $request->name;
        $major->save();

        return new DataResource(true, 'Major Updated Successfully', $major);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $major = Major::find($id);

        if (!$major) {
            return response()->json(['success' => false, 'message' => 'Major not found'], 404);
        }

        $major->delete();

        return new DataResource(true, 'Major Deleted Successfully', null);
    }
}
