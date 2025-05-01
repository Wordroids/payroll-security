<x-app-layout>
    <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-base font-semibold text-gray-900">Users</h1>
                    <p class="mt-2 text-sm text-gray-700">
                        A list of all the Users in your system including their name, phone, address, NIC and dates.
                    </p>
                </div>
                <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                    <a href="{{ route('employees.create') }}"
                       class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        Add User
                    </a>
                </div>
            </div>

            <div class="mt-8 flow-root">
                <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                        <div class="overflow-hidden shadow-sm ring-1 ring-black/5 sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900 sm:pl-6">ID</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Name</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Email</th>
                                        <th class="relative py-3.5 pr-4 pl-3 sm:pr-6">
                                            <span class="sr-only">Edit</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @forelse ($users as $user)
                                        <tr>
                                            <td class="py-4 pr-3 pl-4 text-sm font-medium text-gray-900 whitespace-nowrap sm:pl-6">
                                                {{ $user->id }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                {{ $user->name }}
                                            </td>

                                            <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                                {{ $user->email }}
                                            </td>
                                           
                                            <td class="relative py-4 pr-4 pl-3 text-right text-sm font-medium whitespace-nowrap sm:pr-6">
                                                <a href="{{ route('users.edit', $user->id) }}"
                                                   class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                                            
                                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline-block"
                                                      onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                                </form>
                                            </td>
                                            
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                                No employees found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
