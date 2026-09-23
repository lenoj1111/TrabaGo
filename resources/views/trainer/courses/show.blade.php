@extends('layouts.trainer')

@section('title', $course->title . ' - Course & Assessment Management')

@section('content')
<div x-data="{ 
    addQuestionModal: false, 
    editQuestionModal: false,
    activeQuestion: {
        id: null,
        question: '',
        options: ['', '', '', ''],
        correct_answer: 0,
        explanation: '',
        points: 1
    }
}" class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl space-y-8">
        
        <!-- Breadcrumb & Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                <a href="{{ route('trainer.courses') }}" class="hover:text-green-700 transition-colors">Courses Catalog</a>
                <span>/</span>
                <span class="text-slate-900 font-bold truncate max-w-md">{{ $course->title }}</span>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('trainer.courses') }}" class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-colors">
                    &larr; Back to Catalog
                </a>
            </div>
        </div>

        <!-- Course Header Card -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-green-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-green-500/20">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-3">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-full bg-green-500/20 border border-green-400/30 px-3 py-0.5 text-xs font-bold text-green-300">
                            {{ $course->training_type === 'laboratory_onsite' ? 'Laboratory / Onsite Hands-on' : 'Online Track' }}
                        </span>
                        <span class="rounded-full bg-white/10 px-3 py-0.5 text-xs font-semibold text-slate-300">
                            {{ $course->duration_months ?: 1 }} Month Track
                        </span>
                        @if($course->auto_generate_certificate ?? true)
                            <span class="rounded-full bg-green-400/20 border border-green-300/30 px-3 py-0.5 text-xs font-bold text-green-300">
                                🎓 Auto-Generate Certificate Enabled
                            </span>
                        @else
                            <span class="rounded-full bg-amber-400/20 border border-amber-300/30 px-3 py-0.5 text-xs font-bold text-amber-300">
                                📋 Manual Assessment & Certificate Approval
                            </span>
                        @endif
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight">{{ $course->title }}</h1>
                    <p class="text-xs text-slate-300 max-w-3xl leading-relaxed">{{ $course->description }}</p>

                    @if(!empty($course->skills))
                        <div class="pt-2 flex flex-wrap items-center gap-1.5">
                            <span class="text-[11px] font-bold text-green-400 uppercase tracking-wider mr-1">Target Skills:</span>
                            @foreach(array_map('trim', explode(',', $course->skills)) as $s)
                                @if(!empty($s))
                                    <span class="rounded-lg bg-green-950/80 border border-green-700/50 px-2.5 py-0.5 text-xs text-green-200 font-medium">
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
                        <div class="text-2xl font-black text-green-400">{{ $course->passing_score ?: 80 }}%</div>
                    </div>
                    <div class="rounded-2xl bg-white/10 backdrop-blur-md border border-white/10 p-4 text-center">
                        <div class="text-[10px] font-bold text-slate-300 uppercase tracking-wider">Trainees Enrolled</div>
                        <div class="text-2xl font-black text-white">{{ count($enrollments) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Tabs / Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left 2 Cols: Trainer Assessment Builder (Strictly Trainer-Authored) -->
            <div class="lg:col-span-2 space-y-6">
                
                <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xl">📝</span>
                                <h2 class="text-lg font-black text-slate-900">Trainer-Created Assessment</h2>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Author evaluation questions for this course. Questions are strictly created by you and are not built-in or automatically generated.
                            </p>
                        </div>

                        <button @click="addQuestionModal = true" type="button" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-2xl bg-green-600 hover:bg-green-500 text-white text-xs font-extrabold shadow-md shadow-green-600/20 transition-all">
                            <span>➕</span> Add Question
                        </button>
                    </div>

                    <!-- Questions List -->
                    <div class="space-y-4">
                        @forelse($assessments as $index => $item)
                            @php
                                $choices = is_array($item->options) ? $item->options : json_decode($item->options ?? '[]', true);
                            @endphp
                            <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-5 space-y-3 hover:border-green-300 transition-colors">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-start gap-2.5">
                                        <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-slate-900 text-white text-xs font-bold shrink-0">
                                            {{ $index + 1 }}
                                        </span>
                                        <h4 class="text-xs font-bold text-slate-900 leading-snug">{{ $item->question }}</h4>
                                    </div>

                                    <div class="flex items-center gap-1 shrink-0">
                                        <!-- Edit Question Button -->
                                        <button @click="activeQuestion = {
                                            id: {{ $item->assessment_id }},
                                            question: '{{ addslashes($item->question) }}',
                                            options: {{ json_encode($choices) }},
                                            correct_answer: {{ (int) $item->correct_answer }},
                                            explanation: '{{ addslashes($item->explanation ?? '') }}',
                                            points: {{ (int) ($item->points ?? 1) }}
                                        }; editQuestionModal = true;" type="button" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200/60 transition-colors" title="Edit Question">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </button>

                                        <!-- Delete Question -->
                                        <form action="{{ route('trainer.courses.assessments.destroy', [$course->training_id, $item->assessment_id]) }}" method="POST" onsubmit="return confirm('Delete this assessment question?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Delete Question">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Choices -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1 pl-8">
                                    @foreach($choices as $cIndex => $choice)
                                        <div class="rounded-xl px-3 py-2 text-xs flex items-center gap-2 border {{ (int)$item->correct_answer === $cIndex ? 'border-green-500 bg-green-50 text-green-950 font-bold' : 'border-slate-200 bg-white text-slate-700' }}">
                                            <span class="h-4 w-4 rounded-full flex items-center justify-center text-[10px] {{ (int)$item->correct_answer === $cIndex ? 'bg-green-600 text-white font-bold' : 'bg-slate-100 text-slate-500' }}">
                                                {{ chr(65 + $cIndex) }}
                                            </span>
                                            <span class="truncate">{{ $choice }}</span>
                                            @if((int)$item->correct_answer === $cIndex)
                                                <span class="ml-auto text-[10px] text-green-700 uppercase font-black tracking-wider">Correct</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                                @if(!empty($item->explanation))
                                    <div class="pl-8 text-[11px] text-slate-500 italic">
                                        <span class="font-bold text-slate-600 not-italic">Rationale:</span> {{ $item->explanation }}
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center space-y-2">
                                <div class="text-3xl">✏️</div>
                                <h3 class="text-xs font-bold text-slate-800">No assessment questions published yet</h3>
                                <p class="text-[11px] text-slate-500 max-w-md mx-auto">
                                    Assessments are not automatically generated. Click "Add Question" above to author multiple-choice evaluation questions for your trainees.
                                </p>
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
                        <span class="text-xs font-bold text-green-800 bg-green-50 px-3 py-1 rounded-full border border-green-200">
                            {{ count($enrollments) }} Trainees
                        </span>
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
                                            <span class="rounded-full px-2.5 py-0.5 text-[10px] font-extrabold uppercase {{ $enr->status === 'completed' ? 'bg-green-100 text-green-800' : ($enr->status === 'failed' ? 'bg-rose-100 text-rose-800' : 'bg-blue-100 text-blue-800') }}">
                                                {{ str_replace('_', ' ', $enr->status) }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-3 font-semibold text-slate-700">
                                            {{ $enr->score !== null ? $enr->score . '%' : 'Pending' }}
                                        </td>
                                        <td class="py-3 px-3">
                                            @if($enr->certificate_issued)
                                                <span class="text-green-700 font-bold flex items-center gap-1 text-[11px]">
                                                    <span>🎓</span> {{ $enr->certificate_no ?: 'Issued' }}
                                                </span>
                                            @else
                                                <span class="text-slate-400 text-[11px]">Not issued</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-3 text-right">
                                            @if(!$enr->certificate_issued && $enr->status === 'completed')
                                                <form action="{{ route('trainer.enrollments.certificate', $enr->enrollment_id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-2.5 py-1 rounded-lg bg-green-600 hover:bg-green-500 text-white text-[11px] font-bold shadow-sm">
                                                        Issue Certificate
                                                    </button>
                                                </form>
                                            @elseif($enr->certificate_issued)
                                                <a href="{{ route('trainer.certificates.preview', $enr->enrollment_id) }}" target="_blank" class="text-green-700 hover:underline text-[11px] font-bold">
                                                    View Cert &rarr;
                                                </a>
                                            @else
                                                <a href="{{ route('trainer.enrollments.evaluate', $enr->enrollment_id) }}" class="text-slate-600 hover:text-green-700 text-[11px] font-bold">
                                                    Evaluate &rarr;
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-6 text-center text-slate-400 text-xs">
                                            No jobseekers enrolled in this course yet.
                                        </td>
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
                                @if(!empty($topic->video_url))
                                    <div class="text-[10px] text-green-700 truncate">
                                        🔗 {{ $topic->video_url }}
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="p-4 text-center text-slate-400 text-xs italic">
                                No chapters added yet.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Course Configuration Card -->
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                    <h3 class="text-sm font-black text-slate-900 pb-3 border-b border-slate-100">Certification Settings</h3>
                    
                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <span class="text-slate-400">Issuance Rule:</span>
                            <span class="font-bold {{ ($course->auto_generate_certificate ?? true) ? 'text-green-700' : 'text-amber-700' }}">
                                {{ ($course->auto_generate_certificate ?? true) ? 'Automatic upon passing' : 'Manual Trainer Assessment' }}
                            </span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <span class="text-slate-400">Passing Score:</span>
                            <span class="font-bold text-slate-800">{{ $course->passing_score ?: 80 }}%</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <span class="text-slate-400">Track Type:</span>
                            <span class="font-bold text-slate-800">{{ $course->training_type }}</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span class="text-slate-400">Duration:</span>
                            <span class="font-bold text-slate-800">{{ $course->duration_months ?: 1 }} Months</span>
                        </div>
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
                <form action="{{ route('trainer.courses.assessments.store', $course->training_id) }}" method="POST">
                    @csrf
                    <div class="bg-gradient-to-r from-slate-950 via-green-950 to-slate-900 px-6 py-5 text-white flex items-center justify-between border-b border-green-500/20">
                        <div class="flex items-center gap-3">
                            <span class="text-xl">➕</span>
                            <div>
                                <h3 class="text-base font-bold text-white">Add Assessment Question</h3>
                                <p class="text-xs text-green-300/80">Trainer-authored question for trainee evaluation.</p>
                            </div>
                        </div>
                        <button @click="addQuestionModal = false" type="button" class="text-slate-400 hover:text-white text-xl font-bold">&times;</button>
                    </div>

                    <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                        <!-- Question Prompt -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Question Prompt <span class="text-rose-500">*</span></label>
                            <textarea name="question" rows="3" required placeholder="State the question clearly..." class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-green-500 outline-none"></textarea>
                        </div>

                        <!-- Choices -->
                        <div class="space-y-3">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Answer Choices & Correct Option <span class="text-rose-500">*</span></label>
                            <p class="text-[11px] text-slate-400">Select the radio button next to the correct answer choice.</p>

                            @for($i = 0; $i < 4; $i++)
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="correct_answer" value="{{ $i }}" {{ $i === 0 ? 'checked' : '' }} class="h-4 w-4 text-green-600 focus:ring-green-500 border-slate-300" title="Mark as correct answer">
                                    <span class="text-xs font-bold text-slate-400 w-4">{{ chr(65 + $i) }}</span>
                                    <input type="text" name="options[]" required placeholder="Option {{ chr(65 + $i) }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs font-medium text-slate-800 focus:border-green-500 outline-none">
                                </div>
                            @endfor
                        </div>

                        <!-- Explanation -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Explanation / Rationale (Optional)</label>
                            <textarea name="explanation" rows="2" placeholder="Explain why the correct answer is standard procedure..." class="w-full rounded-xl border border-slate-200 px-4 py-2 text-xs font-medium text-slate-800 focus:border-green-500 outline-none"></textarea>
                        </div>

                        <!-- Points -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Points</label>
                            <input type="number" name="points" value="1" min="1" max="10" class="w-32 rounded-xl border border-slate-200 px-3 py-2 text-xs font-medium text-slate-800 focus:border-green-500 outline-none">
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-3 rounded-b-3xl">
                        <button @click="addQuestionModal = false" type="button" class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-green-600 hover:bg-green-500 text-white text-xs font-extrabold shadow-md shadow-green-600/20 transition-all">
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
                <form :action="'/trainer/courses/{{ $course->training_id }}/assessments/' + activeQuestion.id" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="bg-slate-900 px-6 py-5 text-white flex items-center justify-between border-b border-slate-800">
                        <div class="flex items-center gap-3">
                            <span class="text-xl">✏️</span>
                            <div>
                                <h3 class="text-base font-bold text-white">Edit Assessment Question</h3>
                                <p class="text-xs text-slate-400">Modify prompt, options, or correct answer.</p>
                            </div>
                        </div>
                        <button @click="editQuestionModal = false" type="button" class="text-slate-400 hover:text-white text-xl font-bold">&times;</button>
                    </div>

                    <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Question Prompt <span class="text-rose-500">*</span></label>
                            <textarea name="question" x-model="activeQuestion.question" rows="3" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-medium text-slate-800 focus:border-green-500 outline-none"></textarea>
                        </div>

                        <div class="space-y-3">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Answer Choices & Correct Option <span class="text-rose-500">*</span></label>
                            
                            <template x-for="(opt, idx) in activeQuestion.options" :key="idx">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="correct_answer" :value="idx" :checked="activeQuestion.correct_answer == idx" class="h-4 w-4 text-green-600 focus:ring-green-500 border-slate-300">
                                    <span class="text-xs font-bold text-slate-400 w-4" x-text="String.fromCharCode(65 + idx)"></span>
                                    <input type="text" name="options[]" x-model="activeQuestion.options[idx]" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs font-medium text-slate-800 focus:border-green-500 outline-none">
                                </div>
                            </template>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Explanation / Rationale</label>
                            <textarea name="explanation" x-model="activeQuestion.explanation" rows="2" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-xs font-medium text-slate-800 focus:border-green-500 outline-none"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Points</label>
                            <input type="number" name="points" x-model="activeQuestion.points" min="1" max="10" class="w-32 rounded-xl border border-slate-200 px-3 py-2 text-xs font-medium text-slate-800 focus:border-green-500 outline-none">
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-3 rounded-b-3xl">
                        <button @click="editQuestionModal = false" type="button" class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-green-600 hover:bg-green-500 text-white text-xs font-bold shadow-md shadow-green-600/20 transition-all">
                            Update Question
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
