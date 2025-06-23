@if(in_array( $role, ['admin', 'medical']))

	<hr class="my-6 mt-16">

	<!-- Медичні особливості -->
	<h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center">
		<span class="mr-2">🩺</span> Медичні особливості
	</h2>

	<!-- Деталі алергії -->
	<div class="mb-4">
		<label class="block font-medium text-gray-700 mb-1">Деталі алергії</label>
		<textarea name="allergy_details" rows="4"
				class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
	                                 @error('allergy_details') border-red-500 @enderror">{{ old('allergy_details', $member?->allergy_details) }}</textarea>
		@error('allergy_details')
		<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
		@enderror
	</div>

	<!-- Медичні обмеження -->
	<div class="mb-4">
		<label class="block font-medium text-gray-700 mb-1">Заборонені продукти</label>
		<textarea name="medical_restrictions" rows="4"
				class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
	                     @error('medical_restrictions') border-red-500 @enderror">{{ old('medical_restrictions', $member?->medical_restrictions) }}</textarea>
		@error('medical_restrictions')
		<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
		@enderror
	</div>

	<!-- Фізичні обмеження -->
	<div class="mb-4">
		<label class="inline-flex items-center cursor-pointer">
			<input type="checkbox" name="physical_limitations" value="1"
					{{ old('physical_limitations', $member?->physical_limitations) ? 'checked' : '' }}
					class="mr-2 h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500
	                      @error('physical_limitations') border-red-500 @enderror">
			<span class="text-gray-700 font-medium">Є обмеження активності?</span>
		</label>
		@error('physical_limitations')
		<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
		@enderror
	</div>

	<!-- Інші медичні нотатки -->
	<div class="mb-4">
		<label class="block font-medium text-gray-700 mb-1">Інші медичні нотатки</label>
		<textarea name="other_health_notes" rows="4"
				class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
	                     @error('other_health_notes') border-red-500 @enderror">{{ old('other_health_notes', $member?->other_health_notes) }}</textarea>
		@error('other_health_notes')
		<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
		@enderror
	</div>
@endif
