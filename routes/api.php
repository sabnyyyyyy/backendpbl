<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\DentistController;
use App\Http\Controllers\Api\PatientController;

Route::get('/test', function () {
    return response()->json(['message' => 'API Working']);
});

// ── Dentists ──────────────────────────────────────────────────────────────────
Route::get('/dentists',         [DentistController::class, 'index']);
Route::post('/dentists',        [DentistController::class, 'store']);
Route::put('/dentists/{id}',    [DentistController::class, 'update']);
Route::delete('/dentists/{id}', [DentistController::class, 'destroy']);

// ── Appointments ──────────────────────────────────────────────────────────────
Route::get('/appointments',          [AppointmentController::class, 'index']);
Route::post('/appointments',         [AppointmentController::class, 'store']);
Route::put('/appointments/{id}',     [AppointmentController::class, 'update']);
Route::patch('/appointments/{id}',   [AppointmentController::class, 'update']); // alias for PATCH

// ── Patients ──────────────────────────────────────────────────────────────────
Route::get('/patients',         [PatientController::class, 'index']);
Route::get('/patients/{id}',    [PatientController::class, 'show']);
Route::post('/patients',        [PatientController::class, 'store']);
Route::put('/patients/{id}',    [PatientController::class, 'update']);
Route::delete('/patients/{id}', [PatientController::class, 'destroy']);

Route::post('/login', [AuthController::class, 'login']);