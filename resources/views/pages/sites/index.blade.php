<x-app-layout>
     <style>
        /* hover effect to all table rows */
        table tbody tr:hover {
            background-color: #f3f4f6;
            cursor: pointer;
        }


        table tbody tr:hover td {
            color: #111827; 
        }
    </style>
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">Sites</h2>
                <p class="mt-1 text-sm text-gray-600">List of all guard hiring sites.</p>
            </div>
            <a href="{{ route('sites.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                + Add Site
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 border border-green-300 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto bg-white rounded shadow">
          <div style="height: calc(100vh - 250px); overflow: auto;">
            <table class="min-w-full text-sm text-left text-gray-600">
                <thead class="bg-gray-100 text-gray-800 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="sticky top-0 z-10 bg-gray-50 py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900 sm:pl-6">Name</th>
                        <th class="sticky top-0 z-10 bg-gray-50 py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900 sm:pl-6">Location</th>
                        <th class="sticky top-0 z-10 bg-gray-50 py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900 sm:pl-6">Contact Person</th>
                        <th class="sticky top-0 z-10 bg-gray-50 py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900 sm:pl-6">Phone</th>
                        <th class="sticky top-0 z-10 bg-gray-50 py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900 sm:pl-6">Start Date</th>
                        <th class="sticky top-0 z-10 bg-gray-50 py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900 sm:pl-6">No. Of Guards</th>
                        <th class="sticky top-0 z-10 bg-gray-50 py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900 sm:pl-6">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($sites as $site)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $site->name }}</td>
                            <td class="px-4 py-3">{{ $site->location }}</td>
                            <td class="px-4 py-3">{{ $site->contact_person }}</td>
                            <td class="px-4 py-3">{{ $site->contact_number }}</td>
                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($site->start_date)->format('Y-m-d') }}</td>
                            <td class="px-4 py-3">{{ $site->no_of_guards }}</td>

                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('sites.assign', $site->id) }}"
                                   class="text-blue-600 hover:text-blue-900 mr-4">Assign Guards</a>

                                <a href="{{ route('sites.edit', $site->id) }}"
                                   class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                                <a href="{{ route('sites.view', $site->id) }}"
                                   class="text-indigo-600 hover:text-indigo-900 mr-4">View</a>
                                <form action="{{ route('sites.destroy', $site->id) }}" method="POST" class="inline-block"
                                      onsubmit="return confirm('Are you sure you want to delete this site?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-4 text-center text-gray-500">No sites found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
          </div>
        </div>
    </div>
</x-app-layout>
