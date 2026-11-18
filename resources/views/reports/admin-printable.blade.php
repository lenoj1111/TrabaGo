<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} | TrabaGo DMDP</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; padding: 0 !important; }
            .report-sheet { max-width: none !important; border: 0 !important; box-shadow: none !important; padding: 0 !important; }
            thead { display: table-header-group; }
            tr { break-inside: avoid; }
            @page { size: A4 landscape; margin: 12mm; }
        }
    </style>
</head>
<body class="min-h-screen bg-slate-100 p-4 text-slate-900 sm:p-8">
    <main class="mx-auto max-w-7xl">
        <div class="no-print mb-5 flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('admin.reports') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700">Back to Platform Reports</a>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.reports.print', array_merge(request()->query(), ['export' => 1])) }}" class="rounded-lg border border-emerald-700 bg-white px-4 py-2 text-sm font-bold text-emerald-800">Download CSV</a>
                <button type="button" onclick="window.print()" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-bold text-white">Print / Save PDF</button>
            </div>
        </div>

        <section class="report-sheet border border-slate-300 bg-white p-6 shadow-sm sm:p-10">
            <header class="border-b-2 border-slate-900 pb-5 text-center">
                <div class="mb-3 flex items-center justify-center gap-5">
                    <img src="{{ asset('images/cebu.png') }}" alt="Cebu City Government" class="h-14 w-14 object-contain">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-600">Republic of the Philippines | Cebu City Government</p>
                        <h1 class="mt-1 text-base font-black uppercase">Department of Manpower Development and Placement</h1>
                        <p class="text-xs font-semibold text-emerald-800">TrabaGo Platform Administrative Report</p>
                    </div>
                    <img src="{{ asset('images/dmdp.png') }}" alt="DMDP" class="h-14 w-14 object-contain">
                </div>
                <h2 class="text-xl font-black uppercase">{{ $title }}</h2>
                <p class="mt-1 text-xs text-slate-600">Generated {{ $generatedAt->format('F d, Y h:i A') }} | Aggregate administrative use</p>
            </header>

            <form method="GET" action="{{ route('admin.reports.print') }}" class="no-print my-5 grid grid-cols-1 items-end gap-3 rounded-lg border border-slate-200 bg-slate-50 p-4 sm:grid-cols-2 lg:grid-cols-5">
                <input type="hidden" name="type" value="{{ $type }}">
                <label class="text-xs font-semibold text-slate-700">From
                    <input type="date" name="from" value="{{ $filters['from'] }}" class="mt-1 block w-full rounded-md border-slate-300 text-sm">
                </label>
                <label class="text-xs font-semibold text-slate-700">To
                    <input type="date" name="to" value="{{ $filters['to'] }}" class="mt-1 block w-full rounded-md border-slate-300 text-sm">
                </label>
                @if(in_array($type, ['jobseekers', 'hired', 'specialized-skills', 'barangay-skills'], true))
                    <label class="text-xs font-semibold text-slate-700">Barangay
                        <select name="barangay" class="mt-1 block w-full rounded-md border-slate-300 text-sm">
                            <option value="" @selected(!$filters['barangayFilter'])>All barangays</option>
                            @foreach($barangayNames as $barangayName)
                                <option value="{{ $barangayName }}" @selected($filters['barangayFilter'] === $barangayName)>{{ $barangayName }}</option>
                            @endforeach
                            <option value="__unassigned__" @selected($filters['barangayFilter'] === '__unassigned__')>Unassigned / Not recorded</option>
                        </select>
                    </label>
                @endif
                @if(in_array($type, ['specialized-skills', 'barangay-skills'], true))
                    <label class="text-xs font-semibold text-slate-700">Skill category
                        <select name="skill_type" class="mt-1 block w-full rounded-md border-slate-300 text-sm">
                            <option value="all" @selected($filters['skillType'] === 'all')>All categories</option>
                            <option value="technical" @selected($filters['skillType'] === 'technical')>Technical</option>
                            <option value="21st_century" @selected($filters['skillType'] === '21st_century')>21st Century</option>
                        </select>
                    </label>
                @endif
                <button class="rounded-md bg-slate-900 px-4 py-2 text-sm font-bold text-white">Apply filters</button>
            </form>

            <div class="mb-3 flex items-center justify-between text-xs text-slate-600">
                <span>Records: <strong class="text-slate-900">{{ number_format($rows->count()) }}</strong></span>
                <span>Filters apply to the generated report dataset.</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left text-xs">
                    @if($type === 'jobseekers')
                        <thead><tr class="bg-slate-100"><th class="border border-slate-300 p-2">Jobseeker</th><th class="border border-slate-300 p-2">Barangay</th><th class="border border-slate-300 p-2">Employment status</th><th class="border border-slate-300 p-2">Skills</th><th class="border border-slate-300 p-2">Registered</th></tr></thead>
                        <tbody>@forelse($rows as $row)<tr><td class="border border-slate-300 p-2">{{ $row['name'] }}</td><td class="border border-slate-300 p-2">{{ $row['barangay'] }}</td><td class="border border-slate-300 p-2">{{ $row['employment_status'] }}</td><td class="border border-slate-300 p-2">{{ $row['skills'] }}</td><td class="border border-slate-300 p-2">{{ $row['registered'] }}</td></tr>@empty<tr><td colspan="5" class="border border-slate-300 p-5 text-center">No matching records.</td></tr>@endforelse</tbody>
                    @elseif($type === 'employers')
                        <thead><tr class="bg-slate-100"><th class="border border-slate-300 p-2">Employer</th><th class="border border-slate-300 p-2">Accreditation</th><th class="border border-slate-300 p-2">Accredited date</th><th class="border border-slate-300 p-2">Job postings</th><th class="border border-slate-300 p-2">Registered</th></tr></thead>
                        <tbody>@forelse($rows as $row)<tr><td class="border border-slate-300 p-2">{{ $row['company'] }}</td><td class="border border-slate-300 p-2">{{ $row['accreditation'] }}</td><td class="border border-slate-300 p-2">{{ $row['accredited_date'] }}</td><td class="border border-slate-300 p-2">{{ $row['job_postings'] }}</td><td class="border border-slate-300 p-2">{{ $row['registered'] }}</td></tr>@empty<tr><td colspan="5" class="border border-slate-300 p-5 text-center">No matching records.</td></tr>@endforelse</tbody>
                    @elseif($type === 'hired')
                        <thead><tr class="bg-slate-100"><th class="border border-slate-300 p-2">Jobseeker</th><th class="border border-slate-300 p-2">Barangay</th><th class="border border-slate-300 p-2">Employer</th><th class="border border-slate-300 p-2">Position</th><th class="border border-slate-300 p-2">Hired date</th><th class="border border-slate-300 p-2">Employment record</th></tr></thead>
                        <tbody>@forelse($rows as $row)<tr><td class="border border-slate-300 p-2">{{ $row['jobseeker'] }}</td><td class="border border-slate-300 p-2">{{ $row['barangay'] }}</td><td class="border border-slate-300 p-2">{{ $row['company'] }}</td><td class="border border-slate-300 p-2">{{ $row['position'] }}</td><td class="border border-slate-300 p-2">{{ $row['hired_date'] }}</td><td class="border border-slate-300 p-2">{{ $row['resignation_status'] }}</td></tr>@empty<tr><td colspan="6" class="border border-slate-300 p-5 text-center">No matching records.</td></tr>@endforelse</tbody>
                    @elseif($type === 'training')
                        <thead><tr class="bg-slate-100"><th class="border border-slate-300 p-2">Program</th><th class="border border-slate-300 p-2">Training type</th><th class="border border-slate-300 p-2">Status</th><th class="border border-slate-300 p-2">Enrollments</th><th class="border border-slate-300 p-2">Passed</th><th class="border border-slate-300 p-2">Certificates</th></tr></thead>
                        <tbody>@forelse($rows as $row)<tr><td class="border border-slate-300 p-2">{{ $row['program'] }}</td><td class="border border-slate-300 p-2">{{ $row['training_type'] }}</td><td class="border border-slate-300 p-2">{{ $row['status'] }}</td><td class="border border-slate-300 p-2">{{ number_format($row['enrollments']) }}</td><td class="border border-slate-300 p-2">{{ number_format($row['passed']) }}</td><td class="border border-slate-300 p-2">{{ number_format($row['certificates']) }}</td></tr>@empty<tr><td colspan="6" class="border border-slate-300 p-5 text-center">No matching records.</td></tr>@endforelse</tbody>
                    @elseif($type === 'application-funnel')
                        <thead><tr class="bg-slate-100"><th class="border border-slate-300 p-2">Application stage</th><th class="border border-slate-300 p-2">Applications</th><th class="border border-slate-300 p-2">Share of filtered applications</th></tr></thead>
                        <tbody>@forelse($rows as $row)<tr><td class="border border-slate-300 p-2">{{ $row['stage'] }}</td><td class="border border-slate-300 p-2">{{ number_format($row['applications']) }}</td><td class="border border-slate-300 p-2">{{ $row['share'] }}</td></tr>@empty<tr><td colspan="3" class="border border-slate-300 p-5 text-center">No matching records.</td></tr>@endforelse</tbody>
                    @elseif($type === 'hiring-trends')
                        <thead><tr class="bg-slate-100"><th class="border border-slate-300 p-2">Hiring month</th><th class="border border-slate-300 p-2">Recorded hires</th></tr></thead>
                        <tbody>@forelse($rows as $row)<tr><td class="border border-slate-300 p-2">{{ $row['month'] }}</td><td class="border border-slate-300 p-2">{{ number_format($row['hires']) }}</td></tr>@empty<tr><td colspan="2" class="border border-slate-300 p-5 text-center">No matching records.</td></tr>@endforelse</tbody>
                    @else
                        <thead><tr class="bg-slate-100"><th class="border border-slate-300 p-2">Skill</th><th class="border border-slate-300 p-2">Category</th><th class="border border-slate-300 p-2">Barangay</th><th class="border border-slate-300 p-2">Jobseekers</th></tr></thead>
                        <tbody>@forelse($rows as $row)<tr><td class="border border-slate-300 p-2">{{ $row['skill'] }}</td><td class="border border-slate-300 p-2">{{ $row['category'] }}</td><td class="border border-slate-300 p-2">{{ $row['barangay'] }}</td><td class="border border-slate-300 p-2">{{ number_format($row['jobseekers']) }}</td></tr>@empty<tr><td colspan="4" class="border border-slate-300 p-5 text-center">No matching records.</td></tr>@endforelse</tbody>
                    @endif
                </table>
            </div>

            <footer class="mt-8 border-t border-slate-300 pt-3 text-[10px] text-slate-500">
                Internal administrative report. Handle all personal information in accordance with RA 10173 and DMDP access policies.
            </footer>
        </section>
    </main>
</body>
</html>
