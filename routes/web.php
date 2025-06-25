<?php

use App\Http\Controllers\SquadAnalyticsController;
use App\Http\Controllers\SquadController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('qr.scan'));

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
	Route::get('/qr-scan', fn () => view('pages.qr-scanner'))->name('qr.scan');
	Route::get('/member/{code}', [MemberController::class, 'show'])->name('member.edit');
	Route::get('/members', [MemberController::class, 'index'])->name('members.index');
	Route::post('/member/{code}', [MemberController::class, 'store'])->name('member.store');

	Route::get('/squads', [SquadController::class, 'index'])->name('squads.index');
	Route::post('/squads', [SquadController::class, 'store'])->name('squads.store');

	Route::get('/squads/analytics', [SquadAnalyticsController::class, 'index'])->name('squads.analytics');
});
