@extends('layouts.app')

@section('content')
	<div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-lg">
		<h1 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
			<span class="mr-2">📋</span> Список учасників
		</h1>

		@if ($members->isEmpty())
			<p class="text-gray-500 text-center">Учасників не знайдено.</p>
		@else
			<div class="overflow-x-auto">
				<table class="w-full text-sm text-left text-gray-700">
					<thead class="text-xs uppercase bg-gray-100 text-gray-600">
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
							$birthDate = $member->birth_date ? \Carbon\Carbon::parse($member->birth_date) : null;
							$isBirthdayWeek = $birthDate &&
								$birthDate->month == 7 &&
								$birthDate->day >= 13 &&
								$birthDate->day <= 19;
						@endphp
						<tr class="border-b">
							<td class="px-4 py-2">{{ $loop->iteration }}</td>
							<td class="px-4 py-2">{{ $member->full_name ?? 'Невідомо' }}</td>
							<td class="px-4 py-2 {{ $isBirthdayWeek ? 'bg-green-400' : '' }}">
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
