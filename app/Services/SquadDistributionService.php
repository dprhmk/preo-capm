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

		$shuffledMembers = $members->shuffle()->values();
		$totalMembers = $shuffledMembers->count();
		$baseMembersPerSquad = (int) floor($totalMembers / $squadCount);
		$extraMembers = $totalMembers % $squadCount;

		$squads = array_fill(0, $squadCount, []);

		// Початковий розподіл
		$memberIndex = 0;
		for ($i = 0; $i < $squadCount; ++$i) {
			$membersForThisSquad = $baseMembersPerSquad + ($i < $extraMembers ? 1 : 0);
			for ($j = 0; $j < $membersForThisSquad && $memberIndex < $totalMembers; ++$j) {
				$squads[$i][] = $shuffledMembers[$memberIndex];
				++$memberIndex;
			}
		}

		// Балансування
		$maxIterations = 30;
		for ($iteration = 0; $iteration < $maxIterations; ++$iteration) {
			$stats = $this->calculateSquadStats($squads);
			$variances = $this->calculateVariances($stats);

			if (
				$variances['physical'] < 100 &&
				$variances['mental'] < 100 &&
				$variances['age'] < 1 &&
				$variances['male_count'] < 0.5
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
		return array_map(function ($member) {
			$count = count($member);
			if ($count === 0) {
				return [
					'physical' => 0,
					'mental' => 0,
					'avg_age' => 0,
					'male_count' => 0,
				];
			}

			$physical = array_sum(array_map(fn ($m) => $m->physical_score ?? 0, $member)) / $count;
			$mental = array_sum(array_map(fn ($m) => $m->mental_score ?? 0, $member)) / $count;
			$ages = array_filter(array_map(fn ($m) => $m->birth_date ? now()->diffInYears($m->birth_date) : null, $member));
			$avgAge = !empty($ages) ? array_sum($ages) / count($ages) : 0;
			$maleCount = count(array_filter($member, fn ($m) => $m->gender === 'male'));

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
							$newVariances['male_count'] * 2;

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
