<x-app-layout>
    <div style="padding:1rem; border: 2px dashed #e5e7eb; border-radius: 0.5rem;">
        <div style="padding-left: 1rem; padding-right: 1rem;">
            <div style="display: flex; flex-direction: column; align-items: flex-start;">
                <div style="flex: 1 1 auto;">
                    <h1 style="font-size: 1rem; font-weight: 600; color: #111827;">Guards</h1>
                    <p style="margin-top: 0.5rem; font-size: 0.875rem; color: #374151;">
                        A list of all the Guards in your system including their name, phone, address, NIC and dates.
                    </p>
                </div>
                <div style="margin-top: 1rem; margin-left: 0;">
                    <a href="{{ route('employees.create') }}"
                        style="display: block; border-radius: 0.375rem; background-color: #4f46e5; padding: 0.5rem 0.75rem; text-align: center; font-size: 0.875rem; font-weight: 600; color: white; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
                        Add Guard
                    </a>
                </div>
            </div>

            <div style="margin-top: 2rem; height: calc(100vh - 235px); display: flex; flex-direction: column;">
                <div style="flex: 1; overflow: auto; position: relative;">
                    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; overflow: auto;">
                    <div style="display: flex; min-width: max-content;">
                            <!-- Fixed columns (Emp No and Name) -->
                            <div style="position: sticky; left: 0; z-index: 20; background-color: white;">
                                <table style="border-collapse: collapse;">
                                    <thead style="background-color: #f9fafb; position: sticky; top: 0; z-index: 30;">
                                        <tr>
                                            <th
                                                style="padding: 0.875rem 0.75rem 0.875rem 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: #111827; width: 100px; border-bottom: 1px solid #e5e7eb;">
                                                Emp No</th>
                                            <th
                                                style="padding: 0.875rem 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: #111827; width: 150px; border-bottom: 1px solid #e5e7eb;">
                                                Name</th>
                                    </tr>
                                </thead>
                                <tbody style="background-color: white;">
                                    @forelse ($employees->sortBy('emp_no') as $employee)
                                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                                <td
                                                    style="padding: 1rem 0.75rem 1rem 1rem; font-size: 0.875rem; font-weight: 500; color: #111827; white-space: nowrap; background-color: white;">
                                                {{ $employee->emp_no }}
                                            </td>
                                                <td style="padding: 1rem 1rem; font-size: 0.875rem; color: #6b7280; white-space: nowrap; background-color: white;">
                                                {{ $employee->name }}
                                            </td>
                                            </tr>
                                        @empty
                                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                                <td colspan="2"
                                                    style="padding: 1rem 1.5rem; text-align: center; font-size: 0.875rem; color: #6b7280; background-color: white;">
                                                    No employees found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Scrollable columns -->
                            <div>
                                <table style="border-collapse: collapse;">
                                    <thead style="background-color: #f9fafb; position: sticky; top: 0; z-index: 10;">
                                        <tr>
                                            <th
                                                style="padding: 0.875rem 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: #111827; min-width: 120px; border-bottom: 1px solid #e5e7eb;">
                                                Rank</th>
                                            <th
                                                style="padding: 0.875rem 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: #111827; min-width: 120px; border-bottom: 1px solid #e5e7eb;">
                                                Phone</th>
                                            <th
                                                style="padding: 0.875rem 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: #111827; min-width: 180px; border-bottom: 1px solid #e5e7eb;">
                                                Address</th>
                                            <th
                                                style="padding: 0.875rem 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: #111827; min-width: 120px; border-bottom: 1px solid #e5e7eb;">
                                                NIC</th>
                                            <th
                                                style="padding: 0.875rem 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: #111827; min-width: 120px; border-bottom: 1px solid #e5e7eb;">
                                                Date of Birth</th>
                                            <th
                                                style="padding: 0.875rem 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: #111827; min-width: 120px; border-bottom: 1px solid #e5e7eb;">
                                                Date of Hire</th>
                                            <th
                                                style="padding: 0.875rem 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: #111827; min-width: 120px; border-bottom: 1px solid #e5e7eb;">
                                                Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody style="background-color: white;">
                                        @forelse ($employees->sortBy('emp_no') as $employee)
                                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                                <td
                                                    style="padding: 1rem 1rem; font-size: 0.875rem; color: #6b7280; white-space: nowrap;">
                                                {{ $employee->rank }}
                                            </td>
                                                <td
                                                    style="padding: 1rem 1rem; font-size: 0.875rem; color: #6b7280; white-space: nowrap;">
                                                {{ $employee->phone }}
                                            </td>
                                                <td
                                                    style="padding: 1rem 1rem; font-size: 0.875rem; color: #6b7280; white-space: nowrap;">
                                                {{ $employee->address }}
                                            </td>
                                                <td
                                                    style="padding: 1rem 1rem; font-size: 0.875rem; color: #6b7280; white-space: nowrap;">
                                                {{ $employee->nic }}
                                            </td>
                                                <td
                                                    style="padding: 1rem 1rem; font-size: 0.875rem; color: #6b7280; white-space: nowrap;">
                                                {{ \Carbon\Carbon::parse($employee->date_of_birth)->format('Y-m-d') }}
                                            </td>
                                                <td
                                                    style="padding: 1rem 1rem; font-size: 0.875rem; color: #6b7280; white-space: nowrap;">
                                                {{ \Carbon\Carbon::parse($employee->date_of_hire)->format('Y-m-d') }}
                                            </td>
                                                <td
                                                    style="padding: 1rem 1rem; font-size: 0.875rem; font-weight: 500; text-align: right; white-space: nowrap;">
                                                <a href="{{ route('employees.edit', $employee->id) }}"
                                                        style="color: #4f46e5; margin-right: 1rem;">Edit</a>
                                                <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" style="display: inline-block;"
                                                      onsubmit="return confirm('Are you sure you want to delete this employee?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" style="color: #dc2626;">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                                <td colspan="7"
                                                    style="padding: 1rem 1.5rem; text-align: center; font-size: 0.875rem; color: #6b7280;">
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
    </div>
</x-app-layout>
