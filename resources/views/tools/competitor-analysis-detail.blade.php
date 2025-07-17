@extends('layouts.app')

@section('title', 'Competitor Analysis - ' . $analysis->domain)

@section('content')
<div>
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
                    <a href="{{ route('competitor-analysis.index') }}" class="hover:text-blue-600">Competitor Analysis</a>
                    <span>/</span>
                    <span class="text-gray-900">{{ $analysis->domain }}</span>
                </nav>
                <h1 class="text-3xl font-bold text-gray-900">{{ $analysis->title ?: $analysis->domain }}</h1>
                <p class="mt-2 text-gray-600">{{ $analysis->url }}</p>
            </div>
            <div class="flex space-x-4">
                <button onclick="rescanAnalysis({{ $analysis->id }})" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Rescan
                </button>
                <a href="{{ route('competitor-analysis.export', $analysis->id) }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-download mr-2"></i>
                    Export
                </a>
            </div>
        </div>
    </div>

    <!-- Analysis Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-trophy text-blue-500 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Strategy Score</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $analysis->strategy_score }}/100</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-tools text-green-500 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Tools Detected</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $analysis->total_tools_detected }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-chart-line text-purple-500 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Strategies</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $analysis->strategy_summary_count }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-link text-red-500 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Backlinks</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $analysis->total_backlinks ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-key text-yellow-500 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Keywords</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $analysis->total_keywords ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-gray-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-clock text-gray-500 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Last Scanned</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $analysis->time_since_last_scan }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Strategy Summary -->
    @if($analysis->strategy_summary && count($analysis->strategy_summary) > 0)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">
            <i class="fas fa-chart-pie mr-2 text-blue-600"></i>
            Strategy Summary
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($analysis->strategy_summary as $strategy)
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="font-medium text-gray-900">{{ $strategy['category'] }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ $strategy['description'] }}</p>
                        <div class="mt-2 flex items-center space-x-2">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $strategy['tools'] }} tools
                            </span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $strategy['confidence'] }} confidence
                            </span>
                        </div>
                    </div>
                    <div class="ml-4">
                        @php
                            $icons = [
                                'Analytics' => 'fas fa-chart-line',
                                'Social Media Marketing' => 'fab fa-facebook',
                                'Paid Advertising' => 'fas fa-bullhorn',
                                'Retargeting' => 'fas fa-crosshairs',
                                'Email Marketing' => 'fas fa-envelope',
                                'Customer Support' => 'fas fa-headset',
                                'SEO Optimization' => 'fas fa-search',
                                'Platform' => 'fas fa-cog'
                            ];
                            $iconClass = $icons[$strategy['category']] ?? 'fas fa-circle';
                        @endphp
                        <i class="{{ $iconClass }} text-2xl text-gray-400"></i>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Actionable Insights -->
    @if($analysis->actionable_insights && count($analysis->actionable_insights) > 0)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">
            <i class="fas fa-lightbulb mr-2 text-yellow-600"></i>
            Actionable Insights
        </h2>
        
        <div class="space-y-4">
            @foreach($analysis->actionable_insights as $insight)
            <div class="flex items-start space-x-3 p-4 {{ $insight['type'] === 'opportunity' ? 'bg-yellow-50 border-l-4 border-yellow-400' : 'bg-green-50 border-l-4 border-green-400' }} rounded-lg">
                <div class="flex-shrink-0">
                    @if($insight['type'] === 'opportunity')
                        <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                    @else
                        <i class="fas fa-star text-green-600"></i>
                    @endif
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <h3 class="font-medium text-gray-900">{{ $insight['category'] }}</h3>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                            {{ $insight['priority'] === 'High' ? 'bg-red-100 text-red-800' : 
                               ($insight['priority'] === 'Medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                            {{ $insight['priority'] }} Priority
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">{{ $insight['message'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Detailed Analysis -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Marketing Tools -->
        <div class="space-y-8">
            @if($analysis->analytics_tools && count($analysis->analytics_tools) > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-chart-line mr-2 text-blue-600"></i>
                    Analytics Tools
                </h3>
                <div class="space-y-3">
                    @foreach($analysis->analytics_tools as $tool)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <h4 class="font-medium text-gray-900">{{ $tool['name'] }}</h4>
                            <p class="text-sm text-gray-600">{{ $tool['type'] }}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ $tool['confidence'] }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($analysis->social_pixels && count($analysis->social_pixels) > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fab fa-facebook mr-2 text-blue-600"></i>
                    Social Media Pixels
                </h3>
                <div class="space-y-3">
                    @foreach($analysis->social_pixels as $pixel)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <h4 class="font-medium text-gray-900">{{ $pixel['name'] }}</h4>
                            <p class="text-sm text-gray-600">{{ $pixel['platform'] }}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ $pixel['confidence'] }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($analysis->advertising_networks && count($analysis->advertising_networks) > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-bullhorn mr-2 text-red-600"></i>
                    Advertising Networks
                </h3>
                <div class="space-y-3">
                    @foreach($analysis->advertising_networks as $network)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <h4 class="font-medium text-gray-900">{{ $network['name'] }}</h4>
                            <p class="text-sm text-gray-600">{{ $network['type'] }}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ $network['confidence'] }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($analysis->chat_widgets && count($analysis->chat_widgets) > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-headset mr-2 text-green-600"></i>
                    Chat Widgets
                </h3>
                <div class="space-y-3">
                    @foreach($analysis->chat_widgets as $widget)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <h4 class="font-medium text-gray-900">{{ $widget['name'] }}</h4>
                            <p class="text-sm text-gray-600">{{ $widget['type'] }}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ $widget['confidence'] }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Technical Analysis -->
        <div class="space-y-8">
            @if($analysis->seo_tools && count($analysis->seo_tools) > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-search mr-2 text-purple-600"></i>
                    SEO Tools
                </h3>
                <div class="space-y-3">
                    @foreach($analysis->seo_tools as $tool)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <h4 class="font-medium text-gray-900">{{ $tool['name'] }}</h4>
                            <p class="text-sm text-gray-600">{{ $tool['type'] }}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ $tool['confidence'] }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($analysis->cms_detection && count($analysis->cms_detection) > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-cog mr-2 text-gray-600"></i>
                    Platform Detection
                </h3>
                <div class="space-y-3">
                    @foreach($analysis->cms_detection as $cms)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <h4 class="font-medium text-gray-900">{{ $cms['name'] }}</h4>
                            <p class="text-sm text-gray-600">{{ $cms['type'] }}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ $cms['confidence'] }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($analysis->opengraph_tags && count($analysis->opengraph_tags) > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-share-alt mr-2 text-blue-600"></i>
                    OpenGraph Tags
                </h3>
                <div class="space-y-2">
                    @foreach($analysis->opengraph_tags as $property => $content)
                    <div class="flex items-start space-x-3">
                        <span class="text-sm font-medium text-gray-500 w-24 flex-shrink-0">{{ str_replace('og:', '', $property) }}:</span>
                        <span class="text-sm text-gray-900">{{ $content }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($analysis->schema_markup && count($analysis->schema_markup) > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-code mr-2 text-green-600"></i>
                    Schema Markup
                </h3>
                <div class="space-y-3">
                    @foreach($analysis->schema_markup as $schema)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <h4 class="font-medium text-gray-900">{{ $schema['type'] }}</h4>
                            <p class="text-sm text-gray-600">{{ $schema['format'] }}</p>
                        </div>
                        <i class="fas fa-check text-green-600"></i>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Backlinks and Keywords Analysis -->
    @php
        $hasValidBacklinks = $analysis->backlinks_data && count($analysis->backlinks_data) > 0 && 
                           collect($analysis->backlinks_data)->filter(function($backlink) {
                               return isset($backlink['domain']) && $backlink['domain'] !== 'Unknown Domain' && 
                                      isset($backlink['url']) && $backlink['url'] !== 'N/A';
                           })->count() > 0;
                           
        $hasValidKeywords = $analysis->keywords_data && count($analysis->keywords_data) > 0 && 
                          collect($analysis->keywords_data)->filter(function($keyword) {
                              return isset($keyword['keyword']) && $keyword['keyword'] !== 'Unknown Keyword';
                          })->count() > 0;
    @endphp

    @if($hasValidBacklinks || $hasValidKeywords)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
        <!-- Backlinks Analysis -->
        @if($hasValidBacklinks)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-link mr-2 text-red-600"></i>
                    Backlinks Analysis
                </h3>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                    {{ $analysis->total_backlinks }} Total
                </span>
            </div>
            
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @foreach($analysis->backlinks_data as $backlink)
                    @if(isset($backlink['domain']) && $backlink['domain'] !== 'Unknown Domain' && isset($backlink['url']) && $backlink['url'] !== 'N/A')
                    <div class="flex items-start justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="text-sm font-medium text-gray-900">{{ $backlink['domain'] }}</span>
                                @if(isset($backlink['authority_score']))
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                    {{ $backlink['authority_score'] >= 70 ? 'bg-green-100 text-green-800' : 
                                       ($backlink['authority_score'] >= 40 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    DA {{ $backlink['authority_score'] }}
                                </span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-600 break-all">{{ $backlink['url'] }}</p>
                            @if(isset($backlink['anchor_text']) && $backlink['anchor_text'])
                            <p class="text-xs text-blue-600 mt-1">Anchor: "{{ $backlink['anchor_text'] }}"</p>
                            @endif
                        </div>
                        @if(isset($backlink['type']))
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 ml-2">
                            {{ ucfirst($backlink['type']) }}
                        </span>
                        @endif
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
        @endif

        <!-- Keywords Analysis -->
        @if($hasValidKeywords)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-key mr-2 text-yellow-600"></i>
                    Keywords Analysis
                </h3>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                    {{ $analysis->total_keywords }} Total
                </span>
            </div>
            
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @foreach($analysis->keywords_data as $keyword)
                    @if(isset($keyword['keyword']) && $keyword['keyword'] !== 'Unknown Keyword')
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="text-sm font-medium text-gray-900">{{ $keyword['keyword'] }}</span>
                                @if(isset($keyword['difficulty']))
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                    {{ $keyword['difficulty'] <= 30 ? 'bg-green-100 text-green-800' : 
                                       ($keyword['difficulty'] <= 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $keyword['difficulty'] }}% Difficulty
                                </span>
                                @endif
                            </div>
                            @if(isset($keyword['search_volume']))
                            <p class="text-xs text-gray-600">Volume: {{ number_format($keyword['search_volume']) }}/month</p>
                            @endif
                            @if(isset($keyword['position']))
                            <p class="text-xs text-blue-600">Position: #{{ $keyword['position'] }}</p>
                            @endif
                        </div>
                        @if(isset($keyword['type']))
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            {{ ucfirst($keyword['type']) }}
                        </span>
                        @endif
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
        @endif
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
</script>
@endsection
