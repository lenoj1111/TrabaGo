@extends('layouts.jpo')

@section('title', 'ID Verification')

@section('content')
<div x-data="{ reviewModal: false, selectedUser: null }" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto w-full max-w-[1680px] space-y-8">
        <div class="rounded-3xl border border-emerald-500/20 bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 text-white shadow-xl sm:p-10">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-400/30 bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300">
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                    Jobseeker Identity Review
                </span>
                <h1 class="text-3xl font-black tracking-tight sm:text-4xl">ID Verification</h1>
                <p class="max-w-3xl text-sm text-slate-300">Review submitted government IDs and compare them with each jobseeker's registered profile.</p>
            </div>
        </div>

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

        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-slate-100 p-6 sm:flex-row sm:items-center sm:justify-between sm:p-8">
                <div>
                    <h2 class="text-xl font-black text-slate-900">Pending Manual ID Submissions</h2>
                    <p class="mt-1 text-xs text-slate-500">Jobseekers with a government-issued ID awaiting manual review.</p>
                </div>
                <span class="inline-flex w-fit items-center rounded-full border border-amber-200 bg-amber-50 px-3.5 py-1.5 text-xs font-black text-amber-800">
                    {{ $pendingJobseekers->count() }} Awaiting Review
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/70 text-[11px] font-black uppercase tracking-wider text-slate-500">
                            <th class="px-6 py-4">Jobseeker Name</th>
                            <th class="px-6 py-4">Date of Birth</th>
                            <th class="px-6 py-4">Document Name</th>
                            <th class="px-6 py-4">Submitted Date</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($pendingJobseekers as $jobseeker)
                            @php
                                $document = $jobseeker->valid_id_doc;
                                $documentName = $document['original_name'] ?? ($document['name'] ?? 'Government-Issued Valid ID');
                                $filePath = $document['file_path'] ?? $document['path'] ?? '';
                                $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                                $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);

                                $address = is_string($jobseeker->address) ? json_decode($jobseeker->address, true) : $jobseeker->address;
                                $addressString = is_array($address)
                                    ? implode(', ', array_filter([
                                        $address['street'] ?? '',
                                        $address['barangay'] ?? '',
                                        $address['city'] ?? '',
                                        $address['province'] ?? '',
                                    ]))
                                    : '';

                                $education = is_string($jobseeker->education) ? json_decode($jobseeker->education, true) : $jobseeker->education;
                                $educationString = is_array($education)
                                    ? ($education['level'] ?? $education['educational_level'] ?? $education['degree'] ?? 'N/A')
                                    : 'N/A';

                                $appData = [
                                    'full_name' => trim(implode(' ', array_filter([
                                        $jobseeker->first_name ?? '',
                                        $jobseeker->middle_name ?? '',
                                        $jobseeker->last_name ?? '',
                                    ]))),
                                    'email' => $jobseeker->user_email ?: $jobseeker->email ?: 'No email',
                                    'phone' => $jobseeker->mobile_number ?: 'Not provided',
                                    'dob' => $jobseeker->birth_date ? date('M d, Y', strtotime($jobseeker->birth_date)) : 'Not set',
                                    'gender' => $jobseeker->sex ?? 'Not set',
                                    'civil_status' => $jobseeker->civil_status ?? 'Not set',
                                    'address' => $addressString ?: 'Not provided',
                                    'education' => $educationString,
                                    'is_pwd' => $jobseeker->is_pwd ? 'Yes (' . ($jobseeker->pwd_type ?? 'No type specified') . ')' : 'No',
                                    'doc_name' => $documentName,
                                    'doc_url' => route('jpo.jobseekers.valid-id', $jobseeker->jobseeker_id),
                                    'image_url' => $isImage && $filePath ? asset('storage/' . $filePath) : null,
                                    'approve_url' => route('jpo.idverification.approve', $jobseeker->jobseeker_id),
                                    'reject_url' => route('jpo.idverification.reject', $jobseeker->jobseeker_id),
                                ];
                                $uploadedAt = $document['uploaded_at'] ?? null;
                            @endphp
                            <tr class="transition-colors hover:bg-slate-50/70">
                                <td class="px-6 py-5">
                                    <div class="text-sm font-black text-slate-900">{{ $appData['full_name'] }}</div>
                                    <div class="mt-1 text-[11px] font-medium text-slate-500">{{ $appData['email'] }}</div>
                                </td>
                                <td class="px-6 py-5 font-bold text-slate-700">{{ $appData['dob'] }}</td>
                                <td class="px-6 py-5">
                                    <div class="max-w-[200px] truncate font-bold text-slate-800" title="{{ $documentName }}">{{ $documentName }}</div>
                                    <a href="{{ $appData['doc_url'] }}" target="_blank" rel="noopener" class="mt-1 inline-flex font-extrabold text-emerald-700 hover:text-emerald-900 hover:underline">View ID File</a>
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
                                    <div class="mt-1 text-xs text-slate-400">New ID submissions awaiting review will appear here.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <div x-show="reviewModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-900/70 p-4 backdrop-blur-sm">
        <div @click.away="reviewModal = false" class="w-full max-w-5xl space-y-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-2xl sm:p-8">
            <template x-if="selectedUser">
                <div class="space-y-6">
                    <div class="flex items-start justify-between border-b border-slate-100 pb-4">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-emerald-600">Manual Review</span>
                            <h3 class="mt-0.5 text-2xl font-black text-slate-900" x-text="selectedUser.full_name"></h3>
                            <p class="mt-1 text-xs text-slate-500" x-text="selectedUser.email + ' • ' + selectedUser.phone"></p>
                        </div>
                        <button type="button" @click="reviewModal = false" class="text-2xl font-bold text-slate-400 hover:text-slate-700" aria-label="Close">&times;</button>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="space-y-4">
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-400">Registered Profile Information</h4>
                            <div class="space-y-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm">
                                <div class="grid grid-cols-2 gap-4">
                                    <div><span class="block text-[10px] font-bold uppercase text-slate-400">Full Name</span><span class="font-bold text-slate-900" x-text="selectedUser.full_name"></span></div>
                                    <div><span class="block text-[10px] font-bold uppercase text-slate-400">Date of Birth</span><span class="font-bold text-slate-900" x-text="selectedUser.dob"></span></div>
                                    <div><span class="block text-[10px] font-bold uppercase text-slate-400">Gender</span><span class="font-bold text-slate-900" x-text="selectedUser.gender"></span></div>
                                    <div><span class="block text-[10px] font-bold uppercase text-slate-400">Civil Status</span><span class="font-bold text-slate-900" x-text="selectedUser.civil_status"></span></div>
                                </div>
                                <div><span class="block text-[10px] font-bold uppercase text-slate-400">Email Address</span><span class="font-bold text-slate-900" x-text="selectedUser.email"></span></div>
                                <div><span class="block text-[10px] font-bold uppercase text-slate-400">Phone Number</span><span class="font-bold text-slate-900" x-text="selectedUser.phone"></span></div>
                                <div><span class="block text-[10px] font-bold uppercase text-slate-400">Full Address</span><span class="font-bold text-slate-900" x-text="selectedUser.address"></span></div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div><span class="block text-[10px] font-bold uppercase text-slate-400">Education Level</span><span class="font-bold text-slate-900" x-text="selectedUser.education"></span></div>
                                    <div><span class="block text-[10px] font-bold uppercase text-slate-400">PWD Status</span><span class="font-bold text-slate-900" x-text="selectedUser.is_pwd"></span></div>
                                </div>
                            </div>
                            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-xs font-medium text-amber-800">
                                Verify that the name and date of birth on the uploaded ID match the registered profile.
                            </div>
                        </div>

                        <div class="space-y-4">
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-400">Submitted Document</h4>
                            <div class="flex max-h-[500px] min-h-[300px] flex-col items-center justify-center space-y-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-center">
                                <template x-if="selectedUser.image_url">
                                    <div class="flex h-[300px] max-h-[400px] w-full items-center justify-center overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                                        <img :src="selectedUser.image_url" :alt="selectedUser.doc_name" class="h-full w-full object-contain">
                                    </div>
                                </template>
                                <template x-if="!selectedUser.image_url">
                                    <div class="flex flex-col items-center py-10">
                                        <svg class="mb-3 h-16 w-16 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="text-sm font-bold text-slate-700" x-text="selectedUser.doc_name"></p>
                                        <p class="mt-1 text-[11px] text-slate-400">PDF or non-image file. Open in a new tab to view.</p>
                                    </div>
                                </template>
                                <a :href="selectedUser.doc_url" target="_blank" rel="noopener" class="mt-auto inline-block w-full rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white transition-colors hover:bg-emerald-600">
                                    Open ID in New Tab &rarr;
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col items-center justify-end gap-3 border-t border-slate-100 pt-4 sm:flex-row">
                        <button type="button" @click="reviewModal = false" class="w-full rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-50 sm:w-auto">Cancel</button>
                        <form :action="selectedUser.reject_url" method="POST" class="w-full sm:w-auto"
                            onsubmit="const reason = prompt('Why does this ID need revision?', 'Please upload a clearer copy of your Government-Issued Valid ID.'); if (!reason || !reason.trim()) return false; this.remarks.value = reason.trim(); return true;">
                            @csrf
                            <input type="hidden" name="remarks" value="">
                            <button type="submit" class="w-full rounded-xl bg-rose-100 px-5 py-2.5 text-sm font-black text-rose-700 transition-colors hover:bg-rose-200">Reject / Return</button>
                        </form>
                        <form :action="selectedUser.approve_url" method="POST" class="w-full sm:w-auto"
                            onsubmit="return confirm('Are you sure you want to approve this ID and verify the account?');">
                            @csrf
                            <button type="submit" class="w-full rounded-xl bg-emerald-600 px-6 py-2.5 text-sm font-black text-white shadow-md transition-colors hover:bg-emerald-500">Approve &amp; Verify</button>
                        </form>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
@endsection
