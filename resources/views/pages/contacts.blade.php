@extends('layouts.app')

@section('content')
	<div class="max-w-4xl mx-auto mb-16 p-4 backdrop-blur-xl bg-white/70 rounded-lg shadow-lg">
		<h1 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
			<span class="mr-2">📞</span> Контакти
		</h1>

		<!-- Виводимо повідомлення про успіх -->
		@if (session('success'))
			<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
				{{ session('success') }}
			</div>
		@endif

		<!-- Результати розподілу -->
		@if ($squads->isNotEmpty())
			<div class="space-y-4 sm:space-y-6">
				@foreach ($squads as $squad)
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
									<th class="px-2 py-1 sm:px-3 sm:py-2">Телефон</th>
									<th class="px-2 py-1 sm:px-3 sm:py-2">Ім’я опікуна</th>
									<th class="px-2 py-1 sm:px-3 sm:py-2 sm:table-cell">Телефон опікуна</th>
									<th class="px-2 py-1 sm:px-4 sm:py-2 sm:table-cell">Дії</th>
								</tr>
								</thead>
								<tbody>
								@foreach ($squad->members as $member)
									<tr class="border-b {{ $member->is_required_filled === false ? 'bg-red-400' : '' }}">
										<td class="px-2 py-1 sm:px-3 sm:py-2">{{ $loop->iteration }}</td>
										<td class="px-2 py-1 sm:px-3 sm:py-2">{{ $member->full_name ?? 'Невідомо' }}</td>
										<td class="px-2 py-1 sm:px-3 sm:py-2">{{ $member?->child_phone ?? '-' }}</td>
										<td class="px-2 py-1 sm:px-3 sm:py-2">{{ $member?->guardian_name ?? 'Невідомо' }}</td>
										<td class="px-2 py-1 sm:px-3 sm:py-2">{{ $member?->parent_phone ?? '-' }}</td>
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