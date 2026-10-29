{{-- ============================================================ --}}
{{-- EMPLOYER LOGIN CREDENTIALS UPDATE MODAL (HR TURNOVER)        --}}
{{-- ============================================================ --}}
<div x-show="credentialsModalOpen" x-cloak
     class="fixed inset-0 z-[80] overflow-y-auto bg-slate-950/60 backdrop-blur-sm transition-all"
     @keydown.escape.window="if (!credentialsForm.isSubmitting) credentialsModalOpen = false">
    <div class="flex min-h-full items-center justify-center p-4 sm:p-6">
        <div @click.away="if (!credentialsForm.isSubmitting) credentialsModalOpen = false"
             class="w-full max-w-lg rounded-3xl bg-white shadow-2xl border border-slate-200 overflow-hidden transition-all text-left">
            
            {{-- Modal Header --}}
            <div class="bg-gradient-to-r from-slate-950 via-indigo-950 to-slate-900 px-6 py-5 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-indigo-500/20 border border-indigo-400/30 flex items-center justify-center text-indigo-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-black tracking-tight text-white">Update Employer Credentials</h3>
                        <p class="text-[11px] text-indigo-200">HR Turnover & Account Access Recovery</p>
                    </div>
                </div>
                <button type="button" 
                        @click="credentialsModalOpen = false"
                        :disabled="credentialsForm.isSubmitting"
                        class="text-slate-400 hover:text-white p-1 rounded-xl hover:bg-white/10 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Modal Form --}}
            <form @submit.prevent="submitCredentialsUpdate()" class="p-6 space-y-4">
                
                {{-- Info Note --}}
                <div class="p-3.5 rounded-2xl bg-indigo-50/70 border border-indigo-100 flex items-start gap-2.5 text-xs text-indigo-950">
                    <svg class="w-4 h-4 text-indigo-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="leading-relaxed">
                        Use this form when the assigned HR has resigned without leaving access. Updating the email and password allows the new HR officer to log in immediately.
                    </p>
                </div>

                {{-- Company Info Pill --}}
                <div class="px-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Company Account</span>
                        <p class="text-xs font-black text-slate-800" x-text="credentialsForm.company_name"></p>
                    </div>
                    <span class="text-[11px] font-mono font-bold px-2 py-0.5 rounded-lg bg-slate-200 text-slate-700">
                        Employer #<span x-text="credentialsForm.employer_id"></span>
                    </span>
                </div>

                {{-- Error Message Box --}}
                <div x-show="credentialsForm.errorMessage" x-cloak
                     class="p-3 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span x-text="credentialsForm.errorMessage"></span>
                </div>

                {{-- Login Email (Primary Email) --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Login Email / Primary Email <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" 
                           x-model="credentialsForm.email" 
                           required 
                           placeholder="new.hr@company.com"
                           class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all font-medium">
                    <p class="text-[10px] text-slate-400 mt-1">Primary email used to sign into the TrabaGo Employer Portal.</p>
                </div>

                {{-- Contact Person & Secondary Email in a 2-col grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            New HR Contact Person
                        </label>
                        <input type="text" 
                               x-model="credentialsForm.contact_person" 
                               placeholder="e.g. Maria Santos (HR Manager)"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all font-medium">
                        <p class="text-[10px] text-slate-400 mt-0.5">Authorized HR Representative</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Contact Phone / Mobile
                        </label>
                        <input type="text" 
                               x-model="credentialsForm.contact_phone" 
                               placeholder="0917xxxxxxx"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all font-medium">
                        <p class="text-[10px] text-slate-400 mt-0.5">HR Hotline / Mobile</p>
                    </div>
                </div>

                {{-- Secondary Email --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Secondary Email (Optional)
                    </label>
                    <input type="email" 
                           x-model="credentialsForm.secondary_email" 
                           placeholder="backup@company.com"
                           class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all font-medium">
                    <p class="text-[10px] text-slate-400 mt-0.5">Backup corporate email for dual-email notifications.</p>
                </div>

                {{-- Password Section --}}
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-700">Account Password</span>
                        <label class="inline-flex items-center gap-1.5 cursor-pointer text-[11px] font-semibold text-slate-500 hover:text-slate-700">
                            <input type="checkbox" x-model="credentialsForm.showPassword" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <span>Show Passwords</span>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <input :type="credentialsForm.showPassword ? 'text' : 'password'" 
                                   x-model="credentialsForm.password" 
                                   placeholder="New Password (min 8)"
                                   autocomplete="new-password"
                                   class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all font-medium">
                        </div>
                        <div>
                            <input :type="credentialsForm.showPassword ? 'text' : 'password'" 
                                   x-model="credentialsForm.password_confirmation" 
                                   placeholder="Confirm New Password"
                                   autocomplete="new-password"
                                   class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all font-medium">
                        </div>
                    </div>
                    <p class="text-[10px] text-slate-500">
                        Leave blank to keep current password. If entered, password must be at least 8 characters.
                    </p>
                </div>

                {{-- Action Buttons --}}
                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button type="button" 
                            @click="credentialsModalOpen = false"
                            :disabled="credentialsForm.isSubmitting"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            :disabled="credentialsForm.isSubmitting"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white text-xs font-bold shadow-md shadow-indigo-600/20 transition-all">
                        <template x-if="!credentialsForm.isSubmitting">
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Save Credentials</span>
                            </span>
                        </template>
                        <template x-if="credentialsForm.isSubmitting">
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                <span>Updating...</span>
                            </span>
                        </template>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    if (!window.employerCredentialsHandler) {
        window.employerCredentialsHandler = function(csrfToken) {
            return {
                credentialsModalOpen: false,
                credentialsForm: {
                    employer_id: '',
                    user_id: '',
                    company_name: '',
                    email: '',
                    secondary_email: '',
                    contact_person: '',
                    contact_phone: '',
                    password: '',
                    password_confirmation: '',
                    showPassword: false,
                    errorMessage: '',
                    isSubmitting: false,
                },
                openCredentialsModal(emp) {
                    this.credentialsForm.employer_id = emp.employer_id || '';
                    this.credentialsForm.user_id = emp.user_id || '';
                    this.credentialsForm.company_name = emp.company_name || 'Employer Account';
                    this.credentialsForm.email = emp.email || '';
                    this.credentialsForm.secondary_email = emp.secondary_email || '';
                    this.credentialsForm.contact_person = emp.contact_person || '';
                    this.credentialsForm.contact_phone = emp.contact_phone || '';
                    this.credentialsForm.password = '';
                    this.credentialsForm.password_confirmation = '';
                    this.credentialsForm.showPassword = false;
                    this.credentialsForm.errorMessage = '';
                    this.credentialsForm.isSubmitting = false;
                    this.credentialsModalOpen = true;
                },
                submitCredentialsUpdate() {
                    this.credentialsForm.isSubmitting = true;
                    this.credentialsForm.errorMessage = '';

                    if (!this.credentialsForm.email || !this.credentialsForm.email.includes('@')) {
                        this.credentialsForm.errorMessage = 'Please enter a valid login email address.';
                        this.credentialsForm.isSubmitting = false;
                        return;
                    }

                    if (this.credentialsForm.password) {
                        if (this.credentialsForm.password.length < 8) {
                            this.credentialsForm.errorMessage = 'New password must be at least 8 characters.';
                            this.credentialsForm.isSubmitting = false;
                            return;
                        }
                        if (this.credentialsForm.password !== this.credentialsForm.password_confirmation) {
                            this.credentialsForm.errorMessage = 'Password confirmation does not match.';
                            this.credentialsForm.isSubmitting = false;
                            return;
                        }
                    }

                    const token = csrfToken || '{{ csrf_token() }}';
                    const employerId = this.credentialsForm.employer_id || this.credentialsForm.user_id;

                    fetch(`/admin/employers/${employerId}/update-credentials`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            email: this.credentialsForm.email,
                            secondary_email: this.credentialsForm.secondary_email,
                            contact_person: this.credentialsForm.contact_person,
                            contact_phone: this.credentialsForm.contact_phone,
                            password: this.credentialsForm.password,
                            password_confirmation: this.credentialsForm.password_confirmation
                        })
                    })
                    .then(async (res) => {
                        const data = await res.json();
                        if (!res.ok || !data.success) {
                            let errorMsg = data.message || data.error || 'Failed to update credentials.';
                            if (data.errors) {
                                errorMsg = Object.values(data.errors).flat().join(' ');
                            }
                            throw new Error(errorMsg);
                        }
                        return data;
                    })
                    .then((data) => {
                        this.credentialsModalOpen = false;
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Credentials Updated!',
                                html: `<div class="text-left text-xs space-y-2 text-slate-700 bg-emerald-50 p-4 rounded-xl border border-emerald-200"><p class="font-bold text-emerald-900">${data.message}</p><p class="text-slate-600">The assigned HR / employer can now log into TrabaGo with their updated email and password.</p></div>`,
                                icon: 'success',
                                confirmButtonColor: '#4f46e5',
                                confirmButtonText: 'Done'
                            }).then(() => location.reload());
                        } else {
                            alert(data.message);
                            location.reload();
                        }
                    })
                    .catch((err) => {
                        this.credentialsForm.errorMessage = err.message || 'An unexpected error occurred.';
                    })
                    .finally(() => {
                        this.credentialsForm.isSubmitting = false;
                    });
                }
            };
        };
    }
</script>
