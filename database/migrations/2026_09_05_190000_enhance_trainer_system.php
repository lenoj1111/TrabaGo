<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add columns to training_programs
        Schema::table('training_programs', function (Blueprint $table) {
            if (!Schema::hasColumn('training_programs', 'auto_generate_certificate')) {
                $table->boolean('auto_generate_certificate')->default(true);
            }
            if (!Schema::hasColumn('training_programs', 'skills')) {
                $table->text('skills')->nullable();
            }
            if (!Schema::hasColumn('training_programs', 'passing_score')) {
                $table->integer('passing_score')->default(80);
            }
            if (!Schema::hasColumn('training_programs', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('training_programs', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });

        // 2. Create training_assessments table for trainer-authored questions
        if (!Schema::hasTable('training_assessments')) {
            Schema::create('training_assessments', function (Blueprint $table) {
                $table->id('assessment_id');
                $table->unsignedBigInteger('training_id');
                $table->text('question');
                $table->string('question_type', 50)->default('multiple_choice');
                $table->json('options'); // array of choices e.g. ["Choice A", "Choice B", "Choice C", "Choice D"]
                $table->integer('correct_answer')->default(0); // 0-indexed index of the correct choice
                $table->text('explanation')->nullable();
                $table->integer('points')->default(1);
                $table->timestamps();

                $table->foreign('training_id')->references('training_id')->on('training_programs')->onDelete('cascade');
            });
        }

        // 3. Add enrolled_skills to training_enrollments
        Schema::table('training_enrollments', function (Blueprint $table) {
            if (!Schema::hasColumn('training_enrollments', 'enrolled_skills')) {
                $table->text('enrolled_skills')->nullable();
            }
        });

        // 4. Add bio to user_profiles if missing
        Schema::table('user_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('user_profiles', 'bio')) {
                $table->text('bio')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_assessments');

        Schema::table('training_programs', function (Blueprint $table) {
            $table->dropColumn(['auto_generate_certificate', 'skills', 'passing_score', 'created_at', 'updated_at']);
        });

        Schema::table('training_enrollments', function (Blueprint $table) {
            $table->dropColumn(['enrolled_skills']);
        });

        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn(['bio']);
        });
    }
};
