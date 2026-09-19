@props(['selected', 'dishId','options', 'active' => ''])
<div >

    <select id="select-{{ $dishId }}" wire:change="statusChanged($event.target.value, {{ $dishId }})" class="block rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 mr-5 px-7 py-2 text-sm text-gray-900 dark:text-white shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200" {{ $active }}>
        @foreach ($options as $id => $name)
            <option
                class="bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-start"
                value="{{ $id }}"
                @if ($id == $selected)
                    selected="selected"
                @endif
            >
                {{ $name }}
            </option>
        @endforeach

    </select>
</div>
