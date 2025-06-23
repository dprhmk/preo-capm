<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Services\MemberService;
use Illuminate\Http\Request;

class MemberController
{
	public function index()
	{
		return view('pages.members');
	}

	public function show(Request $request)
	{
		$role = auth()->user()->role;

		$code = $request->code;

		$member = Member::where('code', $code)->first();

		return view('pages.member', [
			'code' => $code,
			'role' => $role,
			'member' => $member,
		]);
	}

	public function store(Request $request, MemberService $memberService)
	{
		$role = auth()->user()->role;
		return $memberService->storeSection($request, $role);
	}
}