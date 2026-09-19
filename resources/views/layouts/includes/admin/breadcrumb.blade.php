@if (count($breadcrumbs))

    <nav class="mb-4">

        <ol class="flex flex-wrap">
            @foreach ($breadcrumbs as $item)

                <li class="text-sm leading-normal text-slate-700 dark:text-slate-300 {{ !$loop->first ? "pl-2 before:float-left before:pr-2 before:content-['/']" : '' }}">

                    @isset($item['href'])
                        <a href="{{$item['href']}}" class="opacity-50">
                            {{ $item['name'] }}
                        </a>
                    @else
                        {{ $item['name'] }}
                    @endisset

                </li>

            @endforeach

        </ol>

        @if (count($breadcrumbs) > 1)

            <h6 class="font-bold dark:text-white">
                {{ end($breadcrumbs)['name'] }}
            </h6>

        @endif
    </nav>

@endif
