<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
	public function run(): void
	{
		$roles = ['main', 'contacts', 'physical', 'medical', 'mental', 'creative', 'intellect', 'admin'];

		foreach ($roles as $role) {

			$password = match ($role) {
				'admin' => Hash::make('7777'),
				default => Hash::make('1996'),
			};

			User::query()->create(
				[
					'role'     => $role,
					'login'    => $role,
					'password' => $password,
				]
			);
		}
	}
}
