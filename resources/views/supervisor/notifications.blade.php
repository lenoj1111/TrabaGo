@extends('layouts.supervisor')

@section('title', 'Supervisor Notifications - PESD Management')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-4xl space-y-8">
        
        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-1">
                <div class="inline-flex items-center gap-2 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Alerts & Governance Activity
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Supervisor Notifications</h1>
                <p class="text-xs text-slate-300">Live operational alerts for JPO endorsements, employer accreditation reviews, and workflow oversight.</p>
            </div>
            
            <div class="flex items-center gap-3">
                @if(isset($unreadCount) && $unreadCount > 0)
                    <form action="{{ route('supervisor.notifications.read_all') }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold border border-white/20 backdrop-blur-sm transition-all">
                            <span>✓</span> Mark All as Read
                        </button>
                    </form>
                @endif
                <div class="h-12 w-12 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-400 flex items-center justify-center text-white text-xl font-black shadow-lg shadow-emerald-600/30">
                    🔔
                </div>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-2">
                    <h2 class="text-sm font-black text-slate-900">Recent Alerts</h2>
                    @if(isset($unreadCount) && $unreadCount > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800">
                            {{ $unreadCount }} Unread
                        </span>
                    @endif
                </div>
                <span class="text-xs text-slate-400 font-medium">Page {{ $notifications->currentPage() }} of {{ $notifications->lastPage() ?: 1 }}</span>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($notifications as $notif)
                    <div class="py-4 flex items-start justify-between gap-4 {{ $notif->is_read ? 'opacity-75' : 'bg-emerald-50/30 rounded-2xl p-4 my-1 border border-emerald-100/60' }}">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                @if(!$notif->is_read)
                                    <span class="h-2 w-2 rounded-full bg-emerald-500 shrink-0"></span>
                                @endif
                                <h4 class="text-sm font-bold text-slate-900">{{ $notif->title }}</h4>
                            </div>
                            <p class="text-xs text-slate-600 {{ !$notif->is_read ? 'pl-4' : '' }}">{{ $notif->message }}</p>
                            <span class="text-[10px] text-slate-400 block {{ !$notif->is_read ? 'pl-4' : '' }}">{{ date('M d, Y h:i A', strtotime($notif->created_at)) }}</span>
                        </div>

                        @if(!$notif->is_read)
                            <form action="{{ route('supervisor.notifications.read', $notif->notification_id) }}" method="POST" class="shrink-0">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-100 text-emerald-800 text-xs font-bold hover:bg-emerald-200 transition-colors shadow-sm">
                                    Mark Read
                                </button>
                            </form>
                        @else
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider shrink-0 px-2 py-1 bg-slate-100 rounded-lg">Read</span>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-12 space-y-3">
                        <div class="h-12 w-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto text-xl">📭</div>
                        <h4 class="text-sm font-bold text-slate-800">No Notifications Available</h4>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto">You're completely up to date. Endorsement requests and accreditation review notifications will appear here.</p>
                    </div>
                @endforelse
            </div>

            @if($notifications->hasPages())
                <div class="pt-4 border-t border-slate-100">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
