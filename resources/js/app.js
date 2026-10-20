import Alpine from 'alpinejs';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

if (import.meta.env.VITE_REVERB_APP_KEY) {
    try {
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: import.meta.env.VITE_REVERB_APP_KEY,
            wsHost: import.meta.env.VITE_REVERB_HOST,
            wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
            wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
            forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
            enabledTransports: ['ws', 'wss'],
        });
    } catch (e) {
        // Graceful fallback if Reverb is offline
    }
}

// =========================================================================
// INSTANT SIDEBAR & NAVIGATION PREFETCHING
// Prefetches page HTML on mouse hover (pointerenter) or touch start,
// eliminating navigation latency completely before the user clicks.
// =========================================================================
const prefetchedUrls = new Set();

function shouldPrefetch(url) {
    if (!url || prefetchedUrls.has(url)) return false;
    try {
        const parsed = new URL(url, window.location.origin);
        if (parsed.origin !== window.location.origin) return false;
        if (parsed.pathname === window.location.pathname) return false;
        // Don't prefetch action, logout, or state-mutating links
        if (parsed.pathname.includes('logout') || parsed.pathname.includes('delete') || parsed.pathname.includes('download')) return false;
        return true;
    } catch (e) {
        return false;
    }
}

function prefetchLink(url) {
    if (!shouldPrefetch(url)) return;
    prefetchedUrls.add(url);

    const link = document.createElement('link');
    link.rel = 'prefetch';
    link.href = url;
    link.as = 'document';
    document.head.appendChild(link);
}

document.addEventListener('pointerenter', (e) => {
    const anchor = e.target.closest('a');
    if (anchor && anchor.href) {
        prefetchLink(anchor.href);
    }
}, { capture: true, passive: true });

document.addEventListener('touchstart', (e) => {
    const anchor = e.target.closest('a');
    if (anchor && anchor.href) {
        prefetchLink(anchor.href);
    }
}, { capture: true, passive: true });

// Instant tactile feedback on sidebar link click
document.addEventListener('click', (e) => {
    const anchor = e.target.closest('aside a, nav a');
    if (anchor && anchor.href && shouldPrefetch(anchor.href)) {
        anchor.classList.add('opacity-75');
    }
}, { capture: true, passive: true });

window.Alpine = Alpine;
Alpine.start();
