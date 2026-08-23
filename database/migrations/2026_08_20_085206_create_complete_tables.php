<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. USERS
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('email', 150)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 255);
            $table->enum('role', ['admin', 'supervisor', 'pesd_supervisor', 'jpo', 'trainer', 'lmo', 'employer', 'jobseeker']);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->boolean('is_approved')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });

        // 2. USER PROFILES
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id('profile_id');
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('full_name', 150)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('position', 100)->nullable();
            $table->string('department', 150)->nullable();
            $table->string('office', 150)->nullable();
            $table->string('specialization', 150)->nullable();
            $table->string('area', 150)->nullable();
            $table->enum('trainer_type', ['dmdp', 'partner'])->default('dmdp');
            $table->string('partner_institution', 255)->nullable();
            $table->boolean('is_trainer_approved')->default(0);
            $table->unsignedBigInteger('trainer_approved_by')->nullable();
            $table->date('trainer_approved_at')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('user_id')->on('users');
            $table->foreign('trainer_approved_by')->references('user_id')->on('users');
        });

        // 3. JOBSEEKERS
        Schema::create('jobseekers', function (Blueprint $table) {
            $table->id('jobseeker_id');
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('middle_name', 100)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('sex', 20)->nullable();
            $table->string('civil_status', 50)->nullable();
            $table->string('citizenship', 100)->nullable();
            $table->string('mobile_number', 50)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('employment_status', 50)->nullable();
            $table->foreign('user_id')->references('user_id')->on('users');
        });

        // 4. JOBSEEKER DETAILS (JSON)
        Schema::create('jobseeker_details', function (Blueprint $table) {
            $table->id('detail_id');
            $table->unsignedBigInteger('jobseeker_id')->unique();
            $table->json('address')->nullable();
            $table->json('education')->nullable();
            $table->json('work_experience')->nullable();
            $table->json('eligibility')->nullable();
            $table->json('language_proficiency')->nullable();
            $table->json('training_certificates')->nullable();
            $table->foreign('jobseeker_id')->references('jobseeker_id')->on('jobseekers');
        });

        // 5. JOBSEEKER SKILLS
        Schema::create('jobseeker_skills', function (Blueprint $table) {
            $table->id('skill_id');
            $table->unsignedBigInteger('jobseeker_id');
            $table->string('skill_name', 100);
            $table->enum('skill_type', ['technical', '21st_century']);
            $table->foreign('jobseeker_id')->references('jobseeker_id')->on('jobseekers');
        });

        // 6. JOB PREFERENCES
        Schema::create('job_preferences', function (Blueprint $table) {
            $table->id('preference_id');
            $table->unsignedBigInteger('jobseeker_id')->unique();
            $table->string('occupation1', 150)->nullable();
            $table->string('occupation2', 150)->nullable();
            $table->string('occupation3', 150)->nullable();
            $table->string('industry1', 150)->nullable();
            $table->string('industry2', 150)->nullable();
            $table->string('industry3', 150)->nullable();
            $table->string('preferred_location', 255)->nullable();
            $table->string('salary_expectation', 100)->nullable();
            $table->foreign('jobseeker_id')->references('jobseeker_id')->on('jobseekers');
        });

        // 7. SOCIAL STATUS
        Schema::create('social_status', function (Blueprint $table) {
            $table->id('status_id');
            $table->unsignedBigInteger('jobseeker_id')->unique();
            $table->boolean('is_4ps')->nullable();
            $table->string('household_id', 100)->nullable();
            $table->boolean('is_ofw')->nullable();
            $table->boolean('is_pwd')->nullable();
            $table->string('pwd_type', 255)->nullable();
            $table->foreign('jobseeker_id')->references('jobseeker_id')->on('jobseekers');
        });

        // 8. EMPLOYERS
        Schema::create('employers', function (Blueprint $table) {
            $table->id('employer_id');
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('company_name', 150);
            $table->boolean('is_accredited')->default(0);
            $table->date('accredited_at')->nullable();
            $table->foreign('user_id')->references('user_id')->on('users');
        });

        // 9. JOB POSTINGS
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id('job_id');
            $table->unsignedBigInteger('employer_id')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->text('qualifications')->nullable();
            $table->integer('vacancy_count')->default(1);
            $table->date('valid_until')->nullable();
            $table->boolean('accepts_disability')->nullable();
            $table->string('disability_type', 100)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'closed'])->default('pending');
            $table->enum('created_by', ['employer', 'admin'])->default('employer');
            $table->date('created_at')->nullable();
            $table->date('approved_at')->nullable();
            $table->foreign('employer_id')->references('employer_id')->on('employers');
            $table->foreign('admin_id')->references('profile_id')->on('user_profiles');
        });

        // 10. JOB APPLICATIONS
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id('application_id');
            $table->unsignedBigInteger('job_id');
            $table->unsignedBigInteger('jobseeker_id');
            $table->enum('status', ['pending', 'reviewed', 'interview', 'hired', 'rejected'])->default('pending');
            $table->boolean('referred_by_jpo')->default(0);
            $table->text('jpo_notes')->nullable();
            $table->dateTime('jpo_evaluated_at')->nullable();
            $table->dateTime('interview_schedule')->nullable();
            $table->enum('interview_mode', ['online', 'onsite'])->nullable();
            $table->string('interview_location', 255)->nullable();
            $table->enum('interview_status', ['scheduled', 'completed', 'cancelled'])->nullable();
            $table->enum('jobseeker_response', ['pending', 'confirmed', 'declined'])->nullable();
            $table->date('hired_date')->nullable();
            $table->timestamps();
            $table->foreign('job_id')->references('job_id')->on('job_postings');
            $table->foreign('jobseeker_id')->references('jobseeker_id')->on('jobseekers');
        });

        // 11. JPO ASSESSMENTS
        Schema::create('jpo_assessments', function (Blueprint $table) {
            $table->id('assessment_id');
            $table->unsignedBigInteger('application_id')->unique();
            $table->unsignedBigInteger('jpo_id');
            $table->enum('recommendation', ['refer', 'training']);
            $table->text('remarks')->nullable();
            $table->date('referral_date')->nullable();
            $table->text('referral_notes')->nullable();
            $table->foreign('application_id')->references('application_id')->on('job_applications');
            $table->foreign('jpo_id')->references('profile_id')->on('user_profiles');
        });

        // 12. TRAINING PROGRAMS
        Schema::create('training_programs', function (Blueprint $table) {
            $table->id('training_id');
            $table->unsignedBigInteger('trainer_id');
            $table->string('title', 150);
            $table->enum('training_type', ['online', 'laboratory_onsite'])->default('online');
            $table->integer('duration_months')->nullable();
            $table->text('description')->nullable();
            $table->foreign('trainer_id')->references('profile_id')->on('user_profiles');
        });

        // 13. TRAINING TOPICS
        Schema::create('training_topics', function (Blueprint $table) {
            $table->id('topic_id');
            $table->unsignedBigInteger('training_id');
            $table->string('title', 150);
            $table->text('video_url')->nullable();
            $table->integer('topic_order')->default(0);
            $table->json('questions')->nullable();
            $table->foreign('training_id')->references('training_id')->on('training_programs');
        });

        // 14. TRAINING ENROLLMENTS
        Schema::create('training_enrollments', function (Blueprint $table) {
            $table->id('enrollment_id');
            $table->unsignedBigInteger('jobseeker_id');
            $table->unsignedBigInteger('training_id');
            $table->enum('training_type', ['online', 'laboratory_onsite']);
            $table->enum('status', ['enrolled', 'in_progress', 'completed', 'failed'])->default('enrolled');
            $table->integer('current_topic')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('lab_remarks')->nullable();
            $table->json('answers')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->boolean('passed')->default(0)->nullable();
            $table->string('certificate_no', 100)->nullable();
            $table->boolean('certificate_issued')->default(0)->nullable();
            $table->dateTime('certificate_issued_at')->nullable();
            $table->text('trainer_feedback')->nullable();
            $table->foreign('jobseeker_id')->references('jobseeker_id')->on('jobseekers');
            $table->foreign('training_id')->references('training_id')->on('training_programs');
        });

        // 15. EMPLOYER ACCREDITATION
        Schema::create('employer_accreditation', function (Blueprint $table) {
            $table->id('accreditation_id');
            $table->unsignedBigInteger('employer_id')->unique();
            $table->json('documents')->nullable();
            $table->text('ocr_raw_text')->nullable();
            $table->string('ocr_classified_document_type', 100)->nullable();
            $table->json('ocr_extracted_fields')->nullable();
            $table->decimal('ocr_confidence_score', 5, 2)->nullable();
            $table->enum('ocr_validation_status', ['pending', 'auto_approved', 'manual_review', 'rejected'])->default('pending');
            $table->dateTime('auto_approved_at')->nullable();
            $table->string('status', 50)->default('submitted_to_jpo');
            $table->boolean('jpo_reviewed')->default(0);
            $table->dateTime('jpo_reviewed_at')->nullable();
            $table->text('jpo_remarks')->nullable();
            $table->boolean('supervisor_approved')->default(0);
            $table->dateTime('supervisor_approved_at')->nullable();
            $table->text('supervisor_remarks')->nullable();
            $table->boolean('admin_approved')->default(0);
            $table->date('admin_approved_at')->nullable();
            $table->date('submitted_at')->nullable();
            $table->date('approved_at')->nullable();
            $table->foreign('employer_id')->references('employer_id')->on('employers');
        });

        // 16. NOTIFICATIONS
        Schema::create('notifications', function (Blueprint $table) {
            $table->id('notification_id');
            $table->unsignedBigInteger('user_id');
            $table->string('title', 150);
            $table->text('message');
            $table->string('type', 50);
            $table->boolean('is_read')->default(0);
            $table->unsignedBigInteger('related_id')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('user_id')->on('users');
        });

        // 17. PLACEMENT REPORTS
        Schema::create('placement_reports', function (Blueprint $table) {
            $table->id('report_id');
            $table->unsignedBigInteger('jpo_id')->nullable();
            $table->unsignedBigInteger('employer_id');
            $table->enum('report_type', ['dmdp', 'employer_monthly']);
            $table->date('report_month');
            $table->json('report_data');
            $table->enum('status', ['pending', 'submitted_to_jpo', 'jpo_evaluated', 'approved', 'rejected'])->default('submitted_to_jpo');
            $table->boolean('jpo_evaluated')->default(0);
            $table->dateTime('jpo_evaluated_at')->nullable();
            $table->text('jpo_remarks')->nullable();
            $table->text('admin_remarks')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->date('approved_at')->nullable();
            $table->foreign('jpo_id')->references('profile_id')->on('user_profiles');
            $table->foreign('employer_id')->references('employer_id')->on('employers');
        });

        // 18. POSTING RESTRICTIONS
        Schema::create('posting_restrictions', function (Blueprint $table) {
            $table->id('restriction_id');
            $table->unsignedBigInteger('employer_id');
            $table->date('restriction_start_date');
            $table->date('restriction_end_date');
            $table->text('reason')->nullable();
            $table->foreign('employer_id')->references('employer_id')->on('employers');
        });

        // 19. AUDIT LOGS
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->unsignedBigInteger('user_id');
            $table->text('action');
            $table->timestamps();
            $table->foreign('user_id')->references('user_id')->on('users');
        });
    }

    public function down(): void
    {
        // Drop in reverse order to avoid foreign key constraint issues
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('posting_restrictions');
        Schema::dropIfExists('placement_reports');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('employer_accreditation');
        Schema::dropIfExists('training_enrollments');
        Schema::dropIfExists('training_topics');
        Schema::dropIfExists('training_programs');
        Schema::dropIfExists('jpo_assessments');
        Schema::dropIfExists('job_applications');
        Schema::dropIfExists('job_postings');
        Schema::dropIfExists('employers');
        Schema::dropIfExists('social_status');
        Schema::dropIfExists('job_preferences');
        Schema::dropIfExists('jobseeker_skills');
        Schema::dropIfExists('jobseeker_details');
        Schema::dropIfExists('jobseekers');
        Schema::dropIfExists('user_profiles');
        Schema::dropIfExists('users');
    }
};