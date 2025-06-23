@if(in_array( $role, ['admin', 'main']))
	@push('partial-head-scripts')
		///
	@endpush

	<hr class="my-6 mt-16">

	<h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center">
		<span class="mr-2">📋</span> Основна інформація
	</h2>

	<div class="mb-4">
		<label class="block font-medium text-gray-700 mb-1">Повне ім’я</label>
		<input type="text" name="full_name" value="{{ old('full_name', $member?->full_name) }}"
				class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
	                      @error('full_name') border-red-500 @enderror" required>
		@error('full_name')
		<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
		@enderror
	</div>
	<div class="mb-4">
		<label class="block font-medium text-gray-700 mb-1">Дата народження</label>
		<input type="date" name="birth_date" value="{{ old('birth_date', $member?->birth_date?->format('Y-m-d')) }}"
				class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
		                              @error('birth_date') border-red-500 @enderror" required>
		@error('birth_date')
		<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
		@enderror
	</div>
	<div class="mb-4">
		<label class="block font-medium text-gray-700 mb-1">Вік</label>
		<input type="number" name="age" inputmode="numeric" value="{{ old('age', $member?->age) }}"
				class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
		                              @error('age') border-red-500 @enderror" required>
		@error('age')
		<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
		@enderror
	</div>
	<div class="mb-4">
		<label class="block font-medium">Стать</label>
		<select name="gender" class="w-full border rounded px-3 py-2 @error('gender') border-red-500 @enderror" required>
			<option value="">Не вказано</option>
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
			<option value="">Не вказано</option>
			<option value="stationary" {{ (old('residence_type') ?? $member?->residence_type) == 'stationary' ? 'selected' : '' }}>Стаціонар</option>
			<option value="non-stationary" {{ (old('residence_type') ?? $member?->residence_type) == 'non-stationary' ? 'selected' : '' }}>Не стаціонар</option>
		</select>
		@error('residence_type')
		<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
		@enderror
	</div>

	@push('partial-scripts')
		<script>

		</script>
	@endpush
@endif