@extends('layouts.app')

@section('title', 'SERP Preview Tool - SEO Audit Pro')
@section('page-title', 'SERP Preview Tool')

@section('content')
<div x-data="serpPreviewData()">
    <!-- Header -->
    <div class="mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">SERP Preview Tool</h1>
                    <p class="text-gray-600">Preview how your page will appear in Google search results and optimize for better click-through rates.</p>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                    <button 
                        @click="showSavedPreviews = !showSavedPreviews"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        <i class="fas fa-history mr-2"></i>
                        <span x-text="showSavedPreviews ? 'Hide Saved' : 'Saved Previews'"></span>
                    </button>
                    @endauth
                    <div class="hidden lg:block">
                        <div class="w-16 h-16 bg-green-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-search text-green-600 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Input Form -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Enter Page Details</h3>
        
        <div class="space-y-6">
            <!-- Page Title -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Page Title
                    <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    x-model="pageTitle"
                    placeholder="Enter your page title..."
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    maxlength="100"
                >
                <div class="flex justify-between items-center mt-2">
                    <div class="flex items-center space-x-4">
                        <span 
                            :class="titleLength <= 60 ? 'text-green-600' : 'text-red-600'"
                            class="text-sm font-medium"
                            x-text="titleLength + ' / 60 characters'"
                        ></span>
                        <span 
                            x-show="titleLength > 60"
                            class="text-xs text-red-600"
                        >
                            ⚠️ Title may be truncated in search results
                        </span>
                        <span 
                            x-show="titleLength < 30"
                            class="text-xs text-orange-600"
                        >
                            💡 Consider a longer title for better SEO
                        </span>
                    </div>
                </div>
            </div>

            <!-- Target URL -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Target URL
                    <span class="text-red-500">*</span>
                </label>
                <input 
                    type="url" 
                    x-model="targetUrl"
                    placeholder="https://example.com/page"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                >
            </div>

            <!-- Meta Description -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Meta Description
                    <span class="text-red-500">*</span>
                </label>
                <textarea 
                    x-model="metaDescription"
                    placeholder="Enter your meta description..."
                    rows="3"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent resize-none"
                    maxlength="200"
                ></textarea>
                <div class="flex justify-between items-center mt-2">
                    <div class="flex items-center space-x-4">
                        <span 
                            :class="descriptionLength <= 160 ? 'text-green-600' : 'text-red-600'"
                            class="text-sm font-medium"
                            x-text="descriptionLength + ' / 160 characters'"
                        ></span>
                        <span 
                            x-show="descriptionLength > 160"
                            class="text-xs text-red-600"
                        >
                            ⚠️ Description may be truncated
                        </span>
                        <span 
                            x-show="descriptionLength < 120"
                            class="text-xs text-orange-600"
                        >
                            💡 Consider a longer description
                        </span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            @auth
            <div class="flex flex-wrap gap-4">
                <button 
                    @click="savePreview()"
                    :disabled="!pageTitle || !targetUrl || !metaDescription"
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                    <i class="fas fa-save mr-2"></i>
                    Save Preview
                </button>
                <button 
                    @click="exportPdf()"
                    :disabled="!pageTitle || !targetUrl || !metaDescription"
                    class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                    <i class="fas fa-file-pdf mr-2"></i>
                    Export PDF
                </button>
            </div>
            @else
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    <a href="{{ route('login') }}" class="font-medium underline">Login</a> to save previews and export PDFs.
                </p>
            </div>
            @endauth
        </div>
    </div>

    <!-- SERP Preview -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Search Result Preview</h3>
        
        <!-- Google Search Bar Mockup -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg border">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                    <span class="text-white font-bold text-sm">G</span>
                </div>
                <div class="flex-1 bg-white border border-gray-300 rounded-full px-4 py-2">
                    <span class="text-gray-600">your search query</span>
                </div>
                <button class="px-4 py-2 bg-blue-600 text-white rounded text-sm">Search</button>
            </div>
            <div class="text-sm text-gray-600">About 1,234,567 results (0.45 seconds)</div>
        </div>

        <!-- Search Result -->
        <div class="border-l-4 border-blue-500 pl-4 bg-gray-50 p-4 rounded-r-lg">
            <div class="max-w-2xl">
                <!-- URL -->
                <div class="text-sm text-green-700 mb-1" x-show="targetUrl">
                    <span x-text="formattedUrl"></span>
                    <i class="fas fa-chevron-down ml-1 text-xs"></i>
                </div>
                
                <!-- Title -->
                <div class="mb-2">
                    <h3 class="text-xl text-blue-600 hover:underline cursor-pointer" x-show="pageTitle">
                        <span x-text="displayTitle"></span>
                    </h3>
                    <div x-show="!pageTitle" class="text-xl text-gray-400 italic">
                        Your page title will appear here...
                    </div>
                </div>
                
                <!-- Description -->
                <div class="text-sm text-gray-600 leading-relaxed">
                    <span x-show="metaDescription" x-text="displayDescription"></span>
                    <span x-show="!metaDescription" class="italic">
                        Your meta description will appear here...
                    </span>
                </div>
                
                <!-- Additional elements to make it look realistic -->
                <div class="mt-3 flex items-center space-x-4 text-xs text-gray-500">
                    <span>★★★★☆ 4.5 · 2,341 reviews</span>
                    <span>•</span>
                    <span>$$$</span>
                </div>
            </div>
        </div>

        <!-- Optimization Tips -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <h4 class="font-semibold text-green-800 mb-2">
                    <i class="fas fa-check-circle mr-2"></i>
                    Good Practices
                </h4>
                <ul class="text-sm text-green-700 space-y-1">
                    <li x-show="titleLength >= 30 && titleLength <= 60">✓ Title length is optimal</li>
                    <li x-show="descriptionLength >= 120 && descriptionLength <= 160">✓ Description length is optimal</li>
                    <li x-show="targetUrl">✓ URL is provided</li>
                    <li x-show="pageTitle && pageTitle.includes('|')">✓ Brand included in title</li>
                </ul>
            </div>
            
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                <h4 class="font-semibold text-orange-800 mb-2">
                    <i class="fas fa-lightbulb mr-2"></i>
                    Suggestions
                </h4>
                <ul class="text-sm text-orange-700 space-y-1">
                    <li x-show="titleLength < 30">• Consider a longer, more descriptive title</li>
                    <li x-show="titleLength > 60">• Shorten title to avoid truncation</li>
                    <li x-show="descriptionLength < 120">• Add more details to meta description</li>
                    <li x-show="descriptionLength > 160">• Shorten description to avoid truncation</li>
                    <li x-show="pageTitle && !pageTitle.includes('|')">• Consider adding your brand name</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Saved Previews (for authenticated users) -->
    @auth
    <div x-show="showSavedPreviews" class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Saved Previews</h3>
            <button 
                @click="loadSavedPreviews()"
                class="text-blue-600 hover:text-blue-800"
            >
                <i class="fas fa-refresh mr-1"></i>
                Refresh
            </button>
        </div>
        
        <div class="space-y-4">
            <template x-for="preview in savedPreviews" :key="preview.id">
                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900" x-text="preview.preview_name"></h4>
                            <div class="text-sm text-blue-600 mt-1" x-text="preview.page_title"></div>
                            <div class="text-sm text-gray-600 mt-1" x-text="preview.meta_description"></div>
                            <div class="text-xs text-gray-500 mt-2" x-text="'Created: ' + new Date(preview.created_at).toLocaleDateString()"></div>
                        </div>
                        <div class="flex items-center space-x-2 ml-4">
                            <button 
                                @click="loadPreview(preview)"
                                class="text-blue-600 hover:text-blue-800"
                                title="Load this preview"
                            >
                                <i class="fas fa-eye"></i>
                            </button>
                            <button 
                                @click="deletePreview(preview.id)"
                                class="text-red-600 hover:text-red-800"
                                title="Delete this preview"
                            >
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
            
            <div x-show="savedPreviews.length === 0" class="text-center py-8 text-gray-500">
                <i class="fas fa-search text-3xl mb-4"></i>
                <p>No saved previews found. Create and save your first preview above.</p>
            </div>
        </div>
    </div>
    @endauth

    <!-- Error/Success Messages -->
    <div x-show="message" class="fixed bottom-4 right-4 z-50">
        <div 
            :class="messageType === 'success' ? 'bg-green-600' : 'bg-red-600'"
            class="text-white px-6 py-3 rounded-lg shadow-lg"
        >
            <span x-text="message"></span>
        </div>
    </div>
</div>

<script>
function serpPreviewData() {
    return {
        pageTitle: '',
        targetUrl: '',
        metaDescription: '',
        showSavedPreviews: false,
        savedPreviews: @json($recentPreviews ?? []),
        message: '',
        messageType: 'success',

        get titleLength() {
            return this.pageTitle.length;
        },

        get descriptionLength() {
            return this.metaDescription.length;
        },

        get displayTitle() {
            return this.pageTitle.length > 60 
                ? this.pageTitle.substring(0, 60) + '...' 
                : this.pageTitle;
        },

        get displayDescription() {
            return this.metaDescription.length > 160 
                ? this.metaDescription.substring(0, 160) + '...' 
                : this.metaDescription;
        },

        get formattedUrl() {
            if (!this.targetUrl) return '';
            let url = this.targetUrl.replace(/^https?:\/\//, '').replace(/^www\./, '');
            return url.length > 50 ? url.substring(0, 50) + '...' : url;
        },

        async savePreview() {
            if (!this.pageTitle || !this.targetUrl || !this.metaDescription) {
                this.showMessage('Please fill in all required fields', 'error');
                return;
            }

            try {
                const response = await fetch('/api/serp-preview/save', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        page_title: this.pageTitle,
                        target_url: this.targetUrl,
                        meta_description: this.metaDescription
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.showMessage('Preview saved successfully!', 'success');
                    this.loadSavedPreviews();
                } else {
                    this.showMessage(data.message || 'Failed to save preview', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                this.showMessage('Network error occurred', 'error');
            }
        },

        async exportPdf() {
            if (!this.pageTitle || !this.targetUrl || !this.metaDescription) {
                this.showMessage('Please fill in all required fields', 'error');
                return;
            }

            try {
                const response = await fetch('/api/serp-preview/export-pdf', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        page_title: this.pageTitle,
                        target_url: this.targetUrl,
                        meta_description: this.metaDescription
                    })
                });

                if (response.ok) {
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `serp-preview-${new Date().toISOString().split('T')[0]}.pdf`;
                    a.click();
                    window.URL.revokeObjectURL(url);
                    this.showMessage('PDF exported successfully!', 'success');
                } else {
                    this.showMessage('Failed to export PDF', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                this.showMessage('Network error occurred', 'error');
            }
        },

        async loadSavedPreviews() {
            try {
                const response = await fetch('/api/serp-preview/previews');
                const data = await response.json();

                if (data.success) {
                    this.savedPreviews = data.data.data || [];
                }
            } catch (error) {
                console.error('Error loading previews:', error);
            }
        },

        loadPreview(preview) {
            this.pageTitle = preview.page_title;
            this.targetUrl = preview.target_url;
            this.metaDescription = preview.meta_description;
            this.showMessage('Preview loaded!', 'success');
        },

        async deletePreview(previewId) {
            if (!confirm('Are you sure you want to delete this preview?')) return;

            try {
                const response = await fetch('/api/serp-preview/delete', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        preview_id: previewId
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.savedPreviews = this.savedPreviews.filter(p => p.id !== previewId);
                    this.showMessage('Preview deleted successfully!', 'success');
                } else {
                    this.showMessage(data.message || 'Failed to delete preview', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                this.showMessage('Network error occurred', 'error');
            }
        },

        showMessage(text, type = 'success') {
            this.message = text;
            this.messageType = type;
            setTimeout(() => {
                this.message = '';
            }, 3000);
        }
    }
}
</script>
@endsection
