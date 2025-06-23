@extends('layouts.app')

@section('content')
	<div class="max-w-xl mx-auto p-4">
		<h1 class="text-2xl font-bold mb-4">Основна Інформація</h1>

		<form method="POST" action="{{ route('member.store.section', ['code' => $code]) }}">
			@csrf

			<div class="mb-4">
				<label class="block font-medium text-gray-700 mb-1">Повне ім’я</label>
				<input type="text" name="full_name" value="{{ old('full_name', $member?->full_name) }}"
						class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
	                              @error('full_name') border-red-500 @enderror">
				@error('full_name')
				<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
				@enderror
			</div>
{{--			<div class="mb-4">--}}
{{--				<label class="block font-medium">ПІБ</label>--}}
{{--				<input type="text" name="full_name" value="{{ old('full_name') ?? $member?->full_name }}"--}}
{{--						class="w-full border rounded px-3 py-2 @error('full_name') border-red-500 @enderror" required>--}}
{{--				@error('full_name')--}}
{{--				<p class="text-red-500 text-sm mt-1">{{ $message }}</p>--}}
{{--				@enderror--}}
{{--			</div>--}}

			<div class="mb-4">
				<label class="block font-medium">Дата народження</label>
				<input type="date" name="birth_date"
						value="{{ old('birth_date') ?? $member?->birth_date?->format('Y-m-d') }}"
						class="w-full border rounded px-3 py-2 @error('birth_date') border-red-500 @enderror" required>
				@error('birth_date')
				<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
				@enderror
			</div>

			<div class="mb-4">
				<label class="block font-medium">Вік</label>
				<input type="number" name="age" inputmode="numeric" value="{{ old('age') ?? $member?->age }}"
						class="w-full border rounded px-3 py-2 @error('age') border-red-500 @enderror" required>
				@error('age')
				<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
				@enderror
			</div>

			<div class="mb-4">
				<label class="block font-medium">Стать</label>
				<select name="gender" class="w-full border rounded px-3 py-2 @error('gender') border-red-500 @enderror" required>
					<option value="male" {{ (old('gender') ?? $member?->gender) == 'male' ? 'selected' : '' }}>Чоловіча</option>
					<option value="female" {{ (old('gender') ?? $member?->gender) == 'female' ? 'selected' : '' }}>Жіноча</option>
				</select>
				@error('gender')
				<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
				@enderror
			</div>

			<div class="mb-4">
				<label class="block font-medium">Тип проживання</label>
				<select name="residence_type" class="w-full border rounded px-3 py-2 @error('residence_type') border-red-500 @enderror" required>
					<option value="stationary" {{ (old('residence_type') ?? $member?->residence_type) == 'stationary' ? 'selected' : '' }}>Стаціонар</option>
					<option value="non-stationary" {{ (old('residence_type') ?? $member?->residence_type) == 'non-stationary' ? 'selected' : '' }}>Нестаціонар</option>
				</select>
				@error('residence_type')
				<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
				@enderror
			</div>

			<div class="max-w-xs mx-auto mt-12 mb-12">
				<label class="block relative group">
					<input type="checkbox"
							name="is_bad_boy"
							value="1"
							{{ (old('is_bad_boy') ?? $member?->is_bad_boy) ? 'checked' : '' }}
							class="sr-only peer"
							id="is-bad-boy-checkbox"
					>

					<div
							class="w-full cursor-pointer px-5 py-4 text-lg font-extrabold rounded-xl border-4 border-gray-400 bg-gray-200 text-gray-600
                   select-none text-center uppercase transition
                   peer-checked:bg-red-700 peer-checked:text-white peer-checked:border-red-900 peer-checked:scale-105 peer-checked:shadow-lg
                   group-hover:bg-gray-300 group-hover:text-gray-800"
					>
			<span id="is-bad-boy-label">
				{{ (old('is_bad_boy') ?? $member?->is_bad_boy) ? '🚨 Гавнюк (вже помічено)' : '😡 Відмітити як Гавнюка' }}
			</span>
					</div>

					<span
							class="absolute left-1/2 -bottom-7 -translate-x-1/2 text-xs text-red-600 font-semibold opacity-90
                   group-hover:opacity-100 transition-opacity select-none pointer-events-none whitespace-nowrap"
					>
			⚠️ Тільки якщо все дуже погано!
		</span>
				</label>

				@error('is_bad_boy')
				<p class="text-red-500 text-sm mt-1 font-semibold animate-shake max-w-xs mx-auto">{{ $message }}</p>
				@enderror
			</div>

			<button type="submit" class="block mx-auto mt-12 bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700 text-lg font-semibold">
				Зберегти
			</button>

		</form>
	</div>
@endsection

@section('scripts')
	<script>
		document.addEventListener('DOMContentLoaded', () => {
			const checkbox = document.getElementById('is-bad-boy-checkbox');
			const label = document.getElementById('is-bad-boy-label');
			const isMarkedAsBadBoy = !!"{{ $member?->is_bad_boy }}"

			const updateLabel = () => {
				label.textContent = checkbox.checked ? '🚨 Гавнюк' : '😡 Відмітити як Гавнюка';
			};

			if( ! isMarkedAsBadBoy) {
				updateLabel()
			}

			checkbox?.addEventListener('change', () => {
				updateLabel();

				if (checkbox.checked) {
					if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
					const audio = new Audio('/ta-da.mp3');
					audio.volume = 1;
					audio.play();
				}
			});

		});
	</script>
@endsection