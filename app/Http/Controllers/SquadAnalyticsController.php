<?php

namespace App\Http\Controllers;

use App\Models\Squad;
use App\Services\SquadDistributionService;
use Illuminate\Http\Request;

class SquadAnalyticsController
{
	protected $squadDistributionService;

	public function __construct(SquadDistributionService $squadDistributionService)
	{
		$this->squadDistributionService = $squadDistributionService;
	}

	public function index()
	{
		$squads = Squad::with('members')->get();
		$analyticsData = [];

		if ($squads->isNotEmpty()) {
			// Обчислюємо статистику для кожного загону
			$squadsArray = $squads->map(function ($squad) {
				return $squad->members->toArray();
			})->toArray();

			$stats = $this->squadDistributionService->calculateSquadStats($squadsArray);

			// Готуємо дані для графіків
			$analyticsData = [
				'labels' => $squads->pluck('name')->toArray(),
				'physical_scores' => array_column($stats, 'physical'),
				'mental_scores' => array_column($stats, 'mental'),
				'avg_ages' => array_column($stats, 'avg_age'),
				'male_counts' => array_column($stats, 'male_count'),
				'member_counts' => $squads->map(fn($squad) => count($squad->members))->toArray(),
			];

			// Обчислюємо кількість жінок
			$analyticsData['female_counts'] = array_map(function ($count, $male_count) {
				return $count - $male_count;
			}, $analyticsData['member_counts'], $analyticsData['male_counts']);
		}

		return view('pages.squads-analytics', compact('squads', 'analyticsData'));
	}
}
