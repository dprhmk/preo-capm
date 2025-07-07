<?php

namespace App\Http\Controllers;

use App\Models\Squad;

class ContactsController
{
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

		return view('pages.contacts', [
			'squads' => $squads,
		]);
	}
}
