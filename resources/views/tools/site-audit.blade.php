@extends('layouts.app')

@section('title', 'Site Audit Tool - SEO Audit Pro')
@section('page-title', 'Site Audit Tool')

@section('content')
<div x-data="siteAuditData()">
    <!-- Header -->
    <div class="mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Complete Site Audit</h2>
                    <p class="text-gray-600">Comprehensive SEO analysis including on-page issues, technical SEO, and performance metrics.</p>
                </div>
                <div class="hidden lg:block">
                    <div class="w-16 h-16 bg-primary-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-search text-2xl text-primary-600"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Audit Form -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 mb-8">
        <div class="max-w-2xl mx-auto">
            <div class="text-center mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Enter Website URL</h3>
                <p class="text-gray-600">We'll analyze your website for SEO issues and provide detailed recommendations.</p>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Website URL</label>
                    <input 
                        type="url" 
                        x-model="auditUrl"
                        placeholder="https://example.com"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    >
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Audit Type</label>
                        <select x-model="auditType" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <option value="basic">Basic Audit</option>
                            <option value="comprehensive">Comprehensive Audit</option>
                            <option value="technical">Technical SEO Focus</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Device Type</label>
                        <select x-model="deviceType" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <option value="desktop">Desktop</option>
                            <option value="mobile">Mobile</option>
                            <option value="both">Both</option>
                        </select>
                    </div>
                </div>
                
                <button 
                    @click="runAudit()"
                    :disabled="loading || !auditUrl"
                    class="w-full px-6 py-4 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center text-lg font-semibold"
                >
                    <span x-show="!loading">
                        <i class="fas fa-search mr-3"></i>
                        Start Site Audit
                    </span>
                    <span x-show="loading" x-cloak>
                        <div class="loading-spinner mr-3"></div>
                        Analyzing Website...
                    </span>
                </button>
                
                <!-- View History Button -->
                <a href="{{ route('site-audit.history') }}" 
                   class="w-full px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 flex items-center justify-center font-medium">
                    <i class="fas fa-history mr-2"></i>
                    View Audit History
                </a>
            </div>
        </div>
    </div>

    <!-- Progress Bar -->
    <div x-show="loading" x-cloak class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 mb-8">
        <div class="text-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Analyzing Your Website</h3>
            <p class="text-gray-600">This may take a few moments...</p>
        </div>
        
        <div class="w-full bg-gray-200 rounded-full h-3 mb-4">
            <div class="bg-primary-600 h-3 rounded-full transition-all duration-500" :style="`width: ${progress}%`"></div>
        </div>
        
        <div class="text-center text-sm text-gray-600" x-text="progressText"></div>
    </div>

    <!-- Audit Results -->
    <div x-show="auditResults" x-cloak class="space-y-8">
        <!-- Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Overall Score</p>
                        <p class="text-3xl font-bold" :class="getScoreColor(auditResults?.audit_score)" x-text="auditResults?.audit_score || 0"></p>
                        <p class="text-xs text-gray-500 mt-1">out of 100</p>
                    </div>
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center" :class="getScoreBgColor(auditResults?.audit_score)">
                        <i class="fas fa-trophy text-lg" :class="getScoreTextColor(auditResults?.audit_score)"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Load Speed</p>
                        <p class="text-3xl font-bold text-gray-900" x-text="auditResults?.page_load_speed || 0"></p>
                        <p class="text-xs text-gray-500 mt-1">milliseconds</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-tachometer-alt text-green-600 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Issues Found</p>
                        <p class="text-3xl font-bold text-red-600" x-text="auditResults?.recommendations?.length || 0"></p>
                        <p class="text-xs text-gray-500 mt-1">recommendations</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Links</p>
                        <p class="text-3xl font-bold text-gray-900" x-text="(auditResults?.internal_links_count || 0) + (auditResults?.external_links_count || 0)"></p>
                        <p class="text-xs text-gray-500 mt-1">internal + external</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-link text-blue-600 text-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Results Tabs -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="border-b border-gray-200">
                <nav class="flex space-x-8 px-6" aria-label="Tabs">
                    <button 
                        @click="activeTab = 'overview'"
                        :class="activeTab === 'overview' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap"
                    >
                        Overview
                    </button>
                    <button 
                        @click="activeTab = 'technical'"
                        :class="activeTab === 'technical' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap"
                    >
                        Technical SEO
                    </button>
                    <button 
                        @click="activeTab = 'content'"
                        :class="activeTab === 'content' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap"
                    >
                        Content Analysis
                    </button>
                    <button 
                        @click="activeTab = 'performance'"
                        :class="activeTab === 'performance' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap"
                    >
                        Performance
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-6">
                <!-- Overview Tab -->
                <div x-show="activeTab === 'overview'" x-cloak>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Basic Information -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="font-medium text-gray-700">Page Title</span>
                                    <span class="text-gray-600" x-text="auditResults?.title || 'Not found'"></span>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="font-medium text-gray-700">Meta Description</span>
                                    <span class="text-gray-600" x-text="auditResults?.meta_description ? 'Present' : 'Missing'"></span>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="font-medium text-gray-700">Status Code</span>
                                    <span class="text-gray-600" x-text="auditResults?.status_code || 'Unknown'"></span>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="font-medium text-gray-700">SSL Certificate</span>
                                    <span :class="auditResults?.ssl_certificate ? 'text-green-600' : 'text-red-600'" x-text="auditResults?.ssl_certificate ? 'Valid' : 'Missing'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Content Structure -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Content Structure</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="font-medium text-gray-700">H1 Tags</span>
                                    <span class="text-gray-600" x-text="auditResults?.h1_tags?.length || 0"></span>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="font-medium text-gray-700">H2 Tags</span>
                                    <span class="text-gray-600" x-text="auditResults?.h2_tags?.length || 0"></span>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="font-medium text-gray-700">Word Count</span>
                                    <span class="text-gray-600" x-text="auditResults?.word_count || 0"></span>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="font-medium text-gray-700">Images</span>
                                    <span class="text-gray-600" x-text="auditResults?.images_count || 0"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Technical Tab -->
                <div x-show="activeTab === 'technical'" x-cloak>
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Technical SEO Checks</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="p-4 border border-gray-200 rounded-lg">
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium text-gray-700">Canonical URL</span>
                                        <div class="flex items-center">
                                            <i :class="auditResults?.canonical_url ? 'fas fa-check-circle text-green-500' : 'fas fa-times-circle text-red-500'"></i>
                                            <span class="ml-2 text-sm" :class="auditResults?.canonical_url ? 'text-green-600' : 'text-red-600'" x-text="auditResults?.canonical_url ? 'Found' : 'Missing'"></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="p-4 border border-gray-200 rounded-lg">
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium text-gray-700">Robots Meta</span>
                                        <div class="flex items-center">
                                            <i :class="auditResults?.robots_meta ? 'fas fa-check-circle text-green-500' : 'fas fa-times-circle text-red-500'"></i>
                                            <span class="ml-2 text-sm" :class="auditResults?.robots_meta ? 'text-green-600' : 'text-red-600'" x-text="auditResults?.robots_meta || 'Not set'"></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="p-4 border border-gray-200 rounded-lg">
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium text-gray-700">Mobile Friendly</span>
                                        <div class="flex items-center">
                                            <i :class="auditResults?.mobile_friendly ? 'fas fa-check-circle text-green-500' : 'fas fa-times-circle text-red-500'"></i>
                                            <span class="ml-2 text-sm" :class="auditResults?.mobile_friendly ? 'text-green-600' : 'text-red-600'" x-text="auditResults?.mobile_friendly ? 'Yes' : 'No'"></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="p-4 border border-gray-200 rounded-lg">
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium text-gray-700">Schema Markup</span>
                                        <div class="flex items-center">
                                            <i :class="auditResults?.schema_markup?.length > 0 ? 'fas fa-check-circle text-green-500' : 'fas fa-times-circle text-red-500'"></i>
                                            <span class="ml-2 text-sm" :class="auditResults?.schema_markup?.length > 0 ? 'text-green-600' : 'text-red-600'" x-text="auditResults?.schema_markup?.length > 0 ? 'Found' : 'Missing'"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sitemap & Robots -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Crawling & Indexing</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="p-4 border border-gray-200 rounded-lg">
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium text-gray-700">XML Sitemap</span>
                                        <div class="flex items-center">
                                            <i :class="auditResults?.sitemap_url ? 'fas fa-check-circle text-green-500' : 'fas fa-times-circle text-red-500'"></i>
                                            <span class="ml-2 text-sm" :class="auditResults?.sitemap_url ? 'text-green-600' : 'text-red-600'" x-text="auditResults?.sitemap_url ? 'Found' : 'Not found'"></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="p-4 border border-gray-200 rounded-lg">
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium text-gray-700">Robots.txt</span>
                                        <div class="flex items-center">
                                            <i :class="auditResults?.robots_txt_status === 'found' ? 'fas fa-check-circle text-green-500' : 'fas fa-times-circle text-red-500'"></i>
                                            <span class="ml-2 text-sm" :class="auditResults?.robots_txt_status === 'found' ? 'text-green-600' : 'text-red-600'" x-text="auditResults?.robots_txt_status === 'found' ? 'Found' : 'Not found'"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content Tab -->
                <div x-show="activeTab === 'content'" x-cloak>
                    <div class="space-y-6">
                        <!-- Heading Structure -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Heading Structure</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="space-y-2">
                                    <template x-for="(h1, index) in auditResults?.h1_tags || []" :key="index">
                                        <div class="flex items-center space-x-2">
                                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium">H1</span>
                                            <span class="text-gray-700" x-text="h1"></span>
                                        </div>
                                    </template>
                                    
                                    <template x-for="(h2, index) in (auditResults?.h2_tags || []).slice(0, 5)" :key="'h2-' + index">
                                        <div class="flex items-center space-x-2">
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">H2</span>
                                            <span class="text-gray-700" x-text="h2"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Images Analysis -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Images Analysis</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-blue-50 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-blue-600" x-text="auditResults?.images_count || 0"></div>
                                    <div class="text-sm text-blue-800">Total Images</div>
                                </div>
                                <div class="bg-red-50 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-red-600" x-text="auditResults?.images_without_alt || 0"></div>
                                    <div class="text-sm text-red-800">Missing Alt Text</div>
                                </div>
                                <div class="bg-green-50 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-green-600" x-text="(auditResults?.images_count || 0) - (auditResults?.images_without_alt || 0)"></div>
                                    <div class="text-sm text-green-800">With Alt Text</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance Tab -->
                <div x-show="activeTab === 'performance'" x-cloak>
                    <div class="space-y-6">
                        <!-- Speed Metrics -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance Metrics</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-blue-600" x-text="auditResults?.page_load_speed || 0"></div>
                                    <div class="text-sm text-blue-800">Load Time (ms)</div>
                                </div>
                                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-green-600" x-text="auditResults?.internal_links_count || 0"></div>
                                    <div class="text-sm text-green-800">Internal Links</div>
                                </div>
                                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-purple-600" x-text="auditResults?.external_links_count || 0"></div>
                                    <div class="text-sm text-purple-800">External Links</div>
                                </div>
                                <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-orange-600" x-text="auditResults?.word_count || 0"></div>
                                    <div class="text-sm text-orange-800">Word Count</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recommendations -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">SEO Recommendations</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <template x-for="(recommendation, index) in auditResults?.recommendations || []" :key="index">
                        <div class="flex items-start space-x-3 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex-shrink-0">
                                <i class="fas fa-lightbulb text-yellow-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-gray-800" x-text="recommendation"></p>
                            </div>
                        </div>
                    </template>
                    
                    <div x-show="!auditResults?.recommendations || auditResults.recommendations.length === 0" class="text-center py-8">
                        <i class="fas fa-check-circle text-green-500 text-3xl mb-2"></i>
                        <p class="text-gray-600">Great! No major SEO issues found.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button 
                @click="generateReport()"
                class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 flex items-center justify-center"
            >
                <i class="fas fa-eye mr-2"></i>
                View Detailed Report
            </button>
            
            <button 
                @click="saveAudit()"
                class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center justify-center"
            >
                <i class="fas fa-check mr-2"></i>
                View in History
            </button>
            
            <button 
                @click="runNewAudit()"
                class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 flex items-center justify-center"
            >
                <i class="fas fa-redo mr-2"></i>
                New Audit
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function siteAuditData() {
    return {
        auditUrl: '',
        auditType: 'comprehensive',
        deviceType: 'desktop',
        loading: false,
        progress: 0,
        progressText: '',
        auditResults: null,
        activeTab: 'overview',
        
        async runAudit() {
            if (!this.auditUrl) {
                showNotification('Please enter a valid URL', 'error');
                return;
            }
            
            this.loading = true;
            this.progress = 0;
            this.auditResults = null;
            
            // Simulate progress
            this.simulateProgress();
            
            try {
                const response = await apiRequest('/api/site-audit', {
                    method: 'POST',
                    body: {
                        url: this.auditUrl
                    }
                });
                
                if (response.success) {
                    this.auditResults = response.data;
                    this.auditResults.audit_id = response.audit_id;
                    this.progress = 100;
                    this.progressText = 'Audit completed!';
                    showNotification('Site audit completed successfully!', 'success');
                } else {
                    throw new Error(response.message || 'Audit failed');
                }
            } catch (error) {
                console.error('Audit error:', error);
                showNotification('Error running audit: ' + error.message, 'error');
            } finally {
                this.loading = false;
            }
        },
        
        simulateProgress() {
            const steps = [
                { progress: 10, text: 'Fetching webpage...' },
                { progress: 25, text: 'Analyzing HTML structure...' },
                { progress: 40, text: 'Checking meta tags...' },
                { progress: 55, text: 'Testing page speed...' },
                { progress: 70, text: 'Analyzing links...' },
                { progress: 85, text: 'Checking technical SEO...' },
                { progress: 95, text: 'Generating recommendations...' }
            ];
            
            let currentStep = 0;
            const interval = setInterval(() => {
                if (currentStep < steps.length && this.loading) {
                    this.progress = steps[currentStep].progress;
                    this.progressText = steps[currentStep].text;
                    currentStep++;
                } else {
                    clearInterval(interval);
                }
            }, 800);
        },
        
        getScoreColor(score) {
            if (score >= 80) return 'text-green-600';
            if (score >= 60) return 'text-yellow-600';
            return 'text-red-600';
        },
        
        getScoreBgColor(score) {
            if (score >= 80) return 'bg-green-100';
            if (score >= 60) return 'bg-yellow-100';
            return 'bg-red-100';
        },
        
        getScoreTextColor(score) {
            if (score >= 80) return 'text-green-600';
            if (score >= 60) return 'text-yellow-600';
            return 'text-red-600';
        },
        
        async generateReport() {
            if (!this.auditResults) return;
            
            try {
                showNotification('Redirecting to detailed view...', 'info');
                
                // Redirect to detailed view where user can see full report
                if (this.auditResults.audit_id) {
                    window.location.href = `/site-audit/${this.auditResults.audit_id}`;
                } else {
                    showNotification('Please save the audit first', 'warning');
                }
                
            } catch (error) {
                showNotification('Error generating report: ' + error.message, 'error');
            }
        },
        
        async saveAudit() {
            if (!this.auditResults) return;
            
            // Since audit is already saved in the database, show success message
            showNotification('Audit has been saved to your history!', 'success');
            
            // Optionally redirect to audit detail page
            if (this.auditResults.audit_id) {
                setTimeout(() => {
                    window.location.href = `/site-audit/${this.auditResults.audit_id}`;
                }, 1000);
            }
        },
        
        runNewAudit() {
            this.auditUrl = '';
            this.auditResults = null;
            this.loading = false;
            this.progress = 0;
            this.progressText = '';
            this.activeTab = 'overview';
        }
    };
}
</script>
@endpush
@endsection
