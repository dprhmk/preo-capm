@extends('layouts.app')

@push('head-scripts')
	<script src="https://unpkg.com/html5-qrcode"></script>
@endpush

@section('content')
	<div class="max-w-4xl mx-auto mb-16 p-6 backdrop-blur-xl bg-white/70 rounded-lg shadow-lg">

{{--		<h2 class="text-2xl font-bold mb-4">Сканування QR-коду</h2>--}}

		<div id="qr-reader" class="mx-auto mb-4" style="width: 300px;"></div>

		<p class="mb-4 text-gray-600">Наведіть камеру на QR-код, щоб перейти за посиланням.</p>

		<div class="mb-4">
			<select id="cameraSelect" class="w-full border rounded px-3 py-2"></select>
		</div>

		<!-- Виводимо повідомлення про успіх -->
		@if (session('success'))
			<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
				{{ session('success') }}
			</div>
		@endif

		<div id="qr-reader-results" class="mb-6 text-sm text-gray-500"></div>

		<hr class="my-6">

		<h3 class="text-lg font-semibold mb-2">Або введіть код вручну:</h3>
		<div class="flex gap-2 justify-center">
			<input id="manualCodeInput" type="text" placeholder="Введіть код" class="border rounded px-3 py-2">
			<button onclick="handleManualCode()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
				Перейти
			</button>
		</div>
		<div id="manual-error" class="text-red-600 mt-2 text-sm hidden">Будь ласка, введіть код.</div>
	</div>
@endsection

@push('scripts')
	<script>
		let html5QrCode;
		const resultContainer = document.getElementById("qr-reader-results");
		const cameraSelect = document.getElementById("cameraSelect");
		const qrReaderEl = document.getElementById("qr-reader");
		const config = { fps: 10, qrbox: 250 };
		const LS_KEY = 'preferred_camera_id';

		async function startScanner(cameraId) {
			if (!html5QrCode) {
				html5QrCode = new Html5Qrcode("qr-reader");
			} else {
				try {
					await html5QrCode.stop();
					await html5QrCode.clear();
				} catch (e) {
					console.warn("Failed to stop scanner", e);
				}
			}

			qrReaderEl.innerHTML = "";

			try {
				await html5QrCode.start(
					cameraId,
					config,
					decodedText => {
						if ("vibrate" in navigator) {
							navigator.vibrate(100);
						}
						html5QrCode.stop();
						window.location.href = decodedText;
					},
					error => {
						// optional error handling
					}
				);
			} catch (err) {
				console.error("Start error:", err);
				resultContainer.textContent = "❌ Не вдалося запустити сканер.";
			}
		}

		function handleManualCode() {
			const input = document.getElementById("manualCodeInput");
			const error = document.getElementById("manual-error");
			const code = input.value.trim();

			if (code === '') {
				error.classList.remove('hidden');
				return;
			}
			error.classList.add('hidden');


			const link = `{{ env('APP_URL') }}/member/${encodeURIComponent(code)}`;
			window.location.href = link;
		}

		document.addEventListener('DOMContentLoaded', () => {
			Html5Qrcode.getCameras().then(cameras => {
				if (!cameras.length) {
					cameraSelect.innerHTML = `<option disabled>Камеру не знайдено</option>`;
					return;
				}

				cameraSelect.innerHTML = '';
				cameras.forEach((camera, i) => {
					const option = document.createElement("option");
					option.value = camera.id;
					option.text = camera.label || `Камера ${i + 1}`;
					cameraSelect.appendChild(option);
				});

				const savedCameraId = localStorage.getItem(LS_KEY);
				const fallbackCameraId = cameras[cameras.length - 1].id;
				const selectedId = cameras.find(cam => cam.id === savedCameraId)?.id || fallbackCameraId;

				cameraSelect.value = selectedId;
				startScanner(selectedId);

				cameraSelect.addEventListener("change", (e) => {
					const selectedCameraId = e.target.value;
					localStorage.setItem(LS_KEY, selectedCameraId);
					startScanner(selectedCameraId);
				});
			}).catch(err => {
				console.error("Помилка отримання камер:", err);
				resultContainer.textContent = "❌ Камери не знайдено або доступ заборонено.";
			});
		});
	</script>
@endpush
