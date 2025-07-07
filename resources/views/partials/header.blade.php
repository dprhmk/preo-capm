@auth
	<header class="py-4">
		<div class="max-w-4xl mx-auto p-6 backdrop-blur-xl bg-white/70 rounded-lg shadow-lg">
			<div class="flex justify-between items-center">
				<!-- Лівий блок: База та роль -->
				<div class="flex items-center space-x-2">
					<span class="text-lg font-medium text-gray-700">База:</span>
					<span class="text-lg font-bold text-blue-600">{{ auth()->user()->role }}</span>
				</div>

				<!-- Правий блок: Навігація та бургер-меню -->
				<div class="flex items-center">
					<!-- Навігація для десктопу -->
					<nav class="hidden sm:flex items-center space-x-4">
						<a href="{{ route('qr.scan') }}"
								class="text-blue-600 font-medium hover:text-blue-800 transition hover:underline duration-200">
							Створити
						</a>
						<a href="{{ route('members.index') }}"
								class="text-blue-600 font-medium hover:text-blue-800 transition hover:underline duration-200">
							Список учасників
						</a>
						<a href="{{ route('contacts.index') }}"
								class="text-blue-600 font-medium hover:text-blue-800 transition hover:underline duration-200">
							Контакти
						</a>
						<a href="{{ route('squads.index') }}"
								class="text-blue-600 font-medium hover:text-blue-800 transition hover:underline duration-200">
							Список загонів
						</a>
						<form method="POST" action="{{ route('logout') }}" class="inline">
							@csrf
							<button type="submit"
									class="text-red-600 font-medium hover:text-red-800 transition hover:underline duration-200">
								Вийти
							</button>
						</form>
					</nav>

					<!-- Бургер-кнопка для мобільних -->
					<button id="burger-button" class="sm:hidden text-gray-700 focus:outline-none">
						<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
						</svg>
					</button>
				</div>
			</div>

			<!-- Випадаюче меню для мобільних -->
			<div id="burger-menu" class="hidden ">
				<nav class="flex flex-col p-4 space-y-6">
					<a href="{{ route('qr.scan') }}"
							class="text-blue-600 font-medium hover:text-blue-800 transition hover:underline duration-200">
						Створити
					</a>
					<a href="{{ route('members.index') }}"
							class="text-blue-600 font-medium hover:text-blue-800 transition hover:underline duration-200">
						Список учасників
					</a>
					<a href="{{ route('contacts.index') }}"
							class="text-blue-600 font-medium hover:text-blue-800 transition hover:underline duration-200">
						Контакти
					</a>
					<a href="{{ route('squads.index') }}"
							class="text-blue-600 font-medium hover:text-blue-800 transition hover:underline duration-200">
						Список загонів
					</a>
					<form method="POST" action="{{ route('logout') }}">
						@csrf
						<button type="submit"
								class="text-red-600 font-medium hover:text-red-800 transition hover:underline duration-200 text-left">
							Вийти
						</button>
					</form>
				</nav>
			</div>
		</div>
	</header>

	@push('scripts')
		<script>
			document.getElementById('burger-button').addEventListener('click', () => {
				const menu = document.getElementById('burger-menu');
				menu.classList.toggle('hidden');
			});
		</script>
	@endpush
@endauth