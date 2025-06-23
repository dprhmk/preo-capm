@extends('layouts.app')

@section('content')
	<div class="max-w-2xl mx-auto mt-16 p-6 bg-white rounded-lg shadow-lg">
		<!-- Заголовок із ім’ям -->
		<div class="mb-8 text-center">
			<h1 class="text-3xl font-extrabold text-blue-600 drop-shadow-md">
				{{ $member->full_name ?? 'Учасник' }}
			</h1>
			<div class="mt-4">
				@if ($member?->photo_url)
					<img src="{{ $member->photo_url }}"
							alt="Фото {{ $member->full_name ?? 'Учасника' }}"
							class="w-32 h-32 object-cover mx-auto">
				@else
					<div class="w-32 h-32 bg-gray-200 flex items-center justify-center text-gray-500 text-base font-semibold mx-auto">
						Без фото
					</div>
				@endif
			</div>
		</div>

		<h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center">
			<span class="mr-2">🧩</span> Інтелектуальні здібності
		</h2>

		<form method="POST" action="{{ route('member.store.section', ['code' => $code]) }}"
				enctype="multipart/form-data">
			@csrf
			<input type="hidden" name="section" value="intellectual">

			<!-- Рівень англійської -->
			<div class="mb-4">
				<label class="block font-medium text-gray-700 mb-1">Рівень англійської</label>
				<select name="english_level" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                                                @error('english_level') border-red-500 @enderror">
					<option value="">Не вказано</option>
					@for ($i = 1; $i <= 3; $i++)
						<option value="{{ $i }}"
								{{ old('english_level', $member?->english_level) == $i ? 'selected' : '' }}>
							{{ $i }}
						</option>
					@endfor
				</select>
				@error('english_level')
				<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
				@enderror
			</div>

			<!-- Загальний рівень IQ -->
			<div class="mb-4">
				<label class="block font-medium text-gray-700 mb-1">Загальний рівень IQ</label>
				<select name="general_iq_level" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                                                @error('general_iq_level') border-red-500 @enderror">
					<option value="">Не вказано</option>
					@for ($i = 1; $i <= 3; $i++)
						<option value="{{ $i }}"
								{{ old('general_iq_level', $member?->general_iq_level) == $i ? 'selected' : '' }}>
							{{ $i }}
						</option>
					@endfor
				</select>
				@error('general_iq_level')
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

@section('scripts')
	<script>
		// Скрипти поки не потрібні
	</script>
@endsection