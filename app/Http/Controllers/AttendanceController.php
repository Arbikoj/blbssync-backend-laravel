<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Teacher;
use App\Models\RfidCard;
use App\Models\Device;
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
            'status'        => 'required|string|in:sakit,izin,hadir,alpa,terlambat',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 422);
        }

        $attendance = Attendance::create([
            'schedule_id' => $request->schedule_id,
            'teacher_id'  => $request->teacher_id,
            'user_type'   => $request->user_type,
            'status'      => $request->status,
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

    public function devices(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string|exists:devices,device_id',
            'uid'       => 'required|string|exists:rfid_cards,uid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ], 422);
        }

        $rfidCard = RfidCard::with('teacher')->where('uid', $request->uid)->first();
        $devices = Device::where('device_id', $request->device_id)->first();

        if (!$rfidCard || !$rfidCard->teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu RFID belum terhubung dengan guru.'
            ], 422);
        }

        if (!$devices) {
            return response()->json([
                'success' => false,
                'message' => 'Device tidak ditemukan.'
            ], 422);
        }

        $teacherId = $rfidCard->teacher_id;

        $hariIni = \Carbon\Carbon::now()->locale('id')->dayName;
        $now     = \Carbon\Carbon::now()->format('H:i:s');

        $schedule = \App\Models\Schedule::with('lesson')
            ->where('day', $hariIni)
            ->where('teacher_id', $teacherId)
            ->whereHas('lesson', function ($q) use ($now) {
                $q->where('start_hour', '<=', $now)
                ->where('end_hour', '>=', $now);
            })
            ->first();

        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada jadwal guru yang aktif pada jam ini.'
            ], 422);
        }

        $timeNow = now('Asia/Jakarta');

        $end = $schedule->lesson->end_hour;
        $endHour = \Carbon\Carbon::createFromFormat('H:i', $end, 'Asia/Jakarta');

        $endMinus = $endHour->copy()->subMinutes(10);

        // Cek apakah guru sudah ada absensi hari ini untuk jadwal ini
        $attendance = Attendance::where('schedule_id', $schedule->id)
            ->where('teacher_id', $teacherId)
            ->whereDate('check_in', now()->toDateString())
            ->first();

        if ($attendance) {
            if (is_null($attendance->check_out)) {
                // Hanya bisa checkout kalau sudah masuk 15 menit terakhir
                if ($timeNow->gte($endMinus)) {
                    $attendance->check_out = $timeNow;
                    $attendance->save();

                    return new DataResource(true, 'Check-out berhasil', $attendance);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Belum bisa check-out, tunggu sampai 10 menit terakhir sebelum jam selesai.'
                    ], 422);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Guru sudah menyelesaikan absensi hari ini untuk jadwal ini.'
                ], 422);
            }
        }

        $start = $schedule->lesson->start_hour;
        $startHour = \Carbon\Carbon::createFromFormat('H:i', $start, 'Asia/Jakarta');

        $startPlus = $startHour->copy()->addMinutes(10);

        $status = 'hadir';
        if ($timeNow->gt($startPlus)) {
            $status = 'terlambat';
        }

        // Jika belum ada absensi → buat baru (check-in)
        $attendance = Attendance::create([
            'schedule_id' => $schedule->id,
            'teacher_id'  => $teacherId,
            'user_type'   => "teacher",
            'check_in'    => now(),
            'status'      => $status,
        ]);

        return new DataResource(true, 'Check-in berhasil', $attendance);
    }
}
