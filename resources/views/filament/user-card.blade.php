<div class="dark:bg-slate-800 gap-6 flex items-center justify-center">
    <div
        class="bg-gray-100 dark:bg-white/5 relative overflow-hidden group rounded-xl p-3 transition-all duration-500 transform flex-grow dark:border-non">
        <div class="flex items-center gap-4">
            @props([
                'user' => filament()->auth()->user(),
            ])

            <x-filament::avatar :src="filament()->getUserAvatarUrl($user)" :alt="__('filament-panels::layout.avatar.alt', ['name' => filament()->getUserName($user)])" :attributes="\Filament\Support\prepare_inherited_attributes($attributes)->class([
                'fi-user-avatar rounded-lg',
            ])" />
            <div class="w-fit transition-all transform duration-500">
                <h3 class="text-gray-600 dark:text-gray-200 font-bold text-xs">
                    {{ Auth::user()->name }}
                </h3>
                <p class="text-gray-400 text-xs">{{ Auth::user()->roles->first()->name ?? 'No Role Assigned' }}</p>
            </div>
            {{-- <div class="flex items-end ml-auto">
                <form action="{{ filament()->getLogoutUrl() }}" method="post" class="my-auto">
                    @csrf

                    <x-filament::button class="flex items-center justify-center p-2 rounded-md" color="gray"
                        tag="button" type="submit">
                        <x-heroicon-m-arrow-left-on-rectangle class="w-5 h-5" color="gray" />
                    </x-filament::button>
                </form>
            </div> --}}
        </div>
    </div>
</div>
