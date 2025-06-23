<!DOCTYPE html>
<html lang="uk">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="csrf-token" content="{{ csrf_token() }}" />
	<title>Табір | @yield('title', 'Форма')</title>

	@vite(['resources/css/app.css', 'resources/js/app.js'])

	@yield('head-scripts')
</head>
<body class="bg-gray-100 min-h-screen font-sans text-gray-800">
	<header class="bg-white shadow mb-6">
		<div class="max-w-4xl mx-auto flex justify-between items-center px-6 py-4">
			<div class="text-lg font-semibold">
				@auth
					Ваша база: <span class="text-blue-600 font-bold">{{ auth()->user()->role }}</span>
				@else
					<span class="text-gray-500">Неавторизований</span>
				@endauth
			</div>

			<nav class="space-x-4 text-sm">
				{{-- Майбутні посилання сюди --}}
				{{-- <a href="{{ route('section.main') }}" class="text-blue-600 hover:underline">Основна база</a> --}}

				@auth
					<form method="POST" action="{{ route('logout') }}" class="inline">
						@csrf
						<button type="submit" class="text-red-600 hover:underline">Вийти</button>
					</form>
				@endauth
			</nav>
		</div>
	</header>

	<div class="max-w-4xl mx-auto p-6">
		@yield('content')
	</div>

	@yield('scripts')
</body>
</html>
