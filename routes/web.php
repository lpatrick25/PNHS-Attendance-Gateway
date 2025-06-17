<?php

use App\Http\Controllers\LoginLogController;
use App\Http\Controllers\LogoutLogController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/login-logs', [LoginLogController::class, 'store']);
Route::post('/logout-logs', [LogoutLogController::class, 'store']);
Route::get('/getStudentByRFIDForLogin/{rfid_no}', [LoginLogController::class, 'getStudentByRFID']);
Route::get('/getStudentByRFIDForLogout/{rfid_no}', [LogoutLogController::class, 'getStudentByRFID']);
Route::get('/recent-logs', [LoginLogController::class, 'recentLogs'])->name('recent-logs');
