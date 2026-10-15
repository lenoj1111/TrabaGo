<!-- Employer Document Inspection Modal -->
<div x-show="docModalOpen" 
     x-cloak 
     class="no-print fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 flex items-center justify-center p-4 sm:p-6"
     @keydown.escape.window="docModalOpen = false">
    
    <div @click.away="docModalOpen = false" 
         class="bg-white rounded-2xl max-w-3xl w-full shadow-xl border border-slate-200 overflow-hidden flex flex-col max-h-[90vh]">
        
        <!-- Modal Header -->
        <div class="bg-slate-900 p-5 sm:p-6 text-white flex items-center justify-between border-b border-slate-800">
            <div class="flex items-center gap-3">
                <div class="h-9 w-9 rounded-lg bg-emerald-600 flex items-center justify-center text-white font-bold text-sm">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block">DMDP Document Inspection Hub</span>
                    <h3 class="text-base sm:text-lg font-bold text-white leading-tight" x-text="activeDocCompany || 'Employer Verification Documents'"></h3>
                </div>
            </div>
            <button @click="docModalOpen = false" 
                    type="button" 
                    class="h-8 w-8 rounded-lg bg-gray-800 hover:bg-gray-700 text-gray-300 hover:text-white flex items-center justify-center transition-colors text-lg font-bold">
                &times;
            </button>
        </div>

        <!-- Document Selection Tabs -->
        <div class="bg-slate-50 border-b border-slate-200 p-3 sm:px-6 flex items-center gap-2 overflow-x-auto">
            <template x-for="(doc, idx) in activeDocList" :key="idx">
                <button type="button" 
                        @click="selectDocument(doc)"
                        class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors flex items-center gap-2 shrink-0"
                        :class="selectedDocKey === doc.key ? 'bg-emerald-600 text-white font-bold' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-100'">
                    <span x-text="doc.label"></span>
                </button>
            </template>
        </div>

        <!-- Modal Body: Document Preview Sheet -->
        <div class="p-6 overflow-y-auto space-y-6 flex-1">
            
            <!-- Document Meta Bar -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-0.5">
                    <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block">Document Classification</span>
                    <p class="text-xs font-bold text-slate-900 truncate" x-text="currentDoc.label || 'Legal Certificate'"></p>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-0.5">
                    <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block">Issuing Authority</span>
                    <p class="text-xs font-bold text-slate-900 truncate" x-text="currentDoc.issuer || 'Government Regulatory Agency'"></p>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-0.5">
                    <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block">Validity Period</span>
                    <p class="text-xs font-bold text-emerald-700 truncate" x-text="currentDoc.validity || 'Current / Valid'"></p>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-0.5">
                    <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block">Verification Status</span>
                    <p class="text-xs font-bold text-emerald-700 flex items-center gap-1.5">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        <span x-text="currentDoc.status || 'Verified Valid'"></span>
                    </p>
                </div>
            </div>

            <!-- Official Document Inspection Certificate View -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 space-y-6">
                <!-- Certificate Header -->
                <div class="text-center space-y-1 pb-4 border-b border-slate-100">
                    <div class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-[10px] font-bold text-slate-700 tracking-wider uppercase">
                        Republic of the Philippines &bull; City of Cebu
                    </div>
                    <h4 class="text-sm font-bold text-slate-900">DEPARTMENT OF MANPOWER DEVELOPMENT AND PLACEMENT</h4>
                    <p class="text-xs text-slate-500">Employer Accreditation & Legal Verification Division</p>
                </div>

                <!-- Document Details Grid -->
                <div class="space-y-3 text-xs">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between py-2 border-b border-slate-100 gap-1">
                        <span class="font-medium text-slate-500">Registered Business Entity:</span>
                        <span class="font-bold text-slate-900 text-sm" x-text="activeDocCompany"></span>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between py-2 border-b border-slate-100 gap-1">
                        <span class="font-medium text-slate-500">Legal Document Type:</span>
                        <span class="font-semibold text-emerald-700" x-text="currentDoc.label"></span>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between py-2 border-b border-slate-100 gap-1">
                        <span class="font-medium text-slate-500">Validity Period:</span>
                        <span class="font-medium text-gray-800" x-text="currentDoc.validity || 'Current / Valid'"></span>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between py-2 border-b border-slate-100 gap-1">
                        <span class="font-medium text-slate-500">Attached File Reference:</span>
                        <span class="font-mono text-slate-700 font-medium" x-text="currentDoc.filename || 'electronic_record.pdf'"></span>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between py-2 border-b border-slate-100 gap-1">
                        <span class="font-medium text-slate-500">Digital Authentication:</span>
                        <span class="font-mono text-[11px] text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 font-semibold">
                            SHA-256 / DMDP-VERIFIED-REG-2026
                        </span>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between py-2 border-b border-slate-100 gap-1">
                        <span class="font-medium text-slate-500">Evaluation Compliance:</span>
                        <span class="font-medium text-gray-800">Meets DMDP City Ordinance Standards for Employment Facilitation</span>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-[11px] text-slate-700">
                        <span><strong>Official Notice:</strong> Original and other documents, when applicable, should be presented for validation.</span>
                    </div>
                </div>

                <!-- Embedded Live Document Preview Pane -->
                <template x-if="currentDoc.url">
                    <div class="rounded-xl border border-slate-200 overflow-hidden bg-slate-100 flex flex-col items-center justify-center min-h-[220px]">
                        <template x-if="currentDoc.url.match(/\.(jpg|jpeg|png|webp|bmp|gif)($|\?)/i)">
                            <div class="p-4 w-full flex justify-center">
                                <img :src="currentDoc.url" :alt="currentDoc.label" class="max-h-[380px] w-auto object-contain rounded-lg shadow-xs" />
                            </div>
                        </template>
                        <template x-if="currentDoc.url.match(/\.pdf($|\?)/i)">
                            <div class="w-full h-[400px]">
                                <iframe :src="currentDoc.url" class="w-full h-full border-0" title="PDF Document Preview"></iframe>
                            </div>
                        </template>
                        <template x-if="!currentDoc.url.match(/\.(jpg|jpeg|png|webp|bmp|gif|pdf)($|\?)/i)">
                            <div class="p-8 text-center space-y-2">
                                <div class="h-14 w-14 rounded-2xl bg-slate-200 text-slate-600 flex items-center justify-center mx-auto">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <p class="text-xs font-bold text-slate-800" x-text="currentDoc.filename || 'Document file'"></p>
                                <p class="text-[11px] text-slate-500">Document file ready to view or download directly below.</p>
                            </div>
                        </template>
                    </div>
                </template>
                <template x-if="!currentDoc.url">
                    <div class="rounded-xl border border-dashed border-slate-300 p-8 text-center space-y-2 bg-slate-50/50">
                        <div class="h-12 w-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-xs font-bold text-slate-800" x-text="currentDoc.label || 'Document Record'"></p>
                        <p class="text-[11px] text-slate-500 max-w-sm mx-auto">Official documentary requirement registered on record. Original physical copy presented and authenticated during DMDP accreditation.</p>
                    </div>
                </template>

                <!-- Document Inspection Action -->
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-sm">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-900 truncate" x-text="currentDoc.filename || (currentDoc.label + '.pdf')"></p>
                            <p class="text-[10px] text-slate-500">Official legal submission for DMDP corporate partner accreditation</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <template x-if="currentDoc.url">
                            <div class="flex flex-wrap items-center gap-2">
                                <a :href="currentDoc.url" 
                                   target="_blank" 
                                   class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-colors shadow-sm shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>View in New Tab</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                                <a :href="currentDoc.url" 
                                   :download="currentDoc.filename || (currentDoc.key + '.pdf')"
                                   class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-colors shadow-sm shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    <span>Download File</span>
                                </a>
                            </div>
                        </template>
                        <template x-if="!currentDoc.url">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-semibold border border-emerald-200 shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Verified Digital Copy</span>
                            </span>
                        </template>
                    </div>
                </div>

            </div>

        </div>

        <!-- Modal Footer -->
        <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex items-center justify-between">
            <span class="text-[11px] text-slate-500">TrabaGo DMDP Enterprise Verification System</span>
            <button type="button" 
                    @click="docModalOpen = false" 
                    class="px-4 py-1.5 rounded-lg bg-slate-900 hover:bg-gray-800 text-white text-xs font-semibold transition-colors">
                Close Viewer
            </button>
        </div>

    </div>
</div>
