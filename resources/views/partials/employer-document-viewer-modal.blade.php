<div x-show="docModalOpen" x-cloak
     class="fixed inset-0 z-[80] overflow-y-auto bg-slate-950/70 backdrop-blur-sm"
     @keydown.escape.window="docModalOpen = false">
    <div class="flex min-h-full items-center justify-center p-4">
        <div @click.away="docModalOpen = false"
             class="w-full max-w-5xl rounded-3xl border border-slate-200 bg-white shadow-2xl overflow-hidden">

            <div class="flex items-start justify-between gap-4 border-b border-slate-100 bg-slate-50/60 p-6">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Document Inspection</span>
                    <h2 class="mt-1 text-lg font-black text-slate-900" x-text="activeDocCompany"></h2>
                    <p class="text-xs text-slate-500">
                        <span x-text="activeDocList.length"></span> document(s) on record
                    </p>
                </div>
                <button type="button" @click="docModalOpen = false"
                        class="text-2xl font-bold text-slate-400 hover:text-slate-800"
                        aria-label="Close documents">&times;</button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12">
                <aside class="lg:col-span-5 border-b lg:border-b-0 lg:border-r border-slate-100 max-h-[60vh] lg:max-h-[70vh] overflow-y-auto">
                    <ul class="divide-y divide-slate-100">
                        <template x-for="doc in activeDocList" :key="doc.key">
                            <li>
                                <button type="button"
                                        @click="selectDocument(doc)"
                                        :class="selectedDocKey === doc.key
                                            ? 'bg-emerald-50 border-l-4 border-emerald-500'
                                            : 'hover:bg-slate-50 border-l-4 border-transparent'"
                                        class="w-full text-left p-4 transition-colors">
                                    <div class="flex items-start gap-3">
                                        <span class="text-2xl shrink-0" x-text="doc.icon"></span>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs font-black text-slate-900 leading-tight" x-text="doc.label"></p>
                                            <p class="text-[10px] text-slate-500 mt-0.5" x-text="doc.issuer"></p>
                                            <div class="flex items-center gap-1.5 mt-1.5">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-200">
                                                    ✓ <span x-text="doc.status"></span>
                                                </span>
                                                <span x-show="doc.revisionStatus === 'new'"
                                                      class="inline-flex items-center px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 text-[10px] font-bold border border-blue-200">
                                                    New
                                                </span>
                                                <span x-show="doc.revisionStatus === 'previous'"
                                                      class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[10px] font-bold border border-slate-200">
                                                    Previous version
                                                </span>
                                            </div>
                                            <p class="text-[10px] text-slate-400 mt-1" x-text="doc.validity"></p>
                                        </div>
                                    </div>
                                </button>
                            </li>
                        </template>

                        <template x-if="activeDocList.length === 0">
                            <li class="p-6 text-center text-xs text-slate-400 italic">
                                No documents attached.
                            </li>
                        </template>
                    </ul>
                </aside>

                <div class="lg:col-span-7 bg-slate-50/40 p-6 max-h-[60vh] lg:max-h-[70vh] overflow-y-auto">
                    <template x-if="currentDoc && currentDoc.key">
                        <div class="space-y-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Currently Viewing</span>
                                    <h3 class="mt-1 text-sm font-black text-slate-900" x-text="currentDoc.label"></h3>
                                    <p class="text-[11px] text-slate-500 mt-0.5" x-text="currentDoc.issuer"></p>
                                </div>
                                <a :href="currentDoc.downloadUrl"
                                   class="shrink-0 inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-emerald-600 text-white text-[11px] font-bold transition-colors">
                                    ⬇ Download
                                </a>
                            </div>

                            <div class="rounded-xl border border-slate-200 bg-white p-3">
                                <template x-if="currentDoc.isImage && currentDoc.previewUrl">
                                    <img :src="currentDoc.previewUrl"
                                         :alt="currentDoc.label"
                                         class="w-full rounded-lg border border-slate-100"
                                         loading="lazy">
                                </template>
                                <template x-if="!currentDoc.isImage && currentDoc.canPreview && currentDoc.previewUrl">
                                    <iframe :src="currentDoc.previewUrl"
                                            :title="currentDoc.label"
                                            class="w-full h-[60vh] rounded-lg border border-slate-100"
                                            loading="lazy"></iframe>
                                </template>
                                <template x-if="!currentDoc.canPreview || !currentDoc.previewUrl">
                                    <div class="flex flex-col items-center justify-center py-12 text-center">
                                        <div class="text-5xl mb-3" x-text="currentDoc.icon || '📄'"></div>
                                        <p class="text-xs font-bold text-slate-700">Preview not available</p>
                                        <p class="text-[11px] text-slate-500 mt-1">Use the Download button above to view this file.</p>
                                        <p class="text-[10px] text-slate-400 mt-3 font-mono break-all" x-text="currentDoc.filename"></p>
                                    </div>
                                </template>
                            </div>

                            <div class="rounded-xl bg-white border border-slate-200 p-4 space-y-2 text-[11px]">
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-slate-500 font-semibold">File Name</span>
                                    <span class="text-slate-900 font-bold truncate max-w-[60%]" x-text="currentDoc.filename || 'N/A'"></span>
                                </div>
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-slate-500 font-semibold">Status</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-200" x-text="currentDoc.status"></span>
                                </div>
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-slate-500 font-semibold">Validity</span>
                                    <span class="text-slate-900 font-bold" x-text="currentDoc.validity"></span>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template x-if="!currentDoc || !currentDoc.key">
                        <div class="flex flex-col items-center justify-center py-20 text-center">
                            <div class="text-4xl mb-3">📁</div>
                            <p class="text-xs font-bold text-slate-700">Select a document from the list</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
