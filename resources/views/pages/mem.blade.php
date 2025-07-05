@php
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
	$index = session()->get('gif_index', 0);
	$nextIndex = ($index + 1) % count($gifs);
	session()->put('gif_index', $nextIndex);
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
	<img src="{{ $gifs[$index] }}" class="w-full object-contain rounded-xl mt-16 sm:h-screen sm:mt-0">
</body>
</html>
