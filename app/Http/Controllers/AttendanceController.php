<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Http\Resources\DataResource;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $sortBy = $request->get('sort_by', 'id');
        $sortDir = $request->get('sort_dir', 'asc');
        $search = $request->get('search');

        $query = Attendance::with([
            'schedule.lesson',
            'schedule.group',
            'schedule.teacher',
            'schedule.subject',
            'teacher'
        ]);

        if ($search) {
            $query->whereHas('teacher', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        $attendances = $query->orderBy($sortBy, $sortDir)->paginate($perPage);

        return new DataResource(true, 'List Data Attendances', $attendances);
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
            'schedule_id'   => 'required|exists:schedules,id',
            'teacher_id'    => 'required|exists:teachers,id',
            'user_type'     => 'required|string|in:teacher,student',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 422);
        }

        $attendance = Attendance::create([
            'schedule_id' => $request->schedule_id,
            'teacher_id'  => $request->teacher_id,
            'user_type'   => $request->user_type,
            'check_in'    => now(),
            'status'      => 'hadir',
        ]);

        return new DataResource(true, 'Attendance Created Successfully', $attendance);
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $attendance = Attendance::with(['schedule', 'teacher'])->find($id);

        if (!$attendance) {
            return response()->json(['success' => false, 'message' => 'Attendance not found'], 404);
        }

        return new DataResource(true, 'Attendance Found', $attendance);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $attendance = Attendance::find($id);

        if (!$attendance) {
            return response()->json(['success' => false, 'message' => 'Attendance not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'schedule_id' => 'required|exists:schedules,id',
            'teacher_id' => 'required|integer',
            'user_type' => 'required|in:teacher,student',
            'check_in' => 'nullable|date',
            'check_out' => 'nullable|date',
            'status' => 'nullable|in:hadir,izin,sakit,alpa',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 422);
        }

        $attendance->update($request->all());

        return new DataResource(true, 'Attendance Updated Successfully', $attendance);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $attendance = Attendance::find($id);

        if (!$attendance) {
            return response()->json(['success' => false, 'message' => 'Attendance not found'], 404);
        }

        $attendance->delete();

        return new DataResource(true, 'Attendance Deleted Successfully', null);
    }
}
