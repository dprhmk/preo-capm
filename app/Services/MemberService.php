<?php

namespace App\Services;

use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MemberService
{
	public function storeSection(Request $request, string $section)
	{
		return match ($section) {
			'main' => $this->storeMainSection($request),
			'contacts' => $this->storeContactSection($request),
			'physical' => $this->storePhysicalSection($request),
			'medical' => $this->storeMedicalSection($request),
			'creative' => $this->storeCreativeSection($request),
			'intellect' => $this->storeIntellectualSection($request),
			default => throw new \InvalidArgumentException("Unknown section: $section"),
		};
	}

	public function storeMainSection(Request $request)
	{
		$data = array_merge($request->route()->parameters, $request->all());
		$data['is_bad_boy'] = isset($data['is_bad_boy']);
		unset($data['_token']);

		$validator = Validator::make(
			$data,
			[
				'code'           => 'required|string',
				'full_name'      => 'required|string|max:255',
				'birth_date'     => 'required|date',
				'age'            => 'required|numeric',
				'gender'         => 'required|in:male,female',
				'residence_type' => 'required|in:stationary,non-stationary',
				'is_bad_boy'     => 'boolean',
			]
		);

		if ($validator->fails()) {
			\Log::info('Main section validation failed', [
				'errors' => $validator->errors()->toArray(),
				'data' => $data,
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

		return redirect()
			->route('qr.scan')
			->with('success', $member->full_name . ' успішно збережено!');
	}

	public function storeContactSection(Request $request): RedirectResponse
	{
		$data = array_merge($request->route()->parameters(), $request->all());
		unset($data['_token']);

		$validator = Validator::make(
			$data,
			[
				'code'               => 'required|string',
				'photo_base64'       => 'nullable|string',
				'child_phone'        => 'nullable|string|max:20',
				'parent_phone'       => 'nullable|string|max:20',
				'guardian_name'      => 'nullable|string|max:255',
				'additional_contact' => 'nullable|string|max:255',
				'social_links'       => 'nullable|array',
				'social_links.*'     => 'nullable|string|max:255',
				'address'            => 'nullable|string|max:500',
			]
		);

		if ($validator->fails()) {
			\Log::info('Contact section validation failed', [
				'errors' => $validator->errors()->toArray(),
				'data' => $data,
			]);
			return redirect()
				->back()
				->withErrors($validator)
				->withInput();
		}

		$validated = $validator->validated();

		// Обробка фото
		if (!empty($validated['photo_base64'])) {
			$image = str_replace('data:image/jpeg;base64,', '', $validated['photo_base64']);
			$image = str_replace(' ', '+', $image);
			$filename = 'photos/' . uniqid('photo_', true) . '.jpg';
			Storage::disk('public')->put($filename, base64_decode($image));
			$validated['photo_url'] = Storage::url($filename);
		}
		unset($validated['photo_base64']);

		$member = Member::query()->updateOrCreate(
			['code' => $validated['code']],
			$validated
		);

		return redirect()
			->route('qr.scan')
			->with('success', 'Контактна інформація успішно збережена!');
	}

	public function storePhysicalSection(Request $request): RedirectResponse
	{
		$data = array_merge($request->route()->parameters(), $request->all());
		$data['does_sport'] = isset($data['does_sport']);
		unset($data['_token']);

		$validator = Validator::make($data, [
			'code'           => 'required|string',
			'height_cm'      => 'required|numeric',
			'body_type'      => 'required|in:thin,medium,plump',
			'does_sport'     => 'boolean',
			'sport_type'     => 'nullable|in:football,volleyball,tennis,wrestling,workout,other',
			'agility_level'  => 'required|integer|min:1|max:3',
			'strength_level' => 'required|integer|min:1|max:3',
		]);

		if ($validator->fails()) {
			\Log::info('Physical section validation failed', [
				'errors' => $validator->errors()->toArray(),
				'data' => $data,
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

		return redirect()
			->route('qr.scan')
			->with('success', 'Фізичні характеристики ' . $member->full_name . ' успішно збережено!');
	}

	public function storeMedicalSection(Request $request): RedirectResponse
	{
		$data = array_merge(['code' => $request->route('code')], $request->all());
		$data['physical_limitations'] = isset($data['physical_limitations']);
		unset($data['_token']);

		$validator = Validator::make($data, [
			'code'                  => 'required|string',
			'allergy_details'       => 'nullable|string|max:1000',
			'medical_restrictions'  => 'nullable|string|max:1000',
			'physical_limitations'  => 'boolean',
			'other_health_notes'    => 'nullable|string|max:1000',
		]);

		if ($validator->fails()) {
			\Log::info('Medical section validation failed', [
				'errors' => $validator->errors()->toArray(),
				'data' => $data,
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

		return redirect()
			->route('qr.scan')
			->with('success', 'Медичні особливості ' . $member->full_name . ' успішно збережено!');
	}

	public function storeMentalSection(Request $request): RedirectResponse
	{
		$data = array_merge(['code' => $request->route('code')], $request->all());
		$data['first_time']  = isset($data['first_time']);
		$data['exceptional'] = isset($data['exceptional']);
		$data['has_panic_attacks'] = isset($data['has_panic_attacks']);
		unset($data['_token']);

		$validator = Validator::make($data, [
			'code'              => 'required|string',
			'first_time'        => 'boolean',
			'exceptional'       => 'boolean',
			'has_panic_attacks' => 'boolean',
			'personality_type'  => 'required|in:extrovert,introvert,ambivert',
		]);

		if ($validator->fails()) {
			\Log::info('Mental section validation failed', [
				'errors' => $validator->errors()->toArray(),
				'data' => $data,
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

		return redirect()
			->route('qr.scan')
			->with('success', 'Дані про психічне здоров’я ' . $member->full_name . ' успішно збережено!');
	}

	public function storeCreativeSection(Request $request): RedirectResponse
	{
		$data = array_merge(['code' => $request->route('code')], $request->all());
		$data['is_musician'] = isset($data['is_musician']) ? 1 : 0;
		unset($data['_token']);

		$validator = Validator::make($data, [
			'code'                  => 'required|string',
			'artistic_ability'      => 'required|integer|min:1|max:3',
			'is_musician'           => 'boolean',
			'musical_instruments'   => 'nullable|in:guitar,piano,drums,vocals,other',
			'poetic_ability'        => 'integer|min:1|max:3',
		]);

		if ($validator->fails()) {
			\Log::info('Creative section validation failed', [
				'errors' => $validator->errors()->toArray(),
				'data' => $data,
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

		return redirect()
			->route('qr.scan')
			->with('success', 'Дані про творчість ' . $member->full_name . ' успішно збережено!');
	}

	public function storeIntellectualSection(Request $request): RedirectResponse
	{
		$data = array_merge(['code' => $request->route('code')], $request->all());
		unset($data['_token']);

		$validator = Validator::make($data, [
			'code'                  => 'required|string',
			'english_level'         => 'nullable|integer|min:1|max:3',
			'general_iq_level'      => 'nullable|integer|min:1|max:3',
		]);

		if ($validator->fails()) {
			\Log::info('Intellectual section validation failed', [
				'errors' => $validator->errors()->toArray(),
				'data' => $data,
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

		return redirect()
			->route('qr.scan')
			->with('success', 'Дані про інтелектуальні здібності ' . $member->full_name . ' успішно збережено!');
	}

	public function storeSingle(Request $request): RedirectResponse
	{
		$data = array_merge(['code' => $request->route('code')], $request->all());
		$data['is_bad_boy'] = isset($data['is_bad_boy']);
		$data['does_sport'] = isset($data['does_sport']);
		$data['physical_limitations'] = isset($data['physical_limitations']);
		$data['first_time'] = isset($data['first_time']);
		$data['has_panic_attacks'] = isset($data['has_panic_attacks']);
		$data['is_musician'] = isset($data['is_musician']);
		unset($data['_token']);

		$validator = Validator::make($data, [
			'code'                  => 'required|string',
			// Основна інформація
			'full_name'             => 'required|string|max:255',
			'birth_date'            => 'required|date',
			'age'                   => 'required|numeric',
			'gender'                => 'required|in:male,female',
			'residence_type'        => 'required|in:stationary,non-stationary',
			'is_bad_boy'            => 'boolean',
			// Контактна інформація
			'photo'                 => 'nullable|image|max:2048',
			'child_phone'           => 'nullable|string|max:20',
			'parent_phone'          => 'nullable|string|max:20',
			'guardian_name'         => 'nullable|string|max:255',
			'additional_contact'    => 'nullable|string|max:255',
			'social_links'          => 'nullable|array',
			'social_links.*'        => 'nullable|url|max:255',
			'address'               => 'nullable|string|max:500',
			// Фізичні характеристики
			'height_cm'             => 'nullable|numeric|min:0|max:250',
			'body_type'             => 'nullable|in:thin,medium,plump',
			'does_sport'            => 'boolean',
			'sport_type'            => 'nullable|in:football,volleyball,tennis,wrestling,workout,other',
			'agility_level'         => 'nullable|integer|min:1|max:3',
			'strength_level'        => 'nullable|integer|min:1|max:3',
			// Медичні особливості
			'allergy_details'       => 'nullable|string|max:1000',
			'medical_restrictions'  => 'nullable|string|max:1000',
			'physical_limitations'  => 'boolean',
			'other_health_notes'    => 'nullable|string|max:1000',
			// Психічне здоров’я
			'first_time'            => 'boolean',
			'psychological_diagnosis' => 'nullable|string|max:255',
			'has_panic_attacks'     => 'boolean',
			'personality_type'      => 'nullable|in:extrovert,introvert,ambivert',
			// Творчість
			'artistic_ability'      => 'nullable|integer|min:1|max:3',
			'is_musician'           => 'boolean',
			'musical_instruments'   => 'nullable|in:guitar,piano,drums,vocals,other',
			'poetic_ability'        => 'nullable|integer|min:1|max:3',
			// Інтелектуальні здібності
			'english_level'         => 'nullable|integer|min:1|max:3',
			'general_iq_level'      => 'nullable|integer|min:1|max:3',
		]);

		if ($validator->fails()) {
			\Log::info('All sections validation failed', [
				'errors' => $validator->errors()->toArray(),
				'data' => $data,
			]);
			return redirect()
				->back()
				->withErrors($validator)
				->withInput();
		}

		$validated = $validator->validated();

		// Обробка фото
		if ($request->hasFile('photo')) {
			$file = $request->file('photo');
			$filename = 'photos/' . uniqid('photo_', true) . '.' . $file->getClientOriginalExtension();
			Storage::disk('public')->put($filename, file_get_contents($file));
			$validated['photo_url'] = Storage::url($filename);
		}
		unset($validated['photo']);

		// Перетворення social_links у JSON
		if (!empty($validated['social_links'])) {
			$validated['social_links'] = json_encode(array_filter($validated['social_links']));
		} else {
			$validated['social_links'] = null;
		}

		$member = Member::query()->updateOrCreate(
			['code' => $validated['code']],
			$validated
		);

		return redirect()
			->route('qr.scan')
			->with('success', 'Дані учасника ' . $member->full_name . ' успішно збережено!');
	}

}