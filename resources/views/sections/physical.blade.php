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
			<span class="mr-2">🏋️‍♂️</span> Фізичні характеристики
		</h2>

		<form method="POST" action="{{ route('member.store.section', ['code' => $code]) }}"
				enctype="multipart/form-data">
			@csrf

			<!-- Зріст -->
			<div class="mb-4">
				<label class="block font-medium text-gray-700 mb-1">Зріст (см)</label>
				<input type="number" name="height_cm" value="{{ old('height_cm', $member?->height_cm) }}"
						class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                              @error('height_cm') border-red-500 @enderror">
				@error('height_cm')
				<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
				@enderror
			</div>

			<!-- Тип тіла -->
			<div class="mb-4">
				<label class="block font-medium text-gray-700 mb-1">Тип тіла</label>
				<select name="body_type" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                                              @error('body_type') border-red-500 @enderror">
					@foreach(['thin' => 'Худий', 'medium' => 'Середній', 'plump' => 'Повненький'] as $key => $value)
						<option value="{{ $key }}"
								{{ old('body_type', $member?->body_type) === $key ? 'selected' : '' }}>
							{{ $value }}
						</option>
					@endforeach
				</select>
				@error('body_type')
				<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
				@enderror
			</div>

			<!-- Займається спортом -->
			<div class="mb-4">
				<label class="inline-flex items-center cursor-pointer">
					<input type="checkbox" name="does_sport" value="1"
							{{ old('does_sport', $member?->does_sport) ? 'checked' : '' }}
							class="mr-2 h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500
                                  @error('does_sport') border-red-500 @enderror">
					<span class="text-gray-700 font-medium">Займається спортом?</span>
				</label>
				@error('does_sport')
				<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
				@enderror
			</div>

			<!-- Вид спорту -->
			<div class="mb-4">
				<label class="block font-medium text-gray-700 mb-1">Вид спорту</label>
				<select name="sport_type" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                                              @error('sport_type') border-red-500 @enderror">
					<option value="">Не вказано</option>
					@foreach(['football' => 'Футбол', 'volleyball' => 'Волейбол', 'tennis' => 'Теніс',
							 'wrestling' => 'Боротьба', 'workout' => 'Воркаут', 'other' => 'Інше'] as $key => $value)
						<option value="{{ $key }}"
								{{ old('sport_type', $member?->sport_type) === $key ? 'selected' : '' }}>
							{{ $value }}
						</option>
					@endforeach
				</select>
				@error('sport_type')
				<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
				@enderror
			</div>

			<!-- Рівень спритності -->
			<div class="mb-4">
				<label class="block font-medium text-gray-700 mb-1">Рівень спритності</label>
				<select name="agility_level" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                                                  @error('agility_level') border-red-500 @enderror">
					@for($i = 1; $i <= 3; $i++)
						<option value="{{ $i }}"
								{{ old('agility_level', $member?->agility_level) == $i ? 'selected' : '' }}>
							{{ $i }}
						</option>
					@endfor
				</select>
				@error('agility_level')
				<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
				@enderror
			</div>

			<!-- Рівень сили -->
			<div class="mb-4">
				<label class="block font-medium text-gray-700 mb-1">Рівень сили</label>
				<select name="strength_level" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                                                   @error('strength_level') border-red-500 @enderror">
					@for($i = 1; $i <= 3; $i++)
						<option value="{{ $i }}"
								{{ old('strength_level', $member?->strength_level) == $i ? 'selected' : '' }}>
							{{ $i }}
						</option>
					@endfor
				</select>
				@error('strength_level')
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