@extends('admin.layouts.app')

@section('title', 'Users Management')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <style>
        .dataTables_wrapper .dataTables_length select {
            @apply pr-8 py-1 rounded-lg border-gray-200 text-sm focus:ring-rose-500 focus:border-rose-500;
        }
        .dataTables_wrapper .dataTables_filter input {
            @apply px-4 py-2 rounded-xl border-gray-100 bg-white/50 text-sm focus:ring-rose-500 focus:border-rose-500 transition-all;
        }
        table.dataTable {
            @apply border-collapse !important;
        }
        table.dataTable thead th {
            @apply border-b border-gray-100 bg-white/30 backdrop-blur-sm px-8 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display !important;
        }
        table.dataTable tbody td {
            @apply border-b border-gray-50 px-8 py-6 text-sm text-gray-600 !important;
        }
        table.dataTable.no-footer {
            @apply border-b-0 !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current, 
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            @apply bg-rose-500 border-rose-500 text-white !important;
            border-radius: 0.75rem !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            @apply border-0 !important;
            border-radius: 0.75rem !important;
        }
    </style>
@endpush

@section('content')
<div class="section-container section-padding">
    <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-6">
        <div>
            <h1 class="text-4xl md:text-5xl font-display font-medium uppercase text-gray-900 mb-2 tracking-wide">
                Users
            </h1>
            <p class="text-gray-500 font-light text-lg">
                Manage admin, content managers, and customers.
            </p>
        </div>
        <a href="{{ route('admin.users.create') }}"
            class="px-6 py-3 bg-gray-900 hover:bg-rose-500 text-white rounded-2xl font-display font-medium uppercase tracking-wider text-xs transition-all duration-300 shadow-lg shadow-gray-200 hover:shadow-rose-200 inline-flex items-center gap-2 group">
            <span>Add User</span>
            <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
        </a>
    </div>

    @if (session('success'))
        <div class="glass-panel border-l-4 border-emerald-500 text-emerald-800 px-6 py-4 rounded-2xl mb-8 flex items-center gap-3 animate-fade-in-up">
            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <span class="font-medium font-sans">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="glass-panel border-l-4 border-red-500 text-red-800 px-6 py-4 rounded-2xl mb-8 flex items-center gap-3 animate-fade-in-up">
            <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <span class="font-medium font-sans">{{ session('error') }}</span>
        </div>
    @endif

    <div class="glass-panel rounded-3xl p-4 mb-8 animate-fade-in-up">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="relative group flex-1">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 group-focus-within:text-rose-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" id="search-input"
                    placeholder="Search by name, username, or email..."
                    class="block w-full pl-11 pr-4 py-3 bg-white/50 border-0 rounded-2xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-rose-200 focus:bg-white transition-all duration-300">
            </div>

            <div class="relative">
                <select id="role-filter"
                    class="block w-full md:w-48 pl-4 pr-10 py-3 bg-white/50 border-0 rounded-2xl text-gray-600 focus:ring-2 focus:ring-rose-200 focus:bg-white transition-all duration-300 appearance-none cursor-pointer">
                    <option value="">All Roles</option>
                    <option value="admin">Admin</option>
                    <option value="content_manager">Content Manager</option>
                    <option value="user">User</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button id="reset-filters"
                    class="px-4 py-3 flex items-center justify-center text-gray-400 hover:text-rose-500 transition-colors bg-white/50 rounded-2xl"
                    title="Reset Filters">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div class="glass-panel rounded-[2rem] overflow-hidden shadow-sm animate-fade-in-up">
        <div class="overflow-x-auto p-4">
            <table id="users-table" class="w-full">
                <thead>
                    <tr>
                        <th class="px-8 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display">User</th>
                        <th class="px-6 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display">Email</th>
                        <th class="px-6 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display">WhatsApp</th>
                        <th class="px-6 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display">Role</th>
                        <th class="px-6 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display">Joined</th>
                        <th class="px-6 py-6 text-right text-xs font-bold text-gray-400 uppercase tracking-widest font-display">Actions</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        const table = $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.users.index') }}",
                data: function (d) {
                    d.role = $('#role-filter').val();
                }
            },
            columns: [
                { data: 'user_info', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'whatsapp', name: 'whatsapp' },
                { data: 'role_label', name: 'role' },
                { data: 'joined_at', name: 'created_at' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-right' }
            ],
            order: [[4, 'desc']],
            dom: '<"flex flex-col md:flex-row justify-between items-center mb-4 gap-4"l><"relative"rt><"flex flex-col md:flex-row justify-between items-center mt-4 gap-4"ip>',
            language: {
                search: "",
                searchPlaceholder: "Search...",
                lengthMenu: "_MENU_ entries per page",
                paginate: {
                    previous: '<i class="fa fa-chevron-left text-xs"></i>',
                    next: '<i class="fa fa-chevron-right text-xs"></i>'
                }
            },
            drawCallback: function() {
                $('.dataTables_paginate .paginate_button').addClass('px-3 py-1 mx-1 transition-all duration-300');
            }
        });

        // Custom search input
        $('#search-input').on('keyup', function() {
            table.search(this.value).draw();
        });

        // Role filter change
        $('#role-filter').on('change', function() {
            table.draw();
        });

        // Reset filters
        $('#reset-filters').on('click', function() {
            $('#search-input').val('');
            $('#role-filter').val('');
            table.search('').draw();
        });
    });
</script>
@endpush
