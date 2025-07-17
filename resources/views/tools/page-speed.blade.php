@extends('layouts.app')

@section('title', 'Page Speed Checker - SEO Audit Pro')
@section('page-title', 'Page Speed Checker')

@section('content')
<div class="space-y-6" x-data="pageSpeedChecker()">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl text-white p-8">
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center space-x-4 mb-6">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-tachometer-alt text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold">Page Speed Checker</h1>
                    <p class="text-blue-100 text-lg">Analyze your website's performance with Google PageSpeed Insights</p>
                </div>
            </div>

            @if($isDemo)
            <div class="bg-amber-500/20 border border-amber-300/30 rounded-lg p-4 mb-6">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-info-circle text-amber-200"></i>
                    <p class="text-amber-100">
                        <strong>Demo Mode:</strong> You're viewing sample data. 
                        <a href="{{ route('register') }}" class="underline hover:text-white">Sign up free</a> 
                        to analyze any website and track performance history.
                    </p>
                </div>
            </div>
            @endif

            <!-- URL Input Form -->
            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-6">
                <div class="space-y-4">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                        <div class="lg:col-span-2">
                            <label for="url" class="block text-sm font-medium text-blue-100 mb-2">Website URL</label>
                            <input 
                                type="url" 
                                id="url" 
                                x-model="url"
                                placeholder="https://example.com"
                                class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white placeholder-blue-200 focus:ring-2 focus:ring-white/50 focus:border-transparent"
                                :disabled="loading"
                            >
                        </div>
                        <div>
                            <label for="strategy" class="block text-sm font-medium text-blue-100 mb-2">Device Type</label>
                            <select 
                                id="strategy" 
                                x-model="strategy"
                                class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white focus:ring-2 focus:ring-white/50 focus:border-transparent"
                                :disabled="loading"
                            >
                                <option value="desktop">Desktop</option>
                                <option value="mobile">Mobile</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex justify-center">
                        <button 
                            @click="analyzeUrl()"
                            :disabled="loading || !url"
                            class="px-8 py-3 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 focus:ring-2 focus:ring-white/50 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                        >
                            <span x-show="!loading" class="flex items-center space-x-2">
                                <i class="fas fa-rocket"></i>
                                <span>Analyze Page Speed</span>
                            </span>
                            <span x-show="loading" class="flex items-center space-x-2">
                                <div class="loading-spinner"></div>
                                <span>Analyzing...</span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Section -->
    <div x-show="result" x-cloak class="fade-in">
        <!-- Data Source Indicator -->
        <div class="mb-4" x-show="result?.raw_data?.fallback">
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-info-circle text-amber-600"></i>
                    <div>
                        <p class="text-amber-800 font-medium">Simulated Performance Data</p>
                        <p class="text-amber-700 text-sm">This analysis uses realistic simulated data due to Google API rate limits. Upgrade to get unlimited real-time analysis.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Overall Score -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-6" x-show="result">
            <div class="text-center">
                <div class="flex justify-center mb-6">
                    <div class="relative w-32 h-32">
                        <svg class="w-32 h-32 transform -rotate-90" viewBox="0 0 120 120">
                            <circle cx="60" cy="60" r="50" stroke="#e5e7eb" stroke-width="8" fill="transparent"></circle>
                            <circle 
                                cx="60" 
                                cy="60" 
                                r="50" 
                                stroke="currentColor" 
                                :class="getScoreColor(result?.performance_score)"
                                stroke-width="8" 
                                fill="transparent"
                                :stroke-dasharray="`${(result?.performance_score || 0) * 3.14159} 314.159`"
                                stroke-linecap="round"
                                class="transition-all duration-1000"
                            ></circle>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <div class="text-3xl font-bold" x-text="result?.performance_score || 0"></div>
                                <div class="text-xs text-gray-500">Performance</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Performance Analysis</h2>
                <p class="text-gray-600 mb-4" x-text="`Analysis for ${result?.url} (${result?.strategy})`"></p>
                
                <!-- Category Scores -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold" :class="getScoreColor(result?.performance_score)" x-text="result?.performance_score || 0"></div>
                        <div class="text-sm text-gray-600">Performance</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold" :class="getScoreColor(result?.accessibility_score)" x-text="result?.accessibility_score || 0"></div>
                        <div class="text-sm text-gray-600">Accessibility</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold" :class="getScoreColor(result?.best_practices_score)" x-text="result?.best_practices_score || 0"></div>
                        <div class="text-sm text-gray-600">Best Practices</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold" :class="getScoreColor(result?.seo_score)" x-text="result?.seo_score || 0"></div>
                        <div class="text-sm text-gray-600">SEO</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Core Web Vitals -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-heartbeat text-red-500 mr-3"></i>
                Core Web Vitals
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- First Contentful Paint -->
                <div class="text-center p-4 border rounded-lg">
                    <div class="text-3xl font-bold mb-2" :class="getVitalColor('fcp', result?.first_contentful_paint)" x-text="formatTime(result?.first_contentful_paint)"></div>
                    <div class="text-sm font-medium text-gray-900 mb-1">First Contentful Paint</div>
                    <div class="text-xs text-gray-500">Time until first content appears</div>
                    <div class="mt-2">
                        <span class="px-2 py-1 text-xs rounded-full" :class="getVitalBadgeColor('fcp', result?.first_contentful_paint)" x-text="getVitalRating('fcp', result?.first_contentful_paint)"></span>
                    </div>
                </div>

                <!-- Largest Contentful Paint -->
                <div class="text-center p-4 border rounded-lg">
                    <div class="text-3xl font-bold mb-2" :class="getVitalColor('lcp', result?.largest_contentful_paint)" x-text="formatTime(result?.largest_contentful_paint)"></div>
                    <div class="text-sm font-medium text-gray-900 mb-1">Largest Contentful Paint</div>
                    <div class="text-xs text-gray-500">Time until largest content loads</div>
                    <div class="mt-2">
                        <span class="px-2 py-1 text-xs rounded-full" :class="getVitalBadgeColor('lcp', result?.largest_contentful_paint)" x-text="getVitalRating('lcp', result?.largest_contentful_paint)"></span>
                    </div>
                </div>

                <!-- Total Blocking Time -->
                <div class="text-center p-4 border rounded-lg">
                    <div class="text-3xl font-bold mb-2" :class="getVitalColor('tbt', result?.total_blocking_time)" x-text="formatMs(result?.total_blocking_time)"></div>
                    <div class="text-sm font-medium text-gray-900 mb-1">Total Blocking Time</div>
                    <div class="text-xs text-gray-500">Time page is blocked from input</div>
                    <div class="mt-2">
                        <span class="px-2 py-1 text-xs rounded-full" :class="getVitalBadgeColor('tbt', result?.total_blocking_time)" x-text="getVitalRating('tbt', result?.total_blocking_time)"></span>
                    </div>
                </div>

                <!-- Cumulative Layout Shift -->
                <div class="text-center p-4 border rounded-lg">
                    <div class="text-3xl font-bold mb-2" :class="getVitalColor('cls', result?.cumulative_layout_shift)" x-text="formatCls(result?.cumulative_layout_shift)"></div>
                    <div class="text-sm font-medium text-gray-900 mb-1">Cumulative Layout Shift</div>
                    <div class="text-xs text-gray-500">Amount of unexpected layout shift</div>
                    <div class="mt-2">
                        <span class="px-2 py-1 text-xs rounded-full" :class="getVitalBadgeColor('cls', result?.cumulative_layout_shift)" x-text="getVitalRating('cls', result?.cumulative_layout_shift)"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Metrics -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-chart-line text-blue-500 mr-3"></i>
                Additional Metrics
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900 mb-1" x-text="formatTime(result?.speed_index)"></div>
                    <div class="text-sm font-medium text-gray-700">Speed Index</div>
                    <div class="text-xs text-gray-500">How quickly content appears</div>
                </div>
                
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900 mb-1" x-text="formatTime(result?.time_to_interactive)"></div>
                    <div class="text-sm font-medium text-gray-700">Time to Interactive</div>
                    <div class="text-xs text-gray-500">When page becomes fully interactive</div>
                </div>
                
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900 mb-1" x-text="formatMs(result?.max_potential_fid)"></div>
                    <div class="text-sm font-medium text-gray-700">Max Potential FID</div>
                    <div class="text-xs text-gray-500">Maximum input delay</div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-xl shadow-lg p-6" x-show="result && !result.demo">
            <div class="flex flex-wrap gap-4 justify-center">
                <button 
                    @click="viewDetailedReport()" 
                    class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors"
                >
                    <i class="fas fa-chart-bar mr-2"></i>
                    View Detailed Report
                </button>
                <button 
                    @click="exportPdf()" 
                    class="px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors"
                >
                    <i class="fas fa-file-pdf mr-2"></i>
                    Export PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Recent Audits -->
    @if(!$isDemo && $recentAudits->count() > 0)
    <div class="bg-white rounded-xl shadow-lg p-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900">Recent Audits</h3>
            <a href="{{ route('tools.page-speed.history') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                View All <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <div class="space-y-4">
            @foreach($recentAudits as $audit)
            <div class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                <div class="flex-1">
                    <div class="font-medium text-gray-900">{{ $audit->url }}</div>
                    <div class="text-sm text-gray-500">
                        {{ $audit->analyzed_at->format('M j, Y g:i A') }} • {{ ucfirst($audit->strategy) }}
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-center">
                        <div class="text-lg font-bold @if($audit->performance_score >= 90) text-green-600 @elseif($audit->performance_score >= 50) text-yellow-600 @else text-red-600 @endif">
                            {{ $audit->performance_score }}
                        </div>
                        <div class="text-xs text-gray-500">Performance</div>
                    </div>
                    <a href="{{ route('tools.page-speed.show', $audit->id) }}" class="text-blue-600 hover:text-blue-700">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Demo CTA -->
    @if($isDemo)
    <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-xl text-white p-8 text-center">
        <h3 class="text-2xl font-bold mb-4">Ready for Real Performance Insights?</h3>
        <p class="text-purple-100 mb-6">Sign up to analyze any website, track performance history, and get detailed reports with actionable recommendations.</p>
        <div class="flex flex-wrap gap-4 justify-center">
            <a href="{{ route('register') }}" class="px-8 py-3 bg-white text-purple-600 font-semibold rounded-lg hover:bg-purple-50 transition-colors">
                Sign Up Free
            </a>
            <a href="{{ route('login') }}" class="px-8 py-3 border border-white/30 text-white font-semibold rounded-lg hover:bg-white/10 transition-colors">
                Sign In
            </a>
        </div>
    </div>
    @endif
</div>

<script>
function pageSpeedChecker() {
    return {
        url: '',
        strategy: 'desktop',
        loading: false,
        result: null,
        
        async analyzeUrl() {
            if (!this.url) {
                showNotification('Please enter a valid URL', 'error');
                return;
            }
            
            this.loading = true;
            this.result = null;
            
            try {
                const response = await apiRequest('/api/page-speed/analyze', {
                    method: 'POST',
                    body: {
                        url: this.url,
                        strategy: this.strategy
                    }
                });
                
                if (response.success) {
                    this.result = response.data;
                    
                    // Show appropriate message based on data source
                    if (response.demo) {
                        showNotification('Demo analysis completed. Sign up for real data!', 'info');
                    } else if (response.source === 'fallback') {
                        showNotification(response.message || 'Analysis completed using simulated data due to API rate limits.', 'warning');
                    } else {
                        showNotification(response.message || 'Analysis completed successfully!', 'success');
                    }
                } else {
                    showNotification(response.message || 'Analysis failed', 'error');
                }
            } catch (error) {
                showNotification('Error during analysis: ' + error.message, 'error');
            } finally {
                this.loading = false;
            }
        },
        
        getScoreColor(score) {
            if (score >= 90) return 'text-green-600';
            if (score >= 50) return 'text-yellow-600';
            return 'text-red-600';
        },
        
        getVitalColor(metric, value) {
            const rating = this.getVitalRating(metric, value);
            if (rating === 'Good') return 'text-green-600';
            if (rating === 'Needs Improvement') return 'text-yellow-600';
            return 'text-red-600';
        },
        
        getVitalBadgeColor(metric, value) {
            const rating = this.getVitalRating(metric, value);
            if (rating === 'Good') return 'bg-green-100 text-green-800';
            if (rating === 'Needs Improvement') return 'bg-yellow-100 text-yellow-800';
            return 'bg-red-100 text-red-800';
        },
        
        getVitalRating(metric, value) {
            if (!value) return 'N/A';
            
            switch (metric) {
                case 'fcp':
                    return value <= 1.8 ? 'Good' : value <= 3.0 ? 'Needs Improvement' : 'Poor';
                case 'lcp':
                    return value <= 2.5 ? 'Good' : value <= 4.0 ? 'Needs Improvement' : 'Poor';
                case 'tbt':
                    return value <= 200 ? 'Good' : value <= 600 ? 'Needs Improvement' : 'Poor';
                case 'cls':
                    return value <= 0.1 ? 'Good' : value <= 0.25 ? 'Needs Improvement' : 'Poor';
                default:
                    return 'N/A';
            }
        },
        
        formatTime(seconds) {
            if (!seconds) return 'N/A';
            return parseFloat(seconds).toFixed(1) + 's';
        },
        
        formatMs(ms) {
            if (!ms) return 'N/A';
            return Math.round(ms) + 'ms';
        },
        
        formatCls(cls) {
            if (!cls) return 'N/A';
            return parseFloat(cls).toFixed(3);
        },
        
        viewDetailedReport() {
            if (this.result && this.result.id) {
                window.location.href = `/tools/page-speed/${this.result.id}`;
            }
        },
        
        exportPdf() {
            if (this.result && this.result.id) {
                window.open(`/tools/page-speed/${this.result.id}/pdf`, '_blank');
            }
        }
    }
}
</script>
@endsection
