<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('members', function (Blueprint $table) {
			$table->id();
			$table->string('code')->unique();
			$table->foreignId('squad_id')->nullable()->constrained()->nullOnDelete();

			/** 1. Основна інформація */
			$table->string('full_name')->nullable();
			$table->date('birth_date')->nullable();
			$table->enum('gender', ['male', 'female'])->nullable();
			$table->enum('residence_type', ['stationary', 'non-stationary'])->nullable();
			$table->boolean('is_bad_boy')->default(false);

			/** 2. Контактна інформація */
			$table->string('photo_url')->nullable();
			$table->string('child_phone')->nullable();
			$table->string('parent_phone')->nullable();
			$table->string('guardian_name')->nullable();
			$table->string('additional_contact')->nullable(); // екстрений контакт
			$table->json('social_links')->nullable(); // Instagram, Telegram, інші
			$table->text('address')->nullable();

			/** 3. Фізичні характеристики */
			$table->unsignedSmallInteger('height_cm')->nullable();
			$table->enum('body_type', ['thin', 'medium', 'plump'])->nullable();
			$table->boolean('does_sport')->default(false);
			$table->enum('sport_type', ['football', 'volleyball', 'tennis', 'wrestling', 'workout', 'other'])->nullable();
			$table->unsignedTinyInteger('agility_level')->nullable(); // 1–3
			$table->unsignedTinyInteger('strength_level')->nullable(); // 1–3

			/** 4. Медичні особливості */
			$table->text('allergy_details')->nullable();
			$table->text('medical_restrictions')->nullable(); // заборонені продукти
			$table->boolean('physical_limitations')->default(false); // обмеження активності
			$table->text('other_health_notes')->nullable();

			/** 5. Психічне здоровʼя */
			$table->boolean('first_time')->default(false);
			$table->boolean('exceptional')->default(false);
			$table->boolean('has_panic_attacks')->default(false);
			$table->enum('personality_type', ['extrovert', 'introvert', 'ambivert'])->nullable();

			/** 6. Творчість */
			$table->unsignedTinyInteger('artistic_ability')->nullable(); // 1–3
			$table->boolean('is_musician')->default(false);
			$table->enum('musical_instruments', ['guitar', 'piano', 'drums', 'vocals', 'other'])->nullable();
			$table->unsignedTinyInteger('poetic_ability')->nullable(); // 1–3

			/** 7. Інтелектуальні здібності */
			$table->unsignedTinyInteger('english_level')->nullable(); // 1–3
			$table->unsignedTinyInteger('general_iq_level')->nullable(); // 1–3

			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('members');
	}
};
