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
			<span class="mr-2">🧠</span> Психолог
		</h2>

		<form method="POST" action="{{ route('member.store.section', ['code' => $code]) }}"
				enctype="multipart/form-data">
			@csrf
			<input type="hidden" name="section" value="mental">

			<!-- Перший раз у таборі -->
			<div class="mb-4">
				<label class="inline-flex items-center cursor-pointer">
					<input type="checkbox" name="first_time" value="1"
							{{ old('first_time', $member?->first_time) ? 'checked' : '' }}
							class="mr-2 h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500
                                  @error('first_time') border-red-500 @enderror">
					<span class="text-gray-700 font-medium">Перший раз у таборі?</span>
				</label>
				@error('first_time')
				<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
				@enderror
			</div>

			<!-- Особлива дитина -->
			<div class="mb-4">
				<label class="inline-flex items-center cursor-pointer">
					<input type="checkbox" name="exceptional" value="1"
							{{ old('exceptional', $member?->exceptional) ? 'checked' : '' }}
							class="mr-2 h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500
                                  @error('exceptional') border-red-500 @enderror">
					<span class="text-gray-700 font-medium">Особлива дитина?</span>
				</label>
				@error('exceptional')
				<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
				@enderror
			</div>

			<!-- Панічні атаки -->
			<div class="mb-4">
				<label class="inline-flex items-center cursor-pointer">
					<input type="checkbox" name="has_panic_attacks" value="1"
							{{ old('has_panic_attacks', $member?->has_panic_attacks) ? 'checked' : '' }}
							class="mr-2 h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500
                                  @error('has_panic_attacks') border-red-500 @enderror">
					<span class="text-gray-700 font-medium">Бувають панічні атаки?</span>
				</label>
				@error('has_panic_attacks')
				<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
				@enderror
			</div>

			<!-- Тип особистості -->
			<div class="mb-4">
				<label class="block font-medium text-gray-700 mb-1">Тип особистості</label>
				<select name="personality_type" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                                                @error('personality_type') border-red-500 @enderror">
					@foreach(['ambivert' => 'Амбіверт', 'extrovert' => 'Екстраверт', 'introvert' => 'Інтроверт'] as $key => $value)
						<option value="{{ $key }}"
								{{ old('personality_type', $member?->personality_type) === $key ? 'selected' : '' }}>
							{{ $value }}
						</option>
					@endforeach
				</select>
				@error('personality_type')
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