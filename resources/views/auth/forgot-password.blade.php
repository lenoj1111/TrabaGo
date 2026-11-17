<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Enter the email address used for your account. If it is registered, we will replace your password and email you a temporary one. Change it after signing in.') }}
    </div>

    @if (session('status'))
        <div role="status" aria-live="polite" class="mb-4 flex items-start gap-3 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
            <svg class="mt-0.5 h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16Zm3.78-9.78a.75.75 0 00-1.06-1.06L9 10.88 7.28 9.16a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.06 0l4.25-4.25Z" clip-rule="evenodd" />
            </svg>
            <p>{{ session('status') }}</p>
        </div>
    @endif

    <form id="forgot-password-form" method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Email Temporary Password') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-4">
        <a href="{{ route('login') }}" class="inline-flex min-h-10 items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:border-emerald-600 hover:bg-emerald-50 hover:text-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2">
            &larr; {{ __('Previous Page') }}
        </a>
    </div>

    <dialog id="send-password-confirmation" aria-labelledby="confirmation-title" aria-describedby="confirmation-message" class="w-[calc(100%-2rem)] max-w-md rounded-xl border border-gray-200 p-0 shadow-2xl backdrop:bg-slate-950/40">
        <div class="p-6">
            <h2 id="confirmation-title" class="text-lg font-bold text-gray-900">{{ __('Send temporary password?') }}</h2>
            <p id="confirmation-message" class="mt-2 text-sm leading-6 text-gray-600">
                {{ __('A new temporary password will replace the current password and be sent to:') }}
            </p>
            <p id="confirmation-email" class="mt-2 break-all rounded-md bg-gray-50 px-3 py-2 text-sm font-semibold text-gray-900"></p>
            <p class="mt-3 text-sm font-medium text-amber-700">
                {{ __('You will need the emailed password to sign in afterward.') }}
            </p>
            <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                <button id="cancel-password-email" type="button" class="inline-flex min-h-10 items-center justify-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2">
                    {{ __('No, go back') }}
                </button>
                <button id="confirm-password-email" type="button" class="inline-flex min-h-10 items-center justify-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2">
                    {{ __('Yes, send email') }}
                </button>
            </div>
        </div>
    </dialog>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('forgot-password-form');
            const emailInput = document.getElementById('email');
            const dialog = document.getElementById('send-password-confirmation');
            const emailPreview = document.getElementById('confirmation-email');
            const cancelButton = document.getElementById('cancel-password-email');
            const confirmButton = document.getElementById('confirm-password-email');
            let confirmed = false;

            form.addEventListener('submit', (event) => {
                if (confirmed) {
                    return;
                }

                event.preventDefault();
                emailPreview.textContent = emailInput.value.trim();
                dialog.showModal();
            });

            cancelButton.addEventListener('click', () => dialog.close());

            confirmButton.addEventListener('click', () => {
                confirmed = true;
                dialog.close();
                form.requestSubmit();
            });
        });
    </script>
</x-guest-layout>
