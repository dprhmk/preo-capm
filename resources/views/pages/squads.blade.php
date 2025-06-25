@extends('layouts.app')

@section('content')
	<div class="max-w-4xl mx-auto p-4 bg-white rounded-lg shadow-lg">
		<h1 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
			<span class="mr-2">🤝</span> Розподіл на загони
		</h1>

		<!-- Форма вибору кількості загонів -->
		<form method="POST" action="{{ route('squads.store') }}" class="mb-6 sm:mb-8">
			@csrf
			<div class="space-y-4">
				<div class="flex items-center gap-4">
					<div class="w-full sm:w-auto">
						<label for="squad_count" class="block font-medium text-gray-700 mb-1 text-sm sm:text-base">Кількість загонів</label>
						<select id="squad_count" name="squad_count" class="w-full border rounded px-2 py-1 sm:px-3 sm:py-2 text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-blue-500 @error('squad_count') border-red-500 @enderror">
							<option value="" disabled selected>Виберіть кількість</option>
							@for ($i = 2; $i <= 6; $i++)
								<option value="{{ $i }}" {{ ($squads->count() == $i) || (old('squad_count') == $i) ? 'selected' : '' }}>{{ $i }}</option>
							@endfor
						</select>
						@error('squad_count')
						<p class="text-red-500 text-xs mt-1">{{ $message }}</p>
						@enderror
					</div>
				</div>

				<button type="submit" class="bg-blue-600 text-white font-medium px-3 py-1 sm:px-4 sm:py-2 rounded text-sm sm:text-base hover:bg-blue-700 transition duration-200">
					{{ $squads->isEmpty() ? 'Розподілити' : 'Перерозподілити' }}
				</button>
			</div>
		</form>

		<!-- Результати розподілу -->
		@if (!empty($squads))
			<div class="space-y-4 sm:space-y-6">
				@foreach ($squads as $squad)
					@php
						$physicalScorePercent = $squad->physical_score / max($squad->members->count(), 1);
						$mentalScorePercent = $squad->mental_score / max($squad->members->count(), 1);
					@endphp
					<div>
						<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-2 sm:mb-4">
							<div>
								<h3 class="text-base sm:text-lg font-semibold text-gray-700">{{ $squad->name }}</h3>
								<span class="text-xs sm:text-sm text-gray-500 block sm:inline">({{ count($squad->members) }} учасників)</span>
							</div>
							<div class="flex flex-col sm:flex-row sm:space-x-4 mt-2 sm:mt-0">
								<div class="flex items-center space-x-1 sm:space-x-2">
									<span class="text-xs sm:text-sm font-medium text-gray-600">Фізична підготовка:</span>
									<div class="w-16 sm:w-20 bg-gray-200 rounded-full h-2">
										<div class="bg-green-600 h-2 rounded-full" style="width: {{ $physicalScorePercent }}%"></div>
									</div>
									<span class="text-xs sm:text-sm text-gray-600">{{ round($squad->physical_score, 0) }}</span>
								</div>
								<div class="flex items-center space-x-1 sm:space-x-2 mt-1 sm:mt-0">
									<span class="text-xs sm:text-sm font-medium text-gray-600">Ментальна креативність:</span>
									<div class="w-16 sm:w-20 bg-gray-200 rounded-full h-2">
										<div class="bg-blue-600 h-2 rounded-full" style="width: {{ $mentalScorePercent }}%"></div>
									</div>
									<span class="text-xs sm:text-sm text-gray-600">{{ round($squad->mental_score, 0) }}</span>
								</div>
							</div>
						</div>
						<p class="text-xs sm:text-sm text-gray-600 mb-2 sm:mb-4">
							Лідер: {{ $squad->leader_name ?? 'Не вказано' }} |
							Помічник: {{ $squad->assistant_name ?? 'Не вказано' }}
						</p>
						<div class="overflow-x-auto rounded-lg">
							<table class="w-full text-xs sm:text-sm text-left text-gray-700">
								<thead class="text-xs bg-gray-100 text-gray-600">
								<tr>
									<th class="px-2 py-1 sm:px-3 sm:py-2">N°</th>
									<th class="px-2 py-1 sm:px-3 sm:py-2">Ім’я</th>
									<th class="px-2 py-1 sm:px-3 sm:py-2">Вік</th>
									<th class="px-2 py-1 sm:px-3 sm:py-2">Стать</th>
									<th class="px-2 py-1 sm:px-3 sm:py-2  sm:table-cell">Дата народження</th>
									<th class="px-2 py-1 sm:px-3 sm:py-2">Фізична підготовка</th>
									<th class="px-2 py-1 sm:px-3 sm:py-2">Креативність</th>
									<th class="px-2 py-1 sm:px-4 sm:py-2  sm:table-cell">Дії</th>
								</tr>
								</thead>
								<tbody>
								@foreach ($squad->members as $member)
									@php
										$birthDate = $member->birth_date ?? null;
										$isBirthdayWeek = $birthDate &&
											$birthDate->month == 7 &&
											$birthDate->day >= 13 &&
											$birthDate->day <= 19;
										$age = $birthDate ? floor($birthDate->diffInYears(now())) : '-';
										$physicalScorePercent = $member->physical_score ? min($member->physical_score, 100) : 0;
										$mentalScorePercent = $member->mental_score ? min($member->mental_score, 100) : 0;
									@endphp
									<tr class="border-b hover:bg-gray-50">
										<td class="px-2 py-1 sm:px-3 sm:py-2">{{ $loop->iteration }}</td>
										<td class="px-2 py-1 sm:px-3 sm:py-2">{{ $member->full_name ?? 'Невідомо' }}</td>
										<td class="px-2 py-1 sm:px-3 sm:py-2">{{ $age }}</td>
										<td class="px-2 py-1 sm:px-3 sm:py-2">{{ $member->gender === 'male' ? 'Чоловік' : 'Жінка' }}</td>
										<td class="px-2 py-1 sm:px-3 sm:py-2  sm:table-cell {{ $isBirthdayWeek ? 'bg-green-400 rounded-lg' : '' }}">
											{{ $birthDate ? $birthDate->format('d.m.Y') : '-' }} {{ $isBirthdayWeek ? '🎂' : '' }}
										</td>
										<td class="px-2 py-1 sm:px-3 sm:py-2">
											<div class="w-16 sm:w-20 bg-gray-200 rounded-full h-2">
												<div class="bg-green-600 h-2 rounded-full" style="width: {{ $physicalScorePercent }}%"></div>
											</div>
											<span class="text-xs sm:text-sm text-gray-600">{{ number_format($member->physical_score, 2) }}</span>
										</td>
										<td class="px-2 py-1 sm:px-3 sm:py-2">
											<div class="w-16 sm:w-20 bg-gray-200 rounded-full h-2">
												<div class="bg-blue-600 h-2 rounded-full" style="width: {{ $mentalScorePercent }}%"></div>
											</div>
											<span class="text-xs sm:text-sm text-gray-600">{{ number_format($member->mental_score, 2) }}</span>
										</td>
										<td class="px-2 py-1 sm:px-4 sm:py-2  sm:table-cell">
											<a href="{{ route('member.edit', $member->code) }}"
													class="text-blue-600 hover:text-blue-800 transition">Редагувати</a>
										</td>
									</tr>
								@endforeach
								</tbody>
							</table>
						</div>
					</div>
				@endforeach
			</div>
		@else
			<p class="text-gray-500 text-center text-sm sm:text-base">Розподіліть учасників, щоб побачити загони.</p>
		@endif
	</div>
@endsection
