@props(['selected', 'dishId','options', 'active' => ''])
<div >

    <select id="select-{{ $dishId }}" wire:change="statusChanged($event.target.value, {{ $dishId }})" class="block rounded-lg border border-gray-300 bg-white mr-5 px-7 py-2 text-sm text-gray-900 shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200" {{ $active }}>
        @foreach ($options as $id => $name)
            <option
                class="bg-white text-gray-900 text-start"
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
