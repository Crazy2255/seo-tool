@extends('layouts.app')

@section('title', 'Competitor Strategy Analyzer')

@section('content')
<div>
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Competitor Strategy Analyzer</h1>
                <p class="mt-2 text-gray-600">Analyze competitor websites to uncover their marketing strategies and tools</p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('competitor-analysis.history') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-history mr-2"></i>
                    Analysis History
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-chart-line text-blue-500 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Analyses</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalAnalyses }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-trophy text-green-500 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Avg Strategy Score</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $avgStrategyScore }}/100</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-tools text-purple-500 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Tools Found</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalToolsFound }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-link text-red-500 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Backlinks Found</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalBacklinks }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-key text-yellow-500 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Keywords Found</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalKeywords }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Analysis Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Analyze Competitor Website</h2>
        
        <form id="competitorAnalysisForm" class="space-y-4">
            <div>
                <label for="competitor_url" class="block text-sm font-medium text-gray-700 mb-2">
                    Website URL
                </label>
                <div class="flex space-x-4">
                    <input type="url" 
                           id="competitor_url" 
                           name="url" 
                           class="flex-1 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="https://example.com" 
                           required>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-medium transition-colors">
                        <i class="fas fa-search mr-2"></i>
                        <span id="analyzeButtonText">Analyze</span>
                    </button>
                </div>
            </div>
            
            <div id="analysisProgress" class="hidden">
                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                    <div class="flex items-center">
                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600 mr-3"></div>
                        <span class="text-blue-700">Analyzing competitor website... This may take a few moments.</span>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Recent Analyses -->
    @if($recentAnalyses->count() > 0)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Recent Analyses</h2>
        
        <div class="space-y-4">
            @foreach($recentAnalyses as $analysis)
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="flex-1">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-globe text-blue-600"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ $analysis->title ?: $analysis->domain }}</h3>
                            <p class="text-sm text-gray-600">{{ $analysis->domain }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center space-x-6">
                    <div class="text-center">
                        <p class="text-sm text-gray-500">Strategy Score</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $analysis->strategy_score }}/100</p>
                    </div>
                    
                    <div class="text-center">
                        <p class="text-sm text-gray-500">Tools Found</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $analysis->total_tools_detected }}</p>
                    </div>
                    
                    <div class="text-center">
                        <p class="text-sm text-gray-500">Backlinks</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $analysis->total_backlinks ?? 0 }}</p>
                    </div>
                    
                    <div class="text-center">
                        <p class="text-sm text-gray-500">Keywords</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $analysis->total_keywords ?? 0 }}</p>
                    </div>
                    
                    <div class="text-center">
                        <p class="text-sm text-gray-500">Last Scanned</p>
                        <p class="text-sm text-gray-900">{{ $analysis->time_since_last_scan }}</p>
                    </div>
                    
                    <div class="flex space-x-2">
                        <a href="{{ route('competitor-analysis.show', $analysis->id) }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm transition-colors">
                            View Details
                        </a>
                        @auth
                        <button onclick="rescanAnalysis({{ $analysis->id }})" 
                                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm transition-colors">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                        <button onclick="deleteAnalysis({{ $analysis->id }})" 
                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-md text-sm transition-colors">
                            <i class="fas fa-trash"></i>
                        </button>
                        @else
                        <a href="{{ route('login') }}" 
                           class="bg-gray-400 text-white px-4 py-2 rounded-md text-sm transition-colors" 
                           title="Please log in to access these features">
                            <i class="fas fa-lock"></i>
                        </a>
                        @endauth
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-search text-gray-400 text-2xl"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No analyses yet</h3>
        <p class="text-gray-600">Start by analyzing your first competitor website above.</p>
    </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('competitorAnalysisForm');
    const analyzeButton = form.querySelector('button[type="submit"]');
    const analyzeButtonText = document.getElementById('analyzeButtonText');
    const progressDiv = document.getElementById('analysisProgress');
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const url = form.querySelector('input[name="url"]').value;
        
        if (!url) {
            alert('Please enter a valid URL');
            return;
        }
        
        // Show loading state
        analyzeButton.disabled = true;
        analyzeButtonText.textContent = 'Analyzing...';
        progressDiv.classList.remove('hidden');
        
        try {
            const response = await fetch('{{ route("competitor-analysis.analyze") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ url: url })
            });
            
            const data = await response.json();
            
            if (data.success) {
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    alert('Analysis completed successfully!');
                    window.location.reload();
                }
            } else {
                alert('Analysis failed: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred during analysis. Please try again.');
        } finally {
            // Reset loading state
            analyzeButton.disabled = false;
            analyzeButtonText.textContent = 'Analyze';
            progressDiv.classList.add('hidden');
        }
    });
});

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
