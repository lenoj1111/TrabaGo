@extends('layouts.jobseeker')

@section('title', 'Document Hub & Verification - TrabaGo')

@section('content')
<div x-data="{ 
    uploadModalOpen: false, 
    activeDocType: 'resume', 
    activeDocLabel: 'Primary Resume / Curriculum Vitae',
    selectedFileName: '',
    selectedFileSize: '',
    previewModalOpen: false,
    previewUrl: '',
    previewTitle: '',
    handleFileSelect(event) {
        const file = event.target.files[0];
        if (file) {
            this.selectedFileName = file.name;
            const sizeInMb = (file.size / (1024 * 1024)).toFixed(2);
            this.selectedFileSize = sizeInMb + ' MB';
        } else {
            this.selectedFileName = '';
            this.selectedFileSize = '';
        }
    },
    openPreview(url, title) {
        this.previewUrl = url;
        this.previewTitle = title;
        this.previewModalOpen = true;
    }
}" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-5xl space-y-8">
        
        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="inline-flex items-center gap-2 rounded-full bg-emerald-400/20 px-3.5 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    DMDP Credential & Verification Vault
                </div>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Document Hub</h1>
                <p class="text-sm text-slate-300 leading-relaxed">
                    Upload your government-issued valid ID to verify your account and unlock job applications. All other documents (resume, training certificates, PWD ID) are optional and can be submitted anytime to enrich your applications.
                </p>
            </div>

            @php
                $uploadedCount = collect($documents)->filter()->count();
                $hasValidId = !empty($documents['valid_id']);
                $validIdStatus = $documents['valid_id']['status'] ?? 'not_uploaded';
            @endphp
            <div class="shrink-0 bg-white/10 backdrop-blur-md rounded-2xl p-5 border border-white/10 text-center min-w-[210px]">
                <span class="text-xs font-bold text-emerald-300 uppercase tracking-wider">Mandatory ID Status</span>
                <p class="text-lg font-black text-white mt-1">
                    @if($hasValidId)
                        @if($validIdStatus === 'verified' || $validIdStatus === 'approved')
                            <span class="inline-flex items-center gap-1.5 text-emerald-300">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                Gov ID Verified
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-amber-300">
                                <span class="h-2 w-2 rounded-full bg-amber-400 animate-pulse"></span>
                                Gov ID Under Review
                            </span>
                        @endif
                    @else
                        <span class="text-rose-300 font-bold">Gov ID Required</span>
                    @endif
                </p>
                <p class="text-[11px] text-slate-300 mt-1">{{ $uploadedCount }} of 4 Stored &bull; ID Mandatory</p>
                <div class="w-full bg-white/20 rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="bg-emerald-400 h-full rounded-full transition-all duration-500" style="width: {{ ($uploadedCount / 4) * 100 }}%"></div>
                </div>
            </div>
        </div>

        <!-- Explainer Info Card -->
        <div class="rounded-3xl bg-emerald-50/70 border border-emerald-200/80 p-6 flex flex-col sm:flex-row items-start gap-4 text-xs text-emerald-950">
            <div class="h-10 w-10 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-sm">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.516 0c.85.493 1.508 1.333 1.508 2.316V18"/></svg>
            </div>
            <div class="space-y-1">
                <h3 class="font-bold text-emerald-900 text-sm">Document Hub & Verification Rules:</h3>
                <p class="text-emerald-800/90 leading-relaxed">
                    1. <strong class="text-emerald-950 font-black">Government-Issued Valid ID (Mandatory):</strong> The only required document to verify your account and unlock job application submissions with DMDP.<br>
                    2. <strong class="text-slate-800">Primary Resume (Optional):</strong> Attached automatically to job applications so employers can evaluate your work experience and portfolio.<br>
                    3. <strong class="text-slate-800">Training & Technical Certifications (Optional):</strong> Boosts your <em>AI Cosine-Similarity Match Score</em> for specialized vocational vacancies.<br>
                    4. <strong class="text-slate-800">PWD ID (Optional):</strong> Unlocks priority matching and tax accommodations for disability-inclusive opportunities.
                </p>
            </div>
        </div>

        <!-- 4 Document Categories Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            @php
                $docCards = [
                    [
                        'type' => 'valid_id',
                        'label' => 'Government-Issued Valid ID',
                        'desc' => 'Philippine Passport, UMID, Driver’s License, Postal ID, PhilSys National ID, or Voter’s ID.',
                        'doc' => $documents['valid_id'] ?? null,
                        'status_text' => 'Mandatory for Account Verification & Applying for Jobs',
                        'is_required' => true,
                    ],
                    [
                        'type' => 'resume',
                        'label' => 'Primary Resume / Curriculum Vitae',
                        'desc' => 'PDF or Word document detailing your work history, skills, and educational background.',
                        'doc' => $documents['resume'] ?? null,
                        'status_text' => 'Optional &bull; Attached to Applications',
                        'is_required' => false,
                    ],
                    [
                        'type' => 'certificate',
                        'label' => 'Training & Technical Certifications',
                        'desc' => 'TESDA NC II, PRC license, vocational certificates, or training completion records.',
                        'doc' => $documents['certificate'] ?? null,
                        'status_text' => 'Optional &bull; Boosts AI Matching Score',
                        'is_required' => false,
                    ],
                    [
                        'type' => 'pwd_id',
                        'label' => 'PWD Identification Card',
                        'desc' => 'Disability identification card for PWD-inclusive matching and priority placement.',
                        'doc' => $documents['pwd_id'] ?? null,
                        'status_text' => 'Optional &bull; For PWD Beneficiaries',
                        'is_required' => false,
                    ],
                ];
            @endphp

            @foreach($docCards as $card)
                @php
                    $isUploaded = !empty($card['doc']);
                    $docData = $card['doc'] ?? [];
                    $fileUrl = $docData['file_url'] ?? ($docData['url'] ?? null);
                    $fileName = $docData['name'] ?? null;
                    $docStatus = $docData['status'] ?? 'under_review';
                    $uploadDate = isset($docData['uploaded_at']) ? date('M d, Y', strtotime($docData['uploaded_at'])) : null;
                @endphp
                <div class="rounded-3xl border {{ $card['is_required'] && !$isUploaded ? 'border-rose-200 bg-rose-50/20' : 'border-slate-200 bg-white' }} p-6 shadow-sm hover:shadow-md hover:border-emerald-300 transition-all flex flex-col justify-between gap-6">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center border border-emerald-200 shadow-xs">
                                    @if($card['type'] === 'resume')
                                        <svg class="w-6 h-6 text-emerald-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                                    @elseif($card['type'] === 'valid_id')
                                        <svg class="w-6 h-6 text-emerald-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15A2.25 2.25 0 0 0 2.25 6.75v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.364a4.125 4.125 0 0 0-6.338 0"/></svg>
                                    @elseif($card['type'] === 'certificate')
                                        <svg class="w-6 h-6 text-emerald-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.504-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.004 0H9.496m5.004 0a3 3 0 0 0 2.25-2.906l.75-5.25a2.25 2.25 0 0 0-2.25-2.569h-6.5a2.25 2.25 0 0 0-2.25 2.569l.75 5.25a3 3 0 0 0 2.25 2.906"/></svg>
                                    @else
                                        <svg class="w-6 h-6 text-emerald-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="4.5" r="2.25"/><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9h4.5l1.5 5.25H12l-1.5-3.75H7.5M9.75 20.25a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9Z"/></svg>
                                    @endif
                                </div>
                                <div>
                                    @if($card['is_required'])
                                        <span class="inline-flex items-center gap-1 rounded-full bg-rose-100 text-rose-800 border border-rose-200 font-extrabold px-2.5 py-0.5 text-[10px] uppercase tracking-wider">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-600 animate-pulse"></span> Mandatory Requirement
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 text-slate-600 font-semibold px-2.5 py-0.5 text-[10px] uppercase tracking-wider">
                                            Optional
                                        </span>
                                    @endif
                                </div>
                            </div>
                            @if($isUploaded)
                                @if($docStatus === 'verified' || $docStatus === 'approved')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 font-extrabold px-3 py-0.5 text-xs">
                                        <svg class="w-3.5 h-3.5 text-emerald-700 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                        <span>Verified Vault Document</span>
                                    </span>
                                @elseif($docStatus === 'rejected')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-100 text-rose-800 border border-rose-300 font-extrabold px-3 py-0.5 text-xs">
                                        <svg class="w-3.5 h-3.5 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        <span>Rejected (Re-upload Needed)</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 text-amber-800 border border-amber-300 font-bold px-3 py-0.5 text-xs">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse"></span> Under DMDP Review
                                    </span>
                                @endif
                            @else
                                @if($card['is_required'])
                                    <span class="rounded-full bg-rose-50 text-rose-700 border border-rose-200 font-bold px-3 py-0.5 text-xs">
                                        Required to Verify
                                    </span>
                                @else
                                    <span class="rounded-full bg-slate-100 text-slate-500 font-semibold px-3 py-0.5 text-xs">
                                        Not Uploaded
                                    </span>
                                @endif
                            @endif
                        </div>

                        <h3 class="text-base font-bold text-slate-900">{{ $card['label'] }}</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">{{ $card['desc'] }}</p>

                        @if($isUploaded && $docStatus === 'rejected')
                            <div class="rounded-2xl bg-rose-50 p-3.5 border border-rose-200 text-xs text-rose-900 space-y-1">
                                <div class="flex items-center gap-1.5 font-bold text-rose-800">
                                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                                    <span>Rejection Notice from DMDP:</span>
                                </div>
                                <p class="text-rose-700 leading-relaxed">
                                    {{ $docData['rejection_reason'] ?: 'This document was reviewed and did not meet verification criteria. Please upload a clear, valid replacement.' }}
                                </p>
                            </div>
                        @endif

                        @if($isUploaded)
                            <div class="rounded-2xl {{ $docStatus === 'rejected' ? 'bg-rose-50/50 border border-rose-200' : 'bg-emerald-50/70 border border-emerald-100' }} p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="truncate">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-xs font-black {{ $docStatus === 'rejected' ? 'text-rose-950' : 'text-emerald-950' }} truncate" title="{{ $fileName }}">
                                            {{ $fileName ?: 'Attached Document' }}
                                        </span>
                                    </div>
                                    @if($uploadDate)
                                        <p class="text-[11px] {{ $docStatus === 'rejected' ? 'text-rose-700' : 'text-emerald-700' }} font-medium mt-0.5">Uploaded on {{ $uploadDate }}</p>
                                    @endif
                                </div>
                                @if($fileUrl)
                                    <div class="flex items-center gap-2 shrink-0">
                                        <a href="{{ $fileUrl }}" target="_blank" class="px-3 py-1.5 rounded-xl {{ $docStatus === 'rejected' ? 'bg-rose-600 hover:bg-rose-700' : 'bg-emerald-600 hover:bg-emerald-700' }} text-white text-xs font-bold transition-all shadow-xs flex items-center gap-1.5">
                                            <span>Preview / View</span>
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="rounded-2xl {{ $card['is_required'] ? 'bg-rose-50/60 border border-rose-200/80 text-rose-900' : 'bg-slate-50 border border-slate-100 text-slate-500' }} p-3.5 text-[11px] font-medium flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 {{ $card['is_required'] ? 'text-rose-600' : 'text-slate-400' }} shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/></svg>
                                <span>{!! $card['status_text'] !!}</span>
                            </div>
                        @endif
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                        @if($isUploaded)
                            <form action="{{ route('jobseeker.documents.delete', $card['type']) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this document from your vault?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-bold text-rose-600 hover:text-rose-800 transition-colors">
                                    Remove
                                </button>
                            </form>

                            <button type="button" 
                                    @click="activeDocType = '{{ $card['type'] }}'; activeDocLabel = '{{ addslashes($card['label']) }}'; selectedFileName = ''; selectedFileSize = ''; uploadModalOpen = true"
                                    class="rounded-xl border border-slate-200 bg-white hover:bg-slate-50 px-4 py-2 text-xs font-bold text-slate-700 transition-colors">
                                Replace File
                            </button>
                        @else
                            <span></span>
                            <button type="button" 
                                    @click="activeDocType = '{{ $card['type'] }}'; activeDocLabel = '{{ addslashes($card['label']) }}'; selectedFileName = ''; selectedFileSize = ''; uploadModalOpen = true"
                                    class="rounded-xl bg-emerald-600 hover:bg-emerald-500 px-5 py-2 text-xs font-bold text-white shadow-sm transition-all hover:scale-105">
                                Upload File &rarr;
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach

        </div>

        <!-- Training Certificates & Credentials List (if any issued or multiple) -->
        @if(!empty($certificatesList) && count($certificatesList) > 0)
            <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Verified Records</span>
                        <h2 class="text-lg font-black text-slate-900">Official Training Certificates & Issued Credentials ({{ count($certificatesList) }})</h2>
                    </div>
                    <button type="button" 
                            @click="activeDocType = 'certificate'; activeDocLabel = 'Training & Technical Certifications'; selectedFileName = ''; selectedFileSize = ''; uploadModalOpen = true"
                            class="text-xs font-bold text-emerald-700 hover:underline">
                        + Add Another Certificate
                    </button>
                </div>

                <div class="divide-y divide-slate-100 border border-slate-100 rounded-2xl overflow-hidden">
                    @foreach($certificatesList as $cert)
                        @php
                            $cUrl = $cert['file_url'] ?? ($cert['url'] ?? null);
                            $cName = $cert['name'] ?? 'Certificate of Completion';
                            $cDate = isset($cert['uploaded_at']) ? date('M d, Y', strtotime($cert['uploaded_at'])) : 'N/A';
                            $cStatus = $cert['status'] ?? 'verified';
                            $cId = $cert['id'] ?? null;
                        @endphp
                        <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white hover:bg-slate-50 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.504-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.004 0H9.496m5.004 0a3 3 0 0 0 2.25-2.906l.75-5.25a2.25 2.25 0 0 0-2.25-2.569h-6.5a2.25 2.25 0 0 0-2.25 2.569l.75 5.25a3 3 0 0 0 2.25 2.906"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-900">{{ $cName }}</h4>
                                    <p class="text-[11px] text-slate-500">Issued / Uploaded on {{ $cDate }} &bull; 
                                        <span class="font-semibold {{ $cStatus === 'verified' ? 'text-emerald-700' : 'text-amber-700' }}">
                                            {{ ucfirst(str_replace('_', ' ', $cStatus)) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 self-end sm:self-center">
                                @if($cUrl)
                                    <a href="{{ $cUrl }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-xs font-bold transition-colors inline-flex items-center gap-1.5">
                                        <span>View Certificate</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                    </a>
                                @endif
                                @if($cId)
                                    <form action="{{ route('jobseeker.documents.delete', 'certificate') }}" method="POST" onsubmit="return confirm('Remove this certificate?');">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="doc_id" value="{{ $cId }}">
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 transition-colors text-xs" title="Remove Certificate">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    <!-- Upload Modal -->
    <div x-show="uploadModalOpen" x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div @click.away="uploadModalOpen = false" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="transform opacity-0 scale-95"
             x-transition:enter-end="transform opacity-100 scale-100"
             class="w-full max-w-lg rounded-3xl bg-white p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">
            
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Vault Upload</span>
                    <h3 class="text-xl font-extrabold text-slate-900 mt-0.5" x-text="activeDocLabel"></h3>
                    <template x-if="activeDocType === 'valid_id'">
                        <span class="inline-flex items-center gap-1 mt-1 text-[11px] font-bold text-rose-700 bg-rose-50 border border-rose-200 px-2.5 py-0.5 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span> Mandatory Requirement: Government ID is required for verification
                        </span>
                    </template>
                    <template x-if="activeDocType !== 'valid_id'">
                        <span class="inline-flex items-center gap-1 mt-1 text-[11px] font-semibold text-slate-600 bg-slate-100 px-2.5 py-0.5 rounded-full">
                            Optional Document &bull; Upload Anytime
                        </span>
                    </template>
                </div>
                <button @click="uploadModalOpen = false" class="text-slate-400 hover:text-slate-600 text-2xl font-bold leading-none">&times;</button>
            </div>

            <form action="{{ route('jobseeker.documents.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <input type="hidden" name="category" :value="activeDocType">

                <!-- Styled File Dropzone -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Choose File</label>
                    <div class="relative border-2 border-dashed border-slate-200 hover:border-emerald-500 rounded-2xl p-6 text-center transition-all bg-slate-50/50 hover:bg-emerald-50/30 group">
                        <input type="file" name="document_file" required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                               @change="handleFileSelect"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        
                        <div class="space-y-2 pointer-events-none">
                            <div class="h-12 w-12 rounded-2xl bg-emerald-100 text-emerald-700 mx-auto flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-emerald-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z"/></svg>
                            </div>
                            <template x-if="!selectedFileName">
                                <div>
                                    <p class="text-xs font-bold text-slate-700">Click to browse or drag and drop your file here</p>
                                    <p class="text-[11px] text-slate-400 mt-1">Accepted formats: PDF, DOC, DOCX, JPG, PNG (Max 5MB)</p>
                                </div>
                            </template>
                            <template x-if="selectedFileName">
                                <div class="bg-white rounded-xl p-3 border border-emerald-200 shadow-2xs">
                                    <p class="text-xs font-black text-emerald-900" x-text="selectedFileName"></p>
                                    <p class="text-[10px] text-emerald-700 font-semibold mt-0.5" x-text="selectedFileSize"></p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-slate-50 p-3 border border-slate-100 text-[11px] text-slate-500 space-y-1">
                    <p class="font-semibold text-slate-700 flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/></svg><span>Verification Note:</span></p>
                    <p>Uploaded documents are encrypted and reviewed by the DMDP placement verification officer to certify your profile.</p>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="uploadModalOpen = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black shadow-md transition-all hover:scale-[1.02]">
                        Upload to Vault
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection
