<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>@yield('title', 'Employer Dashboard') - TrabaGo</title>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
	@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-brand-50/50 text-brand-900 antialiased">
	<header class="sticky top-0 z-40 border-b border-brand-100 bg-white/95 backdrop-blur">
		<div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-5">
			<a href="{{ route('employer.home') }}" class="flex items-center gap-3">
				<span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gold-500 shadow-lg shadow-gold-500/20">
					<svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
						<path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
					</svg>
				</span>
				<span class="text-xl font-extrabold tracking-tight"><span class="text-gold-500">Traba</span><span class="text-brand-900">Go</span></span>
			</a>

			<nav class="hidden items-center gap-1 md:flex">
				<a href="{{ route('employer.home') }}" class="rounded-lg bg-brand-50 px-4 py-2 text-sm font-semibold text-brand-900">Overview</a>
				<a href="{{ route('employer.job-postings') }}" class="rounded-lg px-4 py-2 text-sm font-semibold text-brand-500 transition hover:bg-brand-50 hover:text-brand-900">Job postings</a>
				<a href="{{ route('employer.applications') }}" class="rounded-lg px-4 py-2 text-sm font-semibold text-brand-500 transition hover:bg-brand-50 hover:text-brand-900">Applications</a>
				<a href="{{ route('employer.accreditation') }}" class="rounded-lg px-4 py-2 text-sm font-semibold text-brand-500 transition hover:bg-brand-50 hover:text-brand-900">Accreditation</a>
			</nav>

			<div class="flex items-center gap-3">
				<a href="{{ route('employer.profile') }}" class="hidden text-right sm:block">
					<span class="block text-xs font-bold uppercase tracking-wider text-brand-400">Employer portal</span>
					<span class="block text-sm font-semibold text-brand-900">Company profile</span>
				</a>
				<form method="POST" action="{{ route('logout') }}">
					@csrf
					<button type="submit" class="rounded-lg border border-brand-200 px-3 py-2 text-sm font-semibold text-brand-700 transition hover:border-brand-400 hover:bg-brand-50">Log out</button>
				</form>
			</div>
		</div>
	</header>

	<main>
		@yield('content')
	</main>
</body>
</html>
