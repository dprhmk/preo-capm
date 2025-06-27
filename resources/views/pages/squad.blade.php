@extends('layouts.app')

@section('content')
	<div class="max-w-4xl mx-auto mb-16 p-6 backdrop-blur-xl bg-white/70 rounded-lg shadow-lg">
		<div class="mb-8 text-center">
			<h1 class="text-3xl font-extrabold text-blue-600 drop-shadow-md">
				Редагувати загін: {{ $squad->name }}
			</h1>
		</div>

		<a href="{{ route('squads.index') }}"
				class="block mx-auto mb-6 bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 text-lg font-semibold transition duration-300 shadow-md hover:shadow-lg">
			Назад до каталогу
		</a>

		@if ($errors->any())
			<div class="alert alert-danger mb-6">
				<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif

		<form method="POST" action="{{ route('squads.update', $squad->id) }}">
			@csrf
			@method('PUT')

			<div class="mb-4">
				<label class="block font-medium text-gray-700 mb-1">Назва загону</label>
				<input type="text" name="name" value="{{ old('name', $squad->name) }}"
						class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                              @error('name') border-red-500 @enderror" required minlength="3" maxlength="50">
				@error('name')
				<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
				@enderror
			</div>

			<div class="mb-4">
				<label class="block font-medium text-gray-700 mb-1">Ім’я лідера</label>
				<input type="text" name="leader_name" value="{{ old('leader_name', $squad->leader_name) }}"
						class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                              @error('leader_name') border-red-500 @enderror" required minlength="2" maxlength="100">
				@error('leader_name')
				<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
				@enderror
			</div>

			<div class="mb-4">
				<label class="block font-medium text-gray-700 mb-1">Ім’я помічника</label>
				<input type="text" name="assistant_name" value="{{ old('assistant_name', $squad->assistant_name) }}"
						class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                              @error('assistant_name') border-red-500 @enderror" maxlength="100">
				@error('assistant_name')
				<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
				@enderror
			</div>

			<div class="mb-4">
				<label class="block font-medium text-gray-700 mb-1">Колір загону</label>
				<div class="flex items-center space-x-2">
					<input type="color" name="color" id="color" value="{{ old('color', $squad->color ?? '#000000') }}"
							class="w-16 h-12 border-2 border-gray-300 rounded shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500
                                  @error('color') border-red-500 @enderror"
							{{ old('color', $squad->color) == null ? 'disabled' : '' }}>
				</div>
				@error('color')
				<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
				@enderror
			</div>

			<button type="submit"
					class="block mx-auto mt-12 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 text-lg font-semibold
                           transition duration-300 shadow-md hover:shadow-lg">
				Зберегти
			</button>
		</form>
	</div>
@endsection

@push('scripts')
	<script>

	</script>
@endpush
