<x-app-layout>
    <div class="max-w-3xl mx-auto py-10">
        <h2 class="text-xl font-semibold mb-4">Assign Guards to Site: {{ $site->name }}</h2>

        <form method="POST" action="{{ route('sites.assign.store', $site->id) }}" class="bg-white p-6 rounded shadow">
            @csrf

            <div class="mb-4">
                <label class="block font-medium text-sm text-gray-700">Select Guards</label>
                <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach($employees as $emp)
                        <label class="inline-flex items-center space-x-2">
                            <input type="checkbox" name="employee_ids[]"
                                   value="{{ $emp->id }}"
                                   {{ in_array($emp->id, $assigned) ? 'checked' : '' }}
                                   class="rounded border-gray-300 shadow-sm">
                            <span>{{ $emp->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-500">
                Save Assignments
            </button>
        </form>
    </div>
</x-app-layout>
