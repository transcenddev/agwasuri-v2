<div>
    <button 
        wire:click="toggle" 
        class="relative inline-flex items-center justify-center w-10 h-10 text-white transition-colors rounded-lg hover:bg-cvsu/30 backdrop-blur-sm dark:text-cvsu-200 dark:hover:bg-cvsu-900/50 focus:outline-none focus:ring-2 focus:ring-cvsu-400 drop-shadow-lg"
        title="{{ $darkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode' }}"
    >
        <!-- Sun Icon (Light Mode) -->
        <svg 
            class="w-5 h-5 transition-all duration-300 {{ $darkMode ? 'opacity-0 scale-0 absolute' : 'opacity-100 scale-100' }}" 
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        
        <!-- Moon Icon (Dark Mode) -->
        <svg 
            class="w-5 h-5 transition-all duration-300 {{ $darkMode ? 'opacity-100 scale-100' : 'opacity-0 scale-0 absolute' }}" 
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
        </svg>
    </button>
</div>
