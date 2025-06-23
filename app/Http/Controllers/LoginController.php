<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController
{
	public function showLoginForm()
	{
		if (auth()->user()) {
			return redirect()->route('qr.scan');
		}

		$roles = ['main', 'contacts', 'physical', 'medical', 'mental', 'creative', 'intellect', 'admin'];
		return view('auth.login', compact('roles'));
	}

	public function login(Request $request)
	{
		$credentials = $request->validate([
			'role' => 'required|string',
			'password' => 'required|string',
		]);

		$user = User::where('login', $credentials['role'])->first();

		if ($user && Hash::check($credentials['password'], $user->password)) {
			Auth::login($user);
			return redirect()->route('qr.scan');
		}

		return back()->withErrors([
			'password' => 'Невірний пароль.',
		]);
	}

	public function logout()
	{
		Auth::logout();
		return redirect()->route('login');
	}
}
