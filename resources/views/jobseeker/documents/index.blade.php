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
                    Upload your primary resume, government ID, and vocational certificates. Verified credentials give you an official DMDP verified badge and speed up your job referral and hiring process.
                </p>
            </div>

            @php
                $uploadedCount = collect($documents)->filter()->count();
            @endphp
            <div class="shrink-0 bg-white/10 backdrop-blur-md rounded-2xl p-5 border border-white/10 text-center min-w-[180px]">
                <span class="text-xs font-bold text-emerald-300 uppercase tracking-wider">Vault Status</span>
                <p class="text-xl font-black text-white mt-1">{{ $uploadedCount }} of 4 Stored</p>
                <div class="w-full bg-white/20 rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="bg-emerald-400 h-full rounded-full transition-all duration-500" style="width: {{ ($uploadedCount / 4) * 100 }}%"></div>
                </div>
            </div>
        </div>

        <!-- Explainer Info Card -->
        <div class="rounded-3xl bg-emerald-50/70 border border-emerald-200/80 p-6 flex flex-col sm:flex-row items-start gap-4 text-xs text-emerald-950">
            <div class="h-10 w-10 rounded-2xl bg-emerald-600 text-white flex items-center justify-center font-bold text-lg shrink-0 shadow-sm">
                💡
            </div>
            <div class="space-y-1">
                <h3 class="font-bold text-emerald-900 text-sm">How the Document Hub Works:</h3>
                <p class="text-emerald-800/90 leading-relaxed">
                    1. <strong>Resume:</strong> Attached automatically to job applications so employers can evaluate your qualifications.<br>
                    2. <strong>Valid ID:</strong> Confirms your identity with the Public Employment Service Office (PESO/DMDP).<br>
                    3. <strong>Technical Certifications:</strong> Boosts your <em>AI Cosine-Similarity Match Score</em> for skilled vacancies.<br>
                    4. <strong>PWD ID:</strong> Enables priority matching for disability-inclusive job opportunities.
                </p>
            </div>
        </div>

        <!-- 4 Document Categories Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            @php
                $docCards = [
                    [
                        'type' => 'resume',
                        'label' => 'Primary Resume / Curriculum Vitae',
                        'desc' => 'PDF or Word document detailing your work history, skills, and educational background.',
                        'doc' => $documents['resume'] ?? null,
                        'icon' => '📄',
                        'status_text' => 'Required for Job Applications',
                    ],
                    [
                        'type' => 'valid_id',
                        'label' => 'Government-Issued Valid ID',
                        'desc' => 'Philippine Passport, UMID, Driver’s License, Postal ID, or National ID (PhilSys).',
                        'doc' => $documents['valid_id'] ?? null,
                        'icon' => '🪪',
                        'status_text' => 'Recommended for Verification',
                    ],
                    [
                        'type' => 'certificate',
                        'label' => 'Training & Technical Certifications',
                        'desc' => 'TESDA NC II, PRC license, vocational certificates, or training completion records.',
                        'doc' => $documents['certificate'] ?? null,
                        'icon' => '🎖️',
                        'status_text' => 'Boosts AI Matching Score',
                    ],
                    [
                        'type' => 'pwd_id',
                        'label' => 'PWD Identification Card',
                        'desc' => 'Required for PWD-inclusive matching, tax perks, and priority placement benefits.',
                        'doc' => $documents['pwd_id'] ?? null,
                        'icon' => '♿',
                        'status_text' => 'Optional for PWD Beneficiaries',
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
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md hover:border-emerald-300 transition-all flex flex-col justify-between gap-6">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-3xl">{{ $card['icon'] }}</span>
                            @if($isUploaded)
                                @if($docStatus === 'verified')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 font-extrabold px-3 py-0.5 text-xs">
                                        <span>✓</span> Verified Vault Document
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 text-amber-800 border border-amber-300 font-bold px-3 py-0.5 text-xs">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse"></span> Under DMDP Review
                                    </span>
                                @endif
                            @else
                                <span class="rounded-full bg-slate-100 text-slate-600 font-semibold px-3 py-0.5 text-xs">
                                    Not Uploaded
                                </span>
                            @endif
                        </div>

                        <h3 class="text-base font-bold text-slate-900">{{ $card['label'] }}</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">{{ $card['desc'] }}</p>

                        @if($isUploaded)
                            <div class="rounded-2xl bg-emerald-50/70 p-4 border border-emerald-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="truncate">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-xs font-black text-emerald-950 truncate" title="{{ $fileName }}">
                                            {{ $fileName ?: 'Attached Document' }}
                                        </span>
                                    </div>
                                    @if($uploadDate)
                                        <p class="text-[11px] text-emerald-700 font-medium mt-0.5">Uploaded on {{ $uploadDate }}</p>
                                    @endif
                                </div>
                                @if($fileUrl)
                                    <div class="flex items-center gap-2 shrink-0">
                                        <a href="{{ $fileUrl }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all shadow-xs flex items-center gap-1">
                                            <span>Preview / View</span>
                                            <span class="text-[10px]">↗</span>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="rounded-2xl bg-slate-50 p-3.5 border border-slate-100 text-[11px] text-slate-500 font-medium flex items-center gap-2">
                                <span>📌</span>
                                <span>{{ $card['status_text'] }}</span>
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
                                <div class="h-10 w-10 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center font-bold text-lg shrink-0">
                                    🎖️
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
                                    <a href="{{ $cUrl }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-xs font-bold transition-colors">
                                        View Certificate ↗
                                    </a>
                                @endif
                                @if($cId)
                                    <form action="{{ route('jobseeker.documents.delete', 'certificate') }}" method="POST" onsubmit="return confirm('Remove this certificate?');">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="doc_id" value="{{ $cId }}">
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 transition-colors text-xs" title="Remove Certificate">
                                            ✕
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
                            <div class="h-12 w-12 rounded-2xl bg-emerald-100 text-emerald-700 mx-auto flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                                📁
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
                    <p class="font-semibold text-slate-700">📌 Verification Note:</p>
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
