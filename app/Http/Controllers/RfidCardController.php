<?php

namespace App\Http\Controllers;

use App\Models\RfidCard;
use Illuminate\Http\Request;
use App\Http\Resources\DataResource;
use Illuminate\Support\Facades\Validator;

class RfidCardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $search  = $request->get('search');

        $query = RfidCard::query()
            ->with('teacher');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('uid', 'like', "%$search%")
                ->orWhere('bio', 'like', "%$search%")
                ->orWhereHas('teacher', fn($t) => $t->where('name', 'like', "%$search%"));
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
            'uid'        => 'required|string|max:255|unique:rfid_cards,uid',
            'bio'        => 'nullable|string|max:255',
            'teacher_id' => 'nullable|exists:teachers,id|unique:rfid_cards,teacher_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 422);
        }

        $rfid = RfidCard::create([
            'uid'        => $request->uid,
            'bio'        => $request->bio,
            'teacher_id' => $request->teacher_id,
        ]);

        return new DataResource(true, 'RFID Card Created Successfully', $rfid);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $rfid = RfidCard::with('teacher')->find($id);

        if (!$rfid) {
            return response()->json(['success' => false, 'message' => 'RFID Card not found'], 404);
        }

        return new DataResource(true, 'RFID Card Found', $rfid);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RfidCard $rfidCard)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $rfid = RfidCard::find($id);

        if (!$rfid) {
            return response()->json(['success' => false, 'message' => 'RFID Card not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'uid'        => 'required|string|max:255|unique:rfid_cards,uid,' . $id,
            'bio'        => 'nullable|string|max:255',
            'teacher_id' => 'nullable|exists:teachers,id|unique:rfid_cards,teacher_id,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 422);
        }

        $rfid->uid        = $request->uid;
        $rfid->bio        = $request->bio;
        $rfid->teacher_id = $request->teacher_id;
        $rfid->save();

        return new DataResource(true, 'RFID Card Updated Successfully', $rfid);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $rfid = RfidCard::find($id);

        if (!$rfid) {
            return response()->json(['success' => false, 'message' => 'RFID Card not found'], 404);
        }

        $rfid->delete();

        return new DataResource(true, 'RFID Card Deleted Successfully', null);
    }
}
