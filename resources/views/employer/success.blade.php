@extends('layouts.public')

@section('title', 'Employer Registration Submitted - DMDP Cebu City')

@section('content')
<div class="bg-gradient-to-b from-emerald-50/60 to-white min-h-screen py-16">
    <div class="max-w-2xl mx-auto px-4 text-center">
        <div class="bg-white rounded-3xl border border-emerald-100 shadow-card p-10 space-y-6">
            <div class="w-20 h-20 rounded-full bg-emerald-100 mx-auto flex items-center justify-center shadow-lg shadow-emerald-500/20">
                <svg class="w-10 h-10 text-emerald-600 font-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            
            <div>
                <h1 class="text-3xl font-black text-slate-900 mb-2">Employer Registration Submitted! 🎉</h1>
                <p class="text-slate-600 text-sm">Thank you for registering your company with DMDP Cebu City.</p>
            </div>
            
            <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-100 text-xs text-emerald-800 font-semibold">
                Your company registration and accreditation documents are under review by DMDP officers. You will receive an email confirmation once approved.
            </div>
            
            <div>
                <a href="{{ route('home') }}" class="inline-block px-8 py-3.5 text-white font-extrabold text-sm rounded-2xl transition shadow-xl shadow-emerald-600/30 hover:scale-105 bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 hover:from-emerald-500 hover:to-teal-400">
                    Return to Portal Home &rarr;
                </a>
            </div>
        </div>
    </div>
</div>
@endsection