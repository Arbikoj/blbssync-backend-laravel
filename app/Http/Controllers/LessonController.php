<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\Request;
use App\Http\Resources\DataResource;
use Illuminate\Support\Facades\Validator;

class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $sortBy = $request->get('sort_by', 'state');
        $sortDir = $request->get('sort_dir', 'asc');
        $search = $request->get('search');

        $query = Lesson::query();

        if ($search) {
            $query->where('state', 'like', '%' . $search . '%');
        }

        $lesson = $query->orderBy($sortBy, $sortDir)->paginate($perPage);

        return new DataResource(true, 'List Data Lessons', $lesson);
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
            'state' => 'required|integer',
            'start_hour' => [
                'required',
                'regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/',
            ],
            'end_hour' => [
                'required',
                'regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/',
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 422);
        }

        $lesson = Lesson::create($validator->validated());

        return new DataResource(true, 'Lesson Created Successfully', $lesson);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $lesson = Lesson::find($id);

        if (!$lesson) {
            return response()->json(['success' => false, 'message' => 'Lesson not found'], 404);
        }

        return new DataResource(true, 'Lesson Found', $lesson);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lesson $lesson)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $lesson = Lesson::find($id);

        if (!$lesson) {
            return response()->json(['success' => false, 'message' => 'Lesson not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'state' => 'sometimes|required|integer',
            'start_hour' => [
                'sometimes',
                'required',
                'regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/',
            ],
            'end_hour' => [
                'sometimes',
                'required',
                'regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/',
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 422);
        }

        $lesson->update($validator->validated());

        return new DataResource(true, 'Lesson Updated Successfully', $lesson);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $lesson = Lesson::find($id);

        if (!$lesson) {
            return response()->json(['success' => false, 'message' => 'Lesson not found'], 404);
        }

        $lesson->delete();

        return new DataResource(true, 'Lesson Deleted Successfully', null);
    }
}
