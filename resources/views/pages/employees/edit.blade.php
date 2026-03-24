<x-app-layout>
    <div class="max-w-4xl mx-auto py-10 sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-semibold text-gray-700 mb-6">Edit Employee</h2>

            <form action="{{ route('employees.update', $employee->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="emp_no" class="block text-sm font-medium text-gray-700">Employee No</label>
                    <input type="text" name="emp_no" id="emp_no"
                        value="{{ old('emp_no', $employee->emp_no) }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" id="name"
                        value="{{ old('name', $employee->name) }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                    <input type="text" name="phone" id="phone"
                        value="{{ old('phone', $employee->phone) }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                    <textarea name="address" id="address"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        required>{{ old('address', $employee->address) }}</textarea>
                </div>

                <div>
                    <label for="nic" class="block text-sm font-medium text-gray-700">NIC</label>
                    <input type="text" name="nic" id="nic"
                        value="{{ old('nic', $employee->nic) }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                </div>

                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                    <input type="date" name="date_of_birth" id="date_of_birth"
                        value="{{ old('date_of_birth', $employee->date_of_birth) }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                </div>

                <div>
                    <label for="date_of_hire" class="block text-sm font-medium text-gray-700">Date of Hire</label>
                    <input type="date" name="date_of_hire" id="date_of_hire"
                        value="{{ old('date_of_hire', $employee->date_of_hire) }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                </div>

                {{-- Basic Salary --}}
                <div>
                    <label for="basic_salary" class="block text-sm font-medium text-gray-700">Basic Salary</label>
                    <input type="number" name="basic_salary" id="basic_salary" step="0.01"
                        value="{{ old('basic_salary', $employee->basic_salary ?? '') }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Leave empty to use default">
                </div>

                {{-- Attendance Bonus --}}
                <div>
                    <label for="attendance_bonus" class="block text-sm font-medium text-gray-700">Attendance
                        Bonus</label>
                    <input type="number" name="attendance_bonus" id="attendance_bonus" step="0.01"
                        value="{{ old('attendance_bonus', $employee->attendance_bonus ?? '') }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Leave empty to use default">
                </div>

                <div class="pt-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="include_epf_etf" id="include_epf_etf" value="1"
                            {{ old('include_epf_etf', $employee->include_epf_etf) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <label for="include_epf_etf" class="ml-2 block text-sm text-gray-700">
                            Include EPF & ETF in salary calculations
                        </label>
                    </div>
                </div>
                {{-- Rank --}}
                <div>
                    <label for="rank" class="block text-sm font-medium text-gray-700">Rank</label>
                    <select name="rank" id="rank"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                        <option value="">Select Rank</option>
                        <option value="SSO" {{ $employee->rank == 'SSO' ? 'selected' : '' }}>SSO</option>
                        <option value="OIC" {{ $employee->rank == 'OIC' ? 'selected' : '' }}>OIC</option>
                        <option value="LSO" {{ $employee->rank == 'LSO' ? 'selected' : '' }}>LSO</option>
                        <option value="JSO" {{ $employee->rank == 'JSO' ? 'selected' : '' }}>JSO</option>
                        <option value="CSO" {{ $employee->rank == 'CSO' ? 'selected' : '' }}>CSO</option>
                        <option value="OM" {{$employee->rank == 'OM' ? 'selected' : '' }}>OM</option>
                        <option value="VO" {{ $employee->rank== 'VO' ? 'selected' : '' }}>VO</option>
                        <option value="AOM" {{ $employee->rank== 'AOM' ? 'selected' : '' }}>AOM</option>
                        <option value="BDM" {{ $employee->rank == 'BDM' ? 'selected' : '' }}>BDM</option>
                    </select>
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Update Employee
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
