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
		$processed = $members->map(fn($m) => (object)[
			'member' => $m,
			'age' => $m->birth_date ? $now->diffInYears($m->birth_date) : 0,
			'gender' => $m->gender ?? 'unknown',
			'residence_type' => $m->residence_type ?? 'unknown',
			'physical_score' => $m->physical_score ?? 0,
			'mental_score' => $m->mental_score ?? 0,
		])->shuffle();

		$total = $processed->count();
		$baseSize = intdiv($total, $squadCount);
		$extra = $total % $squadCount;
		$targets = array_map(fn($i) => $baseSize + ($i < $extra ? 1 : 0), range(0, $squadCount - 1));
		$squads = array_fill(0, $squadCount, []);
		$sizes = array_fill(0, $squadCount, 0);

		// Спочатку — рівномірно розподілити non-stationary по віку
		$nonStationary = $processed
			->where('residence_type', 'non-stationary')
			->sortBy('age')
			->values();

		foreach ($nonStationary as $i => $data) {
			$squadIndex = $i % $squadCount;
			$squads[$squadIndex][] = $data->member;
			$sizes[$squadIndex]++;
		}

		// Тепер — решта (сортовані за gender та age)
		$rest = $processed->reject(fn($p) => $p->residence_type === 'non-stationary')
			->groupBy('gender')
			->map(fn($group) => $group->sortBy('age')->values())
			->flatten(1)
			->shuffle(); // трохи рандому

		foreach ($rest as $data) {
			// Знайти загін з найменшим розміром (і ще не перевищив свій target)
			$candidates = array_filter(range(0, $squadCount - 1), fn($i) => $sizes[$i] < $targets[$i]);
			usort($candidates, fn($a, $b) => $sizes[$a] <=> $sizes[$b]);
			$best = $candidates[0];
			$squads[$best][] = $data->member;
			$sizes[$best]++;
		}

		// Опціональне балансування: до 5 обмінів, максимум 500 перевірок
		$maxChecks = 500;
		for ($swap = 0; $swap < 5; $swap++) {
			$stats = $this->stats($squads);
			if (
				$stats['variances']['age'] <= 0.5 &&
				$stats['variances']['male_count'] <= 0.05 &&
				$stats['variances']['non_stationary_count'] <= 0.05 &&
				$stats['variances']['physical'] <= 100 &&
				$stats['variances']['mental'] <= 100
			) break;

			$bestSwap = null;
			$bestScore = INF;
			$checks = 0;

			$indexesI = range(0, $squadCount - 1);
			shuffle($indexesI);

			foreach ($indexesI as $i) {
				$indexesJ = range(0, count($squads[$i]) - 1);
				shuffle($indexesJ);
				foreach ($indexesJ as $j) {
					for ($k = $i + 1; $k < $squadCount; $k++) {
						$indexesL = range(0, count($squads[$k]) - 1);
						shuffle($indexesL);
						foreach ($indexesL as $l) {
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

							if (++$checks >= $maxChecks) break 4;
						}
					}
				}
			}

			if (!$bestSwap) break;
			[$i, $j, $k, $l] = $bestSwap;
			[$squads[$i][$j], $squads[$k][$l]] = [$squads[$k][$l], $squads[$i][$j]];
		}

		\Log::info('Distribution done', ['sizes' => array_map('count', $squads)]);

		return $squads;
	}

	private function stats(array $squads): array
	{
		$means = array_map(function ($squad) {
			$count = count($squad) ?: 1;
			return [
				'age' => collect($squad)->avg(fn($m) => $m->birth_date ? now()->diffInYears($m->birth_date) : 0),
				'male_count' => count(array_filter($squad, fn($m) => $m->gender === 'male')) / $count,
				'non_stationary_count' => count(array_filter($squad, fn($m) => $m->residence_type === 'non-stationary')) / $count,
				'physical' => array_sum(array_map(fn($m) => $m->physical_score ?? 0, $squad)) / $count,
				'mental' => array_sum(array_map(fn($m) => $m->mental_score ?? 0, $squad)) / $count,
			];
		}, $squads);

		$variances = [];
		foreach (['age', 'male_count', 'non_stationary_count', 'physical', 'mental'] as $key) {
			$avg = array_sum(array_column($means, $key)) / count($means);
			$variances[$key] = array_sum(array_map(
					fn($m) => ($m[$key] - $avg) ** 2,
					$means
				)) / count($means);
		}

		return ['means' => $means, 'variances' => $variances];
	}
}
