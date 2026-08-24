@extends('layouts.jobseeker')

@section('title', 'Notifications - TrabaGo')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-4xl space-y-8">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-800 border border-emerald-200">
                    <span class="h-2 w-2 rounded-full bg-emerald-600"></span>
                    Activity & Updates
                </span>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight mt-1">Notifications Center</h1>
                <p class="text-sm text-slate-500">Real-time alerts for applications, interview schedules, and training certificates.</p>
            </div>

            @if($unreadCount > 0)
                <form action="{{ route('jobseeker.notifications.read_all') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 px-4 py-2.5 text-xs font-bold text-slate-700 shadow-xs transition-colors">
                        <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Mark All as Read
                    </button>
                </form>
            @endif
        </div>

        <!-- Filter Category Tabs -->
        <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-4">
            <a href="{{ route('jobseeker.notifications') }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $filter === 'all' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                All Alerts
            </a>
            <a href="{{ route('jobseeker.notifications', ['category' => 'application']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $filter === 'application' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                Applications
            </a>
            <a href="{{ route('jobseeker.notifications', ['category' => 'training']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $filter === 'training' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                Training & Certs
            </a>
            <a href="{{ route('jobseeker.notifications', ['category' => 'interview']) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $filter === 'interview' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                Interviews
            </a>
        </div>

        <!-- Notifications Stream -->
        <div class="space-y-3">
            @forelse($notifications as $notif)
                <div class="rounded-2xl border p-5 shadow-sm transition-all flex items-start justify-between gap-4 {{ $notif->is_read ? 'bg-white border-slate-200' : 'bg-emerald-50/40 border-emerald-300 ring-1 ring-emerald-200' }}">
                    <div class="flex items-start gap-4">
                        <div class="h-10 w-10 rounded-xl flex items-center justify-center text-lg shrink-0 {{ $notif->is_read ? 'bg-slate-100 text-slate-600' : 'bg-emerald-100 text-emerald-800' }}">
                            @if(str_contains(strtolower($notif->title), 'interview'))
                                📅
                            @elseif(str_contains(strtolower($notif->title), 'training') || str_contains(strtolower($notif->title), 'skill'))
                                🎓
                            @elseif(str_contains(strtolower($notif->title), 'hired') || str_contains(strtolower($notif->title), 'accepted'))
                                🏆
                            @else
                                💼
                            @endif
                        </div>

                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-bold text-slate-900">{{ $notif->title }}</h3>
                                @if(!$notif->is_read)
                                    <span class="h-2 w-2 rounded-full bg-emerald-600"></span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-600 leading-relaxed">{{ $notif->message }}</p>
                            <p class="text-[10px] text-slate-400 font-medium">
                                {{ $notif->created_at ? \Carbon\Carbon::parse($notif->created_at)->diffForHumans() : 'Just now' }}
                            </p>
                        </div>
                    </div>

                    @if(!$notif->is_read)
                        <form action="{{ route('jobseeker.notifications.read', $notif->notification_id) }}" method="POST" class="shrink-0">
                            @csrf
                            <button type="submit" class="p-2 rounded-xl text-slate-400 hover:text-emerald-700 hover:bg-emerald-50 transition-colors" title="Mark as read">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-400">
                    No notifications in this category.
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($notifications->hasPages())
            <div class="pt-4 border-t border-slate-200">
                {{ $notifications->links() }}
            </div>
        @endif

    </div>
</div>
@endsection
