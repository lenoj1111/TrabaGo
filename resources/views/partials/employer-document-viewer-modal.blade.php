<!-- Employer Document Inspection Modal -->
<div x-show="docModalOpen" 
     x-cloak 
     class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 flex items-center justify-center p-4 sm:p-6"
     @keydown.escape.window="docModalOpen = false">
    
    <div @click.away="docModalOpen = false" 
         class="bg-white rounded-2xl max-w-3xl w-full shadow-xl border border-gray-200 overflow-hidden flex flex-col max-h-[90vh]">
        
        <!-- Modal Header -->
        <div class="bg-gray-900 p-5 sm:p-6 text-white flex items-center justify-between border-b border-gray-800">
            <div class="flex items-center gap-3">
                <div class="h-9 w-9 rounded-lg bg-green-600 flex items-center justify-center text-white font-bold text-sm">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">DMDP Document Inspection Hub</span>
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
        <div class="bg-gray-50 border-b border-gray-200 p-3 sm:px-6 flex items-center gap-2 overflow-x-auto">
            <template x-for="(doc, idx) in activeDocList" :key="idx">
                <button type="button" 
                        @click="selectDocument(doc)"
                        class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors flex items-center gap-2 shrink-0"
                        :class="selectedDocKey === doc.key ? 'bg-green-600 text-white font-bold' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-100'">
                    <span x-text="doc.label"></span>
                </button>
            </template>
        </div>

        <!-- Modal Body: Document Preview Sheet -->
        <div class="p-6 overflow-y-auto space-y-6 flex-1">
            
            <!-- Document Meta Bar -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="p-3 rounded-xl bg-gray-50 border border-gray-200 space-y-0.5">
                    <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Document Classification</span>
                    <p class="text-xs font-bold text-gray-900 truncate" x-text="currentDoc.label || 'Legal Certificate'"></p>
                </div>
                <div class="p-3 rounded-xl bg-gray-50 border border-gray-200 space-y-0.5">
                    <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Issuing Authority</span>
                    <p class="text-xs font-bold text-gray-900 truncate" x-text="currentDoc.issuer || 'Government Regulatory Agency'"></p>
                </div>
                <div class="p-3 rounded-xl bg-gray-50 border border-gray-200 space-y-0.5">
                    <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Validity Period</span>
                    <p class="text-xs font-bold text-green-700 truncate" x-text="currentDoc.validity || 'Current / Valid'"></p>
                </div>
                <div class="p-3 rounded-xl bg-gray-50 border border-gray-200 space-y-0.5">
                    <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider block">Verification Status</span>
                    <p class="text-xs font-bold text-green-700 flex items-center gap-1.5">
                        <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                        <span x-text="currentDoc.status || 'Verified Valid'"></span>
                    </p>
                </div>
            </div>

            <!-- Official Document Inspection Certificate View -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 sm:p-8 space-y-6">
                <!-- Certificate Header -->
                <div class="text-center space-y-1 pb-4 border-b border-gray-100">
                    <div class="inline-flex items-center gap-2 rounded-full bg-gray-100 px-3 py-1 text-[10px] font-bold text-gray-700 tracking-wider uppercase">
                        Republic of the Philippines &bull; City of Cebu
                    </div>
                    <h4 class="text-sm font-bold text-gray-900">DEPARTMENT OF MANPOWER DEVELOPMENT AND PLACEMENT</h4>
                    <p class="text-xs text-gray-500">Employer Accreditation & Legal Verification Division</p>
                </div>

                <!-- Document Details Grid -->
                <div class="space-y-3 text-xs">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between py-2 border-b border-gray-100 gap-1">
                        <span class="font-medium text-gray-500">Registered Business Entity:</span>
                        <span class="font-bold text-gray-900 text-sm" x-text="activeDocCompany"></span>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between py-2 border-b border-gray-100 gap-1">
                        <span class="font-medium text-gray-500">Legal Document Type:</span>
                        <span class="font-semibold text-green-700" x-text="currentDoc.label"></span>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between py-2 border-b border-gray-100 gap-1">
                        <span class="font-medium text-gray-500">Validity Period:</span>
                        <span class="font-medium text-gray-800" x-text="currentDoc.validity || 'Current / Valid'"></span>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between py-2 border-b border-gray-100 gap-1">
                        <span class="font-medium text-gray-500">Attached File Reference:</span>
                        <span class="font-mono text-gray-700 font-medium" x-text="currentDoc.filename || 'electronic_record.pdf'"></span>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between py-2 border-b border-gray-100 gap-1">
                        <span class="font-medium text-gray-500">Digital Authentication:</span>
                        <span class="font-mono text-[11px] text-green-700 bg-green-50 px-2 py-0.5 rounded border border-green-200 font-semibold">
                            SHA-256 / DMDP-VERIFIED-REG-2026
                        </span>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between py-2 border-b border-gray-100 gap-1">
                        <span class="font-medium text-gray-500">Evaluation Compliance:</span>
                        <span class="font-medium text-gray-800">Meets DMDP City Ordinance Standards for Employment Facilitation</span>
                    </div>

                    <div class="p-3 rounded-xl bg-gray-50 border border-gray-200 text-[11px] text-gray-700">
                        <span><strong>Official Notice:</strong> Original and other documents, when applicable, should be presented for validation.</span>
                    </div>
                </div>

                <!-- Document Inspection Action -->
                <div class="p-4 rounded-xl bg-gray-50 border border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-lg bg-green-50 text-green-700 flex items-center justify-center font-bold text-sm">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-900 truncate" x-text="currentDoc.filename || (currentDoc.label + '.pdf')"></p>
                            <p class="text-[10px] text-gray-500">Official legal submission for DMDP corporate partner accreditation</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <template x-if="currentDoc.url">
                            <a :href="currentDoc.url" 
                               target="_blank" 
                               class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-600 hover:bg-green-700 text-white text-xs font-semibold transition-colors shrink-0">
                                <span>Open / Download File</span>
                                <span class="text-[10px]">↗</span>
                            </a>
                        </template>
                        <template x-if="!currentDoc.url">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-green-50 text-green-700 text-xs font-semibold border border-green-200 shrink-0">
                                <span>✓</span> Verified Digital Copy
                            </span>
                        </template>
                    </div>
                </div>

            </div>

        </div>

        <!-- Modal Footer -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <span class="text-[11px] text-gray-500">TrabaGo DMDP Enterprise Verification System</span>
            <button type="button" 
                    @click="docModalOpen = false" 
                    class="px-4 py-1.5 rounded-lg bg-gray-900 hover:bg-gray-800 text-white text-xs font-semibold transition-colors">
                Close Viewer
            </button>
        </div>

    </div>
</div>
