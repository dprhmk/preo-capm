<?php

namespace App\Models;

use App\Services\MemberScoreService;
use App\Services\MemberService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Member extends Model
{
	use HasFactory;

	protected $table = 'members';
	protected $guarded = ['id'];

	protected $casts = [
		'birth_date' => 'date',
		'social_links' => 'array',
	];

	public function squad(): BelongsTo
	{
		return $this->belongsTo(Squad::class);
	}

	protected static function boot(): void
	{
		parent::boot();

		static::saving(function (Member $member) {
			$memberService = new MemberService();
			$scoreService = app(MemberScoreService::class);


			$scores = $scoreService->calculateScores($member);
			$member->physical_score = $scores['physical_score'];
			$member->mental_score = $scores['mental_score'];

			$member->is_required_filled = $memberService->isRequiredFilled($member);
		});

		static::deleted(function (Member $member) {
			$squad = $member->squad;

			$squadMembers = $squad->members()->get();

			$squad->physical_score = round($squadMembers->sum('physical_score'), 2);
			$squad->mental_score = round($squadMembers->sum('mental_score'), 2);

			$squad->save();
		});
	}
}
