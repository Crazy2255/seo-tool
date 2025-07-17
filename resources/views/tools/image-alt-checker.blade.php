@extends('layouts.app')

@section('title', 'Image ALT Text Checker - SEO Audit Pro')
@section('page-title', 'Image ALT Text Checker')

@section('content')
<div class="space-y-6" x-data="imageAltChecker()">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-green-600 to-teal-600 rounded-xl text-white p-8">
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center space-x-4 mb-6">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-images text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold">Image ALT Text Checker</h1>
                    <p class="text-green-100 text-lg">Analyze your website's image accessibility and SEO optimization</p>
                </div>
            </div>

            @if($isDemo)
            <div class="bg-amber-500/20 border border-amber-300/30 rounded-lg p-4 mb-6">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-info-circle text-amber-200"></i>
                    <p class="text-amber-100">
                        <strong>Demo Mode:</strong> You're viewing sample data. 
                        <a href="{{ route('register') }}" class="underline hover:text-white">Sign up free</a> 
                        to analyze any website and save your audit history.
                    </p>
                </div>
            </div>
            @endif

            <!-- URL Input Form -->
            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-6">
                <div class="space-y-4">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div>
                            <label for="url" class="block text-sm font-medium text-green-100 mb-2">Website URL</label>
                            <input 
                                type="url" 
                                id="url" 
                                x-model="url"
                                placeholder="https://example.com"
                                class="w-full px-4 py-3 bg-white/20 border border-white/30 rounded-lg text-white placeholder-green-200 focus:ring-2 focus:ring-white/50 focus:border-transparent"
                                :disabled="loading"
                            >
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="flex items-center space-x-2 text-green-100">
                                    <input 
                                        type="checkbox" 
                                        x-model="multiPage" 
                                        :disabled="loading"
                                        class="rounded bg-white/20 border-white/30 text-green-600"
                                    >
                                    <span class="text-sm font-medium">Multi-page crawl</span>
                                </label>
                            </div>
                            <div x-show="multiPage">
                                <label for="maxPages" class="block text-sm font-medium text-green-100 mb-1">Max Pages</label>
                                <select 
                                    x-model="maxPages" 
                                    :disabled="loading || !multiPage"
                                    class="w-full px-3 py-2 bg-white/20 border border-white/30 rounded-lg text-white focus:ring-2 focus:ring-white/50"
                                >
                                    <option value="3">3 pages</option>
                                    <option value="5">5 pages</option>
                                    <option value="10">10 pages</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-center">
                        <button 
                            @click="analyzeImages()"
                            :disabled="loading || !url"
                            class="px-8 py-3 bg-white text-green-600 font-semibold rounded-lg hover:bg-green-50 focus:ring-2 focus:ring-white/50 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                        >
                            <span x-show="!loading" class="flex items-center space-x-2">
                                <i class="fas fa-search"></i>
                                <span>Analyze Images</span>
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
        <!-- Overall Statistics -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Accessibility Analysis</h2>
                    <p class="text-gray-600" x-text="result?.page_title || 'Website Analysis'"></p>
                </div>
                <div class="flex space-x-4">
                    <template x-if="result?.id">
                        <div class="flex space-x-2">
                            <a :href="`/tools/image-alt/${result.id}/pdf`" 
                               class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                <i class="fas fa-file-pdf mr-2"></i>Export PDF
                            </a>
                            <a :href="`/tools/image-alt/${result.id}/csv`" 
                               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-file-csv mr-2"></i>Export CSV
                            </a>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Statistics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="text-center p-6 bg-blue-50 rounded-lg">
                    <div class="text-3xl font-bold text-blue-600" x-text="result?.total_images || 0"></div>
                    <div class="text-sm text-blue-800 font-medium">Total Images</div>
                </div>
                <div class="text-center p-6 bg-green-50 rounded-lg">
                    <div class="text-3xl font-bold text-green-600" x-text="result?.images_with_good_alt || 0"></div>
                    <div class="text-sm text-green-800 font-medium">Good ALT Text</div>
                </div>
                <div class="text-center p-6 bg-red-50 rounded-lg">
                    <div class="text-3xl font-bold text-red-600" x-text="result?.images_without_alt || 0"></div>
                    <div class="text-sm text-red-800 font-medium">Missing ALT</div>
                </div>
                <div class="text-center p-6 bg-amber-50 rounded-lg">
                    <div class="text-3xl font-bold text-amber-600" x-text="result?.images_with_issues || 0"></div>
                    <div class="text-sm text-amber-800 font-medium">Need Improvement</div>
                </div>
            </div>

            <!-- Accessibility Score -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Accessibility Score</h3>
                <div class="flex items-center space-x-4">
                    <div class="flex-1">
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="h-3 rounded-full transition-all duration-1000" 
                                 :class="getScoreColor(getAccessibilityScore())"
                                 :style="`width: ${getAccessibilityScore()}%`"></div>
                        </div>
                    </div>
                    <div class="text-2xl font-bold" 
                         :class="getScoreTextColor(getAccessibilityScore())"
                         x-text="`${getAccessibilityScore()}%`"></div>
                </div>
                <p class="text-sm text-gray-600 mt-2" x-text="getScoreDescription(getAccessibilityScore())"></p>
            </div>
        </div>

        <!-- Images Analysis -->
        <div class="bg-white rounded-xl shadow-lg p-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">Image Details</h3>
                <div class="flex space-x-2">
                    <button @click="filterStatus = 'all'" 
                            :class="filterStatus === 'all' ? 'bg-gray-900 text-white' : 'bg-gray-200 text-gray-700'"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        All Images
                    </button>
                    <button @click="filterStatus = 'missing'" 
                            :class="filterStatus === 'missing' ? 'bg-red-600 text-white' : 'bg-red-100 text-red-700'"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Missing ALT
                    </button>
                    <button @click="filterStatus = 'needs_improvement'" 
                            :class="filterStatus === 'needs_improvement' ? 'bg-amber-600 text-white' : 'bg-amber-100 text-amber-700'"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Issues
                    </button>
                    <button @click="filterStatus = 'good'" 
                            :class="filterStatus === 'good' ? 'bg-green-600 text-white' : 'bg-green-100 text-green-700'"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Good ALT
                    </button>
                </div>
            </div>

            <div class="space-y-6">
                <template x-for="(image, index) in getFilteredImages()" :key="index">
                    <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Image Preview -->
                            <div class="space-y-4">
                                <div class="aspect-video bg-gray-100 rounded-lg overflow-hidden">
                                    <img :src="image.src" :alt="image.alt || 'Image preview'" 
                                         class="w-full h-full object-cover"
                                         @error="$event.target.style.display='none'; $event.target.nextElementSibling.style.display='flex'">
                                    <div class="w-full h-full flex items-center justify-center text-gray-400" style="display: none;">
                                        <i class="fas fa-image text-4xl"></i>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-600">
                                    <p><strong>File:</strong> <span x-text="image.file_name"></span></p>
                                    <p><strong>Size:</strong> <span x-text="image.width && image.height ? `${image.width}×${image.height}` : 'Unknown'"></span></p>
                                </div>
                            </div>

                            <!-- Image Details -->
                            <div class="lg:col-span-2 space-y-4">
                                <!-- Status Badge -->
                                <div class="flex items-center space-x-2">
                                    <span :class="getStatusBadgeClass(image.analysis.status)" 
                                          class="px-3 py-1 rounded-full text-sm font-medium">
                                        <span x-text="getStatusText(image.analysis.status)"></span>
                                    </span>
                                    <span class="text-sm text-gray-500">
                                        Score: <span x-text="image.analysis.score"></span>/100
                                    </span>
                                </div>

                                <!-- ALT Text -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Current ALT Text</label>
                                    <div class="p-3 bg-gray-50 rounded-lg">
                                        <code x-text="image.alt || '(empty)'" class="text-sm"></code>
                                        <div class="text-xs text-gray-500 mt-1">
                                            Length: <span x-text="image.analysis.length"></span> characters, 
                                            <span x-text="image.analysis.word_count"></span> words
                                        </div>
                                    </div>
                                </div>

                                <!-- Issues -->
                                <div x-show="image.analysis.issues.length > 0">
                                    <label class="block text-sm font-medium text-red-700 mb-2">Issues Found</label>
                                    <ul class="space-y-1">
                                        <template x-for="issue in image.analysis.issues" :key="issue">
                                            <li class="flex items-center space-x-2 text-sm text-red-600">
                                                <i class="fas fa-exclamation-triangle text-xs"></i>
                                                <span x-text="issue"></span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>

                                <!-- Recommendations -->
                                <div>
                                    <label class="block text-sm font-medium text-green-700 mb-2">Recommendations</label>
                                    <ul class="space-y-1">
                                        <template x-for="recommendation in image.analysis.recommendations" :key="recommendation">
                                            <li class="flex items-center space-x-2 text-sm text-green-600">
                                                <i class="fas fa-lightbulb text-xs"></i>
                                                <span x-text="recommendation"></span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>

                                <!-- Context -->
                                <div x-show="image.context.parent_text || image.context.caption" class="pt-4 border-t border-gray-200">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Context</label>
                                    <div class="text-sm text-gray-600 space-y-1">
                                        <div x-show="image.context.parent_text">
                                            <strong>Surrounding text:</strong> <span x-text="image.context.parent_text.substring(0, 100) + (image.context.parent_text.length > 100 ? '...' : '')"></span>
                                        </div>
                                        <div x-show="image.context.caption">
                                            <strong>Caption:</strong> <span x-text="image.context.caption"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- Recent Audits Section Temporarily Disabled for Testing --}}
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

.fade-in {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

[x-cloak] { display: none !important; }
</style>

<script>
function imageAltChecker() {
    return {
        url: '',
        multiPage: false,
        maxPages: 5,
        loading: false,
        result: null,
        filterStatus: 'all',

        async analyzeImages() {
            if (!this.url || this.loading) return;

            this.loading = true;
            this.result = null;

            try {
                const formData = new FormData();
                formData.append('url', this.url);
                formData.append('multi_page', this.multiPage);
                formData.append('max_pages', this.maxPages);

                const response = await fetch('/api/image-alt/analyze', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    this.result = data.data;
                    this.showNotification(data.message || 'Analysis completed successfully!', 'success');
                } else {
                    this.showNotification(data.message || 'Analysis failed', 'error');
                }
            } catch (error) {
                console.error('Analysis error:', error);
                this.showNotification('Analysis failed: ' + error.message, 'error');
            } finally {
                this.loading = false;
            }
        },

        getFilteredImages() {
            if (!this.result?.images_data) return [];
            
            if (this.filterStatus === 'all') {
                return this.result.images_data;
            }
            
            return this.result.images_data.filter(image => {
                if (this.filterStatus === 'missing') {
                    return image.analysis.status === 'missing';
                } else if (this.filterStatus === 'needs_improvement') {
                    return image.analysis.status === 'needs_improvement' || image.analysis.status === 'poor';
                } else if (this.filterStatus === 'good') {
                    return image.analysis.status === 'good';
                }
                return true;
            });
        },

        getAccessibilityScore() {
            if (!this.result || this.result.total_images === 0) return 100;
            return Math.round((this.result.images_with_good_alt / this.result.total_images) * 100);
        },

        getScoreColor(score) {
            if (score >= 90) return 'bg-green-500';
            if (score >= 70) return 'bg-yellow-500';
            if (score >= 50) return 'bg-orange-500';
            return 'bg-red-500';
        },

        getScoreTextColor(score) {
            if (score >= 90) return 'text-green-600';
            if (score >= 70) return 'text-yellow-600';
            if (score >= 50) return 'text-orange-600';
            return 'text-red-600';
        },

        getScoreDescription(score) {
            if (score >= 90) return 'Excellent accessibility! Most images have proper alt text.';
            if (score >= 70) return 'Good accessibility with room for improvement.';
            if (score >= 50) return 'Fair accessibility. Many images need better alt text.';
            return 'Poor accessibility. Most images are missing or have inadequate alt text.';
        },

        getStatusBadgeClass(status) {
            switch (status) {
                case 'good': return 'bg-green-100 text-green-800';
                case 'needs_improvement': return 'bg-amber-100 text-amber-800';
                case 'poor': return 'bg-orange-100 text-orange-800';
                case 'missing': return 'bg-red-100 text-red-800';
                default: return 'bg-gray-100 text-gray-800';
            }
        },

        getStatusText(status) {
            switch (status) {
                case 'good': return 'Good ALT Text';
                case 'needs_improvement': return 'Needs Improvement';
                case 'poor': return 'Poor ALT Text';
                case 'missing': return 'Missing ALT';
                default: return 'Unknown';
            }
        },

        showNotification(message, type) {
            // You can implement a toast notification system here
            if (type === 'error') {
                alert(message);
            }
        }
    }
}
</script>
@endsection
