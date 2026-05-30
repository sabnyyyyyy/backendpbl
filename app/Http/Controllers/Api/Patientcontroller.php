<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Appointment;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    // GET /api/patients
    // Returns all patients with their appointment stats
    public function index()
    {
        $patients = Patient::withCount('appointments')
            ->with(['appointments' => function ($q) {
                $q->latest('appointment_date')->limit(1);
            }, 'appointments.dentist'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($p) {
                $lastAppt = $p->appointments->first();

                return [
                    'id'         => $p->id,
                    'name'       => $p->full_name,
                    'email'      => $p->email,
                    'phone'      => $p->phone ?? '-',
                    'dob'        => $p->dob ?? null,
                    'gender'     => $p->gender ?? null,
                    'last_visit' => $lastAppt?->appointment_date ?? null,
                    'dentist'    => $lastAppt?->dentist?->name ?? '-',
                    'visits'     => $p->appointments_count,
                    'status'     => $p->appointments_count > 0 ? 'active' : 'inactive',
                ];
            });

        return response()->json($patients);
    }

    // GET /api/patients/{id}
    public function show($id)
    {
        $patient = Patient::with(['appointments.dentist'])
            ->findOrFail($id);

        return response()->json([
            'id'           => $patient->id,
            'name'         => $patient->full_name,
            'email'        => $patient->email,
            'phone'        => $patient->phone,
            'dob'          => $patient->dob,
            'gender'       => $patient->gender,
            'appointments' => $patient->appointments->map(fn($a) => [
                'id'     => $a->id,
                'date'   => $a->appointment_date,
                'time'   => $a->appointment_time,
                'status' => $a->status,
                'dentist'=> $a->dentist?->name,
                'notes'  => $a->notes,
            ]),
        ]);
    }

    // POST /api/patients
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string',
            'email'     => 'required|email|unique:patients,email',
            'phone'     => 'nullable|string',
            'dob'       => 'nullable|date',
            'gender'    => 'nullable|in:Male,Female',
        ]);

        $patient = Patient::create($request->only(['full_name', 'email', 'phone', 'dob', 'gender']));

        return response()->json([
            'message' => 'Patient created successfully',
            'data'    => $patient
        ], 201);
    }

    // PUT /api/patients/{id}
    public function update(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);

        $request->validate([
            'full_name' => 'sometimes|required|string',
            'email'     => 'sometimes|required|email|unique:patients,email,' . $id,
            'phone'     => 'sometimes|nullable|string',
            'dob'       => 'sometimes|nullable|date',
            'gender'    => 'sometimes|nullable|in:Male,Female',
        ]);

        $patient->update($request->only(['full_name', 'email', 'phone', 'dob', 'gender']));

        return response()->json([
            'message' => 'Patient updated successfully',
            'data'    => $patient
        ]);
    }

    // DELETE /api/patients/{id}
    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);

        // Also remove associated appointments
        $patient->appointments()->delete();
        $patient->delete();

        return response()->json([
            'message' => 'Patient deleted successfully'
        ]);
    }
}