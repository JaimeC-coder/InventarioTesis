<?php

namespace App\Themes;

use PowerComponents\LivewirePowerGrid\Themes\Tailwind;

class PowerGridTheme extends Tailwind
{
    public function table(): array
    {
        return [
            'layout' => [
                'base' => 'p-3 align-middle inline-block min-w-full w-full sm:px-6 lg:px-8',
                'div' => 'rounded-t-lg relative border-x border-t border-gray-200 dark:border-gray-700 dark:bg-gray-900',
                'table' => 'min-w-full dark:bg-gray-900',
                'container' => '-my-2 overflow-x-auto sm:-mx-3 lg:-mx-8',
                'actions' => 'flex gap-2',
            ],
            'header' => [
                'thead' => 'shadow-sm rounded-t-lg bg-gray-50 dark:bg-gray-900',
                'tr' => '',
                'th' => 'font-semibold px-3 py-3 text-left text-xs text-gray-500 uppercase tracking-wider whitespace-nowrap dark:text-gray-400',
                'thAction' => '!font-bold',
            ],
            'body' => [
                'tbody' => 'text-gray-700 dark:text-gray-200',
                'tbodyEmpty' => '',
                'tr' => 'border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/60',
                'td' => 'px-3 py-2 whitespace-nowrap dark:text-gray-200',
                'tdEmpty' => 'p-2 whitespace-nowrap dark:text-gray-200',
                'tdSummarize' => 'p-2 whitespace-nowrap dark:text-gray-200 text-sm text-gray-500 text-right space-y-2',
                'trSummarize' => '',
                'tdFilters' => '',
                'trFilters' => '',
                'tdActionsContainer' => 'flex gap-2',
            ],
        ];
    }

    public function checkbox(): array
    {
        return [
            'th' => 'px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider',
            'base' => '',
            'label' => 'flex items-center space-x-3',
            'input' => 'form-checkbox rounded border-gray-300 dark:border-gray-600 bg-transparent transition duration-100 ease-in-out h-4 w-4 text-indigo-600 focus:ring-indigo-500 dark:focus:ring-offset-gray-900',
        ];
    }
}
