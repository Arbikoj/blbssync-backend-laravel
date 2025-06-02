<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Http\Resources\TeacherResource;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teachers = Teacher::all();
        return new TeacherResource(true, 'List Data Teachers', $teachers);
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
            'code' => 'required|integer|max:50|unique:teachers',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 422);
        }

        $teacher = Teacher::create([
            'name' => $request->name,
            'code' => $request->code,
        ]);

        return new TeacherResource(true, 'Teacher Created Successfully', $teacher);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $teacher = Teacher::find($id);

        if (!$teacher) {
            return response()->json(['success' => false, 'message' => 'Teacher not found'], 404);
        }

        return new TeacherResource(true, 'Teacher Found', $teacher);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Teacher $teacher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $teacher = Teacher::find($id);

        if (!$teacher) {
            return response()->json(['success' => false, 'message' => 'Teacher not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'code' => 'sometimes|required|integer|unique:teachers,code,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        if (array_key_exists('name', $validatedData)) {
            $teacher->name = $validatedData['name'];
        }

        if (array_key_exists('code', $validatedData)) {
            $teacher->code = $validatedData['code'];
        }

        $teacher->save();

        return response()->json([
            'success' => true,
            'message' => 'Teacher Updated Successfully',
            'data' => $teacher
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $teacher = Teacher::find($id);

        if (!$teacher) {
            return response()->json(['success' => false, 'message' => 'Teacher not found'], 404);
        }

        $teacher->delete();

        return new TeacherResource(true, 'Teacher Deleted Successfully', null);
    }
}
