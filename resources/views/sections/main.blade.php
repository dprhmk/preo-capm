@if(in_array( $role, ['admin', 'main']))
	@push('head-scripts')

	@endpush

	<hr class="my-6 mt-16">

	<h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center">
		<span class="mr-2">📋</span> Основна інформація
	</h2>

	<!-- Фото -->
	<div class="mb-6">
		<label class="block font-medium text-gray-700 mb-1">Фото</label>
		<div class="flex items-center space-x-4">
			<button type="button" id="capture-photo"
					class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-300">
				📸 Зробити фото
			</button>
			<input type="file" accept="image/*" capture="environment" id="photo-upload" class="hidden">
			<input type="hidden" name="photo_base64" id="photo-base64">
		</div>
		<div id="photo-preview" class="mt-4">
			@if (old('photo_url') || $member?->photo_url)
				<img src="{{ old('photo_url') ?? $member->photo_url }}"
						class="max-w-[150px] rounded shadow-md">
			@endif
		</div>
		@error('photo_url')
			<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
		@enderror
	</div>

	<div class="mb-4">
		<label class="block font-medium text-gray-700 mb-1">Повне ім’я</label>
		<input type="text" name="full_name" value="{{ old('full_name', $member?->full_name) }}"
				class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
	                      @error('full_name') border-red-500 @enderror" required>
		@error('full_name')
			<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
		@enderror
	</div>
	<div class="mb-4">
		<label class="block font-medium text-gray-700 mb-1">Дата народження</label>
		<input type="date" name="birth_date" value="{{ old('birth_date', $member?->birth_date?->format('Y-m-d')) }}"
				class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
		                              @error('birth_date') border-red-500 @enderror" required>
		@error('birth_date')
			<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
		@enderror
	</div>
	<div class="mb-4">
		<label class="block font-medium">Стать</label>
		<select name="gender" class="w-full border rounded px-3 py-2 @error('gender') border-red-500 @enderror" required>
			<option value="">Не вказано</option>
			<option value="male" {{ (old('gender') ?? $member?->gender) == 'male' ? 'selected' : '' }}>Чоловіча</option>
			<option value="female" {{ (old('gender') ?? $member?->gender) == 'female' ? 'selected' : '' }}>Жіноча</option>
		</select>
		@error('gender')
		<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
		@enderror
	</div>
	<div class="mb-4">
		<label class="block font-medium">Тип проживання</label>
		<select name="residence_type" class="w-full border rounded px-3 py-2 @error('residence_type') border-red-500 @enderror" required>
			<option value="">Не вказано</option>
			<option value="stationary" {{ (old('residence_type') ?? $member?->residence_type) == 'stationary' ? 'selected' : '' }}>Стаціонар</option>
			<option value="non-stationary" {{ (old('residence_type') ?? $member?->residence_type) == 'non-stationary' ? 'selected' : '' }}>Загінецький</option>
		</select>
		@error('residence_type')
			<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
		@enderror
	</div>

	@if($squads->isNotEmpty())
		<div class="mb-4">
			<label class="block font-medium">Загін</label>
			<select name="squad_id" class="w-full border rounded px-3 py-2 @error('squad_id') border-red-500 @enderror">
				<option value="">Не вказано</option>
				@foreach($squads as $squad)
					<option value="{{ $squad->id }}" {{ (old('squad_id') ?? $member?->squad?->id) === $squad->id ? 'selected' : '' }}> {{ $squad->name }} | {{ $squad->leader_name }}</option>
				@endforeach
			</select>
			@error('squad_id')
				<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
			@enderror
		</div>

	@endif

	@push('scripts')
		<script>
			document.addEventListener('DOMContentLoaded', () => {
				const captureButton = document.getElementById('capture-photo');
				const photoInput = document.getElementById('photo-upload');
				const photoPreview = document.getElementById('photo-preview');
				const photoBase64Input = document.getElementById('photo-base64');

				captureButton.addEventListener('click', () => {
					photoInput.click();
				});

				photoInput.addEventListener('change', () => {
					const file = photoInput.files[0];
					if (file) {
						const reader = new FileReader();
						reader.onload = (e) => {
							const img = new Image();
							img.src = e.target.result;
							img.onload = () => {
								const canvas = document.createElement('canvas');
								const ctx = canvas.getContext('2d');
								const maxWidth = 640;
								const maxHeight = 480;
								let width = img.width;
								let height = img.height;

								if (width > height) {
									if (width > maxWidth) {
										height = height * (maxWidth / width);
										width = maxWidth;
									}
								} else {
									if (height > maxHeight) {
										width = width * (maxHeight / height);
										height = maxHeight;
									}
								}

								canvas.width = width;
								canvas.height = height;
								ctx.drawImage(img, 0, 0, width, height);

								const base64 = canvas.toDataURL('image/jpeg', 0.5); // Якість 0.5
								photoBase64Input.value = base64;
								photoPreview.innerHTML = `<img src="${base64}" class="max-w-[150px] rounded shadow-md">`;
							};
						};
						reader.readAsDataURL(file);
					}
				});
			});
		</script>
	@endpush
@endif