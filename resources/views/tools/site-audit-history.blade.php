@extends('layouts.app')

@section('title', 'Site Audit History - SEO Audit Pro')
@section('page-title', 'Site Audit History')

@section('content')
<div>
    <!-- Header -->
    <div class="mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Site Audit History</h1>
                    <p class="text-gray-600">View your previous site audits and track SEO improvements over time.</p>
                </div>
                <div class="hidden lg:block">
                    <div class="w-16 h-16 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-search text-blue-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="flex flex-col sm:flex-row gap-4 mb-8">
        <a href="{{ route('tools.site-audit') }}" class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 flex items-center justify-center">
            <i class="fas fa-plus mr-2"></i>
            New Site Audit
        </a>
        <button onclick="exportHistory()" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center justify-center">
            <i class="fas fa-download mr-2"></i>
            Export All
        </button>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Audits</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $audits->total() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-search text-blue-600 text-lg"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Avg Score</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $audits->count() > 0 ? number_format($audits->avg('audit_score'), 0) : 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-trophy text-green-600 text-lg"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">This Month</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $audits->where('created_at', '>=', now()->startOfMonth())->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar text-purple-600 text-lg"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Best Score</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $audits->count() > 0 ? $audits->max('audit_score') : 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-star text-yellow-600 text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Audit History Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Audit History</h3>
        </div>

        @if($audits->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left py-3 px-6 font-semibold text-gray-900">Website</th>
                            <th class="text-center py-3 px-4 font-semibold text-gray-900">Score</th>
                            <th class="text-center py-3 px-4 font-semibold text-gray-900">Status</th>
                            <th class="text-center py-3 px-4 font-semibold text-gray-900">Load Time</th>
                            <th class="text-center py-3 px-4 font-semibold text-gray-900">Issues</th>
                            <th class="text-center py-3 px-4 font-semibold text-gray-900">Date</th>
                            <th class="text-center py-3 px-4 font-semibold text-gray-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($audits as $audit)
                            <tr class="hover:bg-gray-50">
                                <td class="py-4 px-6">
                                    <div>
                                        <div class="font-medium text-gray-900">
                                            {{ $audit->title ?: 'No Title' }}
                                        </div>
                                        <div class="text-sm text-gray-500 break-all">
                                            {{ $audit->url }}
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <div class="flex items-center justify-center">
                                        <div class="w-12 h-12 rounded-full border-4 flex items-center justify-center
                                            @if($audit->audit_score >= 80) border-green-500 text-green-600
                                            @elseif($audit->audit_score >= 60) border-yellow-500 text-yellow-600
                                            @else border-red-500 text-red-600
                                            @endif">
                                            <span class="text-sm font-bold">{{ $audit->audit_score }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium
                                        @if($audit->status_code == 200) bg-green-100 text-green-800
                                        @elseif($audit->status_code >= 300 && $audit->status_code < 400) bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ $audit->status_code }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <span class="text-gray-900 font-medium">
                                        {{ $audit->page_load_speed }}ms
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <span class="text-gray-900 font-medium">
                                        {{ is_array($audit->recommendations) ? count($audit->recommendations) : 0 }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <div class="text-sm text-gray-900">
                                        {{ $audit->created_at->format('M j, Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $audit->created_at->format('g:i A') }}
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('site-audit.show', $audit->id) }}" 
                                           class="text-primary-600 hover:text-primary-800 p-1">
                                            <i class="fas fa-eye" title="View Details"></i>
                                        </a>
                                        <button onclick="deleteAudit({{ $audit->id }})" 
                                                class="text-red-600 hover:text-red-800 p-1">
                                            <i class="fas fa-trash" title="Delete"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $audits->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-search text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No audits found</h3>
                <p class="text-gray-500 mb-6">You haven't run any site audits yet.</p>
                <a href="{{ route('tools.site-audit') }}" 
                   class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                    <i class="fas fa-plus mr-2"></i>
                    Run Your First Audit
                </a>
            </div>
        @endif
    </div>
</div>

<script>
function exportHistory() {
    const audits = @json($audits->items());
    const csvContent = generateHistoryCSV(audits);
    
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `site-audit-history-${new Date().toISOString().split('T')[0]}.csv`;
    link.click();
    window.URL.revokeObjectURL(url);
}

function generateHistoryCSV(audits) {
    const headers = ['URL', 'Title', 'Score', 'Status Code', 'Load Time', 'Issues Count', 'Date'];
    const rows = audits.map(audit => [
        audit.url,
        audit.title || 'No Title',
        audit.audit_score,
        audit.status_code,
        audit.page_load_speed + 'ms',
        audit.recommendations ? audit.recommendations.length : 0,
        new Date(audit.created_at).toLocaleDateString()
    ]);
    
    return [headers, ...rows].map(row => row.map(field => `"${field || ''}"`).join(',')).join('\n');
}

async function deleteAudit(auditId) {
    if (!confirm('Are you sure you want to delete this audit? This action cannot be undone.')) {
        return;
    }
    
    try {
        const response = await fetch(`/api/site-audit/${auditId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            window.location.reload();
        } else {
            alert('Error deleting audit: ' + (data.message || 'Unknown error'));
        }
    } catch (error) {
        alert('Error deleting audit: ' + error.message);
    }
}
</script>
@endsection
