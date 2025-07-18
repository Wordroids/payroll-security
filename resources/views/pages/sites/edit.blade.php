<x-app-layout>
    <div class="max-w-5xl mx-auto py-10 sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-semibold text-gray-700 mb-6">Edit Guard Site</h2>

            <form method="POST" action="{{ route('sites.update', $site->id) }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Site Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $site->name) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                </div>

                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                    <input type="text" name="location" id="location" value="{{ old('location', $site->location) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                </div>

                <div>
                    <label for="contact_person" class="block text-sm font-medium text-gray-700">Contact Person</label>
                    <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person', $site->contact_person) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                </div>

                <div>
                    <label for="contact_number" class="block text-sm font-medium text-gray-700">Contact Number</label>
                    <input type="text" name="contact_number" id="contact_number" value="{{ old('contact_number', $site->contact_number) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $site->email) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                    <textarea name="address" id="address" required
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">{{ old('address', $site->address) }}</textarea>
                </div>

                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                    <input type="text" name="city" id="city" value="{{ old('city', $site->city) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                </div>

                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $site->start_date) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                </div>

                <div>
                    <label for="no_of_guards" class="block text-sm font-medium text-gray-700">Number of Guards</label>
                    <input type="number" name="no_of_guards" id="no_of_guards" value="{{ old('no_of_guards', $site->no_of_guards) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                </div>

                <div>
                    <label for="no_day_shifts" class="block text-sm font-medium text-gray-700">Day Shifts</label>
                    <input type="number" name="no_day_shifts" id="no_day_shifts" value="{{ old('no_day_shifts', $site->no_day_shifts) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                </div>

                <div>
                    <label for="no_night_shifts" class="block text-sm font-medium text-gray-700">Night Shifts</label>
                    <input type="number" name="no_night_shifts" id="no_night_shifts" value="{{ old('no_night_shifts', $site->no_night_shifts) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                </div>

                <div>
                    <label for="site_shift_rate" class="block text-sm font-medium text-gray-700">Site Shift Rate</label>
                    <input type="number" step="0.01" name="site_shift_rate" id="site_shift_rate" value="{{ old('site_shift_rate', $site->site_shift_rate) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                </div>

                <div>
                    <label for="guard_shift_rate" class="block text-sm font-medium text-gray-700">Guard Shift Rate</label>
                    <input type="number" step="0.01" name="guard_shift_rate" id="guard_shift_rate" value="{{ old('guard_shift_rate', $site->guard_shift_rate) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                </div>

                <div class="md:col-span-2">
                    <input type="hidden" name="has_special_ot_hours" value="0">
                    <div class="form-check">
                        <input type="checkbox" name="has_special_ot_hours" id="has_special_ot_hours" value="1"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                            @if (old('has_special_ot_hours', $site->has_special_ot_hours)) checked @endif onclick="toggleSpecialOtRate()">
                        <label for="has_special_ot_hours" class="ml-2 text-sm font-medium text-gray-700">
                            Special overtime Rate
                        </label>
                    </div>
                    @error('has_special_ot_hours')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2" id="special_ot_rate_container"
                    style="display: {{ old('has_special_ot_hours', $site->has_special_ot_hours) ? 'block' : 'none' }}">
                    <label for="special_ot_rate" class="block text-sm font-medium text-gray-700">Special OT Rate (per
                        hour)</label>
                    <input type="number" step="0.01" name="special_ot_rate" id="special_ot_rate"
                        value="{{ old('special_ot_rate', $site->special_ot_rate ?? 200) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                    @error('special_ot_rate')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Update Site
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleSpecialOtRate() {
            const container = document.getElementById('special_ot_rate_container');
            const checkbox = document.getElementById('has_special_ot_hours');
            const rateField = document.getElementById('special_ot_rate');

            container.style.display = checkbox.checked ? 'block' : 'none';

            if (!checkbox.checked) {
                rateField.value = '';
            } else if (!rateField.value) {
                rateField.value = '200';
            }
        }
    </script>
</x-app-layout>
