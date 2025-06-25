<?php

namespace App\Services;

use App\Models\Member;

class MemberScoreService
{
	private array $weights = [
		// Physical factors
		'height_cm' => 0.05,
		'body_type' => 0.10,
		'does_sport' => 0.25,
		'sport_type' => 0.15,
		'agility_level' => 0.25,
		'strength_level' => 0.20,
		'physical_limitations' => -0.3,

		// Mental factors
		'personality_type' => 0.175,
		'artistic_ability' => 0.15,
		'is_musician' => 0.15,
		'musical_instruments' => 0.05,
		'poetic_ability' => 0.175,
		'english_level' => 0.15,
		'general_iq_level' => 0.15,
		'exceptional' => -0.4,
		'has_panic_attacks' => -0.15,
		'first_time' => -0.1,
		'is_bad_boy' => -0.2,
	];

	public function calculateScores(Member $member): array
	{
		$physicalScore = 0;
		$mentalScore = 0;

		/** -------- ФІЗИЧНИЙ БЛОК -------- */

		// Зріст
		$physicalScore += $this->weights['height_cm'] * (
			$member->height_cm ? max(0, ($member->height_cm - 100) / 100) : 0.5
			);

		// Тип тіла
		$bodyTypeMap = ['thin' => 0.5, 'medium' => 1, 'plump' => 0.33];
		$physicalScore += $this->weights['body_type'] * (
			$member->body_type ? ($bodyTypeMap[$member->body_type] ?? 0.5) : 0.5
			);

		// Спорт
		$physicalScore += $this->weights['does_sport'] * ($member->does_sport ? 1 : 0);

		// Тип спорту
		$sportScore = match ($member->sport_type ?? null) {
			'football', 'volleyball', 'tennis', 'wrestling', 'workout' => 1,
			'other' => 0.5,
			default => 0,
		};
		$physicalScore += $this->weights['sport_type'] * $sportScore;

		// Рівень спритності та сили (1–3: 1=погано, 2=нормально, 3=супер)
		$physicalScore += $this->weights['agility_level'] * (
			$member->agility_level ? ($member->agility_level - 1) / 2 : 0.5
			);
		$physicalScore += $this->weights['strength_level'] * (
			$member->strength_level ? ($member->strength_level - 1) / 2 : 0.5
			);

		// Негатив
		$physicalScore += $this->weights['physical_limitations'] * ($member->physical_limitations ? 1 : 0);

		// Масштабування до [0, 100]
		$physicalScore = max(0, $physicalScore * 100);

		/** -------- МЕНТАЛЬНИЙ БЛОК -------- */

		// Тип особистості
		$personalityMap = ['extrovert' => 1, 'ambivert' => 0.5, 'introvert' => 0];
		$mentalScore += $this->weights['personality_type'] * (
			$member->personality_type ? ($personalityMap[$member->personality_type] ?? 0.5) : 0.5
			);

		// Творчі здібності (1–3)
		$mentalScore += $this->weights['artistic_ability'] * (
			$member->artistic_ability ? ($member->artistic_ability - 1) / 2 : 0.5
			);

		// Музикант
		$mentalScore += $this->weights['is_musician'] * ($member->is_musician ? 1 : 0);

		// Музичні інструменти (гітара має більшу вагу)
		$instrumentScore = $member->musical_instruments ? 0.5 : 0;
		$instrumentMultiplier = ($member->musical_instruments === 'guitar') ? 2 : 1;
		$mentalScore += $this->weights['musical_instruments'] * $instrumentScore * $instrumentMultiplier;

		// Поетичні здібності (1–3)
		$mentalScore += $this->weights['poetic_ability'] * (
			$member->poetic_ability ? ($member->poetic_ability - 1) / 2 : 0.5
			);

		// Рівень англійської та IQ (1–3)
		$mentalScore += $this->weights['english_level'] * (
			$member->english_level ? ($member->english_level - 1) / 2 : 0.5
			);
		$mentalScore += $this->weights['general_iq_level'] * (
			$member->general_iq_level ? ($member->general_iq_level - 1) / 2 : 0.5
			);

		// Негативні фактори
		$mentalScore += $this->weights['exceptional'] * ($member->exceptional ? 1 : 0);
		$mentalScore += $this->weights['has_panic_attacks'] * ($member->has_panic_attacks ? 1 : 0);
		$mentalScore += $this->weights['first_time'] * ($member->first_time ? 1 : 0);
		$mentalScore += $this->weights['is_bad_boy'] * ($member->is_bad_boy ? 1 : 0);

		// Масштабування до [0, 100]
		$mentalScore = max(0, $mentalScore * 100);

		return [
			'physical_score' => round($physicalScore, 2),
			'mental_score' => round($mentalScore, 2),
		];
	}
}
