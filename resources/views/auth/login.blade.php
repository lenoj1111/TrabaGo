@extends('layouts.public')

@section('title', 'Account Login - DMDP Cebu City')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-brand-50/50 to-white py-10 md:py-16">
    <div class="max-w-5xl mx-auto px-5">
        <div class="grid grid-cols-1 lg:grid-cols-2 bg-white border border-brand-100 rounded-2xl shadow-card overflow-hidden">
            <div class="relative overflow-hidden px-7 py-10 md:px-10 md:py-14 text-white" style="background: linear-gradient(135deg, #1b2739 0%, #33455e 55%, #405673 100%);">
                <div class="absolute -right-16 -bottom-20 w-64 h-64 rounded-full border border-gold-500/30"></div>
                <div class="absolute -right-8 -bottom-12 w-48 h-48 rounded-full border border-gold-500/20"></div>
                <div class="relative">
                    <div class="w-14 h-14 rounded-xl bg-gold-500 flex items-center justify-center mb-8 shadow-lg shadow-gold-500/30">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="text-gold-400 text-xs font-bold uppercase tracking-widest mb-3">DMDP x TrabaGo</p>
                    <h1 class="text-3xl md:text-4xl font-bold leading-tight mb-4">Welcome back.</h1>
                    <p class="text-brand-200 leading-relaxed max-w-sm">Sign in to continue to your TrabaGo workspace.</p>
                </div>
            </div>

            <div class="px-7 py-10 md:px-10 md:py-14">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-brand-900">Account Login</h2>
                    <p class="text-sm text-brand-500 mt-1">Enter your account details to continue.</p>
                </div>

                @if (session('status'))
                    <div class="mb-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1.5">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                               class="w-full px-4 py-3 bg-white border @error('email') border-red-500 @else border-brand-200 @enderror rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none">
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs font-semibold text-brand-700 uppercase tracking-wider">Password</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs font-medium text-brand-600 hover:text-brand-900">Forgot password?</a>
                            @endif
                        </div>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                               class="w-full px-4 py-3 bg-white border @error('password') border-red-500 @else border-brand-200 @enderror rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none">
                    </div>

                    <label for="remember" class="flex items-center gap-2 text-sm text-brand-600 cursor-pointer">
                        <input id="remember" type="checkbox" name="remember" class="w-4 h-4 text-brand-600 rounded border-brand-300 focus:ring-brand-500">
                        Remember me
                    </label>

                    <button type="submit" class="w-full px-6 py-3 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition hover:-translate-y-0.5" style="background: linear-gradient(135deg, #1b2739 0%, #33455e 100%);">
                        Log in
                    </button>
                </form>

                <p class="text-center text-sm text-brand-600 mt-8">
                    Don't have an account?
                    <a href="{{ route('home') }}" class="font-semibold text-brand-900 hover:text-brand-600">Create an account</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
