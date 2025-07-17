@extends('layouts.app')

@section('title', 'Meta Tag Analyzer - SEO Audit Pro')
@section('page-title', 'Meta Tag Analyzer')

@section('content')
<div x-data="metaAnalyzerData()">
    <!-- Header -->
    <div class="mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Meta Tag Analyzer</h1>
                    <p class="text-gray-600">Analyze and optimize your website's meta tags for better SEO performance.</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('meta-analyzer.history') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-history mr-2"></i>
                        View History
                    </a>
                    <div class="hidden lg:block">
                        <div class="w-16 h-16 bg-purple-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-tags text-purple-600 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- URL Input Form -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Analyze Website Meta Tags</h3>
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input 
                    type="url" 
                    x-model="websiteUrl"
                    placeholder="Enter website URL (e.g., https://example.com)"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    @keyup.enter="analyzeMetaTags()"
                >
            </div>
            <button 
                @click="analyzeMetaTags()"
                :disabled="loading || !websiteUrl"
                class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center min-w-[160px]"
            >
                <span x-show="!loading">
                    <i class="fas fa-search mr-2"></i>
                    Analyze Meta Tags
                </span>
                <span x-show="loading" x-cloak>
                    <div class="loading-spinner mr-2"></div>
                    Analyzing...
                </span>
            </button>
        </div>
    </div>

    <!-- Results Section -->
    <div x-show="analysisResults" x-cloak class="space-y-8">
        <!-- Score Overview -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">SEO Score</h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">Last analyzed:</span>
                    <span class="text-sm text-gray-700" x-text="formatDate(analysisResults?.analyzed_at)"></span>
                </div>
            </div>
            
            <div class="flex items-center space-x-6">
                <div class="relative w-24 h-24">
                    <div class="w-24 h-24 rounded-full border-8" 
                         :class="getScoreColor(analysisResults?.score)"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-2xl font-bold text-gray-900" x-text="analysisResults?.score || 0"></span>
                    </div>
                </div>
                <div>
                    <h4 class="text-xl font-semibold" :class="getScoreTextColor(analysisResults?.score)" x-text="getScoreText(analysisResults?.score)"></h4>
                    <p class="text-gray-600 mt-1" x-text="getScoreDescription(analysisResults?.score)"></p>
                    <div class="flex items-center mt-2 space-x-4">
                        <span class="text-sm text-green-600" x-text="'Issues Found: ' + (analysisResults?.issues_found?.length || 0)"></span>
                        <span class="text-sm text-blue-600" x-text="'Recommendations: ' + (analysisResults?.recommendations?.length || 0)"></span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Meta Tags Analysis Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Title Tag Analysis -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Title Tag</h3>
                    <span class="px-3 py-1 rounded-full text-sm font-medium" 
                          :class="getTitleStatus(analysisResults)">
                        <i :class="getTitleIcon(analysisResults)" class="mr-1"></i>
                        <span x-text="getTitleStatusText(analysisResults)"></span>
                    </span>
                </div>
                
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Title:</label>
                        <p class="text-gray-900 bg-gray-50 p-3 rounded-lg text-sm" 
                           x-text="analysisResults?.title || 'No title found'"></p>
                    </div>
                    
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Length:</span>
                        <span class="font-medium" x-text="(analysisResults?.title_length || 0) + ' characters'"></span>
                    </div>
                    
                    <div class="bg-blue-50 p-3 rounded-lg">
                        <p class="text-xs text-blue-700">
                            <i class="fas fa-info-circle mr-1"></i>
                            Recommended: 30-60 characters for optimal display in search results
                        </p>
                    </div>
                </div>
            </div>

            <!-- Meta Description Analysis -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Meta Description</h3>
                    <span class="px-3 py-1 rounded-full text-sm font-medium" 
                          :class="getDescriptionStatus(analysisResults)">
                        <i :class="getDescriptionIcon(analysisResults)" class="mr-1"></i>
                        <span x-text="getDescriptionStatusText(analysisResults)"></span>
                    </span>
                </div>
                
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Description:</label>
                        <p class="text-gray-900 bg-gray-50 p-3 rounded-lg text-sm" 
                           x-text="analysisResults?.meta_description || 'No meta description found'"></p>
                    </div>
                    
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Length:</span>
                        <span class="font-medium" x-text="(analysisResults?.meta_description_length || 0) + ' characters'"></span>
                    </div>
                    
                    <div class="bg-blue-50 p-3 rounded-lg">
                        <p class="text-xs text-blue-700">
                            <i class="fas fa-info-circle mr-1"></i>
                            Recommended: 120-160 characters for optimal display in search results
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Technical Meta Tags -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Technical Meta Tags</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Viewport -->
                <div class="border rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-medium text-gray-700">Viewport</span>
                        <span class="px-2 py-1 rounded text-xs font-medium" 
                              :class="analysisResults?.viewport ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                            <i :class="analysisResults?.viewport ? 'fas fa-check' : 'fas fa-times'" class="mr-1"></i>
                            <span x-text="analysisResults?.viewport ? 'Present' : 'Missing'"></span>
                        </span>
                    </div>
                    <p class="text-sm text-gray-600" x-text="analysisResults?.viewport || 'Not found'"></p>
                </div>

                <!-- Robots -->
                <div class="border rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-medium text-gray-700">Robots</span>
                        <span class="px-2 py-1 rounded text-xs font-medium" 
                              :class="analysisResults?.robots ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                            <i :class="analysisResults?.robots ? 'fas fa-check' : 'fas fa-exclamation-triangle'" class="mr-1"></i>
                            <span x-text="analysisResults?.robots ? 'Present' : 'Not Set'"></span>
                        </span>
                    </div>
                    <p class="text-sm text-gray-600" x-text="analysisResults?.robots || 'Default behavior'"></p>
                </div>

                <!-- Canonical -->
                <div class="border rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-medium text-gray-700">Canonical URL</span>
                        <span class="px-2 py-1 rounded text-xs font-medium" 
                              :class="analysisResults?.canonical_url ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                            <i :class="analysisResults?.canonical_url ? 'fas fa-check' : 'fas fa-exclamation-triangle'" class="mr-1"></i>
                            <span x-text="analysisResults?.canonical_url ? 'Present' : 'Missing'"></span>
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 break-all" x-text="analysisResults?.canonical_url || 'Not found'"></p>
                </div>
            </div>
        </div>

        <!-- Open Graph Tags -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Open Graph Tags</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- OG Title -->
                <div class="border rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-medium text-gray-700">OG Title</span>
                        <span class="px-2 py-1 rounded text-xs font-medium" 
                              :class="analysisResults?.og_title ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                            <i :class="analysisResults?.og_title ? 'fas fa-check' : 'fas fa-exclamation-triangle'" class="mr-1"></i>
                            <span x-text="analysisResults?.og_title ? 'Present' : 'Missing'"></span>
                        </span>
                    </div>
                    <p class="text-sm text-gray-600" x-text="analysisResults?.og_title || 'Not found'"></p>
                </div>

                <!-- OG Description -->
                <div class="border rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-medium text-gray-700">OG Description</span>
                        <span class="px-2 py-1 rounded text-xs font-medium" 
                              :class="analysisResults?.og_description ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                            <i :class="analysisResults?.og_description ? 'fas fa-check' : 'fas fa-exclamation-triangle'" class="mr-1"></i>
                            <span x-text="analysisResults?.og_description ? 'Present' : 'Missing'"></span>
                        </span>
                    </div>
                    <p class="text-sm text-gray-600" x-text="analysisResults?.og_description || 'Not found'"></p>
                </div>

                <!-- OG Image -->
                <div class="border rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-medium text-gray-700">OG Image</span>
                        <span class="px-2 py-1 rounded text-xs font-medium" 
                              :class="analysisResults?.og_image ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                            <i :class="analysisResults?.og_image ? 'fas fa-check' : 'fas fa-exclamation-triangle'" class="mr-1"></i>
                            <span x-text="analysisResults?.og_image ? 'Present' : 'Missing'"></span>
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 break-all" x-text="analysisResults?.og_image || 'Not found'"></p>
                </div>

                <!-- OG Type -->
                <div class="border rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-medium text-gray-700">OG Type</span>
                        <span class="px-2 py-1 rounded text-xs font-medium" 
                              :class="analysisResults?.og_type ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                            <i :class="analysisResults?.og_type ? 'fas fa-check' : 'fas fa-exclamation-triangle'" class="mr-1"></i>
                            <span x-text="analysisResults?.og_type ? 'Present' : 'Missing'"></span>
                        </span>
                    </div>
                    <p class="text-sm text-gray-600" x-text="analysisResults?.og_type || 'Not found'"></p>
                </div>
            </div>
        </div>

        <!-- Twitter Cards -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Twitter Card Tags</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Twitter Card -->
                <div class="border rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-medium text-gray-700">Twitter Card</span>
                        <span class="px-2 py-1 rounded text-xs font-medium" 
                              :class="analysisResults?.twitter_card ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                            <i :class="analysisResults?.twitter_card ? 'fas fa-check' : 'fas fa-exclamation-triangle'" class="mr-1"></i>
                            <span x-text="analysisResults?.twitter_card ? 'Present' : 'Missing'"></span>
                        </span>
                    </div>
                    <p class="text-sm text-gray-600" x-text="analysisResults?.twitter_card || 'Not found'"></p>
                </div>

                <!-- Twitter Title -->
                <div class="border rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-medium text-gray-700">Twitter Title</span>
                        <span class="px-2 py-1 rounded text-xs font-medium" 
                              :class="analysisResults?.twitter_title ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                            <i :class="analysisResults?.twitter_title ? 'fas fa-check' : 'fas fa-exclamation-triangle'" class="mr-1"></i>
                            <span x-text="analysisResults?.twitter_title ? 'Present' : 'Missing'"></span>
                        </span>
                    </div>
                    <p class="text-sm text-gray-600" x-text="analysisResults?.twitter_title || 'Not found'"></p>
                </div>

                <!-- Twitter Description -->
                <div class="border rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-medium text-gray-700">Twitter Description</span>
                        <span class="px-2 py-1 rounded text-xs font-medium" 
                              :class="analysisResults?.twitter_description ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                            <i :class="analysisResults?.twitter_description ? 'fas fa-check' : 'fas fa-exclamation-triangle'" class="mr-1"></i>
                            <span x-text="analysisResults?.twitter_description ? 'Present' : 'Missing'"></span>
                        </span>
                    </div>
                    <p class="text-sm text-gray-600" x-text="analysisResults?.twitter_description || 'Not found'"></p>
                </div>

                <!-- Twitter Image -->
                <div class="border rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-medium text-gray-700">Twitter Image</span>
                        <span class="px-2 py-1 rounded text-xs font-medium" 
                              :class="analysisResults?.twitter_image ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                            <i :class="analysisResults?.twitter_image ? 'fas fa-check' : 'fas fa-exclamation-triangle'" class="mr-1"></i>
                            <span x-text="analysisResults?.twitter_image ? 'Present' : 'Missing'"></span>
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 break-all" x-text="analysisResults?.twitter_image || 'Not found'"></p>
                </div>
            </div>
        </div>

        <!-- Issues Found -->
        <div x-show="analysisResults?.issues_found && analysisResults.issues_found.length > 0" class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Issues Found</h3>
            
            <div class="space-y-3">
                <template x-for="issue in analysisResults?.issues_found || []" :key="issue">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                            <span class="text-red-800 font-medium" x-text="formatIssue(issue)"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Recommendations -->
        <div x-show="analysisResults?.recommendations && analysisResults.recommendations.length > 0" class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Recommendations</h3>
            
            <div class="space-y-3">
                <template x-for="recommendation in analysisResults?.recommendations || []" :key="recommendation">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-lightbulb text-blue-500 mr-2"></i>
                            <span class="text-blue-800 font-medium" x-text="formatRecommendation(recommendation)"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Actions -->
        <div x-show="analysisResults" class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
            
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('meta-analyzer.history') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-history mr-2"></i>
                    View History
                </a>
            </div>
        </div>
    </div>

    <!-- Error Message -->
    <div x-show="error" x-cloak class="mt-8 bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-red-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Error</h3>
                <p class="mt-1 text-sm text-red-700" x-text="error"></p>
            </div>
        </div>
    </div>
</div>

<style>
.loading-spinner {
    border: 2px solid #f3f3f3;
    border-top: 2px solid #3498db;
    border-radius: 50%;
    width: 16px;
    height: 16px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<script>
function metaAnalyzerData() {
    return {
        websiteUrl: '',
        loading: false,
        analysisResults: null,
        error: null,
        
        async analyzeMetaTags() {
            if (!this.websiteUrl) {
                this.error = 'Please enter a website URL';
                return;
            }
            
            this.loading = true;
            this.error = null;
            this.analysisResults = null;
            
            try {
                const response = await fetch('/api/meta-analyzer', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ 
                        url: this.websiteUrl
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.analysisResults = data.data;
                } else {
                    this.error = data.message || 'Failed to analyze meta tags';
                }
            } catch (err) {
                this.error = 'Network error: ' + err.message;
            } finally {
                this.loading = false;
            }
        },
        
        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
        },
        
        getScoreColor(score) {
            if (score >= 80) return 'border-green-500';
            if (score >= 60) return 'border-yellow-500';
            return 'border-red-500';
        },
        
        getScoreTextColor(score) {
            if (score >= 80) return 'text-green-600';
            if (score >= 60) return 'text-yellow-600';
            return 'text-red-600';
        },
        
        getScoreText(score) {
            if (score >= 80) return 'Excellent';
            if (score >= 60) return 'Good';
            if (score >= 40) return 'Needs Improvement';
            return 'Poor';
        },
        
        getScoreDescription(score) {
            if (score >= 80) return 'Your meta tags are well optimized for SEO';
            if (score >= 60) return 'Your meta tags are fairly good but could be improved';
            if (score >= 40) return 'Your meta tags need some attention';
            return 'Your meta tags need significant improvement';
        },
        
        getTitleStatus(results) {
            if (!results?.title) return 'bg-red-100 text-red-800';
            const length = results.title_length || 0;
            if (length >= 30 && length <= 60) return 'bg-green-100 text-green-800';
            return 'bg-yellow-100 text-yellow-800';
        },
        
        getTitleIcon(results) {
            if (!results?.title) return 'fas fa-times';
            const length = results.title_length || 0;
            if (length >= 30 && length <= 60) return 'fas fa-check';
            return 'fas fa-exclamation-triangle';
        },
        
        getTitleStatusText(results) {
            if (!results?.title) return 'Missing';
            const length = results.title_length || 0;
            if (length >= 30 && length <= 60) return 'Good';
            return 'Needs Improvement';
        },
        
        getDescriptionStatus(results) {
            if (!results?.meta_description) return 'bg-red-100 text-red-800';
            const length = results.meta_description_length || 0;
            if (length >= 120 && length <= 160) return 'bg-green-100 text-green-800';
            return 'bg-yellow-100 text-yellow-800';
        },
        
        getDescriptionIcon(results) {
            if (!results?.meta_description) return 'fas fa-times';
            const length = results.meta_description_length || 0;
            if (length >= 120 && length <= 160) return 'fas fa-check';
            return 'fas fa-exclamation-triangle';
        },
        
        getDescriptionStatusText(results) {
            if (!results?.meta_description) return 'Missing';
            const length = results.meta_description_length || 0;
            if (length >= 120 && length <= 160) return 'Good';
            return 'Needs Improvement';
        },
        
        formatIssue(issue) {
            return issue.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        },
        
        formatRecommendation(recommendation) {
            return recommendation.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        }
    }
}
</script>
@endsection
