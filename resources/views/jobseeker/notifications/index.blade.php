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
                        <div class="h-10 w-10 rounded-xl flex items-center justify-center shrink-0 {{ $notif->is_read ? 'bg-slate-100 text-slate-600' : 'bg-emerald-100 text-emerald-800' }}">
                            @if(str_contains(strtolower($notif->title), 'interview'))
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                            @elseif(str_contains(strtolower($notif->title), 'training') || str_contains(strtolower($notif->title), 'skill'))
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-5.25 6.557q0 .148.01.295"/></svg>
                            @elseif(str_contains(strtolower($notif->title), 'hired') || str_contains(strtolower($notif->title), 'accepted'))
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.504-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.004 0H9.496m5.004 0a3 3 0 0 0 2.25-2.906l.75-5.25a2.25 2.25 0 0 0-2.25-2.569h-6.5a2.25 2.25 0 0 0-2.25 2.569l.75 5.25a3 3 0 0 0 2.25 2.906"/></svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z"/></svg>
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
