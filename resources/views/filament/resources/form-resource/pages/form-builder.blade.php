<x-filament-panels::page>
    @push('scripts')
        @vite(['resources/js/app.js'])
    @endpush

   <div id="app"></div>
</x-filament-panels::page>
