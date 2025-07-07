<?php

namespace App\Http\Controllers;

use App\Models\Squad;
use App\Models\Member;
use App\Services\SquadAnalyticsService;
use App\Services\SquadDistributionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;

class SquadController
{
	protected SquadDistributionService $squadDistributionService;
	protected SquadAnalyticsService $squadAnalyticsService;

	public function __construct(
		SquadDistributionService $squadDistributionService,
		SquadAnalyticsService $squadAnalyticsService
	)
	{
		$this->squadDistributionService = $squadDistributionService;
		$this->squadAnalyticsService = $squadAnalyticsService;
	}

	public function index()
	{
		$squads = Squad::with(['members' => function ($query) {
			$query
				->orderByDesc('is_leader')
				->orderByRaw("`physical_score` + `mental_score` DESC");
		}])
			->get()
			->groupBy('name')
			->map(fn ($squad) => $squad->first())
			->values();

		$analyticsData = $this->squadAnalyticsService->getAnalytics($squads);

		return view('pages.squads', [
			'squads' => $squads,
			'analyticsData' => $analyticsData,
		]);
	}

	public function truncateDb()
	{
		Schema::disableForeignKeyConstraints();
		Squad::truncate();
		Member::truncate();
		Schema::enableForeignKeyConstraints();

		return redirect()->route('squads.index')->with('success', 'Базу очищено');
	}

	public function store(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'squad_count' => 'required|integer|min:1',
		]);

		if ($validator->fails()) {
			return redirect()->back()->withErrors($validator)->withInput();
		}

		$squadCount = (int) $request->input('squad_count');
		$leaderNames = $request->input('leader_names', []);
		$assistantNames = $request->input('assistant_names', []);

		Schema::disableForeignKeyConstraints();
		Squad::truncate();
		Schema::enableForeignKeyConstraints();
		Member::query()->update(['squad_id' => null]);
		$members = Member::all();

		if ($members->isEmpty()) {
			return view('pages.squads', ['squads' => []]);
		}

		$squadMembersList = $this->squadDistributionService->distribute($members, $squadCount);

		foreach ($squadMembersList as $index => $squadMembers) {
			$physicalScore = array_sum(array_map(fn ($m) => $m->physical_score ?? 0, $squadMembers));
			$mentalScore = array_sum(array_map(fn ($m) => $m->mental_score ?? 0, $squadMembers));

			$squad = Squad::create([
				'name' => 'Загін ' . ($index + 1),
				'leader_name' => $leaderNames[$index] ?? null,
				'assistant_name' => $assistantNames[$index] ?? null,
				'physical_score' => round($physicalScore, 2),
				'mental_score' => round($mentalScore, 2),
				'color' => fake()->hexColor(),
			]);

			foreach ($squadMembers as $member) {
				$member->update(['squad_id' => $squad->id]);
			}
		}

		return redirect()->route('squads.index');
	}

	public function edit(Squad $squad)
	{
		return view('pages.squad', compact('squad'));
	}

	public function update(Request $request, Squad $squad)
	{
		$request->validate([
			'name' => 'required|string|min:3|max:50',
			'leader_name' => 'required|string|min:2|max:100',
			'assistant_name' => 'nullable|string|max:100',
			'color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
		]);

		$squad->update($request->only(['name', 'leader_name', 'assistant_name', 'color']));

		return redirect()->route('squads.index')->with('success', 'Загін оновлено!');
	}
}
