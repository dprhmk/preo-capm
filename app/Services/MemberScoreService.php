<?php

namespace App\Services;

use App\Models\Member;

class MemberScoreService
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

    public function calculateScores(Member $member): array
    {
        $physicalScore = 0;
        $mentalScore = 0;

        // Physical Fitness (Фізична підготовка)
        $physicalScore += $this->weights['height_cm'] * ($member->height_cm ? ($member->height_cm - 100) / 100 : 0.5);
        $bodyTypeMap = ['thin' => 0.5, 'medium' => 1, 'plump' => 0.33];
        $physicalScore += $this->weights['body_type'] * ($member->body_type ? $bodyTypeMap[$member->body_type] : 0.5);
        $physicalScore += $this->weights['does_sport'] * ($member->does_sport ? 1 : 0);
        $sportScore = $member->sport_type ? (in_array($member->sport_type, ['football', 'volleyball']) ? 1 : 0.5) : 0;
        $physicalScore += $this->weights['sport_type'] * $sportScore;
        $physicalScore += $this->weights['agility_level'] * ($member->agility_level ? ($member->agility_level - 1) / 2 : 0.5);
        $physicalScore += $this->weights['strength_level'] * ($member->strength_level ? ($member->strength_level - 1) / 2 : 0.5);
        $physicalScore += $this->weights['physical_limitations'] * ($member->physical_limitations ? 1 : 0);

        // Mental Creativity (Ментальна креативність)
        $mentalScore += $this->weights['personality_type'] * ($member->personality_type ? ['extrovert' => 1, 'ambivert' => 0.5, 'introvert' => 0][$member->personality_type] : 0.5);
        $mentalScore += $this->weights['artistic_ability'] * ($member->artistic_ability ? ($member->artistic_ability - 1) / 2 : 0.5);
        $mentalScore += $this->weights['is_musician'] * ($member->is_musician ? 1 : 0);
        $mentalScore += $this->weights['musical_instruments'] * ($member->musical_instruments ? 1 : 0);
        $mentalScore += $this->weights['poetic_ability'] * ($member->poetic_ability ? ($member->poetic_ability - 1) / 2 : 0.5);
        $mentalScore += $this->weights['english_level'] * ($member->english_level ? ($member->english_level - 1) / 2 : 0.5);
        $mentalScore += $this->weights['general_iq_level'] * ($member->general_iq_level ? ($member->general_iq_level - 1) / 2 : 0.5);
        $mentalScore += $this->weights['exceptional'] * ($member->exceptional ? 1 : 0);
        $mentalScore += $this->weights['has_panic_attacks'] * ($member->has_panic_attacks ? 1 : 0);
        $mentalScore += $this->weights['first_time'] * ($member->first_time ? 1 : 0);

        return [
            'physical_score' => number_format($physicalScore, 2),
            'mental_score' => number_format($mentalScore, 2),
        ];
    }
}