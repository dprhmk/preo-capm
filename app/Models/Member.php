<?php

namespace App\Models;

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
}
