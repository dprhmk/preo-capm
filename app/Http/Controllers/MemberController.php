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

	public function delete(Request $request)
	{
		$code = $request->code;
		$member = Member::query()->where('code', $code)->first();

		if($member) {
			$member->delete();
		}

		return redirect()
			->route('members.index')
			->with('success', 'Учасника ' . $member->full_name . ' успішно видалено!');
	}
}