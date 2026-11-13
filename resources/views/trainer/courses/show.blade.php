@extends('layouts.trainer')

@section('title', $course->title . ' - Course & Assessment Management')

@section('content')
<div x-data="{ 
    addQuestionModal: false, 
    editQuestionModal: false,
    deleteModal: false,
    deleteUrl: '',
    newQuestionType: 'multiple_choice',
    activeQuestion: {
        id: null,
        question: '',
        type: 'multiple_choice',
        options: ['', '', '', ''],
        correct_answer: 0,
        explanation: '',
        points: 1,
        image_url: ''
    },
    openEditModal(item) {
        this.activeQuestion = {
            id: item.id,
            question: item.question,
            options: item.options,
            type: item.type,
            correct_answer: item.correct_answer,
            explanation: item.explanation,
            points: item.points,
            image_url: item.image_url
        };
        this.editQuestionModal = true;
    }
}" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="w-full max-w-[1680px] mx-auto space-y-8">
        <!-- ⚠️ CERTIFICATE CONFIGURATION WARNING BANNER -->
        @if(empty($course->cert_content) || empty($course->cert_content['title']) || empty($course->cert_content['competencies']))
        <div class="rounded-3xl border-2 border-rose-300 bg-rose-50/90 p-6 sm:p-8 text-rose-950 shadow-md flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
            <div class="flex items-start gap-4">
                <div class="h-12 w-12 rounded-2xl bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-rose-600 text-white uppercase tracking-wider">
                            Action Required
                        </span>
                    </div>
                    <h3 class="text-lg font-black text-rose-950">Certificate Template Not Configured</h3>
                    <p class="text-sm text-rose-800 mt-1 max-w-2xl font-medium">
                        You haven't customized the certificate for this course yet. Students who pass the quiz will receive a blank or default certificate. Please configure the title and competencies before your students finish the course.
                    </p>
                </div>
            </div>

            <a href="{{ route('trainer.courses.certificate-content', $course->training_id) }}"
                class="shrink-0 inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-rose-600 hover:bg-rose-700 text-white text-sm font-black shadow-lg shadow-rose-600/30 transition-all hover:scale-105">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Configure Certificate Now
            </a>
        </div>
        @endif
        <!-- Breadcrumb & Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                <a href="{{ route('trainer.courses') }}" class="hover:text-emerald-700 transition-colors">Courses Catalog</a>
                <span>/</span>
                <span class="text-slate-900 font-bold truncate max-w-md">{{ $course->title }}</span>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('trainer.courses.certificate-content', $course->training_id) }}" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md shadow-indigo-600/20 transition-colors flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span>Edit Certificate Template</span>
                </a>
                <a href="{{ route('trainer.courses') }}" class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-colors">
                    &larr; Back to Catalog
                </a>
            </div>
        </div>

        <!-- Course Header Card -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-3">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-full bg-emerald-500/20 border border-emerald-400/30 px-3 py-0.5 text-xs font-bold text-emerald-300">
                            {{ $course->training_type === 'laboratory_onsite' ? 'Laboratory / Onsite Hands-on' : 'Online Track' }}
                        </span>
                        <span class="rounded-full bg-white/10 px-3 py-0.5 text-xs font-semibold text-slate-300">
                            {{ $course->training_start_date ? \Carbon\Carbon::parse($course->training_start_date)->format('M j, Y') : 'Date unset' }}
                            to {{ $course->training_end_date ? \Carbon\Carbon::parse($course->training_end_date)->format('M j, Y') : 'Date unset' }}
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight">{{ $course->title }}</h1>
                    <p class="text-xs text-slate-300 max-w-3xl leading-relaxed">{{ $course->description }}</p>

                    @if(!empty($course->skills))
                    <div class="pt-2 flex flex-wrap items-center gap-1.5">
                        <span class="text-[11px] font-bold text-emerald-400 uppercase tracking-wider mr-1">Target Skills:</span>
                        @foreach(array_map('trim', explode(',', $course->skills)) as $s)
                        @if(!empty($s))
                        <span class="rounded-lg bg-emerald-950/80 border border-emerald-700/50 px-2.5 py-0.5 text-xs text-emerald-200 font-medium">
                            {{ $s }}
                        </span>
                        @endif
                        @endforeach
                    </div>
                    @endif
                </div>

                <div class="flex flex-col sm:flex-row md:flex-col gap-3 shrink-0">
                    <div class="rounded-2xl bg-white/10 backdrop-blur-md border border-white/10 p-4 text-center">
                        <div class="text-[10px] font-bold text-slate-300 uppercase tracking-wider">Passing Score</div>
                        <div class="text-2xl font-black text-emerald-400">{{ $course->passing_score ?: 80 }}%</div>
                    </div>
                    <div class="rounded-2xl bg-white/10 backdrop-blur-md border border-white/10 p-4 text-center">
                        <div class="text-[10px] font-bold text-slate-300 uppercase tracking-wider">Trainees Enrolled</div>
                        <div class="text-2xl font-black text-white">{{ count($enrollments) }} / {{ $course->trainee_capacity ?: 'Unlimited' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Tabs / Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Left 2 Cols: Trainer Assessment Builder -->
            <div class="lg:col-span-2 space-y-6">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                        <div>
                            <div class="flex items-center gap-2">
                                <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <h2 class="text-lg font-black text-slate-900">Trainer-Created Assessment</h2>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">Author evaluation questions for this course.</p>
                        </div>

                        <button @click="addQuestionModal = true" type="button" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-extrabold shadow-md shadow-emerald-600/20 transition-all">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Add Question</span>
                        </button>
                    </div>

                    <!-- Questions List -->
                    <div class="space-y-4">
                        @forelse($assessments as $index => $item)
                        @php $choices = is_array($item->options) ? $item->options : json_decode($item->options ?? '[]', true); @endphp
                        <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-5 space-y-3 hover:border-emerald-300 transition-colors">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-start gap-2.5">
                                    <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-slate-900 text-white text-xs font-bold shrink-0">{{ $index + 1 }}</span>
                                    <h4 class="text-xs font-bold text-slate-900 leading-snug">{{ $item->question }}</h4>
                                </div>
                                <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-[10px] font-bold uppercase text-indigo-700">{{ $item->question_type === 'essay' ? 'Essay' : 'Multiple Choice' }}</span>

                                <div class="flex shrink-0 flex-col items-stretch gap-1.5 rounded-xl border border-slate-200 bg-white p-2">

                                    <!-- Edit Button (Clean JS Object) -->
                                    <button x-on:click="openEditModal({
                                        id: {{ $item->assessment_id }},
                                        question: @js($item->question),
                                        options: @js(count($choices) ? array_values($choices) : ['', '', '', '']),
                                        type: @js($item->question_type ?: 'multiple_choice'),
                                        correct_answer: {{ (int) $item->correct_answer }},
                                        explanation: @js($item->explanation ?? ''),
                                        points: {{ (int) ($item->points ?? 1) }},
                                        image_url: @js($item->image_path ? asset('storage/' . $item->image_path) : '')
                                    })" type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-2.5 py-1.5 text-xs font-bold text-slate-700 transition-colors hover:bg-slate-200">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                        <span>Edit</span>
                                    </button>

                                    <!-- Custom Delete Button -->
                                    <button type="button" @click="deleteUrl = '{{ route('trainer.courses.assessments.destroy', [$course->training_id, $item->assessment_id]) }}'; deleteModal = true;" class="inline-flex items-center gap-1.5 rounded-lg bg-rose-50 px-2.5 py-1.5 text-xs font-bold text-rose-700 transition-colors hover:bg-rose-100">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        <span>Delete</span>
                                    </button>
                                </div>
                            </div>
                            @if($item->image_path)
                            <img src="{{ asset('storage/' . $item->image_path) }}" alt="Assessment question illustration" class="ml-8 max-h-64 max-w-full rounded-xl border border-slate-200 object-contain">
                            @endif
                            @if($item->question_type === 'essay')
                            <p class="pl-8 text-xs font-semibold text-indigo-700">Essay response - trainer reviewed</p>
                            @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1 pl-8">
                                @foreach($choices as $cIndex => $choice)
                                <div class="rounded-xl px-3 py-2 text-xs flex items-center gap-2 border {{ (int)$item->correct_answer === $cIndex ? 'border-emerald-500 bg-emerald-50 text-emerald-950 font-bold' : 'border-slate-200 bg-white text-slate-700' }}">
                                    <span class="h-4 w-4 rounded-full flex items-center justify-center text-[10px] {{ (int)$item->correct_answer === $cIndex ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-100 text-slate-500' }}">{{ chr(65 + $cIndex) }}</span>
                                    <span class="truncate">{{ $choice }}</span>
                                </div>
                                @endforeach
                            </div>
                            @endif
                            @if(!empty($item->explanation))
                            <div class="pl-8 text-[11px] text-slate-500 italic"><span class="font-bold text-slate-600 not-italic">Rationale:</span> {{ $item->explanation }}</div>
                            @endif
                        </div>
                        @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center space-y-2">
                            <h3 class="text-xs font-bold text-slate-800">No assessment questions published yet</h3>
                            <p class="text-[11px] text-slate-500 max-w-md mx-auto">Click "Add Question" above to author evaluation questions.</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Enrolled Trainees Card -->
                <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <h3 class="text-base font-black text-slate-900">Enrolled Trainees</h3>
                            <p class="text-xs text-slate-500">Track jobseeker progress and review assessment scores.</p>
                        </div>
                        <span class="text-xs font-bold text-emerald-800 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200">{{ count($enrollments) }} Trainees</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-100">
                                <tr>
                                    <th class="py-3 px-3">Jobseeker</th>
                                    <th class="py-3 px-3">Status</th>
                                    <th class="py-3 px-3">Score</th>
                                    <th class="py-3 px-3">Certificate</th>
                                    <th class="py-3 px-3 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($enrollments as $enr)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="py-3 px-3">
                                        <div class="font-bold text-slate-900">{{ $enr->first_name }} {{ $enr->last_name }}</div>
                                        <div class="text-[11px] text-slate-400">{{ $enr->jobseeker_email }}</div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="rounded-full px-2.5 py-0.5 text-[10px] font-extrabold uppercase {{ $enr->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : ($enr->status === 'failed' ? 'bg-rose-100 text-rose-800' : 'bg-blue-100 text-blue-800') }}">{{ str_replace('_', ' ', $enr->status) }}</span>
                                    </td>
                                    <td class="py-3 px-3 font-semibold text-slate-700">{{ $enr->score !== null ? $enr->score . '%' : 'Pending' }}</td>
                                    <td class="py-3 px-3">
                                        @if($enr->certificate_issued)
                                        <span class="text-emerald-700 font-bold flex items-center gap-1 text-[11px]"><span>{{ $enr->certificate_no ?: 'Issued' }}</span></span>
                                        @else
                                        <span class="text-slate-400 text-[11px]">Not issued</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 text-right">
                                        @if(!$enr->certificate_issued && $enr->status === 'completed')
                                        <form action="{{ route('trainer.enrollments.certificate', $enr->enrollment_id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-[11px] font-bold shadow-sm">Issue Certificate</button>
                                        </form>
                                        @elseif($enr->certificate_issued)
                                        <a href="{{ route('trainer.certificates.preview', $enr->enrollment_id) }}" target="_blank" class="text-emerald-700 hover:underline text-[11px] font-bold">View Cert &rarr;</a>
                                        @else
                                        <a href="{{ route('trainer.enrollments.evaluate', $enr->enrollment_id) }}" class="text-slate-600 hover:text-emerald-700 text-[11px] font-bold">Evaluate &rarr;</a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-slate-400 text-xs">No jobseekers enrolled in this course yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Col: Syllabus / Topics -->
            <div class="space-y-6">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <h3 class="text-sm font-black text-slate-900">Curriculum Syllabus</h3>
                            <p class="text-[11px] text-slate-400">Course chapters and modules</p>
                        </div>
                        <span class="text-xs font-bold text-slate-600">{{ count($topics) }} Modules</span>
                    </div>
                    <div class="space-y-2.5">
                        @forelse($topics as $tIndex => $topic)
                        <div class="p-3 rounded-2xl border border-slate-200/80 bg-slate-50 space-y-1">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-slate-800">#{{ $topic->topic_order ?: ($tIndex + 1) }}. {{ $topic->title }}</span>
                            </div>
                        </div>
                        @empty
                        <div class="p-4 text-center text-slate-400 text-xs italic">No chapters added yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Question Modal -->
    <div x-show="addQuestionModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="addQuestionModal" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="addQuestionModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div x-show="addQuestionModal" x-transition class="relative inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-200">
                <form action="{{ route('trainer.courses.assessments.store', $course->training_id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="bg-slate-900 px-6 py-5 text-white flex items-center justify-between">
                        <h3 class="text-base font-bold text-white">Add Assessment Question</h3>
                        <button @click="addQuestionModal = false" type="button" class="text-slate-400 hover:text-white text-xl font-bold">&times;</button>
                    </div>

                    <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-700">Question Type</label>
                            <select name="question_type" x-model="newQuestionType" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-bold text-slate-800">
                                <option value="multiple_choice">Multiple Choice</option>
                                <option value="essay">Essay</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Question Prompt <span class="text-rose-500">*</span></label>
                            <textarea name="question" rows="3" required placeholder="State the question clearly..." class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-emerald-500 outline-none"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Question Image (Optional)</label>
                            <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-700">
                        </div>

                        <div x-show="newQuestionType === 'multiple_choice'" class="space-y-3">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Answer Choices & Correct Option <span class="text-rose-500">*</span></label>
                            <p class="text-[11px] text-slate-400">Select the radio button next to the correct answer choice.</p>

                            @for($i = 0; $i < 4; $i++)
                                <div class="flex items-center gap-3">
                                <input type="radio" name="correct_answer" value="{{ $i }}" {{ $i === 0 ? 'checked' : '' }}
                                    :required="newQuestionType === 'multiple_choice'"
                                    class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-slate-300">
                                <span class="text-xs font-bold text-slate-400 w-4">{{ chr(65 + $i) }}</span>
                                <input type="text" name="options[]" placeholder="Option {{ chr(65 + $i) }}"
                                    :required="newQuestionType === 'multiple_choice'"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs font-medium text-slate-800 focus:border-emerald-500 outline-none">
                        </div>
                        @endfor
                    </div>

                    <div x-show="newQuestionType === 'essay'" class="rounded-xl border border-indigo-200 bg-indigo-50 p-3 text-xs text-indigo-800">
                        Jobseekers will enter a written response. The trainer reviews and decides the final result.
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Explanation / Rationale (Optional)</label>
                        <textarea name="explanation" rows="2" placeholder="Explain why the correct answer is standard procedure..." class="w-full rounded-xl border border-slate-200 px-4 py-2 text-xs font-medium text-slate-800 focus:border-emerald-500 outline-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Points</label>
                        <input type="number" name="points" value="1" min="1" max="10" class="w-32 rounded-xl border border-slate-200 px-3 py-2 text-xs font-medium text-slate-800 focus:border-emerald-500 outline-none">
                    </div>
            </div>

            <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-3 rounded-b-3xl">
                <button @click="addQuestionModal = false" type="button" class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-extrabold shadow-md shadow-emerald-600/20 transition-all">
                    Save Question
                </button>
            </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Question Modal -->
<div x-show="editQuestionModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="editQuestionModal" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="editQuestionModal = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

        <div x-show="editQuestionModal" x-transition class="relative inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-200">
            <form :action="'/trainer/courses/{{ $course->training_id }}/assessments/' + activeQuestion.id" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="bg-slate-900 px-6 py-5 text-white flex items-center justify-between">
                    <h3 class="text-base font-bold text-white">Edit Assessment Question</h3>
                    <button @click="editQuestionModal = false" type="button" class="text-slate-400 hover:text-white text-xl font-bold">&times;</button>
                </div>

                <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Question Type</label>
                        <select name="question_type" x-model="activeQuestion.type" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-bold text-slate-800">
                            <option value="multiple_choice">Multiple Choice</option>
                            <option value="essay">Essay</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Question Prompt <span class="text-rose-500">*</span></label>
                        <textarea name="question" x-model="activeQuestion.question" rows="3" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-emerald-500 outline-none"></textarea>
                    </div>

                    <div>
                        <template x-if="activeQuestion.image_url">
                            <div class="mb-2 space-y-2">
                                <img :src="activeQuestion.image_url" alt="Current assessment question illustration" class="max-h-40 rounded-xl border border-slate-200 object-contain">
                                <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600">
                                    <input type="checkbox" name="remove_image" value="1" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                                    Remove current image
                                </label>
                            </div>
                        </template>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Replace Image (Optional)</label>
                        <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-700">
                    </div>

                    <div x-show="activeQuestion.type === 'multiple_choice'" class="space-y-3">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Answer Choices & Correct Option <span class="text-rose-500">*</span></label>

                        <template x-for="(opt, idx) in activeQuestion.options" :key="idx">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="correct_answer" :value="idx" :checked="activeQuestion.correct_answer == idx"
                                    :required="activeQuestion.type === 'multiple_choice'"
                                    class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-slate-300">
                                <span class="text-xs font-bold text-slate-400 w-4" x-text="String.fromCharCode(65 + idx)"></span>
                                <input type="text" name="options[]" x-model="activeQuestion.options[idx]"
                                    :required="activeQuestion.type === 'multiple_choice'"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs font-medium text-slate-800 focus:border-emerald-500 outline-none">
                            </div>
                        </template>
                    </div>

                    <div x-show="activeQuestion.type === 'essay'" class="rounded-xl border border-indigo-200 bg-indigo-50 p-3 text-xs text-indigo-800">
                        Jobseekers will enter a written response. The trainer reviews and decides the final result.
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Explanation / Rationale (Optional)</label>
                        <textarea name="explanation" x-model="activeQuestion.explanation" rows="2" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-xs font-medium text-slate-800 focus:border-emerald-500 outline-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Points</label>
                        <input type="number" name="points" x-model="activeQuestion.points" min="1" max="10" class="w-32 rounded-xl border border-slate-200 px-3 py-2 text-xs font-medium text-slate-800 focus:border-emerald-500 outline-none">
                    </div>
                </div>

                <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-3 rounded-b-3xl">
                    <button @click="editQuestionModal = false" type="button" class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-extrabold shadow-md shadow-emerald-600/20 transition-all">
                        Update Question
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- CUSTOM DELETE WARNING MODAL -->
<div x-show="deleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">

        <!-- Background Overlay -->
        <div x-show="deleteModal" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="deleteModal = false"></div>

        <!-- Modal Content -->
        <div x-show="deleteModal" x-transition class="relative inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-rose-100">

            <div class="bg-rose-50 px-6 py-6 flex items-start gap-4">
                <div class="h-12 w-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-black text-rose-950">Delete Assessment Question?</h3>
                    <p class="text-sm text-rose-800 mt-1">
                        Are you absolutely sure you want to delete this question? <strong>This cannot be undone.</strong>
                    </p>
                </div>
            </div>

            <!-- Modal Actions -->
            <div class="bg-white px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button @click="deleteModal = false" type="button" class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-bold hover:bg-slate-50 transition-colors">
                    Cancel
                </button>

                <!-- The actual delete form -->
                <form :action="deleteUrl" method="POST" class="m-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-black shadow-lg shadow-rose-600/30 transition-all">
                        Yes, Delete
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

</div>
@endsection