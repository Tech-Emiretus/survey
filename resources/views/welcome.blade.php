<x-guest-layout>
    <div class="p-10">
        <h1 class="text-2xl font-semibold mb-6 text-center dark:text-[#EDEDEC]">Welcome to Survey Feedback App</h1>
        @if (Route::has('login'))
            <div class="flex flex-col items-center gap-4 w-full">
                <x-primary-link-button href="{{ route('login') }}">
                    Log in
                </x-primary-link-button>

                @if (Route::has('register'))
                    <x-primary-link-button href="{{ route('register') }}">
                        Register
                    </x-primary-link-button>
                @endif
            </div>
        @endif
    </div>
</x-guest-layout>
