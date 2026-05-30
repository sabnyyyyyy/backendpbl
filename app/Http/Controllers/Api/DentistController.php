<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dentist;
use Illuminate\Http\Request;

class DentistController extends Controller
{
    // GET /api/dentists
    public function index()
    {
        return response()->json(
            Dentist::with('schedules')->get()
        );
    }

    // POST /api/dentists
    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string',
            'email'          => 'nullable|email|unique:dentists,email',
            'specialization' => 'required|string',
            'phone'          => 'nullable|string',
        ]);

        $dentist = Dentist::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'specialization' => $request->specialization,
            'phone'          => $request->phone,
        ]);

        return response()->json([
            'message' => 'Dentist created successfully',
            'data'    => $dentist
        ], 201);
    }

    // PUT /api/dentists/{id}
    public function update(Request $request, $id)
    {
        $dentist = Dentist::findOrFail($id);

        $request->validate([
            'name'           => 'sometimes|required|string',
            'email'          => 'sometimes|nullable|email|unique:dentists,email,' . $id,
            'specialization' => 'sometimes|required|string',
            'phone'          => 'sometimes|nullable|string',
        ]);

        $dentist->update($request->only(['name', 'email', 'specialization', 'phone']));

        return response()->json([
            'message' => 'Dentist updated successfully',
            'data'    => $dentist
        ]);
    }

    // DELETE /api/dentists/{id}
    public function destroy($id)
    {
        $dentist = Dentist::findOrFail($id);
        $dentist->delete();

        return response()->json([
            'message' => 'Dentist deleted successfully'
        ]);
    }
}