<!DOCTYPE html>
<html lang="uk">
<head>
	<meta charset="UTF-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<title>Таймер до табору</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<style>
		body {
			background-image: url('/palatka.jpg');
			background-size: cover;
			background-position: center;
			background-repeat: no-repeat;
			background-color: #000;
		}
		@media (max-width: 768px) {
			body {
				background-image: url('/back_ground.jpg');
			}
		}
		.timer-colon {
			display: flex;
			align-items: center;
			height: 100%;
			margin-top: -1.5rem;
		}
	</style>
</head>
<body class="text-white">
	<div class="flex items-start justify-center min-h-screen md:items-center pt-12 md:pt-0">
		<div class="backdrop-blur-xl bg-white/10 rounded-2xl px-6 py-10 md:px-12 md:py-14 text-center shadow-2xl w-full max-w-xl mx-4">
			<h1 class="text-2xl md:text-4xl font-bold mb-8">До табору залишилось:</h1>

			<div id="timer" class="flex justify-center items-center gap-2 md:gap-4 text-xl md:text-3xl font-mono tracking-wider flex-wrap">
				<div class="flex flex-col items-center min-w-[60px]">
					<div id="days" class="text-4xl md:text-5xl">00</div>
					<div class="text-xs mt-1">днів</div>
				</div>
				<div class="timer-colon text-3xl md:text-5xl">:</div>
				<div class="flex flex-col items-center min-w-[60px]">
					<div id="hours" class="text-4xl md:text-5xl">00</div>
					<div class="text-xs mt-1">год</div>
				</div>
				<div class="timer-colon text-3xl md:text-5xl">:</div>
				<div class="flex flex-col items-center min-w-[60px]">
					<div id="minutes" class="text-4xl md:text-5xl">00</div>
					<div class="text-xs mt-1">хв</div>
				</div>
				<div class="timer-colon text-3xl md:text-5xl">:</div>
				<div class="flex flex-col items-center min-w-[60px]">
					<div id="seconds" class="text-4xl md:text-5xl">00</div>
					<div class="text-xs mt-1">сек</div>
				</div>
			</div>
		</div>
	</div>

	@php
		use Illuminate\Support\Carbon;

		date_default_timezone_set('Europe/Kyiv');

		$first = Carbon::parse('2025-07-06 17:00:00', 'Europe/Kyiv');
		$second = Carbon::parse('2025-07-13 17:00:00', 'Europe/Kyiv');
		$third = Carbon::parse('2026-07-12 17:00:00', 'Europe/Kyiv');

		$now = Carbon::now('Europe/Kyiv');

		if (isset($first) && $now->lt($first)) {
			$date = $first->toIso8601String();
		} elseif ($now->lt($second)) {
			$date = $second->toIso8601String();
		} else {
			$date = $third->toIso8601String();
		}
	@endphp

	<script>
		const targetDate = new Date("{{ $date }}");

		function updateTimer() {
			const now = new Date();
			const diff = targetDate - now;

			if (diff <= 0) {
				document.getElementById("timer").innerHTML = "<div class='text-xl md:text-3xl font-semibold'>Час настав!</div>";
				return;
			}

			const days = Math.floor(diff / (1000 * 60 * 60 * 24));
			const hours = Math.floor((diff / (1000 * 60 * 60)) % 24);
			const minutes = Math.floor((diff / (1000 * 60)) % 60);
			const seconds = Math.floor((diff / 1000) % 60);

			document.getElementById("days").textContent = String(days).padStart(2, '0');
			document.getElementById("hours").textContent = String(hours).padStart(2, '0');
			document.getElementById("minutes").textContent = String(minutes).padStart(2, '0');
			document.getElementById("seconds").textContent = String(seconds).padStart(2, '0');
		}

		updateTimer();
		setInterval(updateTimer, 1000);
	</script>
</body>
</html>