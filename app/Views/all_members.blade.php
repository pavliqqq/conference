@extends('layout')

@section('content')

    <div class="max-w-4xl mx-auto mt-10 p-6 bg-white shadow rounded">
        <h2 class="text-xl text-center font-semibold mb-4">All Members</h2>

        <table class="w-full border border-gray-300 rounded overflow-hidden">
            <thead class="bg-gray-100">
            <tr>
                <th class="p-3 text-left border-b">Photo</th>
                <th class="p-3 text-left border-b">Full Name</th>
                <th class="p-3 text-left border-b">Report Subject</th>
                <th class="p-3 text-left border-b">Email</th>
            </tr>
            </thead>
            <tbody id="members-table-body">
            @foreach($members as $member)
                <tr class="border-b">
                    <td class="p-3">
                        <img src="{{ $member['photo'] }}" alt="{{ $member['full_name'] }}"
                             class="h-12 w-12 object-cover rounded-full">
                    </td>
                    <td class="p-3">{{ $member['full_name'] }}</td>
                    <td class="p-3">{{ $member['report_subject'] }}</td>
                    <td class="p-3">
                        <a href="https://mail.google.com/mail/?view=cm&fs=1&to={{ $member['email'] }}" target="_blank" class="text-blue-600 hover:underline">
                            {{ $member['email'] }}
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection