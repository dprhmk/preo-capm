<?php

namespace App\Http\Controllers;

use App\Models\Member;
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
		$role = auth()->user()->role;
		$code = $request->code;
		$member = Member::where('code', $code)->first();

		return view('pages.member', [
			'role' => $role,
			'code' => $code,
			'member' => $member,
		]);
	}

	public function store(Request $request, MemberService $memberService)
	{
		return $memberService->store($request);
	}
}