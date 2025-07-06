@php
	use Illuminate\Support\Facades\Cache;

	$gifs = [
		'/media/gifs/astanavites.gif',
		'/media/gifs/gif1.gif',
		'/media/gifs/o-rocha-andre.gif',
		'/media/gifs/isho.gif',
		'/media/gifs/goose-geese.gif',
		'/media/gifs/ukraine-yanukovich.gif',
		'/media/gifs/monkey.gif',
		'/media/gifs/pig.gif',
	];

	// Отримати поточний індекс із кешу (або 0, якщо його ще нема)
	$index = Cache::get('global_gif_index', 0);

	// Показуємо gif по цьому індексу
	$gifToShow = $gifs[$index];

	// Обчислюємо наступний індекс
	$nextIndex = ($index + 1) % count($gifs);

	// Оновлюємо кеш з новим індексом (на 10 років)
	Cache::put('global_gif_index', $nextIndex, now()->addYears(10));
@endphp

		<!DOCTYPE html>
<html lang="uk">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="csrf-token" content="{{ csrf_token() }}" />
	<title>Табір 2025</title>

	<style>
		body {
			background-color: #000000;
			margin: 0;
			padding: 0;
			overflow: hidden;
		}
	</style>

	@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
	<img src="{{ $gifToShow }}" class="w-full object-contain rounded-xl mt-16 sm:h-screen sm:mt-0">
</body>
</html>
