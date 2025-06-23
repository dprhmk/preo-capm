<header class="bg-gray-100 py-4">
	<div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-lg">
		<div class="flex justify-between items-center">
			<!-- Лівий блок: База та роль -->
			<div class="flex items-center space-x-2">
				@auth
					<span class="text-lg font-medium text-gray-700">База:</span>
					<span class="text-lg font-bold text-blue-600">{{ auth()->user()->role }}</span>
				@else
					<span class="text-lg text-gray-500">Неавторизований</span>
				@endauth
			</div>

			<div class="flex items-center space-x-4">
				@auth
					<nav class="flex items-center space-x-4">
						<a href="{{ route('qr.scan') }}"
								class="text-blue-600 font-medium hover:text-blue-800 transition hover:underline duration-200">
							Створити
						</a>
						<a href="{{ route('members.index') }}"
								class="text-blue-600 font-medium hover:text-blue-800 transition hover:underline duration-200">
							Переглянути список усіх
						</a>
						<form method="POST" action="{{ route('logout') }}" class="inline">
							@csrf
							<button type="submit"
									class="text-red-600 font-medium hover:text-red-800 transition hover:underline duration-200">
								Вийти
							</button>
						</form>
					</nav>
				@endauth
				{{-- Майбутні посилання сюди --}}
				{{-- <a href="{{ route('section.main') }}" class="text-blue-600 hover:text-blue-800 transition">Основна база</a> --}}
			</div>
		</div>
	</div>
</header>
