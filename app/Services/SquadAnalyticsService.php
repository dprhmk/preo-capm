<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class SquadAnalyticsService
{
	public function getAnalytics(Collection $squads): array
	{
		if ($squads->isNotEmpty()) {
			$squadsArray = $squads->map(function ($squad) {
				return $squad->members->toArray();
			})->toArray();

			// Обчислення вікових груп
			$ageGroups = array_map(function ($squad) {
				$ageBuckets = [
					'5-6' => 0,
					'7-8' => 0,
					'9-10' => 0,
					'11-12' => 0,
					'13-14' => 0,
					'15-16' => 0,
					'17-18' => 0,
				];
				foreach ($squad as $member) {
					$birthDate = $member['birth_date'] ?? null;
					if ($birthDate) {
						$age = (int)floor(Carbon::parse($birthDate)->diffInYears(Carbon::now()));
						if ($age >= 5 && $age <= 6) $ageBuckets['5-6']++;
						elseif ($age >= 7 && $age <= 8) $ageBuckets['7-8']++;
						elseif ($age >= 9 && $age <= 10) $ageBuckets['9-10']++;
						elseif ($age >= 11 && $age <= 12) $ageBuckets['11-12']++;
						elseif ($age >= 13 && $age <= 14) $ageBuckets['13-14']++;
						elseif ($age >= 15 && $age <= 16) $ageBuckets['15-16']++;
						elseif ($age >= 17) $ageBuckets['17-18']++;
					}
				}
				return $ageBuckets;
			}, $squadsArray);

			// Фільтрація порожніх вікових груп
			$nonEmptyAgeGroups = array_filter(array_keys($ageGroups[0]), function ($ageGroup) use ($ageGroups) {
				return array_sum(array_column($ageGroups, $ageGroup)) > 0;
			});
			$filteredAgeGroups = array_map(function ($squadAgeGroup) use ($nonEmptyAgeGroups) {
				return array_intersect_key($squadAgeGroup, array_flip($nonEmptyAgeGroups));
			}, $ageGroups);

			// Обчислення відсотків для гендера
			$genderPercentages = array_map(function ($squad) {
				$total = count($squad);
				if ($total === 0) {
					return ['male' => 0, 'female' => 0];
				}
				$maleCount = count(array_filter($squad, fn ($m) => ($m['gender'] ?? '') === 'male'));
				return [
					'male' => round(($maleCount / $total) * 100, 1),
					'female' => round((($total - $maleCount) / $total) * 100, 1),
				];
			}, $squadsArray);

			// Non-stationary учасники в кожному загоні
			$nonStationaryCounts = array_map(function ($squad) {
				return count(array_filter($squad, fn ($m) => ($m['residence_type'] ?? null) === 'non-stationary'));
			}, $squadsArray);

			// Максимальний бал для шкали
			$maxSquadScorePercent = $squads->pluck('members')->max()->count() * 100;

			return [
				'labels' => $squads->pluck('name')->toArray(),
				'physical_scores' => $squads->pluck('physical_score')->toArray(),
				'mental_scores' => $squads->pluck('mental_score')->toArray(),
				'age_groups' => $filteredAgeGroups,
				'gender_percentages' => $genderPercentages,
				'member_counts' => $squads->map(fn($squad) => count($squad->members))->toArray(),
				'non_stationary_counts' => $nonStationaryCounts,
				'max_squad_score_percent' => $maxSquadScorePercent,
			];
		}

		return [];
	}
}
