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
					'11-12' => 0,
					'13-14' => 0,
					'15-16' => 0,
					'17-18' => 0,
				];
				foreach ($squad as $member) {
					$birthDate = $member['birth_date'] ?? null;
					if ($birthDate) {
						$age = Carbon::parse($birthDate)->diffInYears(Carbon::now());
						if ($age <= 12) $ageBuckets['11-12']++;
						elseif ($age >= 13 && $age <= 14) $ageBuckets['13-14']++;
						elseif ($age >= 15 && $age <= 16) $ageBuckets['15-16']++;
						elseif ($age >= 17) $ageBuckets['17-18']++;
					}
				}
				return $ageBuckets;
			}, $squadsArray);

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

			// Максимальний бал для шкали
			$maxSquadScorePercent = $squads->pluck('members')->max()->count() * 100;

			return [
				'labels' => $squads->pluck('name')->toArray(),
				'physical_scores' => $squads->pluck('physical_score')->toArray(),
				'mental_scores' => $squads->pluck('mental_score')->toArray(),
				'age_groups' => $ageGroups,
				'gender_percentages' => $genderPercentages,
				'member_counts' => $squads->map(fn($squad) => count($squad->members))->toArray(),
				'max_squad_score_percent' => $maxSquadScorePercent,
			];
		}

		return [];
	}
}
