@if(in_array( $role, ['admin', 'creative']))
	<hr class="my-6 mt-16">

	<h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center">
		<span class="mr-2">🎨</span> Творчість
	</h2>

	<!-- Художні здібності -->
	<div class="mb-4">
		<label class="block font-medium text-gray-700 mb-1">Художні здібності</label>
		<select name="artistic_ability" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                                        @error('artistic_ability') border-red-500 @enderror" required>
			<option value="">Не вказано</option>
			@for ($i = 1; $i <= 3; $i++)
				<option value="{{ $i }}"
						{{ old('artistic_ability', $member?->artistic_ability) == $i ? 'selected' : '' }}>
					{{ $i }}
				</option>
			@endfor
		</select>
		@error('artistic_ability')
		<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
		@enderror
	</div>

	<!-- Музикант -->
	<div class="mb-4">
		<label class="inline-flex items-center cursor-pointer">
			<input type="checkbox" name="is_musician" value="1"
					{{ old('is_musician', $member?->is_musician) ? 'checked' : '' }}
					class="mr-2 h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500
                          @error('is_musician') border-red-500 @enderror">
			<span class="text-gray-700 font-medium">Є музикантом?</span>
		</label>
		@error('is_musician')
		<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
		@enderror
	</div>

	<!-- Музичні інструменти -->
	<div class="mb-4">
		<label class="block font-medium text-gray-700 mb-1">Музичні навички</label>
		<select name="musical_instruments" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                                        @error('musical_instruments') border-red-500 @enderror">
			<option value="">Не вказано</option>
			@foreach(['guitar' => 'Гітара', 'piano' => 'Піаніно', 'drums' => 'Барабани', 'vocals' => 'Вокал', 'other' => 'Інше'] as $key => $value)
				<option value="{{ $key }}"
						{{ old('musical_instruments', $member?->musical_instruments) === $key ? 'selected' : '' }}>
					{{ $value }}
				</option>
			@endforeach
		</select>
		@error('musical_instruments')
		<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
		@enderror
	</div>

	<!-- Поетичні здібності -->
	<div class="mb-4">
		<label class="block font-medium text-gray-700 mb-1">Поетичні здібності</label>
		<select name="poetic_ability" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                  @error('poetic_ability') border-red-500 @enderror" required>
			<option value="">Не вказано</option>
			@for ($i = 1; $i <= 3; $i++)
				<option value="{{ $i }}"
						{{ old('poetic_ability', $member?->poetic_ability) == $i ? 'selected' : '' }}>
					{{ $i }}
				</option>
			@endfor
		</select>
		@error('poetic_ability')
		<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
		@enderror
	</div>
@endif