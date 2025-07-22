<x-app-layout>
    <div class="max-w-4xl mx-auto px-6 py-10">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">
            Assign Guards to Site
        </h1>
        <div class="bg-white shadow rounded-xl p-6">
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-700">
                    Site: <span class="text-indigo-600">{{ $site->name }}</span>
                </h2>
                <p class="text-sm text-gray-500 mt-1">Location: {{ $site->location }}</p>
            </div>

            <form method="POST" action="{{ route('sites.assign.store', $site->id) }}">
                @csrf

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Guards to Assign</label>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 gap-3 max-h-96 overflow-y-auto border rounded p-4">
                        @forelse($employees->sortBy('emp_no') as $emp)
                            <div class="border rounded p-3 hover:bg-gray-50">
                                <div class="flex items-start space-x-3">
                                <input type="checkbox" name="employee_ids[]"
                                       value="{{ $emp->id }}"
                                       {{ in_array($emp->id, $assigned) ? 'checked' : '' }}
                                       class="mt-1 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                                <div class="flex-1">
                                        <div class="text-sm font-medium text-gray-700">
                                            {{ $emp->emp_no }} - {{ $emp->name }}
                                        </div>
                                        <div class="mt-2">
                                            <label for="rank_{{ $emp->id }}" class="block text-xs text-gray-500 mb-1">Rank</label>
                                            <select name="ranks[{{ $emp->id }}]" id="rank_{{ $emp->id }}"
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                <option value="CSO" {{ (isset($emp->pivot->rank) && $emp->pivot->rank == 'CSO') ? 'selected' : '' }}>CSO</option>
                                                <option value="JSO" {{ (isset($emp->pivot->rank) && $emp->pivot->rank == 'JSO') ? 'selected' : '' }}>JSO</option>
                                                <option value="LSO" {{ (isset($emp->pivot->rank) && $emp->pivot->rank == 'LSO') ? 'selected' : '' }}>LSO</option>
                                                <option value="OIC" {{ (isset($emp->pivot->rank) && $emp->pivot->rank == 'OIC') ? 'selected' : '' }}>OIC</option>
                                                <option value="SSO" {{ (isset($emp->pivot->rank) && $emp->pivot->rank == 'SSO') ? 'selected' : '' }}>SSO</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm">No employees found.</p>
                        @endforelse
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white font-medium text-sm rounded-md shadow hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Save Assignments
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
