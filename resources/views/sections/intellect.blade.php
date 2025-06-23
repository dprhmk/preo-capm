@if(in_array( $role, ['admin', 'intellect']))
	<hr class="my-6 mt-16">

	<h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center">
		<span class="mr-2">🧩</span> Інтелектуальні здібності
	</h2>

	<div class="mb-4">
		<label class="block font-medium text-gray-700 mb-1">Рівень англійської</label>
		<select name="english_level" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                                        @error('english_level') border-red-500 @enderror" required>
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
                                        @error('general_iq_level') border-red-500 @enderror" required>
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
@endif