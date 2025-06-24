@extends('layouts.app')

@section('content')
	<div class="max-w-2xl mx-auto mb-16 p-6 bg-white rounded-lg shadow-lg">
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
{{--					<div class="w-32 h-32 bg-gray-200 flex items-center justify-center text-gray-500 text-base font-semibold mx-auto">--}}
{{--						Без фото--}}
{{--					</div>--}}
				@endif
			</div>
		</div>

		<form method="POST" action="{{ route('member.store', ['code' => $code]) }}"
				enctype="multipart/form-data">
			@csrf

			@include('sections.main')
			@include('sections.contacts')
			@include('sections.physical')
			@include('sections.medical')
			@include('sections.mental')
			@include('sections.creative')
			@include('sections.intellect')


			<button type="submit"
					class="block mx-auto mt-12 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 text-lg font-semibold
                           transition duration-300 shadow-md hover:shadow-lg">
				Зберегти
			</button>
		</form>
	</div>
@endsection

@push('scripts')
	<script>
		// Скрипти поки не потрібні
	</script>
@endpush