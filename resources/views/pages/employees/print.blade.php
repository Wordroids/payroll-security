<!DOCTYPE html>
<html>
<head>
    <title>Guards List</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #111827;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .info {
            margin-bottom: 20px;
            text-align: center;
            font-size: 14px;
            color: #374151;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            table-layout: fixed;
        }
        th {
            background-color: #f9fafb;
            text-align: left;
            padding: 12px 16px;
            border: 1px solid #e5e7eb;
            font-weight: 600;
            color: #111827;
            font-size: 0.875rem;
        }
        td {
            padding: 12px 16px;
            border: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 0.875rem;
            word-wrap: break-word;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 12px;
            color: #6b7280;
        }


        .col-empno { width: 80px; }
        .col-name { width: 150px; }
        .col-rank { width: 100px; }
        .col-phone { width: 120px; }
        .col-address { width: 200px; }
        .col-nic { width: 120px; }
        .col-dob { width: 100px; }
        .col-doh { width: 100px; }
    </style>
</head>
<body>
    <h1>Guards List</h1>
    <div class="info">
        Generated on: {{ now()->format('Y-m-d') }}
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-empno">Emp No</th>
                <th class="col-name">Name</th>
                <th class="col-rank">Rank</th>
                <th class="col-phone">Phone</th>
                <th class="col-address">Address</th>
                <th class="col-nic">NIC</th>
                <th class="col-dob">Date of Birth</th>
                <th class="col-doh">Date of Hire</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($employees as $employee)
                <tr>
                    <td>{{ $employee->emp_no }}</td>
                    <td>{{ $employee->name }}</td>
                    <td>{{ $employee->rank }}</td>
                    <td>{{ $employee->phone }}</td>
                    <td>{{ $employee->address }}</td>
                    <td>{{ $employee->nic }}</td>
                    <td>{{ \Carbon\Carbon::parse($employee->date_of_birth)->format('Y-m-d') }}</td>
                    <td>{{ \Carbon\Carbon::parse($employee->date_of_hire)->format('Y-m-d') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">No employees found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Total Guards: {{ $employees->count() }}
    </div>
</body>
</html>
