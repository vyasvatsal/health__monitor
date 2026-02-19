<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Incident Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                    role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-2xl font-bold">{{ $incident->title }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Reported {{ $incident->created_at->format('F j, Y g:i A') }}
                                ({{ $incident->created_at->diffForHumans() }})
                            </p>
                        </div>
                        <div class="flex space-x-2">
                            @php
                                $severityColors = [
                                    'critical' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                    'major' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
                                    'minor' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                    'maintenance' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                ];
                                $color = $severityColors[$incident->severity] ?? 'bg-gray-100 text-gray-800';

                                $statusColors = [
                                    'open' => 'bg-red-100 text-red-800',
                                    'investigating' => 'bg-purple-100 text-purple-800',
                                    'identified' => 'bg-yellow-100 text-yellow-800',
                                    'monitoring' => 'bg-blue-100 text-blue-800',
                                    'resolved' => 'bg-green-100 text-green-800',
                                ];
                                $statusColor = $statusColors[$incident->status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="{{ $color }} px-3 py-1 rounded-full text-sm font-semibold uppercase">
                                {{ $incident->severity }}
                            </span>
                            <span class="{{ $statusColor }} px-3 py-1 rounded-full text-sm font-semibold capitalize">
                                {{ str_replace('_', ' ', $incident->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="prose dark:prose-invert max-w-none mb-8">
                        <h4 class="text-lg font-semibold mb-2">Description</h4>
                        <p class="whitespace-pre-wrap">{{ $incident->description }}</p>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h4 class="text-lg font-semibold mb-4">Timeline</h4>
                        <ol class="relative border-l border-gray-200 dark:border-gray-700 ml-3">
                            <li class="mb-10 ml-6">
                                <span
                                    class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -left-3 ring-8 ring-white dark:ring-gray-900 dark:bg-blue-900">
                                    <svg class="w-2.5 h-2.5 text-blue-800 dark:text-blue-300" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                    </svg>
                                </span>
                                <h3 class="flex items-center mb-1 text-lg font-semibold text-gray-900 dark:text-white">
                                    Incident Reported</h3>
                                <time
                                    class="block mb-2 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
                                    {{ $incident->created_at->format('F j, Y g:i A') }}
                                </time>
                            </li>

                            @if($incident->resolved_at)
                                <li class="mb-10 ml-6">
                                    <span
                                        class="absolute flex items-center justify-center w-6 h-6 bg-green-100 rounded-full -left-3 ring-8 ring-white dark:ring-gray-900 dark:bg-green-900">
                                        <svg class="w-2.5 h-2.5 text-green-800 dark:text-green-300" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                                        </svg>
                                    </span>
                                    <h3 class="flex items-center mb-1 text-lg font-semibold text-gray-900 dark:text-white">
                                        Resolved</h3>
                                    <time
                                        class="block mb-2 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
                                        {{ $incident->resolved_at->format('F j, Y g:i A') }}
                                    </time>
                                </li>
                            @endif
                        </ol>
                    </div>

                    <div class="mt-8 flex space-x-4">
                        <a href="{{ route('incidents.edit', $incident) }}"
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                            Edit Incident
                        </a>
                        <a href="{{ route('incidents.index') }}"
                            class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                            Back to List
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>