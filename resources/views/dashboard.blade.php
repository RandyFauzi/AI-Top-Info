<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Top Info - B2B Lead Intelligence Dashboard</title>
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
    <div class="min-h-full flex" x-data="{ selectedCompany: null }">
        <!-- Sidebar -->
        <aside class="hidden md:flex md:w-64 md:flex-col bg-slate-900 border-r border-slate-800">
            <div class="flex flex-col flex-grow pt-5 pb-4 overflow-y-auto">
                <div class="flex items-center flex-shrink-0 px-6 space-x-2">
                    <span class="p-2 bg-brand-600 rounded-lg text-white font-bold text-lg font-display tracking-tight shadow-lg shadow-brand-500/20">AT</span>
                    <span class="text-xl font-bold text-white tracking-wider font-display">AI Top Info</span>
                </div>
                <div class="mt-8 flex-grow flex flex-col">
                    <nav class="flex-1 px-4 space-y-1">
                        <a href="#" class="bg-slate-800 text-white group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition duration-150">
                            <svg class="mr-3 h-5 w-5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Leads Overview
                        </a>
                        <a href="#" class="text-slate-400 hover:bg-slate-800 hover:text-white group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition duration-150">
                            <svg class="mr-3 h-5 w-5 text-slate-400 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            Signals Stream
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
                    <h2 class="text-2xl font-bold font-display text-white tracking-wide">B2B Lead Intelligence</h2>
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
                            <span>Trigger Pipe Ingestion</span>
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

                <!-- Leads Intelligence Table -->
                <div class="bg-slate-900 rounded-2xl border border-slate-800 overflow-hidden shadow-lg">
                    <div class="px-6 py-4 bg-slate-950/40 border-b border-slate-800 flex items-center justify-between">
                        <h3 class="text-lg font-bold font-display text-white">Target AI Companies</h3>
                        <span class="text-xs bg-slate-800 text-slate-300 px-3 py-1 rounded-full font-medium">Sorted by Lead Score</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-800">
                            <thead class="bg-slate-950">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Company Name</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Intent Category</th>
                                    <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-slate-400 uppercase tracking-wider">Score</th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-slate-400 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800 bg-slate-900/50">
                                @forelse ($companies as $company)
                                    @php
                                        $scoreVal = $company->leadScore ? $company->leadScore->score : 0;
                                        
                                        // Color Coded Badges based on Score
                                        if ($scoreVal >= 90) {
                                            $scoreClass = 'text-emerald-400 bg-emerald-500/10 border border-emerald-500/20';
                                        } elseif ($scoreVal >= 50) {
                                            $scoreClass = 'text-amber-400 bg-amber-500/10 border border-amber-500/20';
                                        } else {
                                            $scoreClass = 'text-slate-400 bg-slate-800/80 border border-slate-700/30';
                                        }
                                    @endphp
                                    <tr class="hover:bg-slate-800/20 transition">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-3">
                                                <div class="h-9 w-9 rounded-lg bg-brand-600/10 border border-brand-500/20 flex items-center justify-center text-brand-400 font-bold font-display">
                                                    {{ substr($company->name, 0, 2) }}
                                                </div>
                                                <span class="text-sm font-bold text-white">{{ $company->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-800 text-slate-300">
                                                {{ $company->leadScore ? $company->leadScore->intent_category : 'General' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold {{ $scoreClass }}">
                                                {{ $scoreVal }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button 
                                                @click="selectedCompany = {{ json_encode([
                                                    'name' => $company->name,
                                                    'contact_email' => $company->contact_email,
                                                    'whatsapp_number' => $company->whatsapp_number,
                                                    'linkedin_url' => $company->linkedin_url,
                                                    'discord_url' => $company->discord_url,
                                                    'lead_score' => [
                                                        'score' => $company->leadScore->score ?? 0,
                                                        'intent_category' => $company->leadScore->intent_category ?? 'N/A',
                                                        'reasoning' => $company->leadScore->reasoning ?? 'N/A'
                                                    ],
                                                    'outreach_strategy' => [
                                                        'target_persona' => $company->outreachStrategy->target_persona ?? 'N/A',
                                                        'suggested_angle' => $company->outreachStrategy->suggested_angle ?? 'N/A',
                                                        'email_draft' => $company->outreachStrategy->email_draft ?? ''
                                                    ]
                                                ]) }}" 
                                                class="bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold uppercase tracking-wider px-4 py-2 rounded-lg transition"
                                            >
                                                View Details
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-slate-500 text-sm">
                                            No lead intelligence gathered yet. Click "Trigger Pipe Ingestion" to fetch new B2B signals!
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>

        <!-- Right Slide-Over (Alpine.js Modal) -->
        <div class="fixed inset-0 overflow-hidden z-50" 
             x-show="selectedCompany !== null" 
             style="display: none;">
            
            <div class="absolute inset-0 overflow-hidden">
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity" 
                     x-show="selectedCompany !== null"
                     x-transition:enter="ease-in-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in-out duration-300"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="selectedCompany = null"></div>

                <!-- Slide-Over Container -->
                <div class="absolute inset-y-0 right-0 pl-10 max-w-full flex">
                    <div class="w-screen max-w-2xl bg-slate-900 border-l border-slate-800 shadow-2xl flex flex-col h-full"
                         x-show="selectedCompany !== null"
                         x-transition:enter="transform transition ease-in-out duration-300 sm:duration-300"
                         x-transition:enter-start="translate-x-full"
                         x-transition:enter-end="translate-x-0"
                         x-transition:leave="transform transition ease-in-out duration-300 sm:duration-300"
                         x-transition:leave-start="translate-x-0"
                         x-transition:leave-end="translate-x-full">
                        
                        <!-- Header -->
                        <div class="p-6 bg-slate-950 border-b border-slate-800 flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-bold font-display text-white" x-text="selectedCompany?.name"></h3>
                                <p class="text-xs text-slate-400 mt-1" x-text="'Category: ' + (selectedCompany?.lead_score?.intent_category || 'N/A')"></p>
                            </div>
                            <button class="text-slate-400 hover:text-white transition p-2 hover:bg-slate-800 rounded-lg" @click="selectedCompany = null">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Content -->
                        <div class="p-8 space-y-8 flex-1 overflow-y-auto">
                            <!-- Score Card -->
                            <div class="bg-slate-950 p-4 rounded-xl border border-slate-800 flex justify-between items-center">
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Pipeline Lead Score</span>
                                <span class="text-lg font-extrabold text-brand-400" x-text="selectedCompany?.lead_score?.score + ' / 100'"></span>
                            </div>

                            <!-- 1. AI Reasoning -->
                            <div class="space-y-3">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center space-x-2">
                                    <svg class="h-4 w-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                    </svg>
                                    <span>AI Reasoning</span>
                                </h4>
                                <div class="bg-slate-950/80 p-5 rounded-xl border border-slate-800 text-sm text-slate-300 leading-relaxed" 
                                     x-text="selectedCompany?.lead_score?.reasoning"></div>
                            </div>

                            <!-- Direct Contacts -->
                            <div class="space-y-3" x-show="selectedCompany?.contact_email || selectedCompany?.whatsapp_number || selectedCompany?.linkedin_url || selectedCompany?.discord_url">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center space-x-2">
                                    <svg class="h-4 w-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    <span>Direct Contacts / Action Center</span>
                                </h4>
                                <div class="bg-slate-950 p-5 rounded-xl border border-slate-800 flex flex-wrap gap-3">
                                    <!-- Email Button -->
                                    <template x-if="selectedCompany?.contact_email">
                                        <a :href="'mailto:' + selectedCompany.contact_email" class="inline-flex items-center space-x-2 bg-indigo-500/10 hover:bg-indigo-500/20 border border-indigo-500/30 text-indigo-400 text-xs font-bold px-4 py-2.5 rounded-lg transition">
                                            <span>✉️ Email:</span>
                                            <span x-text="selectedCompany.contact_email"></span>
                                        </a>
                                    </template>

                                    <!-- WhatsApp Button -->
                                    <template x-if="selectedCompany?.whatsapp_number">
                                        <a :href="'https://wa.me/' + selectedCompany.whatsapp_number" target="_blank" class="inline-flex items-center space-x-2 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/30 text-emerald-400 text-xs font-bold px-4 py-2.5 rounded-lg transition">
                                            <span>💬 Chat WA:</span>
                                            <span x-text="selectedCompany.whatsapp_number"></span>
                                        </a>
                                    </template>

                                    <!-- LinkedIn Button -->
                                    <template x-if="selectedCompany?.linkedin_url">
                                        <a :href="selectedCompany.linkedin_url" target="_blank" class="inline-flex items-center space-x-2 bg-blue-500/10 hover:bg-blue-500/20 border border-blue-500/30 text-blue-400 text-xs font-bold px-4 py-2.5 rounded-lg transition">
                                            <span>🔗 LinkedIn Profile</span>
                                        </a>
                                    </template>

                                    <!-- Discord Button -->
                                    <template x-if="selectedCompany?.discord_url">
                                        <a :href="selectedCompany.discord_url" target="_blank" class="inline-flex items-center space-x-2 bg-purple-500/10 hover:bg-purple-500/20 border border-purple-500/30 text-purple-400 text-xs font-bold px-4 py-2.5 rounded-lg transition">
                                            <span>🎮 Discord Server</span>
                                        </a>
                                    </template>
                                </div>
                            </div>

                            <!-- 2. Target Persona -->
                            <div class="space-y-3">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Target Persona</h4>
                                <div class="bg-slate-950 p-4 rounded-xl border border-slate-800 text-sm text-slate-200" 
                                     x-text="selectedCompany?.outreach_strategy?.target_persona"></div>
                            </div>

                            <!-- 3. Suggested Angle -->
                            <div class="space-y-3">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Suggested Angle</h4>
                                <div class="bg-slate-950 p-4 rounded-xl border border-slate-800 text-sm text-slate-200" 
                                     x-text="selectedCompany?.outreach_strategy?.suggested_angle"></div>
                            </div>

                            <!-- 4. Email Pitch Draft -->
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center space-x-2">
                                        <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        <span>Email Pitch Draft</span>
                                    </h4>
                                    <!-- Copy to Clipboard Button -->
                                    <button class="text-brand-400 hover:text-brand-300 text-xs font-semibold px-3 py-1 bg-brand-500/10 border border-brand-500/20 rounded-lg transition"
                                            x-data="{ copied: false }"
                                            @click="navigator.clipboard.writeText(selectedCompany?.outreach_strategy?.email_draft); copied = true; setTimeout(() => copied = false, 2000)">
                                        <span x-show="!copied">Copy to Clipboard</span>
                                        <span x-show="copied" class="text-emerald-400 font-bold">Copied!</span>
                                    </button>
                                </div>
                                <pre class="bg-slate-950 border border-slate-800 p-5 rounded-xl text-xs text-slate-350 whitespace-pre-wrap font-sans leading-relaxed"
                                     x-text="selectedCompany?.outreach_strategy?.email_draft"></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
