<?php

namespace Database\Factories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MemberFactory extends Factory
{
	protected $model = Member::class;

	public function definition(): array
	{
		return [
			'code' => strtoupper(Str::random(8)),
			'full_name' => $this->faker->name(),
            'birth_date' => $this->faker->dateTimeBetween('-18 years', '-12 years')->format('Y-m-d'),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'residence_type' => $this->faker->randomElement(['stationary', 'non-stationary']),
            'is_bad_boy' => $this->faker->boolean(10),

            'photo_url' => $this->faker->imageUrl(),
            'child_phone' => $this->faker->phoneNumber(),
            'parent_phone' => $this->faker->phoneNumber(),
            'guardian_name' => $this->faker->name(),
            'additional_contact' => $this->faker->phoneNumber(),
            'social_links' => [
				'instagram' => 'https://instagram.com/' . $this->faker->userName(),
                'telegram' => '@' . $this->faker->userName(),
            ],
            'address' => $this->faker->address(),

            'height_cm' => $this->faker->numberBetween(140, 190),
            'body_type' => $this->faker->randomElement(['thin', 'medium', 'plump']),
            'does_sport' => $this->faker->boolean(),
            'sport_type' => $this->faker->randomElement(['football', 'volleyball', 'tennis', 'wrestling', 'workout', 'other']),
            'agility_level' => $this->faker->numberBetween(1, 3),
            'strength_level' => $this->faker->numberBetween(1, 3),

            'allergy_details' => $this->faker->optional()->sentence(),
            'medical_restrictions' => $this->faker->optional()->sentence(),
            'physical_limitations' => $this->faker->optional()->sentence(),
            'other_health_notes' => $this->faker->optional()->sentence(),

			'first_time' => $this->faker->boolean(30),
			'exceptional' => $this->faker->boolean(10),
            'has_panic_attacks' => $this->faker->boolean(20),
            'personality_type' => $this->faker->randomElement(['extrovert', 'introvert', 'ambivert']),

            'artistic_ability' => $this->faker->numberBetween(1, 3),
            'is_musician' => $this->faker->boolean(),
            'musical_instruments' => $this->faker->randomElement( ['guitar', 'piano', 'drums', 'vocals', 'other']),
            'poetic_ability' => $this->faker->numberBetween(1, 3),

            'english_level' => $this->faker->numberBetween(1, 3),
            'general_iq_level' => $this->faker->numberBetween(1, 3),
        ];
    }
}
