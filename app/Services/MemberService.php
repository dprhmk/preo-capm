<?php

namespace App\Services;

use App\Models\Member;
use App\Models\Squad;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MemberService
{
	public function store(Request $request): RedirectResponse
	{
		$validationData = $this->getValidationData($request);
		$validator = Validator::make(...$validationData);

		if ($validator->fails()) {
			Log::info('Validation failed', [
				'errors' => $validator->errors()->toArray(),
			]);
			return redirect()
				->back()
				->withErrors($validator)
				->withInput();
		}

		$validated = $validator->validated();

		$member = Member::query()->updateOrCreate(
			['code' => $validated['code']],
			$validated
		);

		$squads = Squad::all();
		foreach ($squads as $squad) {
			$members = $squad->members;
			$squad->physical_score = $members->pluck('physical_score')->sum();
			$squad->mental_score = $members->pluck('mental_score')->sum();
			$squad->save();
		}

		return redirect()
			->route('qr.scan')
			->with('success', 'Дані ' . $member->full_name . ' успішно збережено!');
	}

	private function getValidationData(Request $request): array
	{
		$role = auth()->user()->role;
		$data = array_merge($request->route()->parameters, $request->all());
		unset($data['_token']);
		$data['code'] = Str::slug($data['code']);

		$rules = [
			'code' => 'required|string',
		];

		if(in_array($role, ['admin', 'main'])) {
			// Обробка фото
			if ( ! empty($data['photo_base64'])) {
				$image = str_replace('data:image/jpeg;base64,', '', $data['photo_base64']);
				$image = str_replace(' ', '+', $image);
				$filename = 'photos/' . uniqid('photo_', true) . '.jpg';
				Storage::disk('public')->put($filename, base64_decode($image));
				$data['photo_url'] = Storage::url($filename);
			}
			unset($data['photo_base64']);

			$rules = array_merge([
				'photo_url'          => 'nullable|string',
				'full_name'      => 'required|string|max:255',
				'birth_date'     => 'required|date',
				'gender'         => 'required|in:male,female',
				'residence_type' => 'required|in:stationary,non-stationary',
				'squad_id'       => 'nullable|numeric|exists:squads,id',
			], $rules);
		}

		if(in_array($role, ['admin', 'contacts'])) {
			$rules = array_merge([
				'child_phone'        => 'nullable|string|max:20',
				'parent_phone'       => 'nullable|string|max:20',
				'guardian_name'      => 'nullable|string|max:255',
				'additional_contact' => 'nullable|string|max:255',
				'social_links'       => 'nullable|array',
				'social_links.*'     => 'nullable|string|max:255',
				'address'            => 'nullable|string|max:500',
			], $rules);
		}

		if(in_array($role, ['admin', 'physical'])) {
			$data['does_sport'] = isset($data['does_sport']) ? 1 : 0;

			$rules = array_merge([
				'height_cm'      => 'required|numeric',
				'body_type'      => 'required|in:thin,medium,plump',
				'does_sport'     => 'boolean',
				'sport_type'     => 'nullable|in:football,volleyball,tennis,wrestling,workout,other',
				'agility_level'  => 'required|integer|min:1|max:3',
				'strength_level' => 'required|integer|min:1|max:3',
			], $rules);
		}

		if(in_array($role, ['admin', 'medical'])) {
			$data['physical_limitations'] = isset($data['physical_limitations']) ? 1 : 0;

			$rules = array_merge([
				'allergy_details'       => 'nullable|string|max:1000',
				'medical_restrictions'  => 'nullable|string|max:1000',
				'physical_limitations'  => 'boolean',
				'other_health_notes'    => 'nullable|string|max:1000',
			], $rules);
		}

		if(in_array($role, ['admin', 'mental'])) {
			$data['first_time']  = isset($data['first_time']) ? 1 : 0;
			$data['exceptional'] = isset($data['exceptional']) ? 1 : 0;
			$data['has_panic_attacks'] = isset($data['has_panic_attacks']) ? 1 : 0;
			$data['is_bad_boy'] = isset($data['is_bad_boy']) ? 1 : 0;

			$rules = array_merge([
				'first_time'        => 'boolean',
				'exceptional'       => 'boolean',
				'has_panic_attacks' => 'boolean',
				'personality_type'  => 'required|in:extrovert,introvert,ambivert',
				'is_bad_boy'        => 'boolean',
			], $rules);
		}

		if(in_array($role, ['admin', 'creative'])) {
			$data['is_musician'] = isset($data['is_musician']) ? 1 : 0;

			$rules = array_merge([
				'artistic_ability'      => 'required|integer|min:1|max:3',
				'is_musician'           => 'boolean',
				'musical_instruments'   => 'nullable|in:guitar,piano,drums,vocals,other',
				'poetic_ability'        => 'integer|min:1|max:3',
			], $rules);
		}

		if(in_array($role, ['admin', 'intellect'])) {
			$rules = array_merge([
				'english_level'         => 'nullable|integer|min:1|max:3',
				'general_iq_level'      => 'nullable|integer|min:1|max:3',
			], $rules);
		}

		return [$data, $rules];
	}
}
