@extends('layouts.app')

@section('title', 'Competitor Analysis History')

@section('content')
<div>
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
                    <a href="{{ route('competitor-analysis.index') }}" class="hover:text-blue-600">Competitor Analysis</a>
                    <span>/</span>
                    <span class="text-gray-900">History</span>
                </nav>
                <h1 class="text-3xl font-bold text-gray-900">Analysis History</h1>
                <p class="mt-2 text-gray-600">View and manage your competitor analysis history</p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('competitor-analysis.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    New Analysis
                </a>
            </div>
        </div>
    </div>

    @if($analyses->count() > 0)
    <!-- Analysis List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Your Competitor Analyses</h2>
        </div>
        
        <div class="divide-y divide-gray-200">
            @foreach($analyses as $analysis)
            <div class="p-6 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-globe text-blue-600"></i>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">
                                    {{ $analysis->title ?: $analysis->domain }}
                                </h3>
                                <p class="text-sm text-gray-600">{{ $analysis->url }}</p>
                                <div class="flex items-center space-x-4 mt-2">
                                    <span class="text-sm text-gray-500">
                                        <i class="fas fa-calendar-alt mr-1"></i>
                                        {{ $analysis->last_scanned_at ? $analysis->last_scanned_at->format('M d, Y') : 'Never' }}
                                    </span>
                                    <span class="text-sm text-gray-500">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ $analysis->time_since_last_scan }}
                                    </span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                        {{ $analysis->scan_status === 'completed' ? 'bg-green-100 text-green-800' : 
                                           ($analysis->scan_status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ ucfirst($analysis->scan_status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-8">
                        <div class="text-center">
                            <p class="text-sm text-gray-500">Strategy Score</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $analysis->strategy_score ?: 0 }}/100</p>
                        </div>
                        
                        <div class="text-center">
                            <p class="text-sm text-gray-500">Tools Found</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $analysis->total_tools_detected ?: 0 }}</p>
                        </div>
                        
                        <div class="text-center">
                            <p class="text-sm text-gray-500">Strategies</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $analysis->strategy_summary_count ?: 0 }}</p>
                        </div>
                        
                        <div class="flex flex-col space-y-2">
                            @if($analysis->scan_status === 'completed')
                            <a href="{{ route('competitor-analysis.show', $analysis->id) }}" 
                               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm transition-colors text-center">
                                View Details
                            </a>
                            @endif
                            
                            <div class="flex space-x-2">
                                <button onclick="rescanAnalysis({{ $analysis->id }})" 
                                        class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-md text-sm transition-colors">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                                @if($analysis->scan_status === 'completed')
                                <a href="{{ route('competitor-analysis.export', $analysis->id) }}" 
                                   class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-md text-sm transition-colors">
                                    <i class="fas fa-download"></i>
                                </a>
                                @endif
                                <button onclick="deleteAnalysis({{ $analysis->id }})" 
                                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-md text-sm transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if($analysis->scan_status === 'failed' && $analysis->scan_error)
                <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-md">
                    <p class="text-sm text-red-700">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Error: {{ $analysis->scan_error }}
                    </p>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        @if($analyses->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $analyses->links() }}
        </div>
        @endif
    </div>
    @else
    <!-- Empty State -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-search text-gray-400 text-2xl"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No competitor analyses yet</h3>
        <p class="text-gray-600 mb-4">Start analyzing your competitors to gain valuable insights about their marketing strategies.</p>
        <a href="{{ route('competitor-analysis.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors">
            <i class="fas fa-plus mr-2"></i>
            Start Your First Analysis
        </a>
    </div>
    @endif
</div>

<script>
async function rescanAnalysis(analysisId) {
    if (!confirm('Are you sure you want to rescan this analysis?')) {
        return;
    }
    
    try {
        const response = await fetch(`/api/competitor-analysis/${analysisId}/rescan`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Analysis updated successfully!');
            window.location.reload();
        } else {
            alert('Rescan failed: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred during rescan. Please try again.');
    }
}

async function deleteAnalysis(analysisId) {
    if (!confirm('Are you sure you want to delete this analysis? This action cannot be undone.')) {
        return;
    }
    
    try {
        const response = await fetch(`/api/competitor-analysis/${analysisId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Analysis deleted successfully!');
            window.location.reload();
        } else {
            alert('Delete failed: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred during deletion. Please try again.');
    }
}
</script>
@endsection
