<x-app-layout>
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">Guard Sites</h2>
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
            <table class="min-w-full text-sm text-left text-gray-600">
                <thead class="bg-gray-100 text-gray-800 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Location</th>
                        <th class="px-4 py-3">Contact Person</th>
                        <th class="px-4 py-3">Phone</th>
                        <th class="px-4 py-3">Start Date</th>
                        <th class="px-4 py-3 ">No. Of Guards</th>
                        <th class="px-4 py-3 ">Site Rate</th>
                        <th class="px-4 py-3 ">Guard Rate</th>
                        <th class="px-4 py-3 text-center">Actions</th>
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
                            <td class="px-4 py-3">Rs.{{ $site->site_shift_rate }}.00</td>
                            <td class="px-4 py-3">Rs.{{ $site->guard_shift_rate }}.00</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('sites.assign', $site->id) }}"
                                   class="text-blue-600 hover:text-blue-900 mr-4">Assign Guards</a>
                                   
                                <a href="{{ route('sites.edit', $site->id) }}"
                                   class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>

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
</x-app-layout>
