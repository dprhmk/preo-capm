<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Squad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class SquadController
{
	public function index()
	{
		$squads = Squad::with('members')
			->get()
			->groupBy('name')
			->map(fn ($squad) => $squad->first())
			->values();

		return view('pages.squads', compact('squads'));
	}

	public function store(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'squad_count' => 'required|integer'
		]);

		if ($validator->fails()) {
			return redirect()->back()->withErrors($validator)->withInput();
		}

		$squadCount = (int) $request->input('squad_count');
		$leaderNames = $request->input('leader_names', []);
		$assistantNames = $request->input('assistant_names', []);
		$members = Member::all();

		if ($members->isEmpty()) {
			return view('pages.squads', ['squads' => []]);
		}

		$squads = $this->distributeMembers($members, $squadCount);

		Schema::disableForeignKeyConstraints();
		Squad::query()->truncate();
		Schema::enableForeignKeyConstraints();
		Member::query()->update(['squad_id' => null]);

		foreach ($squads as $index => $squadMembers) {
			$physicalScore = array_sum(array_map(fn ($member) => $member->physical_score, $squadMembers));
			$mentalScore = array_sum(array_map(fn ($member) => $member->mental_score, $squadMembers));

			$squad = Squad::create([
				'name' => 'Загін ' . ($index + 1),
				'leader_name' => $leaderNames[$index] ?? null,
				'assistant_name' => $assistantNames[$index] ?? null,
				'physical_score' => number_format($physicalScore, 2),
				'mental_score' => number_format($mentalScore, 2),
			]);

			foreach ($squadMembers as $member) {
				$member->update(['squad_id' => $squad->id]);
			}
		}

		$squads = Squad::with('members')
			->get()
			->groupBy('name')
			->map(fn ($squad) => $squad->first())
			->values();

		return view('pages.squads', compact('squads'));
	}

	private function distributeMembers($members, int $squadCount): array
	{
		// Підготовка учасників
		$scoredMembers = $members->map(fn ($member) => [
			'member' => $member,
			'physical_score' => $member->physical_score,
			'mental_score' => $member->mental_score,
			'height_cm' => $member->height_cm ?? 150,
			'gender' => $member->gender ?? 'unknown',
		])->sortByDesc(fn ($scored) => $scored['physical_score'] + $scored['mental_score'])->values();

		// Ініціалізація загонів
		$squads = array_fill(0, $squadCount, []);
		$minMembersPerSquad = floor($scoredMembers->count() / $squadCount);

		// Попередній розподіл для рівної кількості
		foreach ($scoredMembers as $index => $scoredMember) {
			$squads[$index % $squadCount][] = $scoredMember;
		}

		// Оптимізація за physical_score, mental_score, height_cm і gender
		$maxIterations = 100;
		$iteration = 0;

		while ($iteration < $maxIterations) {
			$variances = $this->calculateVariances($squads);
			if ($variances['physical'] < 0.01 && $variances['mental'] < 0.01 && $variances['height'] < 100 && $variances['gender'] < 2) {
				break;
			}

			$bestSwap = null;
			$bestVariance = PHP_INT_MAX;

			// Перевіряємо всі можливі обміни
			for ($i = 0; $i < $squadCount; $i++) {
				for ($j = $i + 1; $j < $squadCount; $j++) {
					// Перевіряємо, чи не порушили мінімальну кількість учасників
					if (count($squads[$i]) <= $minMembersPerSquad || count($squads[$j]) <= $minMembersPerSquad) {
						continue;
					}

					foreach (array_keys($squads[$i]) as $memberI) {
						foreach (array_keys($squads[$j]) as $memberJ) {
							$tempSquads = $squads;
							$temp = $tempSquads[$i][$memberI];
							$tempSquads[$i][$memberI] = $tempSquads[$j][$memberJ];
							$tempSquads[$j][$memberJ] = $temp;

							$newVariances = $this->calculateVariances($tempSquads);
							$totalVariance = $newVariances['physical'] + $newVariances['mental'] + ($newVariances['height'] / 100) + ($newVariances['gender'] * 2);

							if ($totalVariance < $bestVariance) {
								$bestVariance = $totalVariance;
								$bestSwap = [$i, $memberI, $j, $memberJ];
							}
						}
					}
				}
			}

			if ($bestSwap) {
				[$squadI, $memberI, $squadJ, $memberJ] = $bestSwap;
				$temp = $squads[$squadI][$memberI];
				$squads[$squadI][$memberI] = $squads[$squadJ][$memberJ];
				$squads[$squadJ][$memberJ] = $temp;
			} else {
				break;
			}

			$iteration++;
		}

		return array_map(fn ($squad) => array_map(fn ($scored) => $scored['member'], $squad), $squads);
	}

	private function calculateVariances(array $squads): array
	{
		$stats = $this->calculateSquadStats($squads);
		return [
			'physical' => $this->variance(array_column($stats, 'physical_score')),
			'mental' => $this->variance(array_column($stats, 'mental_score')),
			'height' => $this->variance(array_column($stats, 'avg_height')),
			'gender' => $this->variance(array_map(fn ($stats) => ($stats['male_count'] ?? 0) - ($stats['female_count'] ?? 0), $stats)),
		];
	}

	private function calculateSquadStats(array $squads): array
	{
		return array_map(function ($squad) {
			$physicalScore = array_sum(array_map(fn ($scored) => $scored['physical_score'], $squad));
			$mentalScore = array_sum(array_map(fn ($scored) => $scored['mental_score'], $squad));
			$heights = array_filter(array_map(fn ($scored) => $scored['height_cm'], $squad));
			$avgHeight = !empty($heights) ? array_sum($heights) / count($heights) : 150;
			$maleCount = count(array_filter($squad, fn ($scored) => $scored['gender'] === 'male'));
			$femaleCount = count(array_filter($squad, fn ($scored) => $scored['gender'] === 'female'));

			return [
				'physical_score' => $physicalScore,
				'mental_score' => $mentalScore,
				'avg_height' => $avgHeight,
				'male_count' => $maleCount,
				'female_count' => $femaleCount,
			];
		}, $squads);
	}

	private function variance(array $values): float
	{
		if (count($values) <= 1) {
			return 0;
		}

		$mean = array_sum($values) / count($values);
		$squaredDiffs = array_map(fn ($value) => pow($value - $mean, 2), $values);

		return array_sum($squaredDiffs) / count($values);
	}
}
