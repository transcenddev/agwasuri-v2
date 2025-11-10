<x-app-layout>
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

        {{-- Combined Filter Row (Similar to Dashboard) --}}
        <div class="flex flex-col mb-6 md:flex-row md:justify-between md:items-start gap-6">
             {{-- User Filter (Only for Admin) --}}
             @if(Auth::check() && auth()->user()->isAdmin())
                <div class="flex-grow"> {{-- Removed shadow/rounding from dashboard example for simplicity --}}
                    <livewire:user-filter />
                </div>
             @endif

             {{-- Date Filter (For Everyone) --}}
             <div class="md:w-auto">
                <livewire:date-filter />
             </div>
        </div>

        {{-- Classify Fish Component --}}
        {{-- Container with padding/background moved here --}}
        <div class="p-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <livewire:classify-fish />
        </div>
    </div>
</x-app-layout>
