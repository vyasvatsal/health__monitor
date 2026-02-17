@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#1e293b] overflow-hidden shadow-xl sm:rounded-lg border border-slate-700">
                <div class="p-6 text-white">
                    <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Report New Incident
                    </h2>

                    <form action="{{ route('incidents.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-slate-300 mb-2">Incident Title</label>
                            <input type="text" name="title" id="title" required
                                class="w-full bg-[#0f172a] border border-slate-600 rounded-lg p-2.5 text-white focus:ring-red-500 focus:border-red-500"
                                placeholder="e.g., Database Connection Failure">
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="severity" class="block text-sm font-medium text-slate-300 mb-2">Severity</label>
                                <select name="severity" id="severity" required
                                    class="w-full bg-[#0f172a] border border-slate-600 rounded-lg p-2.5 text-white focus:ring-red-500 focus:border-red-500">
                                    <option value="minor">Minor Issue</option>
                                    <option value="major">Major Outage</option>
                                    <option value="critical">Critical Failure</option>
                                    <option value="maintenance">Scheduled Maintenance</option>
                                </select>
                            </div>
                            <div>
                                <label for="status" class="block text-sm font-medium text-slate-300 mb-2">Initial
                                    Status</label>
                                <select name="status" id="status" required
                                    class="w-full bg-[#0f172a] border border-slate-600 rounded-lg p-2.5 text-white focus:ring-red-500 focus:border-red-500">
                                    <option value="investigating">Investigating</option>
                                    <option value="identified">Identified</option>
                                    <option value="monitoring">Monitoring</option>
                                    <option value="resolved">Resolved</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="description" class="block text-sm font-medium text-slate-300 mb-2">Description /
                                Update</label>
                            <textarea name="description" id="description" rows="4" required
                                class="w-full bg-[#0f172a] border border-slate-600 rounded-lg p-2.5 text-white focus:ring-red-500 focus:border-red-500"
                                placeholder="Describe the issue and current impact..."></textarea>
                        </div>

                        <div class="flex justify-end gap-3">
                            <a href="{{ route('dashboard') }}"
                                class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition-colors">
                                Cancel
                            </a>
                            <button type="submit"
                                class="px-4 py-2 bg-red-600 hover:bg-red-500 text-white font-bold rounded-lg transition-colors shadow-lg shadow-red-500/20">
                                Create Incident
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection