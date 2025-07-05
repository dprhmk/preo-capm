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
		$members = Member::query()->paginate(150);

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