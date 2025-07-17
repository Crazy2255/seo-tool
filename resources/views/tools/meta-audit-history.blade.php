@extends('layouts.app')

@section('title', 'Recent Meta Tag Audits - SEO Audit Pro')
@section('page-title', 'Recent Meta Tag Audits')

@section('content')
<div>
    <!-- Header -->
    <div class="mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Recent Meta Tag Audits</h1>
                    <p class="text-gray-600">View your recent meta tag analyses (last 30 days) and track improvements over time.</p>
                    <div class="mt-2 flex items-center text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Showing audits from the last 30 days (maximum 50 results)
                    </div>
                </div>
                <div class="hidden lg:block">
                    <div class="w-16 h-16 bg-purple-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-history text-purple-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="flex flex-col sm:flex-row gap-4 mb-8">
        <a href="{{ route('tools.meta-analyzer') }}" class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 flex items-center justify-center">
            <i class="fas fa-plus mr-2"></i>
            New Analysis
        </a>
        <button onclick="exportHistory()" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center justify-center">
            <i class="fas fa-download mr-2"></i>
            Export Recent
        </button>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Recent Audits</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $audits->total() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-blue-600 text-lg"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Avg Score</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($audits->avg('score'), 0) }}</p>
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
                    <p class="text-3xl font-bold text-gray-900">{{ $audits->where('analyzed_at', '>=', now()->startOfMonth())->count() }}</p>
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
                    <p class="text-3xl font-bold text-gray-900">{{ $audits->max('score') ?? 0 }}</p>
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
            <h3 class="text-lg font-semibold text-gray-900">Recent Audit History</h3>
            <p class="text-sm text-gray-500 mt-1">Showing audits from the last 30 days</p>
        </div>

        @if($audits->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left py-3 px-6 font-semibold text-gray-900">Website</th>
                            <th class="text-center py-3 px-4 font-semibold text-gray-900">Score</th>
                            <th class="text-center py-3 px-4 font-semibold text-gray-900">Status</th>
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
                                            @if($audit->score >= 80) border-green-500 text-green-600
                                            @elseif($audit->score >= 60) border-yellow-500 text-yellow-600
                                            @else border-red-500 text-red-600
                                            @endif">
                                            <span class="text-sm font-bold">{{ $audit->score }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium
                                        @if($audit->score >= 80) bg-green-100 text-green-800
                                        @elseif($audit->score >= 60) bg-yellow-100 text-yellow-800
                                        @elseif($audit->score >= 40) bg-orange-100 text-orange-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ $audit->getStatusText() }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <span class="text-gray-900 font-medium">
                                        {{ count($audit->issues_found ?? []) }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <div class="text-sm text-gray-900">
                                        {{ $audit->analyzed_at->format('M j, Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $audit->analyzed_at->format('g:i A') }}
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('meta-analyzer.show', $audit->id) }}" 
                                           class="text-primary-600 hover:text-primary-800 p-1">
                                            <i class="fas fa-eye" title="View Details"></i>
                                        </a>
                                        <a href="{{ route('meta-analyzer.pdf', $audit->id) }}" 
                                           class="text-green-600 hover:text-green-800 p-1">
                                            <i class="fas fa-download" title="Download PDF"></i>
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
                <h3 class="text-lg font-medium text-gray-900 mb-2">No recent audits found</h3>
                <p class="text-gray-500 mb-6">You haven't run any meta tag analyses in the last 30 days.</p>
                <a href="{{ route('tools.meta-analyzer') }}" 
                   class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                    <i class="fas fa-plus mr-2"></i>
                    Run New Analysis
                </a>
            </div>
        @endif
    </div>
</div>

<script>
function exportHistory() {
    // Create CSV content for recent audits
    const audits = @json($audits->items());
    const csvContent = generateHistoryCSV(audits);
    
    // Download CSV
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `meta-audit-recent-history-${new Date().toISOString().split('T')[0]}.csv`;
    link.click();
    window.URL.revokeObjectURL(url);
}

function generateHistoryCSV(audits) {
    const headers = ['URL', 'Title', 'Score', 'Status', 'Issues Count', 'Title Length', 'Meta Description Length', 'Date'];
    const rows = audits.map(audit => [
        audit.url,
        audit.title || 'No Title',
        audit.score,
        getStatusText(audit.score),
        audit.issues_found ? audit.issues_found.length : 0,
        audit.title_length || 0,
        audit.meta_description_length || 0,
        new Date(audit.analyzed_at).toLocaleDateString()
    ]);
    
    return [headers, ...rows].map(row => row.map(field => `"${field || ''}"`).join(',')).join('\n');
}

function getStatusText(score) {
    if (score >= 80) return 'Excellent';
    if (score >= 60) return 'Good';
    if (score >= 40) return 'Needs Improvement';
    return 'Poor';
}

async function deleteAudit(auditId) {
    if (!confirm('Are you sure you want to delete this audit? This action cannot be undone.')) {
        return;
    }
    
    try {
        const response = await fetch(`/api/meta-analyzer/audit/${auditId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Reload the page to update the list
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
