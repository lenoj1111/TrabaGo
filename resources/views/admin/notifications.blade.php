@extends('layouts.admin')

@section('title', 'Notifications Center - TrabaGo Admin')

@section('content')
<div class="min-h-screen bg-slate-50/80 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-4xl space-y-8">
        
        <!-- Header -->
        <div class="rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-10 text-white shadow-xl border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-1">
                <div class="inline-flex items-center gap-2 rounded-full bg-emerald-400/20 px-3 py-1 text-xs font-bold text-emerald-300 border border-emerald-400/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Central Activity & Authorization Alerts
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Admin Notifications</h1>
                <p class="text-xs text-slate-300">Live operational alerts for pending authorizations, accreditations, and system updates.</p>
            </div>
            
            <div class="flex items-center gap-3">
                @if($unreadCount > 0)
                    <form action="{{ route('admin.notifications.read_all') }}" method="POST">
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

        <!-- Notifications Container -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-2">
                    <h2 class="text-sm font-black text-slate-900">Recent Alerts</h2>
                    @if($unreadCount > 0)
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-700 border border-rose-200">
                            {{ $unreadCount }} Unread
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">
                            All Caught Up
                        </span>
                    @endif
                </div>
                <span class="text-xs text-slate-400 font-medium">Page {{ $notifications->currentPage() }} of {{ $notifications->lastPage() ?: 1 }}</span>
            </div>

            <!-- Notifications List -->
            <div class="divide-y divide-slate-100">
                @forelse($notifications as $notif)
                    @php
                        $badgeBg = 'bg-slate-100 text-slate-700 border-slate-200';
                        $badgeIcon = '🔔';
                        $badgeLabel = 'System Alert';
                        $actionUrl = null;
                        $actionText = 'View Details';

                        if ($notif->type === 'job_approval') {
                            $badgeBg = 'bg-amber-50 text-amber-800 border-amber-200';
                            $badgeIcon = '💼';
                            $badgeLabel = 'Job Authorization';
                            $actionUrl = route('admin.approvals.index');
                            $actionText = 'Review Postings';
                        } elseif ($notif->type === 'accreditation') {
                            $badgeBg = 'bg-purple-50 text-purple-800 border-purple-200';
                            $badgeIcon = '🏛️';
                            $badgeLabel = 'Accreditation';
                            $actionUrl = route('admin.approvals.index');
                            $actionText = 'Review Accreditation';
                        } elseif ($notif->type === 'placement_report') {
                            $badgeBg = 'bg-teal-50 text-teal-800 border-teal-200';
                            $badgeIcon = '📊';
                            $badgeLabel = 'Placement Report';
                            $actionUrl = route('admin.approvals.index');
                            $actionText = 'Review Report';
                        } elseif ($notif->type === 'user_approval' || $notif->type === 'staff') {
                            $badgeBg = 'bg-blue-50 text-blue-800 border-blue-200';
                            $badgeIcon = '👥';
                            $badgeLabel = 'Staff Account';
                            $actionUrl = route('admin.users.index');
                            $actionText = 'Manage Accounts';
                        }
                    @endphp

                    <div class="py-4.5 flex flex-col sm:flex-row sm:items-start justify-between gap-4 transition-colors {{ $notif->is_read ? 'opacity-60 bg-slate-50/50 rounded-2xl px-4 my-1.5' : 'bg-emerald-50/20 rounded-2xl px-4 my-1.5 border border-emerald-100/60' }}">
                        <div class="space-y-1.5 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[10px] font-extrabold border {{ $badgeBg }}">
                                    <span>{{ $badgeIcon }}</span>
                                    {{ $badgeLabel }}
                                </span>
                                @if(!$notif->is_read)
                                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                @endif
                                <span class="text-[11px] text-slate-400">
                                    {{ date('M d, Y • h:i A', strtotime($notif->created_at)) }}
                                </span>
                            </div>

                            <h3 class="text-sm font-bold text-slate-900 {{ !$notif->is_read ? 'font-black' : '' }}">
                                {{ $notif->title }}
                            </h3>
                            <p class="text-xs text-slate-600 leading-relaxed">
                                {{ $notif->message }}
                            </p>
                        </div>

                        <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                            @if($actionUrl)
                                <a href="{{ $actionUrl }}" 
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-emerald-600 text-white text-xs font-bold transition-all shadow-sm">
                                    {{ $actionText }} &rarr;
                                </a>
                            @endif

                            @if(!$notif->is_read)
                                <form action="{{ route('admin.notifications.read', $notif->notification_id) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold border border-emerald-200 transition-colors"
                                            title="Mark as read">
                                        ✓ Mark Read
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-16 text-center space-y-3">
                        <div class="mx-auto h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                            ✨
                        </div>
                        <h4 class="text-sm font-bold text-slate-800">No Notifications Available</h4>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto">You're completely up to date. System notifications and authorization alerts will show up here as they occur.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($notifications->hasPages())
                <div class="pt-4 border-t border-slate-100 flex justify-center">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
