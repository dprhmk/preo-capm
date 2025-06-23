@extends('layouts.app')

@section('content')
	<div class="max-w-md mx-auto mt-10 p-6 bg-white shadow rounded">
		<h1 class="text-xl font-bold mb-4">Вхід до системи</h1>

		@if ($errors->any())
			<div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
				<ul class="list-disc pl-5">
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif

		<form method="POST" action="{{ url('/login') }}">
			@csrf

			<div class="mb-4">
				<label class="block mb-1 font-medium">База:</label>
				<select name="role" class="w-full border rounded px-3 py-2">
					@foreach ($roles as $role)
						<option value="{{ $role }}">{{ ucfirst($role) }}</option>
					@endforeach
				</select>
			</div>

			<div class="mb-4">
				<label class="block mb-1 font-medium">Пароль:</label>
				<input type="password" name="password" class="w-full border rounded px-3 py-2" required>
			</div>

			<button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
				Увійти
			</button>
		</form>
	</div>
@endsection
