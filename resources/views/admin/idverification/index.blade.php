@extends('layouts.admin')

@section('title', 'ID Verification')

@section('content')
<div x-data="{ 
    reviewModal: false, 
    selectedUser: null 
}" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto w-full max-w-[1680px] space-y-8">
        
        <!-- Header -->
        <div class="rounded-3xl border border-emerald-500/20 bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 text-white shadow-xl sm:p-10">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-400/30 bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300">
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                    Jobseeker Identity Review
                </span>
                <h1 class="text-3xl font-black tracking-tight sm:text-4xl">ID Verification</h1>
                <p class="max-w-3xl text-sm text-slate-300">
                    Review submitted government IDs for manual verification. Compare the ID against the registered profile before approving.
                </p>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success') || session('info'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-800" role="status">
                {{ session('success') ?? session('info') }}
            </div>
        @endif
        @if($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-semibold text-rose-800" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Main Table -->
        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-slate-100 p-6 sm:flex-row sm:items-center sm:justify-between sm:p-8">
                <div>
                    <h2 class="text-xl font-black text-slate-900">Pending Manual ID Submissions</h2>
                    <p class="mt-1 text-xs text-slate-500">These jobseekers have bypassed the AI scanner or reached the maximum attempts.</p>
                </div>
                <span class="inline-flex w-fit items-center rounded-full border border-amber-200 bg-amber-50 px-3.5 py-1.5 text-xs font-black text-amber-800">
                    {{ $pendingJobseekers->count() }} Awaiting Review
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/70 text-[11px] font-black uppercase tracking-wider text-slate-500">
                            <th class="px-6 py-4">Jobseeker Registered Name</th>
                            <th class="px-6 py-4">Date of Birth</th>
                            <th class="px-6 py-4">ID Document</th>
                            <th class="px-6 py-4">Submitted</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($pendingJobseekers as $jobseeker)
                            @php
                                $document = $jobseeker->valid_id_doc;
                                $documentName = $document['original_name'] ?? ($document['name'] ?? 'Government-Issued Valid ID');
                                $uploadedAt = $document['uploaded_at'] ?? null;
                                
                                // Determine if the file is an image to show the preview
                                $filePath = $document['file_path'] ?? $document['path'] ?? '';
                                $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                $imageUrl = $filePath ? asset('storage/' . $filePath) : null;

                                // Parse Address (if JSON) and Education
                                $address = is_string($jobseeker->address) ? json_decode($jobseeker->address, true) : $jobseeker->address;
                                $addressString = '';
                                if (is_array($address)) {
                                    $addressString = implode(', ', array_filter([
                                        $address['street'] ?? '',
                                        $address['barangay'] ?? '',
                                        $address['city'] ?? '',
                                        $address['province'] ?? ''
                                    ]));
                                }

                                $education = is_string($jobseeker->education) ? json_decode($jobseeker->education, true) : $jobseeker->education;
                                $educationString = is_array($education) ? ($education['level'] ?? ($education['educational_level'] ?? 'N/A')) : 'N/A';

                                // Prepare data for the Modal
                                $appData = [
                                    'id' => $jobseeker->jobseeker_id,
                                    'full_name' => trim(($jobseeker->first_name ?? '') . ' ' . ($jobseeker->middle_name ?? '') . ' ' . ($jobseeker->last_name ?? '')),
                                    'email' => $jobseeker->user_email ?: $jobseeker->email ?: 'No email',
                                    'phone' => $jobseeker->mobile_number ?: 'Not provided',
                                    'dob' => $jobseeker->birth_date ? date('M d, Y', strtotime($jobseeker->birth_date)) : 'Not set',
                                    'gender' => $jobseeker->gender ?? 'Not set',
                                    'civil_status' => $jobseeker->civil_status ?? 'Not set',
                                    'address' => $addressString ?: 'Not provided',
                                    'education' => $educationString,
                                    'is_pwd' => $jobseeker->is_pwd ? 'Yes (' . ($jobseeker->pwd_type ?? 'No type specified') . ')' : 'No',
                                    'doc_name' => $documentName,
                                    'doc_url' => route('admin.jobseekers.valid-id', $jobseeker->jobseeker_id),
                                    'image_url' => $isImage ? $imageUrl : null,
                                    'approve_url' => route('admin.approvals.jobseekers.approve', $jobseeker->jobseeker_id),
                                    'reject_url' => route('admin.approvals.jobseekers.reject', $jobseeker->jobseeker_id),
                                ];
                            @endphp
                            <tr class="transition-colors hover:bg-slate-50/70">
                                <td class="px-6 py-5">
                                    <div class="font-black text-sm text-slate-900">
                                        {{ $appData['full_name'] }}
                                    </div>
                                    <div class="mt-1 text-[11px] font-medium text-slate-500">
                                        {{ $appData['email'] }}
                                    </div>
                                </td>
                                <td class="px-6 py-5 font-bold text-slate-700">
                                    {{ $appData['dob'] }}
                                </td>
                                <td class="px-6 py-5">
                                    <div class="max-w-[200px] truncate font-bold text-slate-800" title="{{ $documentName }}">
                                        {{ $documentName }}
                                    </div>
                                    <a href="{{ $appData['doc_url'] }}" target="_blank" rel="noopener"
                                       class="mt-1 inline-flex items-center gap-1 font-extrabold text-emerald-700 hover:text-emerald-900 hover:underline">
                                        View ID File
                                    </a>
                                </td>
                                <td class="px-6 py-5 text-slate-500">
                                    @if($uploadedAt && strtotime($uploadedAt))
                                        {{ date('M d, Y', strtotime($uploadedAt)) }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <button type="button" @click='selectedUser = @json($appData); reviewModal = true'
                                            class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-extrabold text-white shadow-sm transition-colors hover:bg-emerald-600">
                                        Review Details
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="font-bold text-slate-700">No pending ID submissions</div>
                                    <div class="mt-1 text-xs text-slate-400">New manual ID submissions will appear here.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <!-- ========================================== -->
    <!-- REVIEW MODAL                               -->
    <!-- ========================================== -->
    <div x-show="reviewModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="reviewModal = false" class="bg-white rounded-3xl max-w-5xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">
            
            <template x-if="selectedUser">
                <div class="space-y-6">
                    <!-- Modal Header -->
                    <div class="flex items-start justify-between border-b border-slate-100 pb-4">
                        <div>
                            <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Manual Review</span>
                            <h3 class="text-2xl font-black text-slate-900 mt-0.5" x-text="selectedUser.full_name"></h3>
                            <p class="text-xs text-slate-500 mt-1" x-text="selectedUser.email + ' • ' + selectedUser.phone"></p>
                        </div>
                        <button @click="reviewModal = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold">&times;</button>
                    </div>

                    <!-- Layout: Details Left, ID Right -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Registered Details (Expanded) -->
                        <div class="space-y-4">
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-400">Registered Profile Information</h4>
                            
                            <div class="rounded-2xl bg-slate-50 p-4 border border-slate-200 space-y-3 text-sm">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <span class="block text-[10px] font-bold text-slate-400 uppercase">Full Name</span>
                                        <span class="font-bold text-slate-900" x-text="selectedUser.full_name"></span>
                                    </div>
                                    <div>
                                        <span class="block text-[10px] font-bold text-slate-400 uppercase">Date of Birth</span>
                                        <span class="font-bold text-slate-900" x-text="selectedUser.dob"></span>
                                    </div>
                                    <div>
                                        <span class="block text-[10px] font-bold text-slate-400 uppercase">Gender</span>
                                        <span class="font-bold text-slate-900" x-text="selectedUser.gender"></span>
                                    </div>
                                    <div>
                                        <span class="block text-[10px] font-bold text-slate-400 uppercase">Civil Status</span>
                                        <span class="font-bold text-slate-900" x-text="selectedUser.civil_status"></span>
                                    </div>
                                </div>
                                <div>
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase">Email Address</span>
                                    <span class="font-bold text-slate-900" x-text="selectedUser.email"></span>
                                </div>
                                <div>
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase">Phone Number</span>
                                    <span class="font-bold text-slate-900" x-text="selectedUser.phone"></span>
                                </div>
                                <div>
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase">Full Address</span>
                                    <span class="font-bold text-slate-900" x-text="selectedUser.address"></span>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <span class="block text-[10px] font-bold text-slate-400 uppercase">Education Level</span>
                                        <span class="font-bold text-slate-900" x-text="selectedUser.education"></span>
                                    </div>
                                    <div>
                                        <span class="block text-[10px] font-bold text-slate-400 uppercase">PWD Status</span>
                                        <span class="font-bold text-slate-900" x-text="selectedUser.is_pwd"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-800 font-medium">
                                Please verify that the Name and Date of Birth on the uploaded ID match the Registered Profile exactly. If they match, click Approve.
                            </div>
                        </div>

                        <!-- Submitted ID Preview -->
                        <div class="space-y-4">
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-400">Submitted Document</h4>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-center space-y-3 flex flex-col justify-center items-center h-full max-h-[500px]">
                                
                                <!-- Image Preview -->
                                <template x-if="selectedUser.image_url">
                                    <div class="w-full h-full min-h-[300px] max-h-[400px] rounded-xl overflow-hidden border border-slate-200 shadow-sm bg-white flex items-center justify-center">
                                        <img :src="selectedUser.image_url" :alt="selectedUser.doc_name" class="object-contain w-full h-full">
                                    </div>
                                </template>

                                <!-- Fallback Icon for PDFs / Missing Images -->
                                <template x-if="!selectedUser.image_url">
                                    <div class="py-10 flex flex-col items-center">
                                        <svg class="w-16 h-16 text-slate-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="text-sm font-bold text-slate-700" x-text="selectedUser.doc_name"></p>
                                        <p class="text-[11px] text-slate-400 mt-1">PDF or non-image file. Open in new tab to view.</p>
                                    </div>
                                </template>

                                <a :href="selectedUser.doc_url" target="_blank" rel="noopener"
                                   class="inline-block px-4 py-2 rounded-xl bg-slate-900 hover:bg-emerald-600 text-white text-xs font-bold transition-colors mt-auto w-full">
                                    Open ID in New Tab &rarr;
                                </a>
                            </div>
                        </div>

                    </div>

                    <!-- Modal Footer (Actions) -->
                    <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-end gap-3">
                        <button type="button" @click="reviewModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-bold w-full sm:w-auto">
                            Cancel
                        </button>
                        
                        <!-- Reject Form -->
                        <form :action="selectedUser.reject_url" method="POST" class="w-full sm:w-auto"
                              onsubmit="const reason = prompt('Why does this ID need revision?', 'Please upload a clearer copy of your Government-Issued Valid ID.'); if (!reason || !reason.trim()) return false; this.remarks.value = reason.trim(); return true;">
                            @csrf
                            <input type="hidden" name="remarks" value="">
                            <button type="submit" class="w-full px-5 py-2.5 rounded-xl bg-rose-100 text-rose-700 hover:bg-rose-200 text-sm font-black transition-colors">
                                Reject / Return
                            </button>
                        </form>

                        <!-- Approve Form -->
                        <form :action="selectedUser.approve_url" method="POST" class="w-full sm:w-auto"
                              onsubmit="return confirm('Are you sure you want to approve this ID and verify the account?');">
                            @csrf
                            <button type="submit" class="w-full px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-black shadow-md transition-colors">
                                Approve & Verify
                            </button>
                        </form>
                    </div>

                </div>
            </template>
        </div>
    </div>

</div>
@endsection