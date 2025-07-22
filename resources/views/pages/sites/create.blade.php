<x-app-layout>
    <div class="max-w-5xl mx-auto py-10 sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-semibold text-gray-700 mb-6">Add New Guard Site</h2>

            <form method="POST" action="{{ route('sites.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Site Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                    <input type="text" name="location" id="location" value="{{ old('location') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                    @error('location')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="contact_person" class="block text-sm font-medium text-gray-700">Contact Person</label>
                    <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                    @error('contact_person')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="contact_number" class="block text-sm font-medium text-gray-700">Contact Number</label>
                    <input type="text" name="contact_number" id="contact_number" value="{{ old('contact_number') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                    @error('contact_number')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                    <textarea name="address" id="address" required
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                    <input type="text" name="city" id="city" value="{{ old('city') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                    @error('city')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                    @error('start_date')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="no_of_guards" class="block text-sm font-medium text-gray-700">Number of Guards</label>
                    <input type="number" name="no_of_guards" id="no_of_guards" value="{{ old('no_of_guards') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                    @error('no_of_guards')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="no_day_shifts" class="block text-sm font-medium text-gray-700">Day Shifts</label>
                    <input type="number" name="no_day_shifts" id="no_day_shifts" value="{{ old('no_day_shifts') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                    @error('no_day_shifts')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="no_night_shifts" class="block text-sm font-medium text-gray-700">Night Shifts</label>
                    <input type="number" name="no_night_shifts" id="no_night_shifts" value="{{ old('no_night_shifts') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                    @error('no_night_shifts')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>


                <div class="md:col-span-2">
                    <div class="form-check">
                        <input type="checkbox" name="has_special_ot_hours" id="has_special_ot_hours" value="1"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                            @if (old('has_special_ot_hours')) checked @endif onclick="toggleSpecialOtRate()">
                        <label for="has_special_ot_hours" class="ml-2 text-sm font-medium text-gray-700">
                            Special Overtime Rate
                        </label>
                    </div>
                    @error('has_special_ot_hours')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2" id="special_ot_rate_container"
                    style="display: {{ old('has_special_ot_hours') ? 'block' : 'none' }}">
                    <label for="special_ot_rate" class="block text-sm font-medium text-gray-700">Special OT Rate (per
                        hour)</label>
                    <input type="number" step="0.01" name="special_ot_rate" id="special_ot_rate"
                        value="{{ old('special_ot_rate', 200) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                    @error('special_ot_rate')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Rank Rates</h3>
                    <div id="rank-rates-container">

                    </div>
                    <button type="button" onclick="addRankRate()"
                        class="mt-2 inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Add Rank Rate
                    </button>
                </div>
                <div class="md:col-span-2">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Save Site
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
    function toggleSpecialOtRate() {
        const container = document.getElementById('special_ot_rate_container');
        const checkbox = document.getElementById('has_special_ot_hours');
        container.style.display = checkbox.checked ? 'block' : 'none';
    }

     //To add Rank Rates
        function addRankRate() {
        const container = document.getElementById('rank-rates-container');
        const index = container.children.length;

        const rankDiv = document.createElement('div');
        rankDiv.className = 'rank-rate-entry mb-4 p-3 border rounded';
        rankDiv.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="ranks_${index}_rank" class="block text-sm font-medium text-gray-700">Rank</label>
                    <select name="ranks[${index}][rank]" id="ranks_${index}_rank" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                        <option value="">Select Rank</option>
                        <option value="SSO">SSO</option>
                        <option value="OIC">OIC</option>
                        <option value="LSO">LSO</option>
                        <option value="JSO">JSO</option>
                        <option value="CSO">CSO</option>
                    </select>
                </div>
                <div>
                    <label for="ranks_${index}_site_shift_rate" class="block text-sm font-medium text-gray-700">Site Shift Rate</label>
                    <input type="number" step="0.01" name="ranks[${index}][site_shift_rate]" id="ranks_${index}_site_shift_rate" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                </div>
                <div>
                    <label for="ranks_${index}_guard_shift_rate" class="block text-sm font-medium text-gray-700">Guard Shift Rate</label>
                    <input type="number" step="0.01" name="ranks[${index}][guard_shift_rate]" id="ranks_${index}_guard_shift_rate" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                </div>
            </div>
            <button type="button" onclick="this.parentElement.remove()"
                    class="mt-2 inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                Remove
            </button>
        `;

        container.appendChild(rankDiv);
    }

    // to add one rank rate by default
    document.addEventListener('DOMContentLoaded', function() {
        addRankRate();
    });
</script>
</x-app-layout>
