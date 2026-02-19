<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Incidents') }}
            </h2>
            <a href="{{ route('incidents.create') }}"
                class="px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Report Incident
            </a>
        </div>
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

                    @if($incidents->count() > 0)
                        <div class="relative overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Title</th>
                                        <th scope="col" class="px-6 py-3">Severity</th>
                                        <th scope="col" class="px-6 py-3">Status</th>
                                        <th scope="col" class="px-6 py-3">Reported At</th>
                                        <th scope="col" class="px-6 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($incidents as $incident)
                                        <tr
                                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                            <th scope="row"
                                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                <a href="{{ route('incidents.show', $incident) }}"
                                                    class="hover:underline hover:text-blue-500">
                                                    {{ $incident->title }}
                                                </a>
                                            </th>
                                            <td class="px-6 py-4">
                                                @php
                                                    $severityColors = [
                                                        'critical' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                                        'major' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
                                                        'minor' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                                        'maintenance' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                                    ];
                                                    $color = $severityColors[$incident->severity] ?? 'bg-gray-100 text-gray-800';
                                                @endphp
                                                <span
                                                    class="{{ $color }} text-xs font-medium mr-2 px-2.5 py-0.5 rounded uppercase">
                                                    {{ $incident->severity }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                @php
                                                    $statusColors = [
                                                        'open' => 'bg-red-100 text-red-800',
                                                        'investigating' => 'bg-purple-100 text-purple-800',
                                                        'identified' => 'bg-yellow-100 text-yellow-800',
                                                        'monitoring' => 'bg-blue-100 text-blue-800',
                                                        'resolved' => 'bg-green-100 text-green-800',
                                                    ];
                                                    $statusColor = $statusColors[$incident->status] ?? 'bg-gray-100 text-gray-800';
                                                @endphp
                                                <span
                                                    class="{{ $statusColor }} text-xs font-medium mr-2 px-2.5 py-0.5 rounded capitalize">
                                                    {{ str_replace('_', ' ', $incident->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                {{ $incident->created_at->diffForHumans() }}
                                            </td>
                                            <td class="px-6 py-4 text-right space-x-2">
                                                <a href="{{ route('incidents.edit', $incident) }}"
                                                    class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>

                                                <form action="{{ route('incidents.destroy', $incident) }}" method="POST"
                                                    class="inline-block"
                                                    onsubmit="return confirm('Are you sure you want to delete this incident?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="font-medium text-red-600 dark:text-red-500 hover:underline">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-10">
                            <div
                                class="bg-gray-100 dark:bg-gray-700 rounded-full h-20 w-20 flex items-center justify-center mx-auto mb-4">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">No incidents reported</h3>
                            <p class="mt-1 text-gray-500 dark:text-gray-400">Everything is running smoothly! Use the button
                                above to report an issue if one arises.</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>