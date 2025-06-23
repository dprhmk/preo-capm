@if(in_array($role, ['admin', 'physical']))
	<hr class="my-6 mt-16">

	<!-- Фізичні характеристики -->
	<h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center">
		<span class="mr-2">💪</span> Фізичні характеристики
	</h2>

	<!-- Зріст -->
	<div class="mb-4">
		<label class="block font-medium text-gray-700 mb-1">Зріст (см)</label>
		<input type="number" inputmode="numeric" name="height_cm" value="{{ old('height_cm', $member?->height_cm) }}"
				class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
	                  @error('height_cm') border-red-500 @enderror" required>
		@error('height_cm')
		<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
		@enderror
	</div>

	<!-- Тип тіла -->
	<div class="mb-4">
		<label class="block font-medium text-gray-700 mb-1">Тип тіла</label>
		<select name="body_type" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
	                                  @error('body_type') border-red-500 @enderror">
			@foreach(['medium' => 'Середній', 'thin' => 'Худий', 'plump' => 'Повненький'] as $key => $value)
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
	                                      @error('agility_level') border-red-500 @enderror" required>
			<option value="">Не вказано</option>
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
	                                       @error('strength_level') border-red-500 @enderror" required>
			<option value="">Не вказано</option>
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
@endif