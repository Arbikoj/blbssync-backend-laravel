<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Http\Resources\DataResource;
use Illuminate\Support\Facades\Validator;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $search  = $request->get('search');

        $dayOrder = ["Senin","Selasa","Rabu","Kamis","Jumat","Sabtu","Minggu"];

        $query = Schedule::query()
            ->leftJoin('lessons', 'schedules.lesson_id', '=', 'lessons.id')
            ->with(['lesson','group','teacher','subject'])
            ->select('schedules.*');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('schedules.day', 'like', "%$search%")
                ->orWhereHas('teacher', fn($t) => $t->where('name', 'like', "%$search%"))
                ->orWhereHas('group', fn($g) => $g->where('name', 'like', "%$search%"))
                ->orWhereHas('subject', fn($s) => $s->where('name', 'like', "%$search%"));
            });
        }

        // ORDER BY hari sesuai urutan, lalu lesson.state
        $query->orderByRaw("FIELD(schedules.day, '".implode("','", $dayOrder)."')")
            ->orderBy('lessons.state', 'asc');

        $scheduler = $query->paginate($perPage);

        return new DataResource(true, 'List Data Schedule', $scheduler);
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
            'day'        => 'required|string|max:50',
            'lesson_id'  => 'required|exists:lessons,id',
            'group_id'   => 'required|exists:groups,id',
            'teacher_id' => 'required|exists:teachers,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 422);
        }

        $schedule = Schedule::create([
            'day' => $request->day,
            'lesson_id' => $request->lesson_id,
            'group_id' => $request->group_id,
            'teacher_id' => $request->teacher_id,
            'subject_id' => $request->subject_id,
        ]);

        return new DataResource(true, 'Schedule Created Successfully', $schedule);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $schedule = Schedule::with(['lesson', 'group', 'teacher', 'subject'])->find($id);

        if (!$schedule) {
            return response()->json(['success' => false, 'message' => 'Schedule not found'], 404);
        }

        return new DataResource(true, 'Schedule Found', $schedule);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schedule $schedule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $schedule = Schedule::find($id);

        if (!$schedule) {
            return response()->json(['success' => false, 'message' => 'Schedule not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'day'        => 'required|string|max:50',
            'lesson_id'  => 'required|exists:lessons,id',
            'group_id'   => 'required|exists:groups,id',
            'teacher_id' => 'required|exists:teachers,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 422);
        }

        $schedule->update($validator->validated());

        return new DataResource(true, 'Schedule Updated Successfully', $schedule);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $schedule = Schedule::find($id);

        if (!$schedule) {
            return response()->json(['success' => false, 'message' => 'Schedule not found'], 404);
        }

        $schedule->delete();

        return new DataResource(true, 'Schedule Deleted Successfully', null);
    }
}
