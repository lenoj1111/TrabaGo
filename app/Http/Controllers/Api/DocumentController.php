<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    /**
     * Upload a document (e.g. Resume, Certificate, ID).
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:20480', // max 20MB
            'category' => 'nullable|string',
        ]);

        $file = $request->file('file');
        $rawCategory = $request->input('category', 'resume');
        $category = ($rawCategory === 'certs') ? 'certificate' : $rawCategory;
        
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $filename = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '-' . time() . '.' . $extension;

        $path = $file->storeAs("documents/{$category}", $filename, 'public');
        $fileUrl = Storage::url($path);

        $docId = 'doc_' . uniqid();

        // If authenticated jobseeker, also attach to details
        if ($request->user() && $request->user()->jobseeker) {
            $jobseeker = $request->user()->jobseeker;
            $details = $jobseeker->details;
            if (!$details) {
                $details = \App\Models\JobseekerDetail::create([
                    'jobseeker_id' => $jobseeker->jobseeker_id,
                    'training_certificates' => [],
                ]);
            }
            if ($details) {
                $existingDocs = is_array($details->training_certificates) 
                    ? $details->training_certificates 
                    : (json_decode($details->training_certificates ?? '[]', true) ?: []);

                $filtered = array_filter($existingDocs, function ($d) use ($category) {
                    $cat = ($d['category'] ?? '') === 'certs' ? 'certificate' : ($d['category'] ?? '');
                    if ($cat !== $category) return true;
                    if ($category === 'certificate' && !empty($d['enrollment_id'])) return true;
                    return false;
                });

                $filtered[] = [
                    'id' => $docId,
                    'category' => $category,
                    'name' => $originalName,
                    'file_url' => $fileUrl,
                    'url' => $fileUrl,
                    'status' => 'under_review',
                    'uploaded_at' => now()->toIso8601String(),
                ];

                $details->training_certificates = array_values($filtered);
                $details->save();
            }
        }

        return response()->json([
            'success' => true,
            'id' => $docId,
            'name' => $originalName,
            'fileUrl' => $fileUrl,
            'file_url' => $fileUrl,
            'category' => $category,
        ]);
    }
}
