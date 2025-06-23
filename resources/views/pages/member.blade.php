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

		<form method="POST" action="{{ route('member.store', ['code' => $member->code]) }}"
				enctype="multipart/form-data">
			@csrf

			@if(in_array( $role, ['admin', 'main']))
				<!-- Основна інформація -->
				<h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center">
					<span class="mr-2">📋</span> Основна інформація
				</h2>
				<div class="mb-4">
					<label class="block font-medium text-gray-700 mb-1">Повне ім’я</label>
					<input type="text" name="full_name" value="{{ old('full_name', $member?->full_name) }}"
							class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
	                              @error('full_name') border-red-500 @enderror">
					@error('full_name')
					<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
					@enderror
				</div>

				<div class="mb-4">
					<label class="block font-medium text-gray-700 mb-1">Дата народження</label>
					<input type="date" name="birth_date" value="{{ old('birth_date', $member?->birth_date->format('Y-m-d')) }}"
							class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
	                              @error('birth_date') border-red-500 @enderror">
					@error('birth_date')
					<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
					@enderror
				</div>
				<div class="mb-4">
					<label class="block font-medium text-gray-700 mb-1">Вік</label>
					<input type="number" name="age" value="{{ old('age', $member?->age) }}"
							class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
	                              @error('age') border-red-500 @enderror">
					@error('age')
					<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
					@enderror
				</div>
				<div class="mb-4">
					<label class="block font-medium text-gray-700 mb-1">Стать</label>
					<select name="gender" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
	                                           @error('gender') border-red-500 @enderror">
						<option value="">Не вказано</option>
						@foreach(['male' => 'Чоловік', 'female' => 'Жінка'] as $key => $value)
							<option value="{{ $key }}"
									{{ old('gender', $member?->gender) === $key ? 'selected' : '' }}>
								{{ $value }}
							</option>
						@endforeach
					</select>
					@error('gender')
					<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
					@enderror
				</div>
				<div class="mb-4">
					<label class="block font-medium text-gray-700 mb-1">Тип проживання</label>
					<select name="residence_type" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
	                                                  @error('residence_type') border-red-500 @enderror">
						<option value="">Не вказано</option>
						@foreach(['stationary' => 'Стаціонарне', 'non-stationary' => 'Не стаціонарне'] as $key => $value)
							<option value="{{ $key }}"
									{{ old('residence_type', $member?->residence_type) === $key ? 'selected' : '' }}>
								{{ $value }}
							</option>
						@endforeach
					</select>
					@error('residence_type')
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




			<!-- Контактна інформація -->
			<h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center">
				<span class="mr-2">📞</span> Контактна інформація
			</h2>
			<div class="mb-4">
				<label class="block font-medium text-gray-700 mb-1">Фото</label>
				<input type="file" name="photo" accept="image/*"
						class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                              @error('photo') border-red-500 @enderror">
				@error('photo')
				<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
				@enderror
			</div>
			<div class="mb-4">
				<label class="block font-medium text-gray-700 mb-1">Телефон дитини</label>
				<input type="text" name="child_phone" value="{{ old('child_phone', $member?->child_phone) }}"
						class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                              @error('child_phone') border-red-500 @enderror">
				@error('child_phone')
				<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
				@enderror
			</div>
			<div class="mb-4">
				<label class="block font-medium text-gray-700 mb-1">Телефон батьків</label>
				<input type="text" name="parent_phone" value="{{ old('parent_phone', $member?->parent_phone) }}"
						class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                              @error('parent_phone') border-red-500 @enderror">
				@error('parent_phone')
				<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
				@enderror
			</div>
			<div class="mb-4">
				<label class="block font-medium text-gray-700 mb-1">Ім’я опікуна</label>
				<input type="text" name="guardian_name" value="{{ old('guardian_name', $member?->guardian_name) }}"
						class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                              @error('guardian_name') border-red-500 @enderror">
				@error('guardian_name')
				<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
				@enderror
			</div>
			<div class="mb-4">
				<label class="block font-medium text-gray-700 mb-1">Екстрений контакт</label>
				<input type="text" name="additional_contact" value="{{ old('additional_contact', $member?->additional_contact) }}"
						class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                              @error('additional_contact') border-red-500 @enderror">
				@error('additional_contact')
				<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
				@enderror
			</div>
			<div class="mb-4">
				<label class="block font-medium text-gray-700 mb-1">Соціальні мережі</label>
				<input type="text" name="social_links[]" value="{{ old('social_links.0', $member?->social_links ? $member->social_links[0] ?? '' : '') }}"
						class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                              @error('social_links.*') border-red-500 @enderror">
				<input type="text" name="social_links[]" value="{{ old('social_links.1', $member?->social_links ? $member->social_links[1] ?? '' : '') }}"
						class="w-full border rounded px-3 py-2 mt-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                              @error('social_links.*') border-red-500 @enderror">
				@error('social_links.*')
				<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
				@enderror
			</div>
			<div class="mb-4">
				<label class="block font-medium text-gray-700 mb-1">Адреса</label>
				<textarea name="address" rows="4"
						class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                                 @error('address') border-red-500 @enderror">{{ old('address', $member?->address) }}</textarea>
				@error('address')
				<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
				@enderror
			</div>

			<!-- Фізичні характеристики -->
			<h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center">
				<span class="mr-2">💪</span> Фізичні характеристики
			</h2>
			<div class="mb-4">
				<label class="block font-medium text-gray-700 mb-1">Зріст (см)</label>
				<input type="number" name="height_cm" value="{{ old('height_cm', $member?->height_cm) }}"
						class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                              @error('height_cm') border-red-500 @enderror">
				@error('height_cm')
				<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
				@enderror
			</div>
			<div class="mb-4">
				<label class="block font-medium text-gray-700 mb-1">Тип статури</label>
				<select name="body_type" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                                              @error('body_type') border-red-500 @enderror">
					<option value="">Не вказано</option>
					@foreach(['thin' => 'Худорлявий', 'medium' => 'Середній', 'plump' => 'Повний'] as $key => $value)
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
			<div class="mb-4">
				<label class="block font-medium text-gray-700 mb-1">Тип спорту</label>
				<select name="sport_type" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                                               @error('sport_type') border-red-500 @enderror">
					<option value="">Не вказано</option>
					@foreach(['football' => 'Футбол', 'volleyball' => 'Волейбол', 'tennis' => 'Теніс', 'wrestling' => 'Боротьба', 'workout' => 'Тренування', 'other' => 'Інше'] as $key => $value)
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
			<div class="mb-4">
				<label class="block font-medium text-gray-700 mb-1">Рівень спритності</label>
				<select name="agility_level" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                                                  @error('agility_level') border-red-500 @enderror">
					<option value="">Не вказано</option>
					@for ($i = 1; $i <= 3; $i++)
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
			<div class="mb-4">
				<label class="block font-medium text-gray-700 mb-1">Рівень сили</label>
				<select name="strength_level" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                                                   @error('strength_level') border-red-500 @enderror">
					<option value="">Не вказано</option>
					@for ($i = 1; $i <= 3; $i++)
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

			<!-- Медичні особливості -->
			<h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center">
				<span class="mr-2">🩺</span> Медичні особливості
			</h2>
			<div class="mb-4">
				<label class="block font-medium text-gray-700 mb-1">Деталі алергії</label>
				<textarea name="allergy_details" rows="4"
						class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                                 @error('allergy_details') border-red-500 @enderror">{{ old('allergy_details', $member?->allergy_details) }}</textarea>
				@error('allergy_details')
				<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
				@enderror
			</div>
			<div class="mb-4">
				<label class="block font-medium text-gray-700 mb-1">Заборонені продукти</label>
				<textarea name="medical_restrictions" rows="4"
						class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                                 @error('medical_restrictions') border-red-500 @enderror">{{ old('medical_restrictions', $member?->medical_restrictions) }}</textarea>
				@error('medical_restrictions')
				<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
				@enderror
			</div>
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
			<div class="mb-4">
				<label class="block font-medium text-gray-700 mb-1">Інші медичні нотатки</label>
				<textarea name="other_health_notes" rows="4"
						class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                                 @error('other_health_notes') border-red-500 @enderror">{{ old('other_health_notes', $member?->other_health_notes) }}</textarea>
				@error('other_health_notes')
				<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
				@enderror
			</div>

			<!-- Психічне здоров’я -->
			<h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center">
				<span class="mr-2">🧠</span> Психічне здоров’я
			</h2>
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
			<div class="mb-4">
				<label class="block font-medium text-gray-700 mb-1">Психологічний діагноз</label>
				<input type="text" name="psychological_diagnosis" value="{{ old('psychological_diagnosis', $member?->psychological_diagnosis) }}"
						class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                              @error('psychological_diagnosis') border-red-500 @enderror">
				@error('psychological_diagnosis')
				<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
				@enderror
			</div>
			<div class="mb-4">
				<label class="inline-flex items-center cursor-pointer">
					<input type="checkbox" name="has_panic_attacks" value="1"
							{{ old('has_panic_attacks', $member?->has_panic_attacks) ? 'checked' : '' }}
							class="mr-2 h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500
                                  @error('has_panic_attacks') border-red-500 @enderror">
					<span class="text-gray-700 font-medium">Є панічні атаки?</span>
				</label>
				@error('has_panic_attacks')
				<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
				@enderror
			</div>
			<div class="mb-4">
				<label class="block font-medium text-gray-700 mb-1">Тип особистості</label>
				<select name="personality_type" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                                                @error('personality_type') border-red-500 @enderror">
					<option value="">Не вказано</option>
					@foreach(['extrovert' => 'Екстраверт', 'introvert' => 'Інтроверт', 'ambivert' => 'Амбіверт'] as $key => $value)
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

			<!-- Творчість -->
			<h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center">
				<span class="mr-2">🎨</span> Творчість
			</h2>
			<div class="mb-4">
				<label class="block font-medium text-gray-700 mb-1">Художні здібності</label>
				<select name="artistic_ability" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                                                @error('artistic_ability') border-red-500 @enderror">
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
			<div class="mb-4">
				<label class="block font-medium text-gray-700 mb-1">Музичні інструменти</label>
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
			<div class="mb-4">
				<label class="block font-medium text-gray-700 mb-1">Поетичні здібності</label>
				<select name="poetic_ability" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
                                                @error('poetic_ability') border-red-500 @enderror">
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

			<!-- Інтелектуальні здібності -->
			<h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center">
				<span class="mr-2">🧩</span> Інтелектуальні здібності
			</h2>
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