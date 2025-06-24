<!DOCTYPE html>
<html lang="uk">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="csrf-token" content="{{ csrf_token() }}" />
	<title>Табір | @yield('title', 'Форма')</title>

	@vite(['resources/css/app.css', 'resources/js/app.js'])

	@stack('head-scripts')
</head>
<body class="bg-gray-100">
	@include('partials.header')

	@yield('content')

	@stack('scripts')
</body>
</html>
