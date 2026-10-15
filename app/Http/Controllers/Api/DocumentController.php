<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobseekerDetail;
use App\Models\Notification;
use App\Traits\ResolvesJobseeker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    use ResolvesJobseeker;

    /**
     * Get all documents in the jobseeker's Document Hub vault.
     */
    public function getAll(Request $request)
    {
        $jobseeker = $this->resolveJobseeker($request, false);
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

        foreach ($docs as &$d) {
            if (!empty($d['file_url']) && !str_starts_with($d['file_url'], 'http://') && !str_starts_with($d['file_url'], 'https://')) {
                $d['file_url'] = url($d['file_url']);
            }
            if (!empty($d['url']) && !str_starts_with($d['url'], 'http://') && !str_starts_with($d['url'], 'https://')) {
                $d['url'] = url($d['url']);
            }
            $cat = $d['category'] ?? '';
            if (array_key_exists($cat, $categories)) {
                $categories[$cat] = $d;
            }
        }
        unset($d);

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
            return response()->json(['success' => false, 'message' => 'No file was uploaded. Please attach a document file.'], 422);
        }

        if (!$file->isValid()) {
            return response()->json(['success' => false, 'message' => 'The uploaded file is invalid or corrupt.'], 422);
        }

        $rawCategory = $request->input('category', 'resume');
        $category = ($rawCategory === 'certs') ? 'certificate' : $rawCategory;

        // 1. Enforce 5MB file size limit
        $maxSizeBytes = 5 * 1024 * 1024;
        if ($file->getSize() > $maxSizeBytes) {
            return response()->json([
                'success' => false, 
                'message' => 'The file size exceeds the maximum limit of 5MB. Please choose a smaller file.'
            ], 422);
        }

        // 2. Category-specific allowed formats
        $allowedExtensions = match($category) {
            'resume' => ['pdf', 'doc', 'docx'],
            'valid_id', 'pwd_id' => ['jpg', 'jpeg', 'png', 'pdf'],
            'certificate' => ['pdf', 'jpg', 'jpeg', 'png'],
            default => ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'],
        };

        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $allowedExtensions)) {
            $allowedList = strtoupper(implode(', ', $allowedExtensions));
            return response()->json([
                'success' => false,
                'message' => "Invalid file format for " . ucfirst(str_replace('_', ' ', $category)) . ". Allowed formats: {$allowedList}.",
            ], 422);
        }

        // 3. Minimum resolution check for government ID images to ensure legibility
        if (in_array($category, ['valid_id', 'pwd_id']) && in_array($extension, ['jpg', 'jpeg', 'png'])) {
            $imageInfo = @getimagesize($file->getRealPath());
            if ($imageInfo) {
                [$width, $height] = $imageInfo;
                if ($width < 300 || $height < 200) {
                    return response()->json([
                        'success' => false,
                        'message' => 'The uploaded ID image is too low-resolution. Please upload a clear photo (at least 300x200 pixels) to ensure legibility.',
                    ], 422);
                }
            }
        }
        
        $originalName = $file->getClientOriginalName();
        $cleanName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
        $filename = time() . '_' . $cleanName . '.' . $extension;

        $user = $this->resolveUser($request);
        $jobseeker = $this->resolveJobseeker($request, true);
        $jobseekerId = $jobseeker ? $jobseeker->jobseeker_id : 'guest';
        $path = $file->storeAs("documents/{$jobseekerId}", $filename, 'public');
        $fileUrl = Storage::url($path);
        if (!str_starts_with($fileUrl, 'http://') && !str_starts_with($fileUrl, 'https://')) {
            $fileUrl = url($fileUrl);
        }

        $docId = 'doc_' . uniqid();

        if ($jobseeker) {
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
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'status' => 'under_review',
                'rejection_reason' => null,
                'reviewed_by' => null,
                'reviewed_at' => null,
                'uploaded_at' => now()->toIso8601String(),
            ];

            $filtered[] = $newDoc;
            $details->training_certificates = array_values($filtered);
            $details->save();

            if ($user) {
                Notification::create([
                    'user_id' => $user->user_id,
                    'title' => 'Document Uploaded',
                    'message' => "Your " . ucfirst(str_replace('_', ' ', $category)) . " has been uploaded and is under review.",
                    'type' => 'approval',
                    'is_read' => false,
                ]);
            }
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
     * Review / Verify a jobseeker document (For Admin / JPO).
     */
    public function review(Request $request, $id)
    {
        $user = $this->resolveUser($request);
        if (!$user || !in_array($user->role, ['admin', 'jpo'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized. Only DMDP Admins and JPO officers can review documents.'], 403);
        }

        $request->validate([
            'status' => 'required|in:approved,verified,rejected,under_review',
            'rejection_reason' => 'required_if:status,rejected|nullable|string|max:500',
            'jobseeker_id' => 'nullable|integer',
        ]);

        $status = $request->input('status');
        $normalizedStatus = ($status === 'verified') ? 'approved' : $status;
        $reason = $request->input('rejection_reason');

        $jobseekerId = $request->input('jobseeker_id');
        $query = JobseekerDetail::query();
        if ($jobseekerId) {
            $query->where('jobseeker_id', $jobseekerId);
        }

        $details = $query->get();
        $targetDetail = null;
        $updatedDoc = null;

        foreach ($details as $detail) {
            $docs = is_array($detail->training_certificates) 
                ? $detail->training_certificates 
                : (json_decode($detail->training_certificates ?? '[]', true) ?: []);

            $found = false;
            foreach ($docs as &$d) {
                if (($d['id'] ?? '') === $id) {
                    $d['status'] = $normalizedStatus;
                    $d['reviewed_by'] = $user->user_id;
                    $d['reviewed_at'] = now()->toIso8601String();
                    if ($normalizedStatus === 'rejected') {
                        $d['rejection_reason'] = $reason;
                    } else {
                        $d['rejection_reason'] = null;
                    }
                    $found = true;
                    $updatedDoc = $d;
                    break;
                }
            }

            if ($found) {
                $detail->training_certificates = array_values($docs);
                $detail->save();
                $targetDetail = $detail;
                break;
            }
        }

        if (!$targetDetail || !$updatedDoc) {
            return response()->json(['success' => false, 'message' => 'Document not found.'], 404);
        }

        $jobseekerUser = $targetDetail->jobseeker?->user;
        if ($jobseekerUser) {
            $catLabel = ucfirst(str_replace('_', ' ', $updatedDoc['category'] ?? 'document'));
            if ($normalizedStatus === 'approved') {
                // Government valid ID is the mandatory requirement to upgrade user to verified
                $jobseeker = $targetDetail->jobseeker;
                if ($jobseeker && $jobseeker->hasDocument('valid_id')) {
                    $jobseekerUser->account_status = 'verified';
                    $jobseekerUser->is_approved = 1;
                    $jobseekerUser->save();
                }

                Notification::create([
                    'user_id' => $jobseekerUser->user_id,
                    'title' => "Document Approved",
                    'message' => "Your {$catLabel} has been reviewed and officially approved by DMDP.",
                    'type' => 'approval',
                    'is_read' => false,
                ]);
            } elseif ($normalizedStatus === 'rejected') {
                Notification::create([
                    'user_id' => $jobseekerUser->user_id,
                    'title' => "Document Rejected - Action Required",
                    'message' => "Your {$catLabel} was reviewed and rejected. Reason: {$reason}. Please upload a clear replacement in your Document Hub.",
                    'type' => 'manual_review',
                    'is_read' => false,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Document has been marked as {$normalizedStatus}.",
            'document' => $updatedDoc,
        ]);
    }

    /**
     * Delete a document from the vault.
     */
    public function delete(Request $request, $id)
    {
        $jobseeker = $this->resolveJobseeker($request, false);
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
