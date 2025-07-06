@php use Illuminate\Support\Carbon; @endphp
@extends('layouts.app')

@php
	$month = 7;
	$first = Carbon::parse('2025-07-06 17:00:00', 'Europe/Kyiv');
	$second = Carbon::parse('2025-07-13 17:00:00', 'Europe/Kyiv');
	$now = Carbon::now('Europe/Kyiv');

	$currentWeekStart = null;
	$currentWeekDays = [];

	if ($now->between($first, $first->copy()->addDays(6))) {
		$currentWeekStart = $first;
		$currentWeekDays = range(6, 12);
	} elseif ($now->between($second, $second->copy()->addDays(6))) {
		$currentWeekStart = $second;
		$currentWeekDays = range(13, 19);
	}
@endphp

@section('content')
	<div class="max-w-4xl mx-auto mb-16 p-6 backdrop-blur-xl bg-white/70 rounded-lg shadow-lg">
		<h1 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
			<span class="mr-2">📋</span> Список учасників
		</h1>

		@if ($members->isEmpty())
			<p class="text-gray-500 text-center">Учасників не знайдено.</p>
		@else
			<div class="overflow-x-auto rounded-lg">
				<table class="w-full text-sm text-left text-gray-700">
					<thead class="text-xs text-gray-600">
					<tr>
						<th scope="col" class="px-4 py-2">N°</th>
						<th scope="col" class="px-4 py-2">Ім’я</th>
						<th scope="col" class="px-4 py-2">Дата народження</th>
						<th scope="col" class="px-4 py-2">Дії</th>
					</tr>
					</thead>
					<tbody>
					@foreach ($members as $member)
						@php
							$birthDate = $member->birth_date ? Carbon::parse($member->birth_date) : null;
							$isBirthdayWeek = $birthDate &&
								$birthDate->month == $month &&
								in_array($birthDate->day, $currentWeekDays);
						@endphp
						<tr class="border-b {{ $member->isRequiredFilled === false ? 'bg-red-400' : '' }}">
							<td class="px-4 py-2">{{ $loop->iteration }}</td>
							<td class="px-4 py-2">{{ $member->full_name ?? 'Невідомо' }}</td>
							<td class="px-4 py-2 {{ $isBirthdayWeek ? 'bg-green-400 rounded-lg' : '' }}">
								{{ $birthDate ? $birthDate->format('d.m.Y') : '-' }} {{ $isBirthdayWeek ? '🎂' : '' }}
							</td>
							<td class="px-4 py-2">
								<a href="{{ route('member.edit', $member->code) }}"
										class="text-blue-600 hover:text-blue-800 transition">Редагувати</a>
							</td>
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>
		@endif
	</div>

	@push('scripts')
		<script>
			console.log('Members table loaded');
		</script>
	@endpush
@endsection
