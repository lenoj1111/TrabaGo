<!-- Employer Document Inspection Modal -->
<div x-show="docModalOpen" 
     x-cloak 
     class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4 sm:p-6"
     @keydown.escape.window="docModalOpen = false">
    
    <div @click.away="docModalOpen = false" 
         class="bg-white rounded-3xl max-w-3xl w-full shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[90vh] animate-in fade-in zoom-in duration-150">
        
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-5 sm:p-6 text-white flex items-center justify-between border-b border-emerald-500/20">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-2xl bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center text-xl text-emerald-400">
                    📂
                </div>
                <div>
                    <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest block">DMDP Document Inspection Hub</span>
                    <h3 class="text-lg sm:text-xl font-black text-white leading-tight" x-text="activeDocCompany || 'Employer Verification Documents'"></h3>
                </div>
            </div>
            <button @click="docModalOpen = false" 
                    type="button" 
                    class="h-8 w-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors text-xl font-bold">
                &times;
            </button>
        </div>

        <!-- Document Selection Tabs -->
        <div class="bg-slate-50 border-b border-slate-200 p-3 sm:px-6 flex items-center gap-2 overflow-x-auto scrollbar-none">
            <template x-for="(doc, idx) in activeDocList" :key="idx">
                <button type="button" 
                        @click="selectDocument(doc)"
                        class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shrink-0"
                        :class="selectedDocKey === doc.key ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-700 hover:bg-emerald-50 hover:text-emerald-900'">
                    <span x-text="doc.icon || '📄'"></span>
                    <span x-text="doc.label"></span>
                </button>
            </template>
        </div>

        <!-- Modal Body: Document Preview Sheet -->
        <div class="p-6 overflow-y-auto space-y-6 flex-1">
            
            <!-- Document Meta Bar -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-0.5">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Document Classification</span>
                    <p class="text-xs font-black text-slate-900 truncate" x-text="currentDoc.label || 'Legal Certificate'"></p>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-0.5">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Issuing Authority</span>
                    <p class="text-xs font-black text-slate-900 truncate" x-text="currentDoc.issuer || 'Government Regulatory Agency'"></p>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-0.5">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Verification Status</span>
                    <p class="text-xs font-black text-emerald-700 flex items-center gap-1">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        <span x-text="currentDoc.status || 'Verified Valid'"></span>
                    </p>
                </div>
            </div>

            <!-- Official Document Inspection Certificate View -->
            <div class="rounded-3xl border-2 border-emerald-100 bg-gradient-to-b from-emerald-50/30 to-white p-6 sm:p-8 space-y-6 shadow-sm relative overflow-hidden">
                <!-- Background Seal Watermark -->
                <div class="absolute -right-8 -bottom-8 opacity-5 text-slate-900 pointer-events-none text-9xl font-black select-none">
                    DMDP
                </div>

                <!-- Certificate Header -->
                <div class="text-center space-y-1 pb-4 border-b border-emerald-100">
                    <div class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-3 py-1 text-[10px] font-black text-emerald-800 tracking-wider uppercase">
                        Republic of the Philippines &bull; City of Cebu
                    </div>
                    <h4 class="text-base font-black text-slate-900">DEPARTMENT OF MANPOWER DEVELOPMENT AND PLACEMENT</h4>
                    <p class="text-xs text-slate-500 font-medium">Employer Accreditation & Legal Verification Division</p>
                </div>

                <!-- Document Details Grid -->
                <div class="space-y-3 text-xs">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between py-2 border-b border-slate-100 gap-1">
                        <span class="font-bold text-slate-500">Registered Business Entity:</span>
                        <span class="font-black text-slate-900 text-sm" x-text="activeDocCompany"></span>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between py-2 border-b border-slate-100 gap-1">
                        <span class="font-bold text-slate-500">Legal Document Type:</span>
                        <span class="font-bold text-emerald-900" x-text="currentDoc.label"></span>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between py-2 border-b border-slate-100 gap-1">
                        <span class="font-bold text-slate-500">Attached File Reference:</span>
                        <span class="font-mono text-slate-700 font-semibold" x-text="currentDoc.filename || 'electronic_record.pdf'"></span>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between py-2 border-b border-slate-100 gap-1">
                        <span class="font-bold text-slate-500">Digital Authentication:</span>
                        <span class="font-mono text-[11px] text-emerald-700 bg-emerald-100/70 px-2 py-0.5 rounded-md font-bold">
                            SHA-256 / DMDP-VERIFIED-REG-2026
                        </span>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between py-2 gap-1">
                        <span class="font-bold text-slate-500">Evaluation Compliance:</span>
                        <span class="font-bold text-slate-800">Meets DMDP City Ordinance Standards for Employment Facilitation</span>
                    </div>
                </div>

                <!-- Document Inspection Action -->
                <div class="p-4 rounded-2xl bg-white border border-emerald-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-lg">
                            📄
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-900 truncate" x-text="currentDoc.filename || (currentDoc.label + '.pdf')"></p>
                            <p class="text-[10px] text-slate-400">Official legal submission for DMDP corporate partner accreditation</p>
                        </div>
                    </div>

                    <template x-if="currentDoc.url">
                        <a :href="currentDoc.url" 
                           target="_blank" 
                           class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition-all shrink-0">
                            <span>Open / Download File</span>
                            <span class="text-[10px]">↗</span>
                        </a>
                    </template>
                    <template x-if="!currentDoc.url">
                        <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 text-xs font-bold border border-emerald-200 shrink-0">
                            <span>✓</span> Verified Digital Copy
                        </span>
                    </template>
                </div>

            </div>

        </div>

        <!-- Modal Footer -->
        <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex items-center justify-between">
            <span class="text-[11px] font-medium text-slate-500">TrabaGo DMDP Enterprise Verification System</span>
            <button type="button" 
                    @click="docModalOpen = false" 
                    class="px-5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-colors">
                Close Viewer
            </button>
        </div>

    </div>
</div>
