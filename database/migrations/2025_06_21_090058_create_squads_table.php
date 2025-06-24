<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
	    Schema::create('squads', function (Blueprint $table) {
		    $table->id();
		    $table->string('name');
		    $table->string('leader_name')->nullable();
		    $table->string('assistant_name')->nullable();
		    $table->decimal('physical_score', 8, 2)->nullable();
		    $table->decimal('mental_score', 8, 2)->nullable();
		    $table->timestamps();
	    });
    }
	
    public function down(): void
    {
        Schema::dropIfExists('squads');
    }
};
