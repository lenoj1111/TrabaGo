<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    /**
     * Display the Contact Us page.
     */
    public function index()
    {
        return view('contact');
    }

    /**
     * Handle public contact form submission.
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:150',
            'email' => 'required|email|max:150',
            'phone' => 'nullable|string|max:50',
            'inquiry_type' => 'required|string|in:general,jobseeker,employer,training,pwd,technical',
            'subject' => 'required|string|max:200',
            'message' => 'required|string|max:3000',
        ]);

        $typeLabels = [
            'general' => 'General Inquiry',
            'jobseeker' => 'Jobseeker Assistance',
            'employer' => 'Employer Accreditation / Hiring',
            'training' => 'Skills Training & Certification',
            'pwd' => 'PWD / Inclusive Employment',
            'technical' => 'Technical Portal Support',
        ];

        $inquiryLabel = $typeLabels[$validated['inquiry_type']] ?? 'General Support';

        // Notify Administrator & JPO staff
        try {
            $staffUsers = User::whereIn('role', ['admin', 'jpo'])->get();
            foreach ($staffUsers as $staff) {
                Notification::create([
                    'user_id' => $staff->user_id,
                    'title' => "New Contact Inquiry: {$inquiryLabel}",
                    'message' => "From {$validated['full_name']} ({$validated['email']}): \"{$validated['subject']}\"",
                    'type' => 'inquiry',
                    'is_read' => false,
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Could not record contact notification: ' . $e->getMessage());
        }

        return redirect()->route('contact')->with('success', 'Thank you for reaching out! Your inquiry has been forwarded to the Cebu City DMDP Placement Office. Our team will review your message and respond within 24–48 business hours.');
    }
}
