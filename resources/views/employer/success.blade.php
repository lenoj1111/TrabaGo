@extends('layouts.public')

@section('title', 'Registration Successful')

@section('content')
<div class="bg-gradient-to-b from-brand-50/50 to-white min-h-screen py-16">
    <div class="max-w-2xl mx-auto px-4 text-center">
        <div class="bg-white rounded-xl border border-brand-100 shadow-card p-8">
            <div class="w-20 h-20 rounded-full bg-green-100 mx-auto flex items-center justify-center mb-4">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            
            <h1 class="text-2xl font-bold text-brand-900 mb-2">Registration Successful! 🎉</h1>
            <p class="text-brand-600 mb-6">Thank you for registering your company!</p>
            
            <p class="text-sm text-brand-500 mb-6">
                Your application is pending admin approval. You will receive a notification once your account is approved.
            </p>
            
            <a href="{{ route('home') }}" class="inline-block px-6 py-2.5 text-white font-semibold rounded-lg transition shadow-lg hover:shadow-xl" 
               style="background: linear-gradient(135deg, #1b2739 0%, #33455e 100%);">
                Return to Home
            </a>
        </div>
    </div>
</div>
@endsection