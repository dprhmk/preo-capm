@if ( ! empty($analyticsData))
	@push('head-scripts')
		<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
	@endpush

		<h2 class="text-base sm:text-lg font-semibold text-gray-800 mb-2 sm:mb-3 flex items-center">
			<span class="mr-2">📊</span> Аналітика загонів
		</h2>

		<div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-16">
			<!-- Стовпчиковий графік: Вікові групи -->
			<div class="bg-white p-4 rounded-lg shadow-sm overflow-hidden">
				<h3 class="text-xs sm:text-sm font-semibold text-gray-600 mb-1">Розподіл за віком</h3>
				<canvas id="ageChart" class="w-full"></canvas>
			</div>

			<!-- Стовпчиковий графік: Бали загонів -->
			<div class="bg-white p-4 rounded-lg shadow-sm overflow-hidden">
				<h3 class="text-xs sm:text-sm font-semibold text-gray-600 mb-1">Бали загонів</h3>
				<canvas id="scoresChart" class="w-full"></canvas>
			</div>

			<!-- Кругові графіки: Гендер -->
			<div class="bg-white p-4 rounded-lg shadow-sm overflow-hidden">
				<h3 class="text-xs sm:text-sm font-semibold text-gray-600 mb-2">Розподіл за гендером</h3>
				<div class="grid grid-cols-3 gap-2">
					@foreach ($analyticsData['labels'] as $index => $label)
						<div class="relative">
							<h4 class="text-xs font-medium text-gray-600 text-center mb-1">{{ $label }}</h4>
							<canvas id="genderChart{{ $index }}" class="w-full" style="height: 3.5rem; max-height: 3.5rem; min-height: 3.5rem;"></canvas>
						</div>
					@endforeach
					@for ($i = count($analyticsData['labels']); $i < 6; $i++)
						<div class="relative"></div>
					@endfor
				</div>
				<div class="flex justify-center mt-2">
					<div class="flex items-center space-x-4">
						<div class="flex items-center">
							<span class="w-3 h-3 rounded-full bg-pink-400 mr-1"></span>
							<span class="text-xs text-gray-600">Жінки</span>
						</div>
						<div class="flex items-center">
							<span class="w-3 h-3 rounded-full bg-blue-500 mr-1"></span>
							<span class="text-xs text-gray-600">Чоловіки</span>
						</div>
					</div>
				</div>
			</div>

			<!-- Стовпчиковий графік: Non-stationary учасники -->
			<div class="bg-white p-4 rounded-lg shadow-sm overflow-hidden">
				<h3 class="text-xs sm:text-sm font-semibold text-gray-600 mb-1">Не стаціонарні учасники (Загінецькі)</h3>
				<canvas id="nonStationaryChart" class="w-full"></canvas>
			</div>

			<!-- Круговий графік: Розподіл учасників -->
			<div class="bg-white p-4 rounded-lg shadow-sm overflow-hidden">
				<h3 class="text-xs sm:text-sm font-semibold text-gray-600 mb-1">Розподіл учасників</h3>
				<canvas id="memberChart" class="w-full"></canvas>
			</div>

		</div>


	@push('scripts')
		<script>
			Chart.register(ChartDataLabels);

			const colors = [
				'rgba(75, 192, 192, 0.8)',  // Бірюзовий
				'rgba(255, 99, 132, 0.8)',  // Рожевий
				'rgba(54, 162, 235, 0.8)',  // Синій
				'rgba(255, 206, 86, 0.8)',  // Жовтий
				'rgba(153, 102, 255, 0.8)', // Фіолетовий
				'rgba(255, 159, 64, 0.8)'   // Помаранчевий
			];

			// Стовпчиковий графік: Non-stationary учасники
			new Chart(document.getElementById('nonStationaryChart'), {
				type: 'bar',
				data: {
					labels: @json($analyticsData['labels']),
					datasets: [{
						label: 'Не стаціонарні',
						data: @json($analyticsData['non_stationary_counts']),
						backgroundColor: 'rgba(153, 102, 255, 0.8)',
						barThickness: window.innerWidth < 640 ? 8 : 16,
					}]
				},
				options: {
					plugins: {
						legend: {
							display: false,
							position: 'bottom',
						},
						datalabels: {
							font: {
								size: 16
							}
						}
					}
				}
			});

			// Круговий графік: Розподіл учасників
			new Chart(document.getElementById('memberChart'), {
				type:    'pie',
				data:    {
					labels: @json($analyticsData['labels']),
					datasets: [{
						data: @json($analyticsData['member_counts']),
						backgroundColor: colors.slice(0, @json(count($analyticsData['labels']))),
					}]
				},
				options: {
					plugins: {
						legend:     {
							position: 'bottom',
						},
						datalabels: {
							font: {
								size: 18
							}
						}
					}
				}
			});

			// Стовпчиковий графік: Бали загонів
			new Chart(document.getElementById('scoresChart'), {
				type:    'bar',
				data:    {
					labels: @json($analyticsData['labels']),
					datasets: [
						{
							label:           'Фізична підготовка',
							data: @json($analyticsData['physical_scores']),
							backgroundColor: 'rgba(75, 192, 192, 0.8)',
							barThickness:    window.innerWidth < 640 ? 8 : 16,
						},
						{
							label:           'Ментальна креативність',
							data: @json($analyticsData['mental_scores']),
							backgroundColor: 'rgba(54, 162, 235, 0.8)',
							barThickness:    window.innerWidth < 640 ? 8 : 16,
						}
					]
				},
				options: {
					plugins: {
						legend:     {
							display:  true,
							position: 'bottom',
						},
						datalabels: {
							font: {
								size: 5
							}
						}
					}
				}
			});

			// Стовпчиковий графік: Вікові групи
			new Chart(document.getElementById('ageChart'), {
				type:    'bar',
				data:    {
					labels: @json($analyticsData['labels']),
					datasets: [
						{
							label:           '11-12',
							data: @json(array_column($analyticsData['age_groups'], '11-12')),
							backgroundColor: 'rgba(75, 192, 192, 0.8)',
						},
						{
							label:           '13-14',
							data: @json(array_column($analyticsData['age_groups'], '13-14')),
							backgroundColor: 'rgba(255, 99, 132, 0.8)',
						},
						{
							label:           '15-16',
							data: @json(array_column($analyticsData['age_groups'], '15-16')),
							backgroundColor: 'rgba(54, 162, 235, 0.8)',
						},
						{
							label:           '17-18',
							data: @json(array_column($analyticsData['age_groups'], '17-18')),
							backgroundColor: 'rgba(255, 206, 86, 0.8)',
						}
					]
				},
				options: {
					plugins: {
						legend: {
							display:  true,
							position: 'bottom',
						}
					}
				}
			});

			// Кругові графіки: Гендер
			@foreach ($analyticsData['gender_percentages'] as $index => $gender)
			new Chart(document.getElementById('genderChart{{ $index }}'), {
				type:    'pie',
				data:    {
					labels:   [],
					datasets: [{
						data:            [@json($gender['male']), @json($gender['female'])],
						backgroundColor: ['rgba(54, 162, 235, 0.8)', 'rgba(255, 99, 132, 0.8)'],
					}]
				},
				options: {
					plugins: {
						legend:     {display: false},
						datalabels: {
							font: {
								size: 10
							}
						}
					}
				}
			});
			@endforeach
		</script>
	@endpush

@endif

