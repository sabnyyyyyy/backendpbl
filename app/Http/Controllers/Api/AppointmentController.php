<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Dentist;
use App\Models\Appointment;

class AppointmentController extends Controller
{
    // GET /api/appointments
    public function index()
    {
        $appointments = Appointment::with(['patient', 'dentist'])
            ->orderBy('appointment_date', 'desc')
            ->get()
            ->map(function ($a) {
                return [
                    'id'               => $a->id,
                    'full_name'        => $a->patient->full_name ?? '-',
                    'email'            => $a->patient->email ?? '-',
                    'dentist'          => $a->dentist->name ?? '-',
                    'dentist_id'       => $a->dentist_id,
                    'appointment_date' => $a->appointment_date,
                    'appointment_time' => $a->appointment_time,
                    'status'           => $a->status,
                    'treatment'        => $a->treatment ?? 'Consultation',
                    'notes'            => $a->notes,
                ];
            });

        return response()->json($appointments);
    }

    // POST /api/appointments
    public function store(Request $request)
    {
        $request->validate([
            'full_name'        => 'required',
            'email'            => 'required|email',
            'phone'            => 'nullable',
            'dentist_id'       => 'required',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
        ]);

        $patient = Patient::firstOrCreate(
            ['email' => $request->email],
            [
                'full_name' => $request->full_name,
                'phone'     => $request->phone,
            ]
        );

        $appointment = Appointment::create([
            'patient_id'       => $patient->id,
            'dentist_id'       => $request->dentist_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'status'           => 'pending',
            'treatment'        => $request->treatment ?? 'Consultation',
            'notes'            => $request->notes,
        ]);

        return response()->json([
            'message' => 'Appointment booked successfully',
            'data'    => $appointment
        ]);
    }

    // PUT /api/appointments/{id}  — used by admin to update status
    // PATCH /api/appointments/{id} — alias, same handler
    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);

        $appointment->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Appointment updated successfully',
            'data'    => $appointment->load(['patient', 'dentist']),
        ]);
    }
}