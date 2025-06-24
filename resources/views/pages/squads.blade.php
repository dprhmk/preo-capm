@extends('layouts.app')

@section('content')
	<div class="max-w-4xl mx-auto p-4 bg-white rounded-b-lg shadow-lg">
		<h1 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
			<span class="mr-2">🤝</span> Розподіл на загони
		</h1>

		<!-- Форма вибору кількості загонів -->
		<form method="POST" action="{{ route('squads.store') }}" class="mb-8">
			@csrf
			<div class="space-y-4">
				<div class="flex items-center gap-4">
					<div>
						<label for="squad_count" class="block font-medium text-gray-700 mb-1">Кількість загонів</label>
						<select id="squad_count" name="squad_count" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('squad_count') border-red-500 @enderror">
							<option value="" disabled selected>Виберіть кількість</option>
							@for ($i = 2; $i <= 6; $i++)
								<option value="{{ $i }}" {{ old('squad_count') == $i ? 'selected' : '' }}>{{ $i }}</option>
							@endfor
						</select>
						@error('squad_count')
						<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
						@enderror
					</div>
				</div>

				<button type="submit" class="bg-blue-600 text-white font-medium px-4 py-2 rounded hover:bg-blue-700 transition duration-200">
					{{ $squads->isEmpty() ? 'Розподілити' : 'Перерозподілити' }}
				</button>
			</div>
		</form>

		<!-- Результати розподілу -->
		@if (!empty($squads))
			<div class="space-y-6">
				@foreach ($squads as $squad)
					@php
						$physicalScorePercent = $squad->physical_score ? min(($squad->physical_score / (count($squad->members) * 1.5)) * 100, 100) : 0;
						$mentalScorePercent = $squad->mental_score ? min(($squad->mental_score / (count($squad->members) * 1.5)) * 100, 100) : 0;
					@endphp
					<div>
						<div class="flex justify-between items-center mb-4">
							<h3 class="text-lg font-semibold text-gray-700">{{ $squad->name }}</h3>
							<div class="flex space-x-6">
								<div class="flex items-center space-x-2">
									<span class="text-sm font-medium text-gray-600">Фізична підготовка:</span>
									<div class="w-24 bg-gray-200 rounded-full h-2.5">
										<div class="bg-green-600 h-2.5 rounded-full" style="width: {{ $physicalScorePercent }}%"></div>
									</div>
									<span class="text-sm text-gray-600">{{ number_format($squad->physical_score, 2) }}</span>
								</div>
								<div class="flex items-center space-x-2">
									<span class="text-sm font-medium text-gray-600">Ментальна креативність:</span>
									<div class="w-24 bg-gray-200 rounded-full h-2.5">
										<div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $mentalScorePercent }}%"></div>
									</div>
									<span class="text-sm text-gray-600">{{ number_format($squad->mental_score, 2) }}</span>
								</div>
							</div>
						</div>
						<p class="text-sm text-gray-600 mb-4">
							Лідер: {{ $squad->leader_name ?? 'Не вказано' }} |
							Помічник: {{ $squad->assistant_name ?? 'Не вказано' }}
						</p>
						<div class="overflow-x-auto rounded-lg">
							<table class="w-full text-sm text-left text-gray-700">
								<thead class="text-xs bg-gray-100 text-gray-600">
								<tr>
									<th class="px-3 py-2">N°</th>
									<th class="px-3 py-2">Ім’я</th>
									<th class="px-3 py-2">Вік</th>
									<th class="px-3 py-2">Дата народження</th>
									<th class="px-3 py-2">Фізична підготовка</th>
									<th class="px-3 py-2">Ментальна креативність</th>
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
										$age = $birthDate ? $birthDate->diffInYears(now()) : '-';
										$physicalScorePercent = $member->physical_score ? min(($member->physical_score / 1.5) * 100, 100) : 0;
										$mentalScorePercent = $member->mental_score ? min(($member->mental_score / 1.5) * 100, 100) : 0;
									@endphp
									<tr class="border-b hover:bg-gray-50">
										<td class="px-3 py-2">{{ $loop->iteration }}</td>
										<td class="px-3 py-2">{{ $member->full_name ?? 'Невідомо' }}</td>
										<td class="px-3 py-2">{{ $age }}</td>
										<td class="px-3 py-2 {{ $isBirthdayWeek ? 'bg-green-400 rounded-lg' : '' }}">
											{{ $birthDate ? $birthDate->format('d.m.Y') : '-' }} {{ $isBirthdayWeek ? '🎂' : '' }}
										</td>
										<td class="px-3 py-2">
											<div class="w-24 bg-gray-200 rounded-full h-2.5">
												<div class="bg-green-600 h-2.5 rounded-full" style="width: {{ $physicalScorePercent }}%"></div>
											</div>
										</td>
										<td class="px-3 py-2">
											<div class="w-24 bg-gray-200 rounded-full h-2.5">
												<div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $mentalScorePercent }}%"></div>
											</div>
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
			<p class="text-gray-500 text-center">Розподіліть учасників, щоб побачити загони.</p>
		@endif
	</div>
@endsection
