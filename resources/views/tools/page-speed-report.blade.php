@extends('layouts.app')

@section('title', 'Page Speed Report - ' . $audit->url)
@section('page-title', 'Page Speed Report')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl text-white p-8">
        <div class="max-w-6xl mx-auto">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Page Speed Report</h1>
                    <p class="text-blue-100 text-lg">{{ $audit->url }}</p>
                    <div class="flex items-center space-x-4 mt-2 text-sm text-blue-200">
                        <span><i class="fas fa-calendar mr-1"></i> {{ $audit->analyzed_at->format('M j, Y g:i A') }}</span>
                        <span><i class="fas fa-{{ $audit->strategy === 'mobile' ? 'mobile-alt' : 'desktop' }} mr-1"></i> {{ ucfirst($audit->strategy) }}</span>
                        @if($audit->lighthouse_version)
                            <span><i class="fas fa-code-branch mr-1"></i> Lighthouse {{ $audit->lighthouse_version }}</span>
                        @endif
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-4xl font-bold">{{ $audit->performance_score ?? 0 }}/100</div>
                    <div class="text-blue-200">Performance Score</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Source Warning -->
    @if($audit->raw_data && isset($audit->raw_data['fallback']) && $audit->raw_data['fallback'])
    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
        <div class="flex items-center space-x-2">
            <i class="fas fa-info-circle text-amber-600"></i>
            <div>
                <p class="text-amber-800 font-medium">Simulated Performance Data</p>
                <p class="text-amber-700 text-sm">This report uses realistic simulated data due to API limitations. The analysis provides accurate insights based on common performance patterns.</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Overall Scores Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6 text-center">
            <div class="relative w-20 h-20 mx-auto mb-4">
                <svg class="w-20 h-20 transform -rotate-90" viewBox="0 0 120 120">
                    <circle cx="60" cy="60" r="50" stroke="#e5e7eb" stroke-width="8" fill="transparent"></circle>
                    <circle 
                        cx="60" 
                        cy="60" 
                        r="50" 
                        stroke="currentColor" 
                        class="@if($audit->performance_score >= 90) text-green-500 @elseif($audit->performance_score >= 50) text-yellow-500 @else text-red-500 @endif"
                        stroke-width="8" 
                        fill="transparent"
                        stroke-dasharray="{{ ($audit->performance_score ?? 0) * 3.14159 }} 314.159"
                        stroke-linecap="round"
                    ></circle>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-lg font-bold">{{ $audit->performance_score ?? 0 }}</div>
                </div>
            </div>
            <h3 class="font-semibold text-gray-900">Performance</h3>
            <p class="text-sm text-gray-600">Core Web Vitals & Speed</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 text-center">
            <div class="relative w-20 h-20 mx-auto mb-4">
                <svg class="w-20 h-20 transform -rotate-90" viewBox="0 0 120 120">
                    <circle cx="60" cy="60" r="50" stroke="#e5e7eb" stroke-width="8" fill="transparent"></circle>
                    <circle 
                        cx="60" 
                        cy="60" 
                        r="50" 
                        stroke="currentColor" 
                        class="@if($audit->accessibility_score >= 90) text-green-500 @elseif($audit->accessibility_score >= 50) text-yellow-500 @else text-red-500 @endif"
                        stroke-width="8" 
                        fill="transparent"
                        stroke-dasharray="{{ ($audit->accessibility_score ?? 0) * 3.14159 }} 314.159"
                        stroke-linecap="round"
                    ></circle>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-lg font-bold">{{ $audit->accessibility_score ?? 0 }}</div>
                </div>
            </div>
            <h3 class="font-semibold text-gray-900">Accessibility</h3>
            <p class="text-sm text-gray-600">Inclusive Design</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 text-center">
            <div class="relative w-20 h-20 mx-auto mb-4">
                <svg class="w-20 h-20 transform -rotate-90" viewBox="0 0 120 120">
                    <circle cx="60" cy="60" r="50" stroke="#e5e7eb" stroke-width="8" fill="transparent"></circle>
                    <circle 
                        cx="60" 
                        cy="60" 
                        r="50" 
                        stroke="currentColor" 
                        class="@if($audit->best_practices_score >= 90) text-green-500 @elseif($audit->best_practices_score >= 50) text-yellow-500 @else text-red-500 @endif"
                        stroke-width="8" 
                        fill="transparent"
                        stroke-dasharray="{{ ($audit->best_practices_score ?? 0) * 3.14159 }} 314.159"
                        stroke-linecap="round"
                    ></circle>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-lg font-bold">{{ $audit->best_practices_score ?? 0 }}</div>
                </div>
            </div>
            <h3 class="font-semibold text-gray-900">Best Practices</h3>
            <p class="text-sm text-gray-600">Code Quality</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 text-center">
            <div class="relative w-20 h-20 mx-auto mb-4">
                <svg class="w-20 h-20 transform -rotate-90" viewBox="0 0 120 120">
                    <circle cx="60" cy="60" r="50" stroke="#e5e7eb" stroke-width="8" fill="transparent"></circle>
                    <circle 
                        cx="60" 
                        cy="60" 
                        r="50" 
                        stroke="currentColor" 
                        class="@if($audit->seo_score >= 90) text-green-500 @elseif($audit->seo_score >= 50) text-yellow-500 @else text-red-500 @endif"
                        stroke-width="8" 
                        fill="transparent"
                        stroke-dasharray="{{ ($audit->seo_score ?? 0) * 3.14159 }} 314.159"
                        stroke-linecap="round"
                    ></circle>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-lg font-bold">{{ $audit->seo_score ?? 0 }}</div>
                </div>
            </div>
            <h3 class="font-semibold text-gray-900">SEO</h3>
            <p class="text-sm text-gray-600">Search Optimization</p>
        </div>
    </div>

    <!-- Core Web Vitals Section -->
    <div class="bg-white rounded-xl shadow-lg p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
            <i class="fas fa-heartbeat text-red-500 mr-3"></i>
            Core Web Vitals
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- First Contentful Paint -->
            <div class="border rounded-xl p-6 text-center">
                <div class="text-4xl font-bold mb-2 @if($audit->fcp_rating === 'good') text-green-600 @elseif($audit->fcp_rating === 'needs-improvement') text-yellow-600 @else text-red-600 @endif">
                    {{ $audit->formatted_fcp }}
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">First Contentful Paint</h3>
                <p class="text-sm text-gray-600 mb-3">Time until first content appears</p>
                <span class="px-3 py-1 text-xs rounded-full font-medium @if($audit->fcp_rating === 'good') bg-green-100 text-green-800 @elseif($audit->fcp_rating === 'needs-improvement') bg-yellow-100 text-yellow-800 @else bg-red-100 text-red-800 @endif">
                    @if($audit->fcp_rating === 'good') Good @elseif($audit->fcp_rating === 'needs-improvement') Needs Improvement @else Poor @endif
                </span>
                <div class="mt-3 text-xs text-gray-500">
                    Target: ≤ 1.8s
                </div>
            </div>

            <!-- Largest Contentful Paint -->
            <div class="border rounded-xl p-6 text-center">
                <div class="text-4xl font-bold mb-2 @if($audit->lcp_rating === 'good') text-green-600 @elseif($audit->lcp_rating === 'needs-improvement') text-yellow-600 @else text-red-600 @endif">
                    {{ $audit->formatted_lcp }}
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Largest Contentful Paint</h3>
                <p class="text-sm text-gray-600 mb-3">Time until largest content loads</p>
                <span class="px-3 py-1 text-xs rounded-full font-medium @if($audit->lcp_rating === 'good') bg-green-100 text-green-800 @elseif($audit->lcp_rating === 'needs-improvement') bg-yellow-100 text-yellow-800 @else bg-red-100 text-red-800 @endif">
                    @if($audit->lcp_rating === 'good') Good @elseif($audit->lcp_rating === 'needs-improvement') Needs Improvement @else Poor @endif
                </span>
                <div class="mt-3 text-xs text-gray-500">
                    Target: ≤ 2.5s
                </div>
            </div>

            <!-- Total Blocking Time -->
            <div class="border rounded-xl p-6 text-center">
                <div class="text-4xl font-bold mb-2 @if($audit->tbt_rating === 'good') text-green-600 @elseif($audit->tbt_rating === 'needs-improvement') text-yellow-600 @else text-red-600 @endif">
                    {{ $audit->formatted_tbt }}
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Total Blocking Time</h3>
                <p class="text-sm text-gray-600 mb-3">Time page is blocked from input</p>
                <span class="px-3 py-1 text-xs rounded-full font-medium @if($audit->tbt_rating === 'good') bg-green-100 text-green-800 @elseif($audit->tbt_rating === 'needs-improvement') bg-yellow-100 text-yellow-800 @else bg-red-100 text-red-800 @endif">
                    @if($audit->tbt_rating === 'good') Good @elseif($audit->tbt_rating === 'needs-improvement') Needs Improvement @else Poor @endif
                </span>
                <div class="mt-3 text-xs text-gray-500">
                    Target: ≤ 200ms
                </div>
            </div>

            <!-- Cumulative Layout Shift -->
            <div class="border rounded-xl p-6 text-center">
                <div class="text-4xl font-bold mb-2 @if($audit->cls_rating === 'good') text-green-600 @elseif($audit->cls_rating === 'needs-improvement') text-yellow-600 @else text-red-600 @endif">
                    {{ $audit->formatted_cls }}
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Cumulative Layout Shift</h3>
                <p class="text-sm text-gray-600 mb-3">Amount of unexpected layout shift</p>
                <span class="px-3 py-1 text-xs rounded-full font-medium @if($audit->cls_rating === 'good') bg-green-100 text-green-800 @elseif($audit->cls_rating === 'needs-improvement') bg-yellow-100 text-yellow-800 @else bg-red-100 text-red-800 @endif">
                    @if($audit->cls_rating === 'good') Good @elseif($audit->cls_rating === 'needs-improvement') Needs Improvement @else Poor @endif
                </span>
                <div class="mt-3 text-xs text-gray-500">
                    Target: ≤ 0.1
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Metrics -->
    <div class="bg-white rounded-xl shadow-lg p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
            <i class="fas fa-chart-line text-blue-500 mr-3"></i>
            Additional Performance Metrics
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center p-6 bg-gray-50 rounded-lg">
                <div class="text-3xl font-bold text-blue-600 mb-2">{{ $audit->formatted_speed_index }}</div>
                <h3 class="font-semibold text-gray-900 mb-2">Speed Index</h3>
                <p class="text-sm text-gray-600">How quickly content appears</p>
            </div>
            
            <div class="text-center p-6 bg-gray-50 rounded-lg">
                <div class="text-3xl font-bold text-blue-600 mb-2">{{ number_format($audit->time_to_interactive, 1) }}s</div>
                <h3 class="font-semibold text-gray-900 mb-2">Time to Interactive</h3>
                <p class="text-sm text-gray-600">When page becomes fully interactive</p>
            </div>
            
            <div class="text-center p-6 bg-gray-50 rounded-lg">
                <div class="text-3xl font-bold text-blue-600 mb-2">{{ number_format($audit->max_potential_fid, 0) }}ms</div>
                <h3 class="font-semibold text-gray-900 mb-2">Max Potential FID</h3>
                <p class="text-sm text-gray-600">Maximum input delay</p>
            </div>
        </div>
    </div>

    <!-- Performance Recommendations -->
    @if(!empty($recommendations))
    <div class="bg-white rounded-xl shadow-lg p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
            <i class="fas fa-lightbulb text-yellow-500 mr-3"></i>
            Performance Recommendations
        </h2>
        
        <div class="space-y-4">
            @foreach($recommendations as $recommendation)
            <div class="border-l-4 @if($recommendation['priority'] === 'high') border-red-500 bg-red-50 @elseif($recommendation['priority'] === 'medium') border-yellow-500 bg-yellow-50 @else border-green-500 bg-green-50 @endif rounded-r-lg p-6">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900 mb-2">{{ $recommendation['title'] }}</h3>
                        <p class="text-gray-700 text-sm">{{ $recommendation['description'] }}</p>
                    </div>
                    <div class="ml-4 text-right">
                        <span class="px-3 py-1 text-xs rounded-full font-medium @if($recommendation['priority'] === 'high') bg-red-100 text-red-800 @elseif($recommendation['priority'] === 'medium') bg-yellow-100 text-yellow-800 @else bg-green-100 text-green-800 @endif">
                            {{ ucfirst($recommendation['priority']) }} Priority
                        </span>
                        <div class="text-xs text-gray-500 mt-1">Impact: {{ $recommendation['impact'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Performance Summary -->
    <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
            <i class="fas fa-clipboard-check text-green-500 mr-3"></i>
            Performance Summary
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h3 class="font-semibold text-gray-900 mb-4">Overall Assessment</h3>
                <div class="space-y-3">
                    @if($audit->performance_score >= 90)
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <span class="text-green-700">Excellent performance! Your site loads quickly and provides a great user experience.</span>
                        </div>
                    @elseif($audit->performance_score >= 50)
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                            <span class="text-yellow-700">Good performance with room for improvement. Consider optimizing the areas highlighted in the recommendations.</span>
                        </div>
                    @else
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-times-circle text-red-500"></i>
                            <span class="text-red-700">Performance needs significant improvement. Focus on the critical issues identified in the recommendations.</span>
                        </div>
                    @endif
                </div>
            </div>
            
            <div>
                <h3 class="font-semibold text-gray-900 mb-4">Core Web Vitals Status</h3>
                @php
                    $goodVitals = 0;
                    $goodVitals += ($audit->fcp_rating === 'good') ? 1 : 0;
                    $goodVitals += ($audit->lcp_rating === 'good') ? 1 : 0;
                    $goodVitals += ($audit->tbt_rating === 'good') ? 1 : 0;
                    $goodVitals += ($audit->cls_rating === 'good') ? 1 : 0;
                @endphp
                
                <div class="space-y-3">
                    @if($goodVitals >= 3)
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <span class="text-green-700">{{ $goodVitals }}/4 Core Web Vitals are in the "Good" range.</span>
                        </div>
                    @elseif($goodVitals >= 2)
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                            <span class="text-yellow-700">{{ $goodVitals }}/4 Core Web Vitals are in the "Good" range.</span>
                        </div>
                    @else
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-times-circle text-red-500"></i>
                            <span class="text-red-700">Only {{ $goodVitals }}/4 Core Web Vitals are in the "Good" range.</span>
                        </div>
                    @endif
                    
                    <div class="text-sm text-gray-600">
                        Core Web Vitals are Google's official metrics for measuring user experience quality.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex flex-wrap gap-4 justify-center">
            <a href="{{ route('tools.page-speed.pdf', $audit->id) }}" 
               class="px-6 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">
                <i class="fas fa-file-pdf mr-2"></i>
                Download PDF Report
            </a>
            
            <a href="{{ route('tools.page-speed') }}" 
               class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                New Analysis
            </a>
            
            <a href="{{ route('tools.page-speed.history') }}" 
               class="px-6 py-3 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-history mr-2"></i>
                View History
            </a>
            
            <button onclick="shareReport()" 
                    class="px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-share mr-2"></i>
                Share Report
            </button>
        </div>
    </div>
</div>

<script>
function shareReport() {
    if (navigator.share) {
        navigator.share({
            title: 'Page Speed Report - {{ $audit->url }}',
            text: 'Check out this page speed analysis for {{ $audit->url }}. Performance Score: {{ $audit->performance_score }}/100',
            url: window.location.href
        });
    } else {
        // Fallback: copy URL to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            showNotification('Report URL copied to clipboard!', 'success');
        });
    }
}
</script>
@endsection
