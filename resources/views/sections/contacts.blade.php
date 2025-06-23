@if(in_array( $role, ['admin', 'contacts']))
	@php
		$socialLinks = old('social_links', $member?->social_links ?? []);
	@endphp

	<hr class="my-6 mt-16">

	<h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center">
		<span class="mr-2">📞</span> Контактна інформація
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

	<!-- Телефон дитини -->
	<div class="mb-4">
		<label class="block font-medium text-gray-700">Телефон дитини</label>
		<input type="tel" inputmode="numeric" name="child_phone" value="{{ old('child_phone', $member?->child_phone) }}"
				class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
	                  @error('child_phone') border-red-500 @enderror">
		@error('child_phone')
		<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
		@enderror
	</div>

	<!-- Опікун -->
	<div class="mb-4">
		<label class="block font-medium text-gray-700">Ім’я опікуна</label>
		<input type="text" name="guardian_name" value="{{ old('guardian_name', $member?->guardian_name) }}"
				class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
	                  @error('guardian_name') border-red-500 @enderror">
		@error('guardian_name')
		<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
		@enderror
	</div>

	<!-- Телефон батьків -->
	<div class="mb-4">
		<label class="block font-medium text-gray-700">Телефон опікуна</label>
		<input type="tel" inputmode="numeric" name="parent_phone" value="{{ old('parent_phone', $member?->parent_phone) }}"
				class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
	                  @error('parent_phone') border-red-500 @enderror">
		@error('parent_phone')
		<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
		@enderror
	</div>

	<!-- Екстрений контакт -->
	<div class="mb-4">
		<label class="block font-medium text-gray-700">Додатковий телефон</label>
		<input type="tel" inputmode="numeric" name="additional_contact"
				value="{{ old('additional_contact', $member?->additional_contact) }}"
				class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
	                  @error('additional_contact') border-red-500 @enderror">
		@error('additional_contact')
		<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
		@enderror
	</div>

	<!-- Соціальні мережі -->
	<div class="mb-4">
		<label class="block font-medium text-gray-700">Соціальні мережі</label>
		<input type="text" name="social_links[instagram]" placeholder="Instagram"
				value="{{ $socialLinks['instagram'] ?? '' }}"
				class="w-full border rounded px-3 py-2 mb-2 focus:outline-none focus:ring-2 focus:ring-blue-500
	                  @error('social_links.instagram') border-red-500 @enderror">
		@error('social_links.instagram')
		<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
		@enderror
		<input type="text" name="social_links[telegram]" placeholder="Telegram"
				value="{{ $socialLinks['telegram'] ?? '' }}"
				class="w-full border rounded px-3 py-2 mb-2 focus:outline-none focus:ring-2 focus:ring-blue-500
	                  @error('social_links.telegram') border-red-500 @enderror">
		@error('social_links.telegram')
		<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
		@enderror
		<input type="text" name="social_links[other]" placeholder="Інше"
				value="{{ $socialLinks['other'] ?? '' }}"
				class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
	                  @error('social_links.other') border-red-500 @enderror">
		@error('social_links.other')
		<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
		@enderror
	</div>

	<!-- Адреса -->
	<div class="mb-4">
		<label class="block font-medium text-gray-700">Адреса проживання</label>
		<textarea name="address" rows="3"
				class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500
	                     @error('address') border-red-500 @enderror" required>{{ old('address', $member?->address) }}</textarea>
		@error('address')
		<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
		@enderror
	</div>

	@push('partial-scripts')
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