<?php

namespace App\Services;

use Illuminate\Support\Collection;

class SquadDistributionService
{
	public function distribute(Collection $members, int $squadCount): array
	{
		if ($members->count() < $squadCount) {
			return array_fill(0, $squadCount, []);
		}

		$totalMembers = $members->count();
		$baseMembersPerSquad = (int) floor($totalMembers / $squadCount);
		$extraMembers = $totalMembers % $squadCount;

		// Розділити учасників за статтю
		$males = $members->filter(fn($m) => $m->gender === 'male')->shuffle()->values();
		$females = $members->filter(fn($m) => $m->gender !== 'male')->shuffle()->values();

		$squads = array_fill(0, $squadCount, []);

		// Початковий розподіл для рівномірного гендерного балансу
		$maleIndex = 0;
		$femaleIndex = 0;
		$malesPerSquad = (int) floor($males->count() / $squadCount);
		$femalesPerSquad = (int) floor($females->count() / $squadCount);
		$extraMales = $males->count() % $squadCount;
		$extraFemales = $females->count() % $squadCount;

		for ($i = 0; $i < $squadCount; ++$i) {
			// Розподіл чоловіків
			$malesForThisSquad = $malesPerSquad + ($i < $extraMales ? 1 : 0);
			for ($j = 0; $j < $malesForThisSquad && $maleIndex < $males->count(); ++$j) {
				$squads[$i][] = $males[$maleIndex];
				++$maleIndex;
			}
			// Розподіл жінок
			$femalesForThisSquad = $femalesPerSquad + ($i < $extraFemales ? 1 : 0);
			for ($j = 0; $j < $femalesForThisSquad && $femaleIndex < $females->count(); ++$j) {
				$squads[$i][] = $females[$femaleIndex];
				++$femaleIndex;
			}
		}

		// Балансування
		$maxIterations = 50; // Збільшено для точнішого балансу
		for ($iteration = 0; $iteration < $maxIterations; ++$iteration) {
			$stats = $this->calculateSquadStats($squads);
			$variances = $this->calculateVariances($stats);

			if (
				$variances['physical'] < 100 &&
				$variances['mental'] < 100 &&
				$variances['age'] < 1 &&
				$variances['male_count'] < 0.1 // Жорсткіший поріг
			) {
				break;
			}

			$bestSwap = $this->findBestSwap($squads, $baseMembersPerSquad, $extraMembers);
			if ($bestSwap === null) {
				break;
			}

			[$squadI, $memberI, $squadJ, $memberJ] = $bestSwap;
			$temp = $squads[$squadI][$memberI];
			$squads[$squadI][$memberI] = $squads[$squadJ][$memberJ];
			$squads[$squadJ][$memberJ] = $temp;
		}

		return $squads;
	}

	public function calculateSquadStats(array $squads): array
	{
		return array_map(function ($squad) {
			$count = count($squad);
			if ($count === 0) {
				return [
					'physical' => 0,
					'mental' => 0,
					'avg_age' => 0,
					'male_count' => 0,
				];
			}

			$physical = array_sum(array_map(fn ($m) => is_array($m) ? ($m['physical_score'] ?? 0) : ($m->physical_score ?? 0), $squad)) / $count;
			$mental = array_sum(array_map(fn ($m) => is_array($m) ? ($m['mental_score'] ?? 0) : ($m->mental_score ?? 0), $squad)) / $count;
			$ages = array_filter(array_map(fn ($m) => $m['birth_date'] ? now()->diffInYears($m['birth_date']) : null, $squad));
			$avgAge = !empty($ages) ? array_sum($ages) / count($ages) : 0;
			$maleCount = count(array_filter($squad, fn ($m) => (is_array($m) ? ($m['gender'] ?? '') : ($m->gender ?? '')) === 'male'));

			return [
				'physical' => $physical,
				'mental' => $mental,
				'avg_age' => $avgAge,
				'male_count' => $maleCount,
			];
		}, $squads);
	}

	private function calculateVariances(array $stats): array
	{
		return [
			'physical' => $this->variance(array_column($stats, 'physical')),
			'mental' => $this->variance(array_column($stats, 'mental')),
			'age' => $this->variance(array_column($stats, 'avg_age')),
			'male_count' => $this->variance(array_column($stats, 'male_count')),
		];
	}

	private function variance(array $values): float
	{
		if (count($values) <= 1) {
			return 0;
		}

		$mean = array_sum($values) / count($values);
		$squaredDiffs = array_map(fn ($value) => ($value - $mean) ** 2, $values);

		return array_sum($squaredDiffs) / count($values);
	}

	private function findBestSwap(array $squads, int $baseMembersPerSquad, int $extraMembers): ?array
	{
		$squadCount = count($squads);
		$bestSwap = null;
		$bestVariance = PHP_INT_MAX;

		for ($i = 0; $i < $squadCount; ++$i) {
			for ($j = $i + 1; $j < $squadCount; ++$j) {
				$minMembersI = $baseMembersPerSquad + ($i < $extraMembers ? 1 : 0);
				$minMembersJ = $baseMembersPerSquad + ($j < $extraMembers ? 1 : 0);

				if (count($squads[$i]) <= $minMembersI || count($squads[$j]) <= $minMembersJ) {
					continue;
				}

				foreach (array_keys($squads[$i]) as $memberI) {
					foreach (array_keys($squads[$j]) as $memberJ) {
						$tempSquads = $squads;
						$temp = $tempSquads[$i][$memberI];
						$tempSquads[$i][$memberI] = $tempSquads[$j][$memberJ];
						$tempSquads[$j][$memberJ] = $temp;

						$newStats = $this->calculateSquadStats($tempSquads);
						$newVariances = $this->calculateVariances($newStats);
						$totalVariance =
							$newVariances['physical'] * 10 +
							$newVariances['mental'] * 10 +
							$newVariances['age'] +
							$newVariances['male_count'] * 10; // Збільшена вага

						if ($totalVariance < $bestVariance) {
							$bestVariance = $totalVariance;
							$bestSwap = [$i, $memberI, $j, $memberJ];
						}
					}
				}
			}
		}

		return $bestSwap;
	}
}
