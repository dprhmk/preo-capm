<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Squad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class SquadController
{
	private array $weights = [
		'residence_type' => 0.1,
		'is_bad_boy' => -0.1,
		'height_cm' => 0.05,
		'body_type' => 0.05,
		'does_sport' => 0.15,
		'sport_type' => 0.1,
		'agility_level' => 0.15,
		'strength_level' => 0.15,
		'physical_limitations' => -0.1,
		'first_time' => -0.05,
		'exceptional' => -0.1,
		'has_panic_attacks' => -0.1,
		'personality_type' => 0.15,
		'artistic_ability' => 0.15,
		'is_musician' => 0.15,
		'musical_instruments' => 0.1,
		'poetic_ability' => 0.15,
		'english_level' => 0.15,
		'general_iq_level' => 0.15,
	];

	public function index(): View
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
			'squad_count' => 'required|integer',
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
			$totalScore = array_sum(array_map(fn ($member) => $this->calculateMemberScore($member), $squadMembers));

			$squad = Squad::create([
				'name' => 'Загін ' . ($index + 1),
				'leader_name' => $leaderNames[$index] ?? null,
				'assistant_name' => $assistantNames[$index] ?? null,
				'total_score' => number_format($totalScore, 2),
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
		$scoredMembers = $members->map(fn ($member) => [
			'member' => $member,
			'score' => $this->calculateMemberScore($member),
		])->sortByDesc('score')->values();

		$squads = array_fill(0, $squadCount, []);

		foreach ($scoredMembers as $index => $scoredMember) {
			$squads[$index % $squadCount][] = $scoredMember['member'];
		}

		return $this->optimizeSquads($squads);
	}

	private function calculateMemberScore($member): float
	{
		$score = 0;
		$score += $this->weights['residence_type'] * ($member->residence_type === 'stationary' ? 1 : 0);
		$score += $this->weights['is_bad_boy'] * ($member->is_bad_boy ? 1 : 0);
		$score += $this->weights['height_cm'] * ($member->height_cm ? ($member->height_cm - 100) / 100 : 0.5);
		$bodyTypeMap = ['thin' => 0.5, 'medium' => 1, 'plump' => 0.33];
		$score += $this->weights['body_type'] * ($member->body_type ? $bodyTypeMap[$member->body_type] : 0.5);
		$score += $this->weights['does_sport'] * ($member->does_sport ? 1 : 0);
		$sportScore = $member->sport_type ? (in_array($member->sport_type, ['football', 'volleyball']) ? 1 : 0.5) : 0;
		$score += $this->weights['sport_type'] * $sportScore;
		$score += $this->weights['agility_level'] * ($member->agility_level ? ($member->agility_level - 1) / 2 : 0.5);
		$score += $this->weights['strength_level'] * ($member->strength_level ? ($member->strength_level - 1) / 2 : 0.5);
		$score += $this->weights['physical_limitations'] * ($member->physical_limitations ? 1 : 0);
		$score += $this->weights['first_time'] * ($member->first_time ? 1 : 0);
		$score += $this->weights['exceptional'] * ($member->exceptional ? 1 : 0);
		$score += $this->weights['has_panic_attacks'] * ($member->has_panic_attacks ? 1 : 0);
		$personalityMap = ['extrovert' => 1, 'ambivert' => 0.5, 'introvert' => 0];
		$score += $this->weights['personality_type'] * ($member->personality_type ? $personalityMap[$member->personality_type] : 0.5);
		$score += $this->weights['artistic_ability'] * ($member->artistic_ability ? ($member->artistic_ability - 1) / 2 : 0.5);
		$score += $this->weights['is_musician'] * ($member->is_musician ? 1 : 0);
		$score += $this->weights['musical_instruments'] * ($member->musical_instruments ? 1 : 0);
		$score += $this->weights['poetic_ability'] * ($member->poetic_ability ? ($member->poetic_ability - 1) / 2 : 0.5);
		$score += $this->weights['english_level'] * ($member->english_level ? ($member->english_level - 1) / 2 : 0.5);
		$score += $this->weights['general_iq_level'] * ($member->general_iq_level ? ($member->general_iq_level - 1) / 2 : 0.5);

		return $score;
	}

	private function optimizeSquads(array $squads): array
	{
		$maxIterations = 100;
		$iteration = 0;

		while ($iteration < $maxIterations) {
			$squadScores = $this->calculateSquadScores($squads);
			$totalScores = array_column($squadScores, 'total');

			if (empty($totalScores) || count($totalScores) <= 1) {
				break;
			}

			$variance = variance($totalScores);

			if ($variance < 0.01) {
				break;
			}

			$maxSquadIndex = array_keys($totalScores, max($totalScores))[0];
			$minSquadIndex = array_keys($totalScores, min($totalScores))[0];

			$bestSwap = $this->findBestSwap($squads, $maxSquadIndex, $minSquadIndex);
			if ($bestSwap) {
				[$maxMemberIndex, $minMemberIndex] = $bestSwap;
				$temp = $squads[$maxSquadIndex][$maxMemberIndex];
				$squads[$maxSquadIndex][$maxMemberIndex] = $squads[$minSquadIndex][$minMemberIndex];
				$squads[$minSquadIndex][$minMemberIndex] = $temp;
			} else {
				break;
			}

			$iteration++;
		}

		return $squads;
	}

	private function calculateSquadScores(array $squads): array
	{
		return array_map(fn ($squad) => [
			'total' => array_sum(array_map(fn ($member) => $this->calculateMemberScore($member), $squad)),
			'scores' => array_map(fn ($member) => $this->calculateMemberScore($member), $squad),
		], $squads);
	}

	private function findBestSwap(array $squads, int $maxSquadIndex, int $minSquadIndex): ?array
	{
		$bestVariance = PHP_INT_MAX;
		$bestSwap = null;

		foreach (array_keys($squads[$maxSquadIndex]) as $maxMemberIndex) {
			foreach (array_keys($squads[$minSquadIndex]) as $minMemberIndex) {
				$tempSquads = $squads;
				$temp = $tempSquads[$maxSquadIndex][$maxMemberIndex];
				$tempSquads[$maxSquadIndex][$maxMemberIndex] = $tempSquads[$minSquadIndex][$minMemberIndex];
				$tempSquads[$minSquadIndex][$minMemberIndex] = $temp;

				$tempScores = $this->calculateSquadScores($tempSquads);
				$variance = variance(array_column($tempScores, 'total'));

				if ($variance < $bestVariance) {
					$bestVariance = $variance;
					$bestSwap = [$maxMemberIndex, $minMemberIndex];
				}
			}
		}

		return $bestSwap;
	}
}

// Допоміжна функція для обчислення дисперсії
function variance(array $values): float
{
	if (count($values) <= 1) {
		return 0;
	}

	$mean = array_sum($values) / count($values);
	$squaredDiffs = array_map(fn ($value) => pow($value - $mean, 2), $values);

	return array_sum($squaredDiffs) / count($values);
}