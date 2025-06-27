<!DOCTYPE html>
<html lang="uk">
<head>
	<meta charset="UTF-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<title>404 - Сторінку не знайдено</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<style>
		body {
			background-image: url('/media/palatka.jpg');
			background-size: cover;
			background-position: center;
			background-repeat: no-repeat;
			background-color: #000;
		}
		@media (max-width: 768px) {
			body {
				background-image: url('/media/back_ground.jpg');
			}
		}
	</style>
</head>
<body class="text-white flex items-center justify-center min-h-screen">
	<div class="backdrop-blur-xl bg-white/10 rounded-2xl px-6 py-10 md:px-12 md:py-14 text-center shadow-2xl w-full max-w-lg mx-4">
		<h1 class="text-5xl md:text-7xl font-bold mb-4">404</h1>
		<h2 class="text-xl md:text-2xl font-semibold mb-6">Сторінку не знайдено</h2>
		<p class="text-base md:text-lg mb-8">Вибачте, але сторінка, яку ви шукаєте, не існує або була переміщена.</p>
		<a href="/" class="inline-block bg-white text-gray-800 font-semibold py-2 px-6 rounded-lg hover:bg-gray-200 transition duration-300">Повернутися на головну</a>
	</div>
</body>
</html>