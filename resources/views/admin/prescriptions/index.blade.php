@extends('admin.layouts.app')

@section('title', 'Prescription Management')

@section('content')
<div class="section-container section-padding">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-6">
        <div>
            <h1 class="text-4xl md:text-5xl font-display font-medium uppercase text-gray-900 mb-2 tracking-wide">
                Prescriptions
            </h1>
            <p class="text-gray-500 font-light text-lg">
                Verify and manage customer prescription uploads.
            </p>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="glass-panel rounded-3xl p-2 mb-8 animate-fade-in-up inline-flex gap-2" style="animation-delay: 0.1s;">
        <a href="{{ route('admin.prescriptions.index', ['status' => 'pending']) }}" 
           class="px-6 py-3 rounded-2xl font-display font-medium uppercase tracking-wider text-xs transition-all duration-300 {{ $status === 'pending' ? 'bg-gray-900 text-white shadow-lg' : 'text-gray-600 hover:bg-white/50' }}">
            Pending ({{ \App\Models\Prescription::where('status', 'pending')->count() }})
        </a>
        <a href="{{ route('admin.prescriptions.index', ['status' => 'verified']) }}" 
           class="px-6 py-3 rounded-2xl font-display font-medium uppercase tracking-wider text-xs transition-all duration-300 {{ $status === 'verified' ? 'bg-gray-900 text-white shadow-lg' : 'text-gray-600 hover:bg-white/50' }}">
            Verified
        </a>
        <a href="{{ route('admin.prescriptions.index', ['status' => 'rejected']) }}" 
           class="px-6 py-3 rounded-2xl font-display font-medium uppercase tracking-wider text-xs transition-all duration-300 {{ $status === 'rejected' ? 'bg-gray-900 text-white shadow-lg' : 'text-gray-600 hover:bg-white/50' }}">
            Rejected
        </a>
        <a href="{{ route('admin.prescriptions.index', ['status' => 'all']) }}" 
           class="px-6 py-3 rounded-2xl font-display font-medium uppercase tracking-wider text-xs transition-all duration-300 {{ $status === 'all' ? 'bg-gray-900 text-white shadow-lg' : 'text-gray-600 hover:bg-white/50' }}">
            All
        </a>
    </div>

    @if(session('success'))
        <div class="glass-panel rounded-3xl p-4 mb-6 bg-green-50 border-l-4 border-green-500 animate-fade-in-up">
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Data Table -->
    <div class="glass-panel rounded-[2rem] overflow-hidden shadow-sm animate-fade-in-up" style="animation-delay: 0.2s;">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100 bg-white/30 backdrop-blur-sm">
                        <th class="px-8 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display">ID</th>
                        <th class="px-6 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display">Patient</th>
                        <th class="px-6 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display">Image</th>
                        <th class="px-6 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display">Status</th>
                        <th class="px-6 py-6 text-left text-xs font-bold text-gray-400 uppercase tracking-widest font-display">Uploaded</th>
                        <th class="px-8 py-6 text-right text-xs font-bold text-gray-400 uppercase tracking-widest font-display">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($prescriptions as $prescription)
                        <tr class="group hover:bg-rose-50/40 transition-colors duration-300">
                            <!-- ID -->
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-500 font-mono text-xs font-bold shadow-inner">
                                        #
                                    </div>
                                    <span class="text-sm font-bold text-gray-900 font-mono tracking-wide">
                                        {{ $prescription->id }}
                                    </span>
                                </div>
                            </td>

                            <!-- Patient -->
                            <td class="px-6 py-6">
                                <div>
                                    <div class="text-sm font-bold text-gray-900 mb-0.5">{{ $prescription->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $prescription->user->email }}</div>
                                    @if($prescription->user->whatsapp)
                                        <div class="text-xs text-gray-500 flex items-center gap-1 mt-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                            </svg>
                                            {{ $prescription->user->whatsapp }}
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Image Preview -->
                            <td class="px-6 py-6">
                                <img 
                                    src="{{ $prescription->image_url }}" 
                                    alt="Prescription" 
                                    class="w-16 h-16 object-cover rounded-lg shadow-md cursor-pointer hover:scale-110 transition-transform"
                                    onclick="window.open('{{ $prescription->image_url }}', '_blank')"
                                >
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-6">
                                @if($prescription->status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-600 border border-amber-100">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Pending
                                    </span>
                                @elseif($prescription->status === 'verified')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-600 border border-emerald-100">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Verified
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-rose-50 text-rose-600 border border-rose-100">
                                        Rejected
                                    </span>
                                @endif
                            </td>

                            <!-- Uploaded Date -->
                            <td class="px-6 py-6">
                                <div class="text-sm text-gray-900">{{ $prescription->created_at->format('d M Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $prescription->created_at->format('H:i') }}</div>
                            </td>

                            <!-- Actions -->
                            <td class="px-8 py-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.prescriptions.show', $prescription) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-400 hover:text-rose-500 hover:border-rose-200 hover:bg-rose-50 transition-all shadow-sm group-hover:bg-white group-hover:border-gray-300"
                                       title="View & Verify">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>

                                    @if($prescription->order)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-blue-50 text-blue-600 border border-blue-100">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            Order
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-24 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-6">
                                        <svg class="w-10 h-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-bold font-display text-gray-900 mb-2">No Prescriptions Found</h3>
                                    <p class="text-gray-500 mb-0 font-light">No prescriptions matching the selected status.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($prescriptions->hasPages())
            <div class="px-8 py-6 border-t border-gray-100 bg-gray-50/50">
                {{ $prescriptions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
