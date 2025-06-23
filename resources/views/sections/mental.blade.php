@if(in_array( $role, ['admin', 'mental']))
	<hr class="my-6 mt-16">

	<h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center">
		<span class="mr-2">🧠</span> Психолог
	</h2>

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

	<div class="mb-4">
		<label class="inline-flex items-center cursor-pointer">
			<input type="checkbox" name="is_bad_boy" value="1"
					{{ old('is_bad_boy', $member?->is_bad_boy) ? 'checked' : '' }}
					class="mr-2 h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500
		                                  @error('is_bad_boy') border-red-500 @enderror">
			<span class="text-gray-700 font-medium">Проблемна поведінка?</span>
		</label>
		@error('is_bad_boy')
		<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
		@enderror
	</div>
@endif