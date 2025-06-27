<!DOCTYPE html>
<html lang="uk">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="csrf-token" content="{{ csrf_token() }}" />
	<title>Табір | @yield('title', 'Форма')</title>

	<style>
		body {
			/*background-image:    url('/palatka.jpg');*/
			/*background-attachment: fixed;*/
			/*background-size: cover;*/
			/*background-position: center;*/
			/*background-repeat:   no-repeat;*/
			background-color:    #d1d1d1;
		}
		/*@media (max-width: 768px) {*/
		/*	body {*/
		/*		background-image: url('/back_ground.jpg');*/
		/*	}*/
		/*}*/
	</style>

	@vite(['resources/css/app.css', 'resources/js/app.js'])

	@stack('head-scripts')
</head>
<body class="bg-gray-100">
	@include('partials.header')

	@yield('content')

	@stack('scripts')
</body>
</html>
