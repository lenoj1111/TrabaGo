<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EmployerRegistrationDualEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_employer_registration_page_displays_dual_email_and_contact_management(): void
    {
        $response = $this->get(route('employer.register'));

        $response->assertStatus(200);
        $response->assertSee('Dual Email & Contact Management', false);
        $response->assertSee('Dual Email Inboxes (Primary & Secondary)', false);
        $response->assertSee('name="primary_email"', false);
        $response->assertSee('name="secondary_email"', false);
        $response->assertSee('Authorized Contact Person');
    }

    public function test_employer_registers_successfully_with_dual_emails_and_contact_information(): void
    {
        $response = $this->post(route('employer.register.post'), [
            'email' => 'primary_corp@visayastech.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'employer',
            'company_name' => 'Visayas Tech Corp',
            'contact_person' => 'Atty. Maria Clara Santos',
            'contact_position' => 'Head of HR',
            'primary_email' => 'primary_corp@visayastech.com',
            'secondary_email' => 'recruitment.backup@visayastech.com',
            'phone' => '(032) 234-5678',
            'terms' => '1',
        ]);
        $response->assertRedirect(route('employer.register.success'));

        $this->assertDatabaseHas('employers', [
            'company_name' => 'Visayas Tech Corp',
            'contact_person' => 'Atty. Maria Clara Santos',
            'primary_email' => 'primary_corp@visayastech.com',
            'secondary_email' => 'recruitment.backup@visayastech.com',
        ]);
    }

    public function test_admin_can_provision_employer_account_with_business_nature_and_dual_email(): void
    {
        $admin = \App\Models\User::create([
            'email' => 'admin_prov@trabago.gov.ph',
            'password' => bcrypt('Password123!'),
            'role' => 'admin',
            'status' => 'active',
            'is_approved' => 1,
        ]);

        $response = $this->actingAs($admin)->post('/admin/users', [
            'role' => 'employer',
            'company_name' => 'Employer',
            'contact_person' => 'HR',
            'email' => 'employer@trabago.com',
            'secondary_email' => 'employer1@trabago.com',
            'contact_phone' => '09926620958',
            'company_address' => 'It park',
            'business_nature' => 'BPO',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'auto_accredit' => '1',
        ]);

        $response->assertRedirect('/admin/employers');

        $this->assertDatabaseHas('employers', [
            'company_name' => 'Employer',
            'contact_person' => 'HR',
            'primary_email' => 'employer@trabago.com',
            'secondary_email' => 'employer1@trabago.com',
            'contact_phone' => '09926620958',
            'business_nature' => 'BPO',
            'is_accredited' => 1,
        ]);
    }
}
