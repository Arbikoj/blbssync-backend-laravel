<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use App\Http\Resources\DataResource;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groups = Group::with('major')->get();
        return new DataResource(true, 'List of Groups', $groups);
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
            'name' => 'required|string|max:255',
            'grade' => 'required|string|max:50',
            'major_id' => 'required|exists:majors,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 422);
        }

        $group = Group::create([
            'name' => $request->name,
            'grade' => $request->grade,
            'major_id' => $request->major_id,
        ]);

        return new DataResource(true, 'Group Created Successfully', $group);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $group = Group::with('major')->find($id);

        if (!$group) {
            return response()->json(['success' => false, 'message' => 'Group not found'], 404);
        }

        return new DataResource(true, 'Group Found', $group);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Group $group)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $group = Group::find($id);

        if (!$group) {
            return response()->json(['success' => false, 'message' => 'Group not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'grade' => 'required|string|max:50',
            'major_id' => 'required|exists:majors,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 422);
        }

        $group->name = $request->name;
        $group->grade = $request->grade;
        $group->major_id = $request->major_id;
        $group->save();

        return new DataResource(true, 'Group Updated Successfully', $group);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $group = Group::find($id);

        if (!$group) {
            return response()->json(['success' => false, 'message' => 'Group not found'], 404);
        }

        $group->delete();

        return new DataResource(true, 'Group Deleted Successfully', null);
    }
}
