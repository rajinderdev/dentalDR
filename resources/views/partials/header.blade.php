<header class="bg-white border-b border-gray-200 sticky top-0 z-20">
    <div class="flex items-center h-11">


        {{-- Search bar: pill/rounded, subtle border, with filter icon --}}
        <div class="flex items-center ml-3 ">
            <div class="flex items-center bg-white border rounded-full px-3 py-1.5 gap-2 search-box">
                <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    type="text"
                    placeholder="Search by keyword"
                    class="text-sm text-gray-500 placeholder-gray-400 focus:outline-none bg-transparent flex-1 w-full"
                />
                {{-- Filter lines icon --}}
                <button class="text-gray-400 hover:text-gray-500 flex-shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6h18M6 12h12M9 18h6"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Add Patients button: outlined, rounded, tight spacing --}}
        <div class="ml-3">
            <button class="flex items-center gap-1.5 border border-gray-300 rounded-full px-3 py-1.5 text-sm text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                Add Patients
                <span class="text-gray-500 font-medium">+</span>
            </button>
        </div>

        {{-- Spacer --}}
        <div class="flex-1"></div>

        {{-- Right: Help, Bell, Avatar + Name --}}
        <div class="flex items-center gap-1 pr-4">

            {{-- Help circle icon --}}
            <button class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-colors">
                <svg class="w-4.5 h-4.5" style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </button>

            {{-- Bell with blue notification dot --}}
            <button class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-colors relative">
                <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-blue-500 rounded-full"></span>
            </button>

            {{-- User avatar photo + name + chevron --}}
            <div class="flex items-center gap-1.5 ml-1 cursor-pointer hover:bg-gray-100 rounded-lg px-2 py-1 transition-colors">
                {{-- Profile picture (real image with initial fallback) --}}
                <div class="relative w-7 h-7 flex-shrink-0">
                    @if(auth()->user()->profile_photo_path ?? false)
                        <img src="{{ auth()->user()->profile_photo_url }}"
                             alt="{{ auth()->user()->name }}"
                             class="w-7 h-7 rounded-full object-cover ring-2 ring-blue-400">
                    @else
                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-xs font-semibold ring-2 ring-blue-300">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </div>
                    @endif
                </div>
                <span class="text-sm font-medium text-gray-700">{{ auth()->user()->name ?? 'Sushil' }}</span>
                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>

        </div>
    </div>
</header>