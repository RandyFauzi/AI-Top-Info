<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Top Info - Global Opportunity Hunter Feed</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        h1, h2, h3, .font-display {
            font-family: 'Outfit', sans-serif;
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f5f3ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            900: '#312e81',
                        },
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full flex flex-col antialiased">
    <div class="min-h-full flex">
        <!-- Sidebar -->
        <aside class="hidden md:flex md:w-64 md:flex-col bg-slate-900 border-r border-slate-800">
            <div class="flex flex-col flex-grow pt-5 pb-4 overflow-y-auto">
                <div class="flex items-center flex-shrink-0 px-6 space-x-2">
                    <span class="p-2 bg-brand-600 rounded-lg text-white font-bold text-lg font-display tracking-tight shadow-lg shadow-brand-500/20">AT</span>
                    <span class="text-xl font-bold text-white tracking-wider font-display">AI Top Info</span>
                </div>
                <div class="mt-8 flex-grow flex flex-col">
                    <nav class="flex-1 px-4 space-y-1">
                        <a href="{{ route('dashboard') }}" class="bg-slate-800 text-white group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition duration-150">
                            <svg class="mr-3 h-5 w-5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1M19 20a2 2 0 002-2V8a2 2 0 00-2-2h-5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Opportunity Feed
                        </a>
                    </nav>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col overflow-hidden">
            <!-- Header / Top Nav -->
            <header class="bg-slate-900/50 border-b border-slate-800 flex items-center justify-between px-8 py-4 backdrop-blur-md sticky top-0 z-10">
                <div class="flex items-center space-x-4">
                    <h2 class="text-2xl font-bold font-display text-white tracking-wide">Global Opportunity Aggregator</h2>
                </div>
                <div class="flex items-center space-x-4">
                    <form action="{{ route('dashboard.ingest') }}" method="POST">
                        @csrf
                        <button type="submit" class="flex items-center space-x-2 bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-500 hover:to-indigo-500 text-white font-medium px-5 py-2.5 rounded-xl shadow-lg shadow-brand-500/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                            <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24" x-show="false" style="display: none;">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89H18"/>
                            </svg>
                            <span>Run Ingestion Hunter</span>
                        </button>
                    </form>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto px-8 py-8 space-y-8">
                <!-- Status Notifications -->
                @if (session('status'))
                    <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 text-sm flex items-center space-x-3 shadow-lg shadow-emerald-500/5">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <!-- KPI Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-gradient-to-br from-slate-900 to-slate-950 p-5 rounded-2xl border border-slate-800 shadow-md">
                        <span class="text-xs font-semibold tracking-wider text-slate-400 uppercase">Total Opportunities</span>
                        <h3 class="text-3xl font-extrabold text-white mt-1 font-display">{{ $totalCount }}</h3>
                    </div>
                    <div class="bg-gradient-to-br from-blue-900/10 to-slate-950 p-5 rounded-2xl border border-blue-500/10 shadow-md">
                        <span class="text-xs font-semibold tracking-wider text-blue-400 uppercase">LinkedIn Leads</span>
                        <h3 class="text-3xl font-extrabold text-blue-400 mt-1 font-display">{{ $linkedinCount }}</h3>
                    </div>
                    <div class="bg-gradient-to-br from-indigo-900/10 to-slate-950 p-5 rounded-2xl border border-indigo-500/10 shadow-md">
                        <span class="text-xs font-semibold tracking-wider text-indigo-400 uppercase">Discord Alerts</span>
                        <h3 class="text-3xl font-extrabold text-indigo-400 mt-1 font-display">{{ $discordCount }}</h3>
                    </div>
                    <div class="bg-gradient-to-br from-sky-900/10 to-slate-950 p-5 rounded-2xl border border-sky-500/10 shadow-md">
                        <span class="text-xs font-semibold tracking-wider text-sky-400 uppercase">Web Job Boards</span>
                        <h3 class="text-3xl font-extrabold text-sky-400 mt-1 font-display">{{ $webCount }}</h3>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-slate-900 p-6 rounded-2xl border border-slate-800 shadow-md">
                    <form method="GET" action="{{ route('dashboard') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold tracking-wider uppercase text-slate-400 mb-2">Search Query</label>
                            <input type="text" name="search" value="{{ $search }}" placeholder="Search title or keywords..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-slate-200 text-sm focus:outline-none focus:border-brand-500 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold tracking-wider uppercase text-slate-400 mb-2">Filter Platform</label>
                            <select name="platform" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-slate-200 text-sm focus:outline-none focus:border-brand-500 transition">
                                <option value="">All Platforms</option>
                                <option value="LinkedIn" {{ $platform == 'LinkedIn' ? 'selected' : '' }}>LinkedIn</option>
                                <option value="Discord" {{ $platform == 'Discord' ? 'selected' : '' }}>Discord</option>
                                <option value="Web" {{ $platform == 'Web' ? 'selected' : '' }}>Web Boards</option>
                            </select>
                        </div>
                        <div class="flex items-end space-x-2">
                            <button type="submit" class="w-full bg-slate-800 hover:bg-slate-700 text-white font-medium px-5 py-2.5 rounded-xl transition">
                                Search Feed
                            </button>
                            @if($search || $platform)
                                <a href="{{ route('dashboard') }}" class="bg-slate-950 border border-slate-800 hover:bg-slate-900 text-slate-400 p-2.5 rounded-xl transition flex items-center justify-center" title="Clear Filters">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Opportunity Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse ($opportunities as $opp)
                        @php
                            $platformColor = 'bg-slate-800 text-slate-400';
                            if ($opp->source_platform === 'LinkedIn') $platformColor = 'bg-blue-500/10 text-blue-400 border border-blue-500/20';
                            if ($opp->source_platform === 'Discord') $platformColor = 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20';
                            if ($opp->source_platform === 'Web') $platformColor = 'bg-sky-500/10 text-sky-400 border border-sky-500/20';
                        @endphp
                        <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6 flex flex-col justify-between shadow-lg hover:border-slate-700 hover:scale-[1.01] transition-all duration-200">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $platformColor }}">
                                        {{ $opp->source_platform }}
                                    </span>
                                    <span class="text-xs text-slate-500 font-medium">
                                        {{ $opp->posted_at ? $opp->posted_at->diffForHumans() : '' }}
                                    </span>
                                </div>
                                <h3 class="text-lg font-bold text-white leading-snug">{{ $opp->title }}</h3>
                                <p class="text-sm text-slate-400 leading-relaxed line-clamp-4">{{ $opp->summary }}</p>
                            </div>

                            <div class="mt-6 space-y-4">
                                <!-- Contact Details -->
                                @if (isset($opp->extracted_contacts['email']) || isset($opp->extracted_contacts['phone_wa']))
                                    <div class="border-t border-slate-800 pt-4 flex flex-col gap-2">
                                        <span class="text-[10px] uppercase font-bold tracking-wider text-slate-500">Extracted Contacts:</span>
                                        <div class="flex flex-wrap gap-2">
                                            @if (!empty($opp->extracted_contacts['email']))
                                                <a href="mailto:{{ $opp->extracted_contacts['email'] }}" class="inline-flex items-center space-x-1.5 bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-400 text-xs font-semibold px-3 py-1.5 rounded-lg border border-indigo-500/20 transition">
                                                    <span>✉️</span>
                                                    <span class="truncate max-w-[120px]">{{ $opp->extracted_contacts['email'] }}</span>
                                                </a>
                                            @endif
                                            @if (!empty($opp->extracted_contacts['phone_wa']))
                                                <a href="https://wa.me/{{ $opp->extracted_contacts['phone_wa'] }}" target="_blank" class="inline-flex items-center space-x-1.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 text-xs font-semibold px-3 py-1.5 rounded-lg border border-emerald-500/20 transition">
                                                    <span>💬</span>
                                                    <span>{{ $opp->extracted_contacts['phone_wa'] }}</span>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <!-- Direct URL Button -->
                                <a href="{{ $opp->source_url }}" target="_blank" class="w-full text-center block bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold uppercase tracking-wider py-3 rounded-xl transition duration-150">
                                    Buka Postingan / Go to Post
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full bg-slate-900 border border-slate-800 rounded-2xl p-12 text-center text-slate-500 text-sm">
                            No dataset sourcing opportunities found yet. Click "Run Ingestion Hunter" to scrape B2B posts!
                        </div>
                    @endforelse
                </div>
            </div>
        </main>
    </div>
</body>
</html>
