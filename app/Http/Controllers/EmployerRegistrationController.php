<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class EmployerRegistrationController extends Controller
{
    /**
     * Show the employer registration form
     */
    public function showRegistrationForm()
    {
        return view('employer.register');
    }

    /**
     * Handle employer registration
     */
    public function register(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            // Step 1: Account Information
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:employer',

            // Step 2: Company Information
            'company_name' => 'required|string|max:150',
            'company_description' => 'nullable|string',
            'industry' => 'nullable|string|max:100',
            'company_size' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:255',
            'company_type' => 'nullable|string|max:100',

            // Step 3: Contact Information
            'contact_person' => 'required|string|max:150',
            'contact_position' => 'required|string|max:100',
            'phone' => 'required|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',

            // Step 4: Documents
            'documents.business_permit' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'documents.sec_registration' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'documents.mayors_permit' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'documents.tin' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'terms' => 'required|accepted',
        ]);

        try {
            DB::beginTransaction();

            // 1. Create user account
            $user = DB::table('users')->insertGetId([
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'employer',
                'status' => 'active',
                'is_approved' => 0,
                'created_at' => Carbon::now(),
            ]);

            // 2. Create employer profile
            $employerId = DB::table('employers')->insertGetId([
                'user_id' => $user,
                'company_name' => $validated['company_name'],
                'is_accredited' => 0,
                'accredited_at' => null,
            ]);

            // 3. Store documents if uploaded
            $documents = [];
            $documentTypes = ['business_permit', 'sec_registration', 'mayors_permit', 'tin'];
            
            foreach ($documentTypes as $docType) {
                if ($request->hasFile("documents.{$docType}")) {
                    $file = $request->file("documents.{$docType}");
                    $path = $file->store("employer_documents/{$employerId}", 'public');
                    $documents[$docType] = $path;
                }
            }

            // 4. Create accreditation record
            DB::table('employer_accreditation')->insert([
                'employer_id' => $employerId,
                'documents' => json_encode($documents),
                'submitted_at' => Carbon::now(),
                'ocr_validation_status' => 'pending',
            ]);

            // 5. Create user profile (for contact information)
            DB::table('user_profiles')->insert([
                'user_id' => $user,
                'full_name' => $validated['contact_person'],
                'position' => $validated['contact_position'],
                'phone' => $validated['phone'],
            ]);

            DB::commit();

            // Redirect to success page
            return redirect()->route('employer.register.success')
                ->with('success', 'Employer registration successful! Please wait for admin approval.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete uploaded files if any
            if (isset($documents)) {
                foreach ($documents as $path) {
                    Storage::disk('public')->delete($path);
                }
            }

            return back()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Show registration success page
     */
    public function success()
    {
        return view('employer.success');
    }
}