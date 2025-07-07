<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Squad;
use App\Services\MemberService;
use Illuminate\Http\Request;

class MemberController
{
	public function index()
	{
		$members = Member::query()->get();

		$members->each(function (Member $member) {
			$requiredFields = [
				'photo_url',
				'full_name',
				'birth_date',
				'gender',
				'residence_type',
				'height_cm',
				'body_type',
				'agility_level',
				'strength_level',
				'personality_type',
				'artistic_ability',
				'poetic_ability',
			];

			$isFilled = true;

			foreach ($requiredFields as $field) {
				if (empty($member->$field)) {
					$isFilled = false;
					break;
				}
			}

			$member->isRequiredFilled = $isFilled;
		});

		return view('pages.members', [
			'members' => $members
		]);
	}


	public function show(Request $request)
	{
		if( ! auth()->check()) {
			return view('pages.mem');
		}

		$role = auth()->user()->role;
		$code = $request->code;
		$member = Member::where('code', $code)->first();
		$squads = Squad::query()->get();

		return view('pages.member', [
			'role' => $role,
			'code' => $code,
			'member' => $member,
			'squads' => $squads
		]);
	}

	public function store(Request $request, MemberService $memberService)
	{
		return $memberService->store($request);
	}
}