<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add verification and contact columns to users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone_number')) {
                $table->string('phone_number', 50)->nullable();
            }
            if (!Schema::hasColumn('users', 'is_contact_verified')) {
                $table->boolean('is_contact_verified')->default(false);
            }
            if (!Schema::hasColumn('users', 'account_status')) {
                $table->string('account_status', 30)->default('unverified');
            }
        });

        // Add filtered unique index for phone_number (handles multiple NULLs in SQL Server)
        try {
            if (DB::getDriverName() === 'sqlsrv') {
                DB::statement("IF NOT EXISTS (SELECT * FROM sys.indexes WHERE name = 'users_phone_number_unique' AND object_id = OBJECT_ID('users'))
                    CREATE UNIQUE NONCLUSTERED INDEX users_phone_number_unique ON users (phone_number) WHERE phone_number IS NOT NULL");
            } else {
                Schema::table('users', function (Blueprint $table) {
                    $table->unique('phone_number', 'users_phone_number_unique');
                });
            }
        } catch (\Throwable $e) {
            // Index might already exist
        }

        // 2. Create otp_verifications table for robust OTP flow
        if (!Schema::hasTable('otp_verifications')) {
            Schema::create('otp_verifications', function (Blueprint $table) {
                $table->id();
                $table->string('identifier', 191)->index(); // email or phone number
                $table->string('otp_code', 10);
                $table->string('type', 30)->default('registration'); // registration, recovery, login
                $table->dateTime('expires_at');
                $table->dateTime('verified_at')->nullable();
                $table->unsignedInteger('attempts')->default(0);
                $table->timestamps();
            });
        }

        // 3. Backfill existing users: approved/active users are marked contact verified and verified account
        try {
            DB::table('users')
                ->where('is_approved', 1)
                ->orWhere('status', 'active')
                ->update([
                    'is_contact_verified' => true,
                    'account_status' => 'verified',
                ]);

            DB::table('users')
                ->where('status', 'pending')
                ->where('account_status', 'unverified')
                ->update([
                    'account_status' => 'pending_review',
                ]);
        } catch (\Throwable $e) {
            // Silently handle if users table empty or during test runs
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_verifications');

        try {
            if (DB::getDriverName() === 'sqlsrv') {
                DB::statement("IF EXISTS (SELECT * FROM sys.indexes WHERE name = 'users_phone_number_unique' AND object_id = OBJECT_ID('users'))
                    DROP INDEX users_phone_number_unique ON users");
            }
        } catch (\Throwable $e) {}

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'phone_number')) {
                $table->dropColumn('phone_number');
            }
            if (Schema::hasColumn('users', 'is_contact_verified')) {
                $table->dropColumn('is_contact_verified');
            }
            if (Schema::hasColumn('users', 'account_status')) {
                $table->dropColumn('account_status');
            }
        });
    }
};
