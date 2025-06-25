<?php

namespace App\Services;

use Illuminate\Support\Collection;

class SquadDistributionService
{
	public function distribute(Collection $members, int $squadCount): array
	{
		if ($members->isEmpty() || $squadCount < 1 || $squadCount > $members->count()) {
			return array_fill(0, $squadCount, []);
		}

		$now = now();
		// Підготовка даних
		$processed = $members->map(fn($m) => (object)[
			'member' => $m,
			'age' => $m->birth_date ? $now->diffInYears($m->birth_date) : 0,
			'gender' => $m->gender ?? 'unknown',
			'residence_type' => $m->residence_type ?? 'unknown',
			'physical_score' => $m->physical_score ?? 0,
			'mental_score' => $m->mental_score ?? 0,
		])->shuffle();

		// Визначення розмірів загонів
		$total = $members->count();
		$baseSize = intdiv($total, $squadCount);
		$extra = $total % $squadCount;
		$targets = array_map(fn($i) => $baseSize + ($i < $extra ? 1 : 0), range(0, $squadCount - 1));

		// Початковий розподіл
		$squads = array_fill(0, $squadCount, []);
		$index = 0;
		$forward = true;

		// Розподіл non-stationary (сортуємо за віком)
		$nonStationary = $processed->filter(fn($p) => $p->residence_type === 'non-stationary')->sortBy('age');
		foreach ($nonStationary as $data) {
			while (count($squads[$index]) >= $targets[$index]) {
				$index = $forward ? $index + 1 : $index - 1;
				if ($index >= $squadCount || $index < 0) {
					$forward = !$forward;
					$index = $forward ? 0 : $squadCount - 1;
				}
			}
			$squads[$index][] = $data->member;
			$index = $forward ? $index + 1 : $index - 1;
			if ($index >= $squadCount || $index < 0) {
				$forward = !$forward;
				$index = $forward ? 0 : $squadCount - 1;
			}
		}

		// Розподіл решти (групуємо за гендером і віком)
		$others = $processed->reject(fn($p) => $p->residence_type === 'non-stationary')
			->groupBy('gender')->map->sortBy('age')->flatten(1);
		foreach ($others as $data) {
			while (count($squads[$index]) >= $targets[$index]) {
				$index = $forward ? $index + 1 : $index - 1;
				if ($index >= $squadCount || $index < 0) {
					$forward = !$forward;
					$index = $forward ? 0 : $squadCount - 1;
				}
			}
			$squads[$index][] = $data->member;
			$index = $forward ? $index + 1 : $index - 1;
			if ($index >= $squadCount || $index < 0) {
				$forward = !$forward;
				$index = $forward ? 0 : $squadCount - 1;
			}
		}

		// Балансування (до 3 обмінів)
		for ($swap = 0; $swap < 3; ++$swap) {
			$stats = $this->stats($squads);
			if (
				$stats['variances']['age'] <= 0.5 &&
				$stats['variances']['male_count'] <= 0.05 &&
				$stats['variances']['non_stationary_count'] <= 0.05 &&
				$stats['variances']['physical'] <= 100 &&
				$stats['variances']['mental'] <= 100
			) {
				break;
			}
			$bestSwap = null;
			$bestScore = INF;
			for ($i = 0; $i < $squadCount - 1; ++$i) {
				for ($j = 0; $j < count($squads[$i]); ++$j) {
					for ($k = $i + 1; $k < $squadCount; ++$k) {
						for ($l = 0; $l < count($squads[$k]); ++$l) {
							$temp = $squads;
							[$temp[$i][$j], $temp[$k][$l]] = [$temp[$k][$l], $temp[$i][$j]];
							$newStats = $this->stats($temp);
							$score = $newStats['variances']['age'] * 100 +
								$newStats['variances']['male_count'] * 100 +
								$newStats['variances']['non_stationary_count'] * 100 +
								$newStats['variances']['physical'] * 10 +
								$newStats['variances']['mental'] * 10;
							if ($score < $bestScore) {
								$bestScore = $score;
								$bestSwap = [$i, $j, $k, $l];
							}
						}
					}
				}
			}
			if (!$bestSwap) {
				\Log::warning('No swap found', ['stats' => $stats['variances']]);
				break;
			}
			[$i, $j, $k, $l] = $bestSwap;
			[$squads[$i][$j], $squads[$k][$l]] = [$squads[$k][$l], $squads[$i][$j]];
		}

		// Логування для діагностики
		$stats = $this->stats($squads);
		\Log::info('Distribution stats', ['variances' => $stats['variances'], 'sizes' => array_map('count', $squads)]);

		return $squads;
	}

	private function stats(array $squads): array
	{
		$means = array_map(function ($squad) {
			$count = count($squad) ?: 1;
			$ages = array_filter(array_map(fn($m) => $m->birth_date ? now()->diffInYears($m->birth_date) : 0, $squad));
			return [
				'age' => $ages ? array_sum($ages) / count($ages) : 0,
				'male_count' => count(array_filter($squad, fn($m) => $m->gender === 'male')) / $count,
				'non_stationary_count' => count(array_filter($squad, fn($m) => $m->residence_type === 'non-stationary')) / $count,
				'physical' => array_sum(array_map(fn($m) => $m->physical_score ?? 0, $squad)) / $count,
				'mental' => array_sum(array_map(fn($m) => $m->mental_score ?? 0, $squad)) / $count,
			];
		}, $squads);

		$variances = [];
		foreach (['age', 'male_count', 'non_stationary_count', 'physical', 'mental'] as $key) {
			$avg = array_sum(array_column($means, $key)) / count($means);
			$variances[$key] = count($means) <= 1 ? 0 : array_sum(array_map(
					fn($m) => ($m[$key] - $avg) ** 2,
					$means
				)) / count($means);
		}

		return ['means' => $means, 'variances' => $variances];
	}
}
