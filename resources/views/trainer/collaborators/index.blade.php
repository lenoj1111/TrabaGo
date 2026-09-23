@extends('layouts.trainer')

@section('title', 'Collaborator Trainers - Skills Trainer Portal')

@section('content')
<div x-data="{ addModal: false }" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-8">
        
        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-green-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-green-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-green-400/20 px-3 py-1 text-xs font-bold text-green-300 border border-green-400/30">
                    <span class="h-2 w-2 rounded-full bg-green-400 animate-pulse"></span>
                    DMDP Training Network
                </span>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight">Collaborator Trainers</h1>
                <p class="text-sm text-slate-300 max-w-2xl">
                    Invite and manage collaborator trainer accounts. Accounts registered by trainers require DMDP Administrator approval before access is enabled.
                </p>
            </div>

            <button @click="addModal = true" type="button" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-green-500 hover:bg-green-400 text-slate-950 text-xs font-extrabold transition-all shadow-lg shadow-green-500/20 hover:scale-105 shrink-0">
                <span>➕</span> Register Collaborator
            </button>
        </div>

        <!-- Admin Approval Requirement Notice -->
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 flex items-start gap-3.5 shadow-sm">
            <div class="h-8 w-8 rounded-xl bg-amber-500/20 text-amber-800 font-black flex items-center justify-center shrink-0">
                ⚠️
            </div>
            <div class="space-y-1 text-xs">
                <h3 class="font-bold text-amber-900">Administrative Security Protocol</h3>
                <p class="text-amber-800 leading-relaxed">
                    When you create a trainer account for a partner or collaborator, the account is created with a <strong>Pending</strong> status. The collaborator will not be permitted to log in until a Cebu City DMDP Administrator reviews and grants official approval. (Accounts created directly by the Administrator are automatically approved).
                </p>
            </div>
        </div>

        <!-- Collaborators Table Card -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <h2 class="text-lg font-black text-slate-900">Training Faculty & Collaborators</h2>
                    <p class="text-xs text-slate-500">Directory of trainer accounts and administrative verification statuses.</p>
                </div>
                <span class="text-xs font-bold text-green-800 bg-green-50 px-3 py-1 rounded-full border border-green-200">
                    {{ $collaborators->total() }} Total Accounts
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-3">Trainer Name</th>
                            <th class="py-3 px-3">Email Address</th>
                            <th class="py-3 px-3">Specialization</th>
                            <th class="py-3 px-3">Office / Institution</th>
                            <th class="py-3 px-3">Type</th>
                            <th class="py-3 px-3">Approval Status</th>
                            <th class="py-3 px-3 text-right">Registered</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($collaborators as $collab)
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3.5 px-3">
                                    <div class="font-bold text-slate-900 flex items-center gap-2">
                                        <div class="h-7 w-7 rounded-lg bg-slate-900 text-white flex items-center justify-center font-bold text-[10px]">
                                            {{ strtoupper(substr($collab->full_name ?? 'T', 0, 2)) }}
                                        </div>
                                        <span>{{ $collab->full_name ?? 'Trainer' }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3 font-medium text-slate-600">
                                    {{ $collab->email }}
                                </td>
                                <td class="py-3.5 px-3 text-slate-700 font-semibold">
                                    {{ $collab->specialization ?: 'General Vocational' }}
                                </td>
                                <td class="py-3.5 px-3 text-slate-600">
                                    {{ $collab->partner_institution ?: ($collab->office ?: 'DMDP Center') }}
                                </td>
                                <td class="py-3.5 px-3">
                                    <span class="rounded-md px-2 py-0.5 text-[10px] font-bold uppercase {{ $collab->trainer_type === 'partner' ? 'bg-purple-100 text-purple-800' : 'bg-slate-100 text-slate-700' }}">
                                        {{ $collab->trainer_type ?: 'dmdp' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3">
                                    @if($collab->is_approved && $collab->status === 'active')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 text-green-800 px-2.5 py-0.5 text-[11px] font-extrabold">
                                            <span class="h-1.5 w-1.5 rounded-full bg-green-600"></span>
                                            Approved & Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 text-amber-900 px-2.5 py-0.5 text-[11px] font-extrabold border border-amber-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-600 animate-pulse"></span>
                                            Pending Admin Approval
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3 text-right text-slate-400 font-medium">
                                    {{ \Carbon\Carbon::parse($collab->created_at)->format('M d, Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400 text-xs">
                                    No collaborator trainers found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pt-4">
                {{ $collaborators->links() }}
            </div>
        </div>

    </div>

    <!-- Register Collaborator Modal -->
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="addModal" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="addModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div x-show="addModal" x-transition class="relative inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-green-100">
                <form action="{{ route('trainer.collaborators.store') }}" method="POST">
                    @csrf
                    <div class="bg-gradient-to-r from-slate-950 via-green-950 to-slate-900 px-6 py-5 text-white flex items-center justify-between border-b border-green-500/20">
                        <div class="flex items-center gap-3">
                            <span class="text-xl">👥</span>
                            <div>
                                <h3 class="text-base font-bold text-white">Register Collaborator Trainer</h3>
                                <p class="text-xs text-green-300/80">Account will be submitted for Admin approval.</p>
                            </div>
                        </div>
                        <button @click="addModal = false" type="button" class="text-slate-400 hover:text-white text-xl font-bold">&times;</button>
                    </div>

                    <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                        <div class="rounded-xl bg-amber-50 border border-amber-200 p-3 text-[11px] text-amber-800 leading-relaxed font-medium">
                            📌 <strong>Approval Workflow:</strong> Accounts created here will be placed on hold until confirmed by the DMDP Administrator.
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Full Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="full_name" required placeholder="e.g. Engr. Maria Santos" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-green-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Email Address <span class="text-rose-500">*</span></label>
                            <input type="email" name="email" required placeholder="collaborator@dmdp-partner.gov.ph" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-green-500 outline-none">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Password <span class="text-rose-500">*</span></label>
                                <input type="password" name="password" required minlength="8" placeholder="Min. 8 characters" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-green-500 outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Confirm Password <span class="text-rose-500">*</span></label>
                                <input type="password" name="password_confirmation" required minlength="8" placeholder="Repeat password" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-green-500 outline-none">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Contact Number</label>
                                <input type="text" name="phone" placeholder="0917-xxx-xxxx" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-green-500 outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Specialization</label>
                                <input type="text" name="specialization" placeholder="e.g. Electronics & Mechatronics" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-green-500 outline-none">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Office / Department</label>
                                <input type="text" name="office" placeholder="DMDP Training Partner Unit" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-green-500 outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Partner Training Institution</label>
                                <input type="text" name="partner_institution" placeholder="e.g. Cebu Technical Institute" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-green-500 outline-none">
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-3 rounded-b-3xl">
                        <button @click="addModal = false" type="button" class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-green-600 hover:bg-green-500 text-white text-xs font-extrabold shadow-md shadow-green-600/20 transition-all">
                            Submit for Admin Approval
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
