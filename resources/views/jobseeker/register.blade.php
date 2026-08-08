@extends('layouts.public')

@section('title', 'DMDP Jobseeker Registration - Department of Manpower Development and Placement')

@section('content')
<div class="bg-gradient-to-b from-brand-50/50 to-white min-h-screen py-8">
    <div class="max-w-5xl mx-auto px-4">
        <!-- Breadcrumb -->
        <div class="bg-white/80 backdrop-blur border border-brand-100 rounded-xl px-5 py-3 mb-6 shadow-sm">
            <nav class="text-sm">
                <ol class="flex items-center gap-2 text-brand-600">
                    <li><a href="{{ route('home') }}" class="hover:text-brand-900 transition-colors">Home</a></li>
                    <li><span class="text-brand-300">/</span></li>
                    <li><a href="#" class="hover:text-brand-900 transition-colors">Jobseeker</a></li>
                    <li><span class="text-brand-300">/</span></li>
                    <li class="text-brand-900 font-semibold">Registration</li>
                </ol>
            </nav>
        </div>

        <!-- Header -->
        <div class="relative overflow-hidden rounded-2xl mb-8" style="background: linear-gradient(135deg, #1b2739 0%, #33455e 50%, #405673 100%);">
            <div class="absolute inset-0 opacity-5">
                <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <pattern id="grid" width="20" height="20" patternUnits="userSpaceOnUse">
                        <circle cx="2" cy="2" r="1" fill="white" />
                    </pattern>
                    <rect width="100" height="100" fill="url(#grid)" />
                </svg>
            </div>
            <div class="absolute top-0 left-0 w-full h-1 flex">
                <div class="w-1/3 h-full" style="background: #1b2739;"></div>
                <div class="w-1/3 h-full" style="background: #b3894a;"></div>
                <div class="w-1/3 h-full" style="background: #ce1126;"></div>
            </div>
            <div class="relative px-6 py-8 md:px-8 md:py-10">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-2xl bg-white/10 backdrop-blur flex items-center justify-center border border-white/20">
                            <svg class="w-8 h-8 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold text-white">Jobseeker Registration</h1>
                            <p class="text-brand-300 text-sm">Department of Manpower Development and Placement</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 bg-white/10 backdrop-blur px-4 py-2 rounded-full border border-white/20">
                        <svg class="w-4 h-4 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                        <span class="text-xs text-white font-medium">Tesseract OCR Ready</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Steps -->
        <div class="bg-white rounded-xl border border-brand-100 p-6 mb-8 shadow-card">
            <div class="relative">
                <div class="flex justify-between items-center">
                    <!-- Step 1 -->
                    <div class="flex flex-col items-center flex-1">
                        <div class="w-12 h-12 rounded-full bg-brand-700 text-white flex items-center justify-center font-bold text-sm relative z-10 shadow-lg shadow-brand-700/30">
                            1
                        </div>
                        <span class="text-xs font-semibold text-brand-700 mt-2">Upload</span>
                    </div>
                    <div class="flex-1 h-0.5 bg-brand-200 relative -mt-6"></div>
                    <!-- Step 2 -->
                    <div class="flex flex-col items-center flex-1">
                        <div class="w-12 h-12 rounded-full bg-brand-100 text-brand-500 flex items-center justify-center font-bold text-sm relative z-10">
                            2
                        </div>
                        <span class="text-xs font-medium text-brand-400 mt-2">Review</span>
                    </div>
                    <div class="flex-1 h-0.5 bg-brand-200 relative -mt-6"></div>
                    <!-- Step 3 -->
                    <div class="flex flex-col items-center flex-1">
                        <div class="w-12 h-12 rounded-full bg-brand-100 text-brand-500 flex items-center justify-center font-bold text-sm relative z-10">
                            3
                        </div>
                        <span class="text-xs font-medium text-brand-400 mt-2">Skills</span>
                    </div>
                    <div class="flex-1 h-0.5 bg-brand-200 relative -mt-6"></div>
                    <!-- Step 4 -->
                    <div class="flex flex-col items-center flex-1">
                        <div class="w-12 h-12 rounded-full bg-brand-100 text-brand-500 flex items-center justify-center font-bold text-sm relative z-10">
                            4
                        </div>
                        <span class="text-xs font-medium text-brand-400 mt-2">Complete</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Section -->
        <div class="bg-white border border-brand-100 rounded-xl shadow-card overflow-hidden">
            <!-- Form Header -->
            <div class="relative overflow-hidden" style="background: linear-gradient(135deg, #f5f7fa 0%, #e9edf3 100%);">
                <div class="absolute top-0 left-0 w-full h-1" style="background: linear-gradient(90deg, #b3894a, #1b2739);"></div>
                <div class="relative px-6 py-5">
                    <h2 class="text-lg font-bold text-brand-900">
                        <svg class="inline-block w-5 h-5 text-gold-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Personal Information
                    </h2>
                    <p class="text-brand-500 text-sm mt-1 ml-7">Upload your resume and we'll auto-fill the fields</p>
                </div>
            </div>

            <!-- Upload Box -->
            <div class="px-6 pt-6">
                <div id="uploadBox" class="relative border-2 border-dashed border-brand-200 rounded-xl p-8 text-center hover:border-gold-400 transition-all duration-300 cursor-pointer bg-gradient-to-b from-brand-50/30 to-white hover:from-brand-50/60 hover:shadow-lg group">
                    <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <span class="text-xs text-brand-400 bg-white px-3 py-1 rounded-full border border-brand-200">Click to upload</span>
                    </div>
                    <div class="w-20 h-20 rounded-2xl bg-brand-100 mx-auto flex items-center justify-center mb-4 group-hover:bg-brand-200 transition-colors">
                        <svg class="w-10 h-10 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                    </div>
                    <p class="text-brand-700 font-medium">Click to upload your resume</p>
                    <p class="text-brand-400 text-sm mt-1">PDF, JPG, PNG · Max 5MB</p>
                    <input type="file" id="fileInput" accept=".pdf,.png,.jpg,.jpeg" class="hidden" />
                </div>

                <!-- File Preview -->
                <div id="filePreview" class="hidden mt-4 bg-gradient-to-r from-brand-50 to-white rounded-xl px-5 py-3 flex items-center justify-between border border-brand-200 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-brand-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <span id="fileName" class="text-brand-700 font-medium">resume.pdf</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="bg-green-100 text-green-700 text-xs font-semibold px-3 py-1.5 rounded-full flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            94% confidence
                        </span>
                        <button id="removeFile" class="text-brand-400 hover:text-red-500 transition p-1 rounded-lg hover:bg-red-50">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Form Fields -->
            <div class="px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="bg-gradient-to-br from-white to-brand-50/30 p-4 rounded-xl border border-brand-100">
                        <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">
                            <span class="text-red-500">*</span> Full Name
                        </label>
                        <input type="text" id="fullName" value="Maria Santos" class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                        <span class="text-xs text-brand-500 mt-1 inline-flex items-center gap-1">
                            <svg class="w-3 h-3 text-gold-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" clip-rule="evenodd"/>
                            </svg>
                            OCR extracted
                        </span>
                    </div>
                    <div class="bg-gradient-to-br from-white to-brand-50/30 p-4 rounded-xl border border-brand-100">
                        <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">
                            <span class="text-red-500">*</span> Email Address
                        </label>
                        <input type="email" id="email" value="maria.santos@email.com" class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                        <span class="text-xs text-brand-500 mt-1 inline-flex items-center gap-1">
                            <svg class="w-3 h-3 text-gold-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" clip-rule="evenodd"/>
                            </svg>
                            OCR extracted
                        </span>
                    </div>
                    <div class="bg-gradient-to-br from-white to-brand-50/30 p-4 rounded-xl border border-brand-100">
                        <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">
                            <span class="text-red-500">*</span> Phone Number
                        </label>
                        <input type="text" id="phone" value="0912 345 6789" class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                        <span class="text-xs text-brand-500 mt-1 inline-flex items-center gap-1">
                            <svg class="w-3 h-3 text-gold-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" clip-rule="evenodd"/>
                            </svg>
                            OCR extracted
                        </span>
                    </div>
                    <div class="bg-gradient-to-br from-white to-brand-50/30 p-4 rounded-xl border border-brand-100">
                        <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">
                            <span class="text-red-500">*</span> Birth Date
                        </label>
                        <input type="text" id="birthdate" value="1995-08-14" class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                        <span class="text-xs text-brand-500 mt-1 inline-flex items-center gap-1">
                            <svg class="w-3 h-3 text-gold-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" clip-rule="evenodd"/>
                            </svg>
                            OCR extracted
                        </span>
                    </div>
                    <div class="md:col-span-2 bg-gradient-to-br from-white to-brand-50/30 p-4 rounded-xl border border-brand-100">
                        <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">
                            <span class="text-red-500">*</span> Address
                        </label>
                        <input type="text" id="address" value="Unit 8, Green Residences, Manila" class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                        <span class="text-xs text-brand-500 mt-1 inline-flex items-center gap-1">
                            <svg class="w-3 h-3 text-gold-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" clip-rule="evenodd"/>
                            </svg>
                            OCR extracted
                        </span>
                    </div>
                    <div class="md:col-span-2 bg-gradient-to-br from-white to-brand-50/30 p-4 rounded-xl border border-brand-100">
                        <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">
                            <span class="text-red-500">*</span> Highest Education
                        </label>
                        <input type="text" id="education" value="Bachelor of Science in Information Technology" class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                        <span class="text-xs text-brand-500 mt-1 inline-flex items-center gap-1">
                            <svg class="w-3 h-3 text-gold-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" clip-rule="evenodd"/>
                            </svg>
                            OCR extracted
                        </span>
                    </div>
                </div>

                <!-- Skills Section -->
                <div class="mt-6 p-5 rounded-xl border border-brand-100" style="background: linear-gradient(135deg, #fdf6ee 0%, #faf0e0 100%);">
                    <h4 class="text-sm font-bold text-brand-900 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Extracted Skills
                        <span class="ml-2 text-xs text-gold-500 font-normal bg-white/60 px-2 py-0.5 rounded-full">OCR detected</span>
                    </h4>
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-white text-brand-700 px-4 py-1.5 rounded-full text-sm border border-brand-200 shadow-sm flex items-center gap-1">
                            <svg class="w-3 h-3 text-gold-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            JavaScript
                        </span>
                        <span class="bg-white text-brand-700 px-4 py-1.5 rounded-full text-sm border border-brand-200 shadow-sm flex items-center gap-1">
                            <svg class="w-3 h-3 text-gold-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            SQL
                        </span>
                        <span class="bg-white text-brand-700 px-4 py-1.5 rounded-full text-sm border border-brand-200 shadow-sm flex items-center gap-1">
                            <svg class="w-3 h-3 text-gold-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Team Collaboration
                        </span>
                        <span class="bg-white text-brand-700 px-4 py-1.5 rounded-full text-sm border border-brand-200 shadow-sm flex items-center gap-1">
                            <svg class="w-3 h-3 text-gold-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Data Analysis
                        </span>
                        <span class="bg-white text-brand-700 px-4 py-1.5 rounded-full text-sm border border-brand-200 shadow-sm flex items-center gap-1">
                            <svg class="w-3 h-3 text-gold-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Communication
                        </span>
                        <span class="bg-white text-brand-700 px-4 py-1.5 rounded-full text-sm border border-brand-200 shadow-sm flex items-center gap-1">
                            <svg class="w-3 h-3 text-gold-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Time Management
                        </span>
                    </div>
                </div>

                <!-- Social Status -->
                <div class="mt-6 p-5 rounded-xl border border-brand-100 bg-white">
                    <label class="block text-sm font-bold text-brand-900 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Social Status
                    </label>
                    <div class="flex flex-wrap gap-4">
                        <label class="flex items-center gap-2 text-sm text-brand-700 cursor-pointer bg-brand-50 px-4 py-2 rounded-lg border border-brand-100 hover:bg-brand-100 transition">
                            <input type="checkbox" checked class="w-4 h-4 text-brand-600 rounded border-brand-300 focus:ring-brand-500" />
                            4Ps Beneficiary
                        </label>
                        <label class="flex items-center gap-2 text-sm text-brand-700 cursor-pointer bg-brand-50 px-4 py-2 rounded-lg border border-brand-100 hover:bg-brand-100 transition">
                            <input type="checkbox" class="w-4 h-4 text-brand-600 rounded border-brand-300 focus:ring-brand-500" />
                            OFW / Dependents
                        </label>
                        <label class="flex items-center gap-2 text-sm text-brand-700 cursor-pointer bg-brand-50 px-4 py-2 rounded-lg border border-brand-100 hover:bg-brand-100 transition">
                            <input type="checkbox" class="w-4 h-4 text-brand-600 rounded border-brand-300 focus:ring-brand-500" />
                            PWD
                        </label>
                    </div>
                </div>

                <!-- Job Preferences -->
                <div class="mt-6 p-5 rounded-xl border border-brand-100" style="background: linear-gradient(135deg, #f0f4f8 0%, #e8edf3 100%);">
                    <label class="block text-sm font-bold text-brand-900 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Job Preferences
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">Desired Occupation 1</label>
                            <input type="text" value="Software Developer" class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">Desired Occupation 2</label>
                            <input type="text" value="Web Developer" class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">Expected Salary (PHP)</label>
                            <input type="text" value="30,000 - 40,000" class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-brand-700 uppercase tracking-wider mb-1">Preferred Location</label>
                            <input type="text" value="Metro Manila" class="w-full px-4 py-2.5 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition outline-none" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="px-6 py-5 border-t border-brand-100" style="background: linear-gradient(135deg, #f5f7fa 0%, #e9edf3 100%);">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <button id="resetBtn" class="w-full sm:w-auto px-6 py-2.5 bg-white border border-brand-200 text-brand-700 font-semibold rounded-lg hover:bg-brand-50 transition flex items-center justify-center gap-2 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Reset
                    </button>
                    <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                        <button id="simulateBtn" class="w-full sm:w-auto px-6 py-2.5 bg-white border border-brand-200 text-brand-700 font-semibold rounded-lg hover:bg-brand-50 transition flex items-center justify-center gap-2 shadow-sm">
                            <svg class="w-4 h-4 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                            Simulate OCR
                        </button>
                        <button id="submitBtn" class="w-full sm:w-auto px-8 py-2.5 text-white font-semibold rounded-lg transition shadow-lg hover:shadow-xl flex items-center justify-center gap-2" style="background: linear-gradient(135deg, #1b2739 0%, #33455e 100%);">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Register
                        </button>
                    </div>
                </div>

                <!-- Status Message -->
                <div id="statusMessage" class="mt-4 p-3 bg-white border border-brand-200 rounded-lg text-sm text-brand-600 flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Ready · Upload a resume to auto-fill
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 flex flex-wrap justify-center gap-6 text-xs text-brand-400 border-t border-brand-100 pt-6">
            <span class="flex items-center gap-1 bg-white px-3 py-1.5 rounded-full border border-brand-100 shadow-sm">
                <svg class="w-3 h-3 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
                Tesseract OCR · Auto-extract
            </span>
            <span class="flex items-center gap-1 bg-white px-3 py-1.5 rounded-full border border-brand-100 shadow-sm">
                <svg class="w-3 h-3 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                </svg>
                Frontend Demo · No DB yet
            </span>
            <span class="flex items-center gap-1 bg-white px-3 py-1.5 rounded-full border border-brand-100 shadow-sm">
                <svg class="w-3 h-3 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                DMDP Capstone Project
            </span>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // DOM elements
    const uploadBox = document.getElementById('uploadBox');
    const fileInput = document.getElementById('fileInput');
    const filePreview = document.getElementById('filePreview');
    const fileName = document.getElementById('fileName');
    const statusMsg = document.getElementById('statusMessage');
    const simulateBtn = document.getElementById('simulateBtn');
    const resetBtn = document.getElementById('resetBtn');
    const submitBtn = document.getElementById('submitBtn');
    const removeFile = document.getElementById('removeFile');

    // Simulate OCR extraction
    function simulateOCR(filename = 'resume_maria.pdf') {
        filePreview.classList.remove('hidden');
        fileName.textContent = filename;
        statusMsg.innerHTML = `
            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            OCR completed · All fields auto-filled with 94% confidence
        `;
        statusMsg.className = 'mt-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700 flex items-center gap-2 shadow-sm';
        
        // Populate fields with sample data
        document.getElementById('fullName').value = 'Maria Santos';
        document.getElementById('email').value = 'maria.santos@email.com';
        document.getElementById('phone').value = '0912 345 6789';
        document.getElementById('birthdate').value = '1995-08-14';
        document.getElementById('address').value = 'Unit 8, Green Residences, Manila';
        document.getElementById('education').value = 'Bachelor of Science in Information Technology';
    }

    // Reset form
    function resetForm() {
        filePreview.classList.add('hidden');
        document.getElementById('fullName').value = '';
        document.getElementById('email').value = '';
        document.getElementById('phone').value = '';
        document.getElementById('birthdate').value = '';
        document.getElementById('address').value = '';
        document.getElementById('education').value = '';
        statusMsg.innerHTML = `
            <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Form reset · Upload a resume to auto-fill
        `;
        statusMsg.className = 'mt-4 p-3 bg-white border border-brand-200 rounded-lg text-sm text-brand-600 flex items-center gap-2 shadow-sm';
    }

    // Upload box click
    uploadBox.addEventListener('click', () => {
        fileInput.click();
    });

    // File selection
    fileInput.addEventListener('change', function(e) {
        if (this.files && this.files.length > 0) {
            const file = this.files[0];
            statusMsg.innerHTML = `
                <svg class="w-4 h-4 animate-spin text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Processing ${file.name} with OCR...
            `;
            statusMsg.className = 'mt-4 p-3 bg-brand-50 border border-brand-200 rounded-lg text-sm text-brand-600 flex items-center gap-2 shadow-sm';
            setTimeout(() => {
                simulateOCR(file.name);
            }, 800);
        }
    });

    // Remove file
    removeFile.addEventListener('click', () => {
        fileInput.value = '';
        resetForm();
    });

    // Simulate button
    simulateBtn.addEventListener('click', () => {
        simulateOCR('resume_simulated.pdf');
    });

    // Reset button
    resetBtn.addEventListener('click', resetForm);

    // Submit button
    submitBtn.addEventListener('click', function() {
        const name = document.getElementById('fullName').value || 'N/A';
        statusMsg.innerHTML = `
            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            ✅ Registration submitted for ${name} (Frontend Demo)
        `;
        statusMsg.className = 'mt-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700 flex items-center gap-2 shadow-sm';
        this.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Processing...';
        setTimeout(() => {
            this.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Registered!';
            setTimeout(() => {
                this.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Register';
            }, 1500);
        }, 600);
    });

    // Drag and drop
    uploadBox.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadBox.className = 'relative border-2 border-dashed border-gold-400 rounded-xl p-8 text-center transition-all duration-300 cursor-pointer bg-brand-100/60 shadow-lg group';
    });
    uploadBox.addEventListener('dragleave', () => {
        uploadBox.className = 'relative border-2 border-dashed border-brand-200 rounded-xl p-8 text-center hover:border-gold-400 transition-all duration-300 cursor-pointer bg-gradient-to-b from-brand-50/30 to-white hover:from-brand-50/60 hover:shadow-lg group';
    });
    uploadBox.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadBox.className = 'relative border-2 border-dashed border-brand-200 rounded-xl p-8 text-center hover:border-gold-400 transition-all duration-300 cursor-pointer bg-gradient-to-b from-brand-50/30 to-white hover:from-brand-50/60 hover:shadow-lg group';
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            fileInput.dispatchEvent(new Event('change'));
        }
    });
</script>
@endpush
@endsection