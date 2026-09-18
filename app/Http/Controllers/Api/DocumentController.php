<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobseekerDetail;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    /**
     * Get all documents in the jobseeker's Document Hub vault.
     */
    public function getAll(Request $request)
    {
        $user = $request->user();
        $jobseeker = $user ? $user->jobseeker : null;
        if (!$jobseeker) {
            return response()->json([
                'documents' => [],
                'categories' => ['resume' => null, 'valid_id' => null, 'certificate' => null, 'pwd_id' => null],
            ]);
        }

        $details = $jobseeker->details;
        $docs = [];
        if ($details && !empty($details->training_certificates)) {
            $docs = is_array($details->training_certificates) 
                ? $details->training_certificates 
                : (json_decode($details->training_certificates, true) ?: []);
        }

        $categories = [
            'resume' => null,
            'valid_id' => null,
            'certificate' => null,
            'pwd_id' => null,
        ];

        foreach ($docs as $d) {
            $cat = $d['category'] ?? '';
            if (array_key_exists($cat, $categories)) {
                $categories[$cat] = $d;
            }
        }

        return response()->json([
            'success' => true,
            'documents' => array_values($docs),
            'categories' => $categories,
        ]);
    }

    /**
     * Upload a document (e.g. Resume, Valid ID, Certificate, PWD ID).
     */
    public function upload(Request $request)
    {
        $request->validate([
            'category' => 'nullable|string|in:resume,valid_id,certificate,certs,pwd_id',
        ]);

        $file = $request->file('file') ?: $request->file('document');
        if (!$file) {
            return response()->json(['success' => false, 'message' => 'No file was uploaded.'], 422);
        }

        $rawCategory = $request->input('category', 'resume');
        $category = ($rawCategory === 'certs') ? 'certificate' : $rawCategory;
        
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $cleanName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
        $filename = time() . '_' . $cleanName . '.' . $extension;

        $user = $request->user();
        $jobseekerId = $user && $user->jobseeker ? $user->jobseeker->jobseeker_id : 'guest';
        $path = $file->storeAs("documents/{$jobseekerId}", $filename, 'public');
        $fileUrl = Storage::url($path);

        $docId = 'doc_' . uniqid();

        if ($user && $user->jobseeker) {
            $jobseeker = $user->jobseeker;
            $details = $jobseeker->details;
            if (!$details) {
                $details = JobseekerDetail::create([
                    'jobseeker_id' => $jobseeker->jobseeker_id,
                    'training_certificates' => [],
                ]);
            }

            $existingDocs = is_array($details->training_certificates) 
                ? $details->training_certificates 
                : (json_decode($details->training_certificates ?? '[]', true) ?: []);

            $filtered = array_filter($existingDocs, function ($d) use ($category) {
                $cat = ($d['category'] ?? '') === 'certs' ? 'certificate' : ($d['category'] ?? '');
                if ($cat !== $category) return true;
                if ($category === 'certificate' && !empty($d['enrollment_id'])) return true;
                return false;
            });

            $newDoc = [
                'id' => $docId,
                'category' => $category,
                'name' => $originalName,
                'file_url' => $fileUrl,
                'url' => $fileUrl,
                'status' => 'under_review',
                'uploaded_at' => now()->toIso8601String(),
            ];

            $filtered[] = $newDoc;
            $details->training_certificates = array_values($filtered);
            $details->save();

            Notification::create([
                'user_id' => $user->user_id,
                'title' => 'Document Uploaded',
                'message' => "Your " . ucfirst(str_replace('_', ' ', $category)) . " has been uploaded and is under review.",
                'type' => 'approval',
                'is_read' => false,
            ]);
        }

        return response()->json([
            'success' => true,
            'id' => $docId,
            'name' => $originalName,
            'fileUrl' => $fileUrl,
            'file_url' => $fileUrl,
            'category' => $category,
            'message' => ucfirst(str_replace('_', ' ', $category)) . ' uploaded successfully to your vault.',
        ]);
    }

    /**
     * Delete a document from the vault.
     */
    public function delete(Request $request, $id)
    {
        $user = $request->user();
        $jobseeker = $user ? $user->jobseeker : null;
        if (!$jobseeker || !$jobseeker->details) {
            return response()->json(['success' => false, 'message' => 'Jobseeker details not found'], 404);
        }

        $details = $jobseeker->details;
        $existingDocs = is_array($details->training_certificates) 
            ? $details->training_certificates 
            : (json_decode($details->training_certificates ?? '[]', true) ?: []);

        $filtered = array_filter($existingDocs, function ($d) use ($id) {
            return ($d['id'] ?? '') !== $id;
        });

        $details->training_certificates = array_values($filtered);
        $details->save();

        return response()->json([
            'success' => true,
            'message' => 'Document removed from vault.',
        ]);
    }
}
