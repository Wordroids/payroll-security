<x-app-layout>
    <div class="max-w-5xl mx-auto py-10 sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded shadow">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold text-gray-700">Site Details</h2>
                <div class="space-x-2">
                    <a href="{{ route('sites.edit', $site->id) }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Edit Site
                    </a>
                    <a href="{{ route('sites.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-white hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Back to Sites
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Site Name</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $site->name }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Location</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $site->location }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Contact Person</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $site->contact_person }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Contact Number</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $site->contact_number }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Email</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $site->email ?? 'N/A' }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Address</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $site->address }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">City</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $site->city ?? 'N/A' }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Start Date</h3>
                   <p class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($site->start_date)->format('M d, Y') }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Number of Guards</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $site->no_of_guards }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Day Shifts</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $site->no_day_shifts ?? 'N/A' }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Night Shifts</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $site->no_night_shifts ?? 'N/A' }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Special OT Rate</h3>
                    <p class="mt-1 text-sm text-gray-900">
                        {{ $site->has_special_ot_hours ? 'Rs. ' . number_format($site->special_ot_rate, 2) . '/hr' : 'No' }}
                    </p>
                </div>
            </div>

            <div class="mt-8">
                <h3 class="text-lg font-medium text-gray-700 mb-4">Rank Rates</h3>

                @if($site->rankRates->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Site Shift Rate</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guard Shift Rate</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($site->rankRates as $rate)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $rate->rank }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rs. {{ number_format($rate->site_shift_rate, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rs. {{ number_format($rate->guard_shift_rate, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-500">No rank rates defined for this site.</p>
                @endif
            </div>

             <!--  Assigned Guards  -->
            <div class="mt-8">
               <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-700">Assigned Guards</h3>
                    <a href="{{ route('sites.assign', $site->id) }}"
                       class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Manage Gaurds
                    </a>
                </div>

                @if($site->employees->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($site->employees as $employee)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $employee->emp_no }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $employee->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $employee->pivot->rank ?? 'Not assigned' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $employee->phone ?? 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <p class="text-sm text-gray-500">No guards are currently assigned to this site.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
