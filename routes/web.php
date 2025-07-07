<?php

use App\Http\Controllers\ContactsController;
use App\Http\Controllers\SquadController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.timer')->name('home');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/member/{code}', [MemberController::class, 'show'])->name('member.edit');

Route::middleware(['auth'])->group(function () {
	Route::get('/qr-scan', fn () => view('pages.qr-scanner'))->name('qr.scan');
	Route::get('/members', [MemberController::class, 'index'])->name('members.index');
	Route::post('/member/{code}', [MemberController::class, 'store'])->name('member.store');

	Route::get('/contacts', [ContactsController::class, 'index'])->name('contacts.index');

	Route::get('/squads', [SquadController::class, 'index'])->name('squads.index');
	Route::post('/squads', [SquadController::class, 'store'])->name('squads.store');
	Route::get('/squads/{squad}/edit', [SquadController::class, 'edit'])->name('squads.edit');
	Route::put('/squads/{squad}', [SquadController::class, 'update'])->name('squads.update');
	Route::post('/truncate-db', [SquadController::class, 'truncateDb'])->name('truncate-db');
});
