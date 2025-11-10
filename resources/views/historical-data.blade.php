<x-app-layout>

    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

        {{-- Combined Filter Row --}}
        <div class="flex flex-col mb-6 md:flex-row md:justify-between md:items-start gap-6">
            {{-- User Filter (Only for Admin) - Takes up remaining space if needed --}}
            @if(Auth::check() && auth()->user()->isAdmin())
               <div class="flex-grow shadow-sm sm:rounded-lg"> {{-- removed p-6, mb-6, bg-white --}}
                   <livewire:user-filter />
               </div>
            @endif

            {{-- Date Filter (For Everyone) - Adjust width as needed --}}
            <div class="md:w-auto"> {{-- Or specify a fixed/relative width --}}
               <livewire:date-filter />
            </div>
       </div>
       
        <div class="p-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <livewire:water-quality-data-table />
        </div>
    </div>

</x-app-layout>
