<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use App\Http\Resources\DataResource;
use Illuminate\Support\Facades\Validator;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subjects = Subject::all();
        return new DataResource(true, 'List Data Subjects', $subjects);
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
            'code' => 'required|string|max:50|unique:subjects',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 422);
        }

        $subject = Subject::create([
            'name' => $request->name,
            'code' => $request->code,
        ]);

        return new DataResource(true, 'Teacher Created Successfully', $subject);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $subject = Subject::find($id);

        if (!$subject) {
            return response()->json(['success' => false, 'message' => 'Teacher not found'], 404);
        }

        return new DataResource(true, 'Teacher Found', $subject);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subject $subject)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $subject = Subject::find($id);

        if (!$subject) {
            return response()->json(['success' => false, 'message' => 'Teacher not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'code' => 'sometimes|required|string|unique:subjects,code,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        if (array_key_exists('name', $validatedData)) {
            $subject->name = $validatedData['name'];
        }

        if (array_key_exists('code', $validatedData)) {
            $subject->code = $validatedData['code'];
        }

        $subject->save();

        return response()->json([
            'success' => true,
            'message' => 'Teacher Updated Successfully',
            'data' => $subject
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $subject = Subject::find($id);

        if (!$subject) {
            return response()->json(['success' => false, 'message' => 'Teacher not found'], 404);
        }

        $subject->delete();

        return new DataResource(true, 'Teacher Deleted Successfully', null);
    }
}
