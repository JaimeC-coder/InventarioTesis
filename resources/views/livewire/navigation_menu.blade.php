<div x-data="{ sidebarOpen: true }" class="contents">

    <aside id="logo-sidebar"
        class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-all -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700 duration-300"
        :class="!sidebarOpen && 'sm:w-16'" aria-label="Sidebar">
        <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
            <ul class="space-y-2 font-medium">

                @foreach ($links as $link)
                    @isset($link['header'])
                        <li class="text-xs text-gray-500 uppercase dark:text-gray-400 px-2 truncate"
                            :class="!sidebarOpen && 'sm:hidden'">
                            {{ $link['header'] }}
                        </li>
                    @else
                        @if (isset($link['submenu']))
                            <li x-data="{ tooltipVisible: false, tooltipStyle: '' }" @scroll.window="tooltipVisible = false">
                                <button type="button" @click="if (!sidebarOpen) sidebarOpen = true"
                                    @mouseenter="if (!sidebarOpen) {
            const rect = $el.getBoundingClientRect();
            tooltipStyle = `top: ${rect.top + rect.height / 2}px; left: ${rect.right + 8}px;`;
            tooltipVisible = true;
        }"
                                    @mouseleave="tooltipVisible = false"
                                    class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                                    aria-controls="dropdown-{{ Str::slug($link['name']) }}"
                                    data-collapse-toggle="dropdown-{{ Str::slug($link['name']) }}">
                                    <x-dynamic-component :component="'icons.nav.' . $link['icon']" class="flex-shrink-0" />
                                    <span class="flex-1 ms-3 text-left whitespace-nowrap"
                                        :class="!sidebarOpen && 'sm:hidden'">
                                        {{ $link['name'] }}
                                    </span>
                                    <svg class="w-3 h-3" :class="!sidebarOpen && 'sm:hidden'" fill="none"
                                        viewBox="0 0 10 6">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m1 1 4 4 4-4" />
                                    </svg>
                                </button>

                                <template x-teleport="body">
                                    <span x-show="tooltipVisible && !sidebarOpen" x-transition.opacity.duration.150ms
                                        :style="tooltipStyle" style="position: fixed; transform: translateY(-50%);"
                                        class="px-2 py-1 rounded-md bg-gray-900 text-white text-xs font-medium whitespace-nowrap z-50 shadow-sm dark:bg-gray-700">
                                        {{ $link['name'] }}
                                    </span>
                                </template>

                                <ul id="dropdown-{{ Str::slug($link['name']) }}" class="hidden py-2 space-y-2">
                                    @foreach ($link['submenu'] as $sub)
                                        <li>
                                            <a href="{{ route($sub['route']) }}"
                                                class="flex items-center w-full p-2 pl-11 text-gray-900 transition duration-75 rounded-lg hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 {{ $sub['active'] ?? false ? 'bg-gray-100 font-semibold dark:bg-gray-700' : '' }}"
                                                :class="!sidebarOpen && 'sm:hidden'">
                                                {{ $sub['name'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @else
                            <li x-data="{ tooltipVisible: false, tooltipStyle: '' }" @scroll.window="tooltipVisible = false">
                                <a href="{{ route($link['route']) }}"
                                    @mouseenter="if (!sidebarOpen) {
                const rect = $el.getBoundingClientRect();
                tooltipStyle = `top: ${rect.top + rect.height / 2}px; left: ${rect.right + 8}px;`;
                tooltipVisible = true;
            }"
                                    @mouseleave="tooltipVisible = false"
                                    class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 {{ $link['active'] ? 'bg-gray-100 font-semibold dark:bg-gray-700' : '' }}">
                                    <x-dynamic-component :component="'icons.nav.' . $link['icon']" class="flex-shrink-0" />
                                    <span class="ms-3" :class="!sidebarOpen && 'sm:hidden'">{{ $link['name'] }}</span>
                                </a>

                                <template x-teleport="body">
                                    <span x-show="tooltipVisible && !sidebarOpen" x-transition.opacity.duration.150ms
                                        :style="tooltipStyle" style="position: fixed; transform: translateY(-50%);"
                                        class="px-2 py-1 rounded-md bg-gray-900 text-white text-xs font-medium whitespace-nowrap z-50 shadow-sm dark:bg-gray-700">
                                        {{ $link['name'] }}
                                    </span>
                                </template>
                            </li>
                        @endif
                    @endisset
                @endforeach

            </ul>
        </div>
    </aside>

    {{-- Botón AHORA es hermano del aside, no descendiente --}}
    <button @click="sidebarOpen = !sidebarOpen"
        class="hidden sm:inline-flex fixed z-50 items-center justify-center w-6 h-6 bg-white border border-gray-200 rounded-full shadow-sm hover:bg-gray-100 dark:bg-gray-700 dark:border-gray-600 top-24 transition-all duration-300"
        :class="sidebarOpen ? 'left-[15.25rem]' : 'left-[3.25rem]'">
        <svg class="w-3 h-3 transition-transform" :class="!sidebarOpen && 'rotate-180'" fill="none"
            stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
        </svg>
    </button>

</div>
