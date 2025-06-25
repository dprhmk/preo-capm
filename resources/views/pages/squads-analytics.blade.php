@extends('layouts.app')

@section('content')
	<div class="w-full max-w-4xl mx-auto p-2 sm:p-4 bg-white rounded-b-lg shadow-lg">
		<h1 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6 flex items-center">
			<span class="mr-2">📊</span> Аналітика загонів
		</h1>

		@if (!empty($squads))
			<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
				<!-- Круговий графік: Розподіл учасників -->
				<div class="bg-gray-50 p-4 rounded-lg">
					<h3 class="text-base sm:text-lg font-semibold text-gray-700 mb-2 sm:mb-4">Розподіл учасників</h3>
					<canvas id="memberCountChart" class="w-full h-48 sm:h-64"></canvas>
				</div>

				<!-- Стовпчиковий графік: Фізична підготовка -->
				<div class="bg-gray-50 p-4 rounded-lg">
					<h3 class="text-base sm:text-lg font-semibold text-gray-700 mb-2 sm:mb-4">Фізична підготовка</h3>
					<canvas id="physicalScoreChart" class="w-full h-48 sm:h-64"></canvas>
				</div>

				<!-- Стовпчиковий графік: Ментальна креативність -->
				<div class="bg-gray-50 p-4 rounded-lg">
					<h3 class="text-base sm:text-lg font-semibold text-gray-700 mb-2 sm:mb-4">Ментальна креативність</h3>
					<canvas id="mentalScoreChart" class="w-full h-48 sm:h-64"></canvas>
				</div>

				<!-- Стовпчиковий графік: Середній вік -->
				<div class="bg-gray-50 p-4 rounded-lg">
					<h3 class="text-base sm:text-lg font-semibold text-gray-700 mb-2 sm:mb-4">Середній вік</h3>
					<canvas id="ageChart" class="w-full h-48 sm:h-64"></canvas>
				</div>

				<!-- Стовпчиковий графік: Стать -->
				<div class="bg-gray-50 p-4 rounded-lg">
					<h3 class="text-base sm:text-lg font-semibold text-gray-700 mb-2 sm:mb-4">Розподіл за статтю</h3>
					<canvas id="genderChart" class="w-full h-48 sm:h-64"></canvas>
				</div>
			</div>

			<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
			<script>
				// Кольори для загонів
				const colors = [
					'rgba(75, 192, 192, 0.8)',
					'rgba(255, 99, 132, 0.8)',
					'rgba(54, 162, 235, 0.8)',
					'rgba(255, 206, 86, 0.8)',
					'rgba(153, 102, 255, 0.8)',
					'rgba(255, 159, 64, 0.8)'
				];

				// Круговий графік: Розподіл учасників
				new Chart(document.getElementById('memberCountChart'), {
					type: 'pie',
					data: {
						labels: @json($analyticsData['labels']),
						datasets: [{
							data: @json($analyticsData['member_counts']),
							backgroundColor: colors.slice(0, @json(count($analyticsData['labels']))),
						}]
					},
					options: {
						responsive: true,
						maintainAspectRatio: false,
						plugins: {
							legend: {
								position: 'bottom',
								labels: { font: { size: window.innerWidth < 640 ? 10 : 12 } }
							}
						}
					}
				});

				// Стовпчиковий графік: Фізична підготовка
				new Chart(document.getElementById('physicalScoreChart'), {
					type: 'bar',
					data: {
						labels: @json($analyticsData['labels']),
						datasets: [{
							label: 'Фізична підготовка',
							data: @json($analyticsData['physical_scores']),
							backgroundColor: colors.slice(0, @json(count($analyticsData['labels']))),
						}]
					},
					options: {
						responsive: true,
						maintainAspectRatio: false,
						plugins: {
							legend: { display: false },
							tooltip: { enabled: true }
						},
						scales: {
							y: { beginAtZero: true, max: 100, title: { display: true, text: 'Бали', font: { size: window.innerWidth < 640 ? 10 : 12 } } }
						}
					}
				});

				// Стовпчиковий графік: Ментальна креативність
				new Chart(document.getElementById('mentalScoreChart'), {
					type: 'bar',
					data: {
						labels: @json($analyticsData['labels']),
						datasets: [{
							label: 'Ментальна креативність',
							data: @json($analyticsData['mental_scores']),
							backgroundColor: colors.slice(0, @json(count($analyticsData['labels']))),
						}]
					},
					options: {
						responsive: true,
						maintainAspectRatio: false,
						plugins: {
							legend: { display: false },
							tooltip: { enabled: true }
						},
						scales: {
							y: { beginAtZero: true, max: 100, title: { display: true, text: 'Бали', font: { size: window.innerWidth < 640 ? 10 : 12 } } }
						}
					}
				});

				// Стовпчиковий графік: Середній вік
				new Chart(document.getElementById('ageChart'), {
					type: 'bar',
					data: {
						labels: @json($analyticsData['labels']),
						datasets: [{
							label: 'Середній вік',
							data: @json($analyticsData['avg_ages']),
							backgroundColor: colors.slice(0, @json(count($analyticsData['labels']))),
						}]
					},
					options: {
						responsive: true,
						maintainAspectRatio: false,
						plugins: {
							legend: { display: false },
							tooltip: { enabled: true }
						},
						scales: {
							y: { beginAtZero: true, max: 20, title: { display: true, text: 'Вік', font: { size: window.innerWidth < 640 ? 10 : 12 } } }
						}
					}
				});

				// Стовпчиковий графік: Стать
				new Chart(document.getElementById('genderChart'), {
					type: 'bar',
					data: {
						labels: @json($analyticsData['labels']),
						datasets: [
							{
								label: 'Чоловіки',
								data: @json($analyticsData['male_counts']),
								backgroundColor: 'rgba(54, 162, 235, 0.8)',
								stack: 'Stack 0',
							},
							{
								label: 'Жінки',
								data: @json($analyticsData['female_counts']),
								backgroundColor: 'rgba(255, 99, 132, 0.8)',
								stack: 'Stack 0',
							}
						]
					},
					options: {
						responsive: true,
						maintainAspectRatio: false,
						plugins: {
							legend: { position: 'bottom', labels: { font: { size: window.innerWidth < 640 ? 10 : 12 } } },
							tooltip: { enabled: true }
						},
						scales: {
							x: { stacked: true },
							y: { stacked: true, beginAtZero: true, title: { display: true, text: 'Кількість', font: { size: window.innerWidth < 640 ? 10 : 12 } } }
						}
					}
				});
			</script>
		@else
			<p class="text-gray-500 text-center text-sm sm:text-base">Розподіліть учасників, щоб побачити аналітику.</p>
		@endif
	</div>
@endsection
