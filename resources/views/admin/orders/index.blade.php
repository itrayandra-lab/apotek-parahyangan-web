@extends('admin.layouts.app')

@section('title', 'Orders Management')

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
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-6">
        <div>
            <h1 class="text-4xl md:text-5xl font-display font-medium uppercase text-gray-900 mb-2 tracking-wide">
                Orders
            </h1>
            <p class="text-gray-500 font-light text-lg">
                Monitor and manage customer orders and payments.
            </p>
        </div>
    </div>

    <!-- Filters Toolbar -->
    <div class="glass-panel rounded-3xl p-4 mb-8 animate-fade-in-up" style="animation-delay: 0.1s;">
        <div class="flex flex-col md:flex-row gap-4">
            
            <!-- Search -->
            <div class="relative flex-1 group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 group-focus-within:text-rose-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input
                    type="text"
                    id="search-input"
                    value="{{ $filters['search'] ?? '' }}"
                    placeholder="Search order # or customer..."
                    class="block w-full pl-11 pr-4 py-3 bg-white/50 border-0 rounded-2xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-rose-200 focus:bg-white transition-all duration-300"
                >
            </div>

            <!-- Order Status Filter -->
            <div class="relative min-w-[180px]">
                <select
                    id="status-filter"
                    class="block w-full pl-4 pr-10 py-3 bg-white/50 border-0 rounded-2xl text-gray-600 focus:ring-2 focus:ring-rose-200 focus:bg-white transition-all duration-300 appearance-none cursor-pointer"
                >
                    <option value="">All Statuses</option>
                    @foreach(['pending_payment','confirmed','processing','shipped','delivered','cancelled','expired'] as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>
                            {{ ucwords(str_replace('_', ' ', $status)) }}
                        </option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            <!-- Payment Status Filter -->
            <div class="relative min-w-[180px]">
                <select
                    id="payment-status-filter"
                    class="block w-full pl-4 pr-10 py-3 bg-white/50 border-0 rounded-2xl text-gray-600 focus:ring-2 focus:ring-rose-200 focus:bg-white transition-all duration-300 appearance-none cursor-pointer"
                >
                    <option value="">Payment Status</option>
                    @foreach(['unpaid','paid','failed','expired','refunded'] as $paymentStatus)
                        <option value="{{ $paymentStatus }}" @selected(($filters['payment_status'] ?? '') === $paymentStatus)>
                            {{ ucwords(str_replace('_', ' ', $paymentStatus)) }}
                        </option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-2">
                <button id="reset-filters" class="px-4 py-3 flex items-center justify-center text-gray-400 hover:text-rose-500 transition-colors bg-white/50 rounded-2xl border border-transparent hover:border-rose-100" title="Reset">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="glass-panel rounded-[2rem] overflow-hidden shadow-sm animate-fade-in-up" style="animation-delay: 0.2s;">
        <div class="overflow-x-auto p-4">
            <table id="orders-table" class="w-full">
                <thead>
                    <tr>
                        <th class="px-8 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display">Order #</th>
                        <th class="px-6 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display">Customer</th>
                        <th class="px-6 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display">Total</th>
                        <th class="px-6 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display">Status</th>
                        <th class="px-6 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display">Payment</th>
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
        const table = $('#orders-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.orders.index') }}",
                data: function (d) {
                    d.status = $('#status-filter').val();
                    d.payment_status = $('#payment-status-filter').val();
                    d.search_val = $('#search-input').val();
                }
            },
            columns: [
                { 
                    data: 'order_number', 
                    name: 'order_number',
                    render: function(data) {
                        return `<div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-500 font-mono text-xs font-bold shadow-inner">#</div>
                                    <span class="text-sm font-bold text-gray-900 font-mono tracking-wide">${data}</span>
                                </div>`;
                    }
                },
                { data: 'customer', name: 'user.name' },
                { data: 'total', name: 'total' },
                { data: 'status_label', name: 'status', orderable: false, searchable: false },
                { data: 'payment_label', name: 'payment_status', orderable: false, searchable: false },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-right' }
            ],
            order: [[0, 'desc']],
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

        // Filter changes
        $('#status-filter, #payment-status-filter').on('change', function() {
            table.draw();
        });

        // Reset filters
        $('#reset-filters').on('click', function() {
            $('#search-input').val('');
            $('#status-filter').val('');
            $('#payment-status-filter').val('');
            table.search('').draw();
        });
    });
</script>
@endpush