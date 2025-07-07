@php use Illuminate\Support\Carbon; @endphp

@extends('layouts.app')

@php
	$first = Carbon::parse('2025-07-06 17:00:00', 'Europe/Kyiv');
	$second = Carbon::parse('2025-07-13 17:00:00', 'Europe/Kyiv');
	$now = Carbon::now('Europe/Kyiv');

	$current = NULL;

	if ($now->between($first, $first->copy()->addDays(6))) {
		$current = $first;
	} elseif ($now->between($second, $second->copy()->addDays(6))) {
		$current = $second;
	}
@endphp

@section('content')
	<div class="max-w-4xl mx-auto mb-16 p-4 backdrop-blur-xl bg-white/70 rounded-lg shadow-lg">
		<h1 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
			<span class="mr-2">🤝</span> Розподіл на загони
		</h1>

		<!-- Виводимо повідомлення про успіх -->
		@if (session('success'))
			<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
				{{ session('success') }}
			</div>
		@endif

		@if(auth()->user()->role === 'admin')
			<!-- Форма вибору кількості загонів -->
			<form method="POST" action="{{ route('squads.store') }}" class="mb-6 sm:mb-8" id="squad-form">
				@csrf
				<div class="space-y-4">
					<div class="flex items-center gap-4">
						<div class="w-full sm:w-auto">
							<label for="squad_count" class="block font-medium text-gray-700 mb-1 text-sm sm:text-base">Кількість загонів</label>
							<select id="squad_count" name="squad_count"
									class="w-full border rounded px-2 py-1 sm:px-3 sm:py-2 text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-blue-500 @error('squad_count') border-red-500 @enderror">
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

					<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
						<button type="submit" class="bg-blue-600 text-white font-medium px-3 py-1 sm:px-4 sm:py-2 rounded text-sm sm:text-base hover:bg-blue-700 transition duration-200">
							{{ $squads->isEmpty() ? 'Розподілити' : 'Перерозподілити' }}
						</button>

						<!-- Посилання "Очистити базу" -->
						<a href="#"
								onclick="if (confirm('Ви впевнені, що хочете очистити базу даних? Цю дію не можливо буде відмінити!')) { let form = document.createElement('form'); form.method = 'POST'; form.action = '{{ route('truncate-db') }}'; let csrf = document.createElement('input'); csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}'; form.appendChild(csrf); document.body.appendChild(form); form.submit(); }"
								class="bg-red-600 text-white font-medium px-3 py-1 sm:px-4 sm:py-2 rounded text-sm sm:text-base hover:bg-red-700 transition duration-200 text-center">
							Очистити базу
						</a>
					</div>
				</div>
			</form>

			@push('scripts')
				<script>
					document.getElementById('squad-form').addEventListener('submit', function(e) {
						const confirmed = confirm('{{ $squads->isEmpty() ? "Ви впевнені, що хочете розподілити учасників по загонах?" : "Ви впевнені, що хочете перерозподілити учасників? Попередній розподіл буде втрачено." }}');
						if (!confirmed) {
							e.preventDefault();
						}
					});
				</script>
			@endpush

		@endif

		<!-- Результати розподілу -->
		@if ($squads->isNotEmpty())
			@include('partials.squads-analytics')

			<div class="space-y-4 sm:space-y-6">
				@php
					$maxSquadScorePercent = $squads?->pluck('members')->max()?->count() * 100;
				@endphp

				@foreach ($squads as $squad)
					@php
						$physicalScorePercent = ($squad->physical_score * 100) / max($maxSquadScorePercent, 1);
						$mentalScorePercent = ($squad->mental_score * 100) / max($maxSquadScorePercent, 1);
					@endphp
					<div>
						<div class="space-y-4 mb-6">
							<!-- Color, Team Name, and Member Count -->
							<div class="flex items-center justify-between">
								<div class="flex items-center gap-2">
									@if ($squad->color)
										<span class="inline-block w-4 h-4 sm:w-5 sm:h-5 rounded-full border-2 border-gray-200 shadow-sm" style="background-color: {{ $squad->color }};"></span>
									@else
										<span class="text-gray-300">–</span>
									@endif
									<h3 class="text-lg sm:text-xl font-extrabold text-gray-900">{{ $squad->name }}</h3>
								</div>
								<span class="text-xs sm:text-sm text-gray-400">({{ count($squad->members) }} учасників)</span>
							</div>
							<!-- Stats and Edit Link -->
							<div class="flex flex-col sm:flex-row sm:items-start gap-3 sm:gap-6">
								<div class="flex flex-col sm:flex-row sm:gap-4 w-full">
									<div class="flex items-center gap-2">
										<span class="w-32 text-sm font-medium text-gray-600">Фізична підготовка:</span>
										<div class="w-20 sm:w-24 bg-gray-200 rounded-full h-2.5">
											<div class="bg-green-600 h-2.5 rounded-full transition-all duration-300" style="width: {{ $physicalScorePercent }}%"></div>
										</div>
										<span class="text-sm text-gray-600">{{ round($squad->physical_score, 0) }}</span>
									</div>
									<div class="flex items-center gap-2">
										<span class="w-32 text-sm font-medium text-gray-600">Ментальна креативність:</span>
										<div class="w-20 sm:w-24 bg-gray-200 rounded-full h-2.5">
											<div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" style="width: {{ $mentalScorePercent }}%"></div>
										</div>
										<span class="text-sm text-gray-600">{{ round($squad->mental_score, 0) }}</span>
									</div>
								</div>
								<a href="{{ route('squads.edit', $squad->id) }}"
										class="block text-blue-600 text-sm font-medium sm:mt-0 mt-3 hover:text-blue-700 transition duration-150">
									Редагувати загін
								</a>
							</div>
							<!-- Leader and Assistant -->
							<div class="flex flex-col sm:flex-row sm:gap-4">
								<div class="flex items-center gap-2 mb-2 sm:mb-0">
									<span class="w-32 text-sm font-semibold text-gray-700">Лідер:</span>
									<span class="text-sm text-gray-800 bg-gray-50 px-3 py-1 rounded-md">{{ $squad->leader_name ?? 'Не вказано' }}</span>
								</div>
								<div class="flex items-center gap-2">
									<span class="w-32 text-sm font-semibold text-gray-700">Помічник:</span>
									<span class="text-sm text-gray-800 bg-gray-50 px-3 py-1 rounded-md">{{ $squad->assistant_name ?? 'Не вказано' }}</span>
								</div>
							</div>
						</div>
						<div class="overflow-x-auto rounded-lg">
							<table class="w-full text-xs sm:text-sm text-left text-gray-700">
								<thead class="text-xs text-gray-600">
								<tr>
									<th class="px-2 py-1 sm:px-3 sm:py-2">N°</th>
									<th class="px-2 py-1 sm:px-3 sm:py-2">Ім’я</th>
									<th class="px-2 py-1 sm:px-3 sm:py-2">Вік</th>
									<th class="px-2 py-1 sm:px-3 sm:py-2">Стать</th>
									<th class="px-2 py-1 sm:px-3 sm:py-2 sm:table-cell">Дата народження</th>
									<th class="px-2 py-1 sm:px-3 sm:py-2">Фізична підготовка</th>
									<th class="px-2 py-1 sm:px-3 sm:py-2">Креативність</th>
									<th class="px-2 py-1 sm:px-4 sm:py-2 sm:table-cell">Дії</th>
								</tr>
								</thead>
								<tbody>
								@foreach ($squad->members as $member)
									@php
										$birthDate = $member->birth_date ? Carbon::parse($member->birth_date) : NULL;
										$isBirthdayWeek = $birthDate
											&& isset($current)
											&& collect(range(0, 6))
												->contains(fn($i) => $birthDate->format('m-d') === $current->copy()->addDays($i)->format('m-d'));

										$age = $birthDate ? floor($birthDate->diffInYears(now())) : '-';
										$physicalScorePercent = $member->physical_score ? min($member->physical_score, 100) : 0;
										$mentalScorePercent = $member->mental_score ? min($member->mental_score, 100) : 0;
									@endphp
									@if($member->is_leader)
										<tr class="border-b {{ $member->is_required_filled === 0 ? 'bg-red-300' : 'bg-blue-300' }}">
									@else
										<tr class="border-b {{ $member->is_required_filled === 0 ? 'bg-red-300' : '' }}">
									@endif
										<td class="px-2 py-1 sm:px-3 sm:py-2">{{ $loop->iteration }}</td>
										<td class="px-2 py-1 sm:px-3 sm:py-2">{{ $member->full_name ?? '-' }}</td>
										<td class="px-2 py-1 sm:px-3 sm:py-2">{{ $age }}</td>
										<td class="px-2 py-1 sm:px-3 sm:py-2">{{ $member->gender === 'male' ? 'Чоловік' : 'Жінка' }}</td>
										<td class="px-2 py-1 sm:px-3 sm:py-2 sm:table-cell {{ $isBirthdayWeek ? 'bg-green-400 rounded-lg' : '' }}">
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
										<td class="px-2 py-1 sm:px-4 sm:py-2 sm:table-cell">
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
			<p class="text-gray-500 text-center text-sm sm:text-base">Учасники ще не розподілені.</p>
		@endif
	</div>
@endsection