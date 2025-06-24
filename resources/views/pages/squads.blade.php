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
					<div>
						<h3 class="text-lg font-semibold text-gray-700 mb-2">{{ $squad->name }}</h3>
						<p class="text-sm text-gray-600 mb-4">
							Лідер: {{ $squad['leader_name'] ?? 'Не вказано' }} |
							Помічник: {{ $squad['assistant_name'] ?? 'Не вказано' }} |
							Загальні очки: {{ $squad['total_score'] ?? 0 }}
						</p>
						<div class="overflow-x-auto rounded-lg">
							<table class="w-full text-sm text-left text-gray-700">
								<thead class="text-xs bg-gray-100 text-gray-600">
								<tr>
									<th class="px-3 py-2">N°</th>
									<th class="px-3 py-2">Ім’я</th>
									<th class="px-3 py-2">Вік</th>
									<th class="px-3 py-2">Стаж</th>
									<th class="px-3 py-2">Тип</th>
									<th class="px-3 py-2">Спритність</th>
									<th class="px-3 py-2">Музикант</th>
									<th class="px-3 py-2">Дата народження</th>
								</tr>
								</thead>
								<tbody>
								@foreach ($squad['members'] as $member)
									@php
										$birthDate = $member['birth_date'] ?? null;
										$isBirthdayWeek = $birthDate &&
											$birthDate->month == 7 &&
											$birthDate->day >= 13 &&
											$birthDate->day <= 19;
										$age = $birthDate ? $birthDate->diffInYears(now()) : '-';
									@endphp
									<tr class="border-b hover:bg-gray-50">
										<td class="px-3 py-2">{{ $loop->iteration }}</td>
										<td class="px-3 py-2">{{ $member->full_name ?? 'Невідомо' }}</td>
										<td class="px-3 py-2">{{ $age }}</td>
										<td class="px-3 py-2">{{ $member->first_time ? 'Новий' : 'Досвідчений' }}</td>
										<td class="px-3 py-2">{{ $member->personality_type ?? '-' }}</td>
										<td class="px-3 py-2">{{ $member->agility_level ?? '-' }}</td>
										<td class="px-3 py-2">{{ $member->is_musician ? 'Так' : 'Ні' }}</td>
										<td class="px-3 py-2 {{ $isBirthdayWeek ? 'bg-green-400 rounded-lg' : '' }}">
											{{ $birthDate ? $birthDate->format('d.m.Y') : '-' }} {{ $isBirthdayWeek ? '🎂' : '' }}
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

	@push('scripts')
		<script>

		</script>
	@endpush
@endsection
