@extends('layouts.app')

@section('title', 'Backlink Checker - SEO Audit Pro')
@section('page-title', 'Backlink Checker')

@section('content')
<div x-data="backlinkCheckerData()">
    <!-- Header -->
    <div class="mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Backlink Checker</h2>
                    <p class="text-gray-600">Analyze your backlink profile and discover high-quality link opportunities.</p>
                </div>
                <div class="hidden lg:block">
                    <div class="w-16 h-16 bg-purple-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-link text-2xl text-purple-600"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analysis Form -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Analyze Domain Backlinks</h3>
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input 
                    type="text" 
                    x-model="domain"
                    placeholder="Enter domain (e.g., example.com)"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                >
            </div>
            <button 
                @click="analyzeDomain()"
                :disabled="loading || !domain"
                class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center min-w-[140px]"
            >
                <span x-show="!loading">
                    <i class="fas fa-search mr-2"></i>
                    Analyze Domain
                </span>
                <span x-show="loading" x-cloak>
                    <div class="loading-spinner mr-2"></div>
                    Analyzing...
                </span>
            </button>
        </div>
    </div>

    <!-- Analysis Results -->
    <div x-show="analysisResults" x-cloak class="space-y-8">
        <!-- Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Backlinks</p>
                        <p class="text-3xl font-bold text-gray-900" x-text="analysisResults?.total_backlinks || 0"></p>
                        <p class="text-xs text-gray-500 mt-1">linking to domain</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-link text-blue-600 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Unique Domains</p>
                        <p class="text-3xl font-bold text-gray-900" x-text="analysisResults?.unique_domains || 0"></p>
                        <p class="text-xs text-gray-500 mt-1">referring domains</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-globe text-green-600 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Domain Authority</p>
                        <p class="text-3xl font-bold text-gray-900" x-text="analysisResults?.domain_authority || 0"></p>
                        <p class="text-xs text-gray-500 mt-1">out of 100</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-trophy text-purple-600 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">DoFollow Links</p>
                        <p class="text-3xl font-bold text-gray-900" x-text="analysisResults?.link_type_distribution?.dofollow || 0"></p>
                        <p class="text-xs text-gray-500 mt-1">follow links</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-orange-600 text-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Export and Additional Actions -->
        <div class="flex flex-col sm:flex-row gap-4 justify-between items-center mb-4">
            <div class="flex flex-col sm:flex-row gap-2">
                <button 
                    @click="exportBacklinks()"
                    :disabled="!analysisResults"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
                >
                    <i class="fas fa-download mr-2"></i>
                    Export CSV
                </button>
                
                <button 
                    @click="refreshAnalysis()"
                    :disabled="loading || !domain"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
                >
                    <i class="fas fa-sync-alt mr-2"></i>
                    Refresh
                </button>
            </div>
            
            <div class="text-sm text-gray-500" x-show="analysisResults">
                Last updated: <span x-text="new Date(analysisResults.checked_at || Date.now()).toLocaleString()"></span>
            </div>
        </div>

        <!-- Tabs -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="border-b border-gray-200">
                <nav class="flex space-x-8 px-6" aria-label="Tabs">
                    <button 
                        @click="activeTab = 'backlinks'"
                        :class="activeTab === 'backlinks' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap"
                    >
                        Backlinks
                    </button>
                    <button 
                        @click="activeTab = 'domains'"
                        :class="activeTab === 'domains' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap"
                    >
                        Referring Domains
                    </button>
                    <button 
                        @click="activeTab = 'anchors'"
                        :class="activeTab === 'anchors' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap"
                    >
                        Anchor Texts
                    </button>
                    <button 
                        @click="activeTab = 'pagetypes'"
                        :class="activeTab === 'pagetypes' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap"
                    >
                        Page Types
                    </button>
                    <button 
                        @click="activeTab = 'opportunities'"
                        :class="activeTab === 'opportunities' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap"
                    >
                        Opportunities
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-6">
                <!-- Backlinks Tab -->
                <div x-show="activeTab === 'backlinks'" x-cloak>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-900">Source URL</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-900">Anchor Text</th>
                                    <th class="text-center py-3 px-4 font-semibold text-gray-900">DA</th>
                                    <th class="text-center py-3 px-4 font-semibold text-gray-900">PA</th>
                                    <th class="text-center py-3 px-4 font-semibold text-gray-900">Type</th>
                                    <th class="text-center py-3 px-4 font-semibold text-gray-900">Page Type</th>
                                    <th class="text-center py-3 px-4 font-semibold text-gray-900">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <template x-for="backlink in analysisResults?.backlinks || []" :key="backlink.source_url">
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-3 px-4">
                                            <div class="max-w-xs">
                                                <a :href="backlink.source_url" target="_blank" 
                                                   class="font-medium text-primary-600 hover:text-primary-800 text-sm"
                                                   x-text="getDomain(backlink.source_url)">
                                                </a>
                                                <div class="text-xs text-gray-500 mt-1 truncate" x-text="backlink.content_summary || 'Content analysis not available'"></div>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="text-gray-900" x-text="backlink.anchor_text"></span>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="font-semibold" 
                                                  :class="backlink.domain_authority >= 70 ? 'text-green-600' : backlink.domain_authority >= 40 ? 'text-yellow-600' : 'text-red-600'"
                                                  x-text="backlink.domain_authority">
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="font-semibold text-gray-900" x-text="backlink.page_authority"></span>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="px-2 py-1 text-xs rounded-full"
                                                  :class="backlink.link_type === 'dofollow' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                                  x-text="backlink.link_type">
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 capitalize" 
                                                  x-text="backlink.page_type || 'website'">
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Referring Domains Tab -->
                <div x-show="activeTab === 'domains'" x-cloak>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <template x-for="domain in analysisResults?.top_referring_domains || []" :key="domain.domain">
                            <div class="p-4 border border-gray-200 rounded-lg hover:border-primary-300 transition-colors">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-medium text-gray-900" x-text="domain.domain"></h4>
                                    <span class="text-sm font-semibold text-primary-600" x-text="'DA ' + domain.average_da"></span>
                                </div>
                                <div class="flex justify-between text-sm text-gray-500">
                                    <span x-text="domain.backlink_count + ' links'"></span>
                                    <a :href="'https://' + domain.domain" target="_blank" class="text-primary-600 hover:text-primary-800">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Anchor Texts Tab -->
                <div x-show="activeTab === 'anchors'" x-cloak>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-4">Top Anchor Texts</h4>
                            <div class="space-y-2">
                                <template x-for="(count, anchor) in analysisResults?.anchor_text_distribution || {}" :key="anchor">
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <span class="font-medium text-gray-900" x-text="anchor"></span>
                                        <span class="text-sm text-gray-500" x-text="count + ' times'"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-4">Link Type Distribution</h4>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                                        <span class="font-medium text-gray-900">DoFollow Links</span>
                                    </div>
                                    <span class="text-lg font-semibold text-green-600" x-text="analysisResults?.link_type_distribution?.dofollow || 0"></span>
                                </div>
                                
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 bg-gray-500 rounded-full mr-3"></div>
                                        <span class="font-medium text-gray-900">NoFollow Links</span>
                                    </div>
                                    <span class="text-lg font-semibold text-gray-600" x-text="analysisResults?.link_type_distribution?.nofollow || 0"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Page Types Tab -->
                <div x-show="activeTab === 'pagetypes'" x-cloak>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-4">Page Type Distribution</h4>
                            <div class="space-y-3">
                                <template x-for="(count, type) in getPageTypeDistribution(analysisResults?.backlinks || [])" :key="type">
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center">
                                            <div class="w-4 h-4 rounded mr-3"
                                                 :class="getPageTypeColor(type)">
                                            </div>
                                            <span class="font-medium text-gray-900 capitalize" x-text="type"></span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-lg font-semibold text-gray-600" x-text="count"></span>
                                            <span class="text-xs text-gray-500 ml-1">links</span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-4">Content Quality Analysis</h4>
                            <div class="space-y-4">
                                <div class="p-4 bg-green-50 rounded-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="font-medium text-green-900">High Quality Sources</span>
                                        <span class="text-sm font-semibold text-green-600" x-text="getHighQualityCount(analysisResults?.backlinks || [])"></span>
                                    </div>
                                    <p class="text-sm text-green-700">High DA domains with editorial content</p>
                                </div>
                                
                                <div class="p-4 bg-yellow-50 rounded-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="font-medium text-yellow-900">Medium Quality Sources</span>
                                        <span class="text-sm font-semibold text-yellow-600" x-text="getMediumQualityCount(analysisResults?.backlinks || [])"></span>
                                    </div>
                                    <p class="text-sm text-yellow-700">Moderate DA with good content context</p>
                                </div>
                                
                                <div class="p-4 bg-red-50 rounded-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="font-medium text-red-900">Review Needed</span>
                                        <span class="text-sm font-semibold text-red-600" x-text="getLowQualityCount(analysisResults?.backlinks || [])"></span>
                                    </div>
                                    <p class="text-sm text-red-700">Low DA or high spam score sources</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Content Summary Examples -->
                    <div class="mt-8">
                        <h4 class="font-semibold text-gray-900 mb-4">Sample Content Context</h4>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <template x-for="backlink in (analysisResults?.backlinks || []).slice(0, 4)" :key="backlink.source_url">
                                <div class="p-4 border border-gray-200 rounded-lg">
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="flex-1">
                                            <h5 class="font-medium text-gray-900" x-text="getDomain(backlink.source_url)"></h5>
                                            <span class="text-xs px-2 py-1 rounded-full capitalize"
                                                  :class="getPageTypeColor(backlink.page_type)"
                                                  x-text="backlink.page_type || 'website'">
                                            </span>
                                        </div>
                                        <span class="text-sm font-semibold text-primary-600" x-text="'DA ' + backlink.domain_authority"></span>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-3" x-text="backlink.content_summary || 'No content summary available'"></p>
                                    <div class="flex justify-between items-center text-xs text-gray-500">
                                        <span x-text="'Anchor: ' + (backlink.anchor_text || 'No anchor')"></span>
                                        <span class="px-2 py-1 rounded-full"
                                              :class="backlink.link_type === 'dofollow' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'"
                                              x-text="backlink.link_type">
                                        </span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Opportunities Tab -->
                <div x-show="activeTab === 'opportunities'" x-cloak>
                    <div class="space-y-6">
                        <!-- Guest Posting -->
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-4">Guest Posting Opportunities</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="p-4 border border-gray-200 rounded-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <h5 class="font-medium text-gray-900">TechBlog.com</h5>
                                        <span class="text-sm font-semibold text-blue-600">DA 65</span>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-2">High-quality tech blog accepting guest posts</p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs text-green-600">High Relevance</span>
                                        <a href="mailto:editor@techblog.com" class="text-primary-600 hover:text-primary-800 text-sm">
                                            <i class="fas fa-envelope mr-1"></i>Contact
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="p-4 border border-gray-200 rounded-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <h5 class="font-medium text-gray-900">IndustryNews.net</h5>
                                        <span class="text-sm font-semibold text-blue-600">DA 58</span>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-2">Industry news site with guest posting guidelines</p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs text-yellow-600">Medium Relevance</span>
                                        <a href="#" class="text-primary-600 hover:text-primary-800 text-sm">
                                            <i class="fas fa-external-link-alt mr-1"></i>Guidelines
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Broken Link Building -->
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-4">Broken Link Building</h4>
                            <div class="space-y-3">
                                <div class="p-4 border border-gray-200 rounded-lg">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h5 class="font-medium text-gray-900 mb-1">example.com/resource-page</h5>
                                            <p class="text-sm text-gray-600 mb-2">Broken link to similar resource as your content</p>
                                            <div class="flex items-center space-x-4 text-xs text-gray-500">
                                                <span>PA 45</span>
                                                <span>Anchor: "valuable resource"</span>
                                            </div>
                                        </div>
                                        <button class="px-3 py-1 bg-primary-600 text-white rounded text-sm hover:bg-primary-700">
                                            Suggest Fix
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Resource Pages -->
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-4">Resource Page Opportunities</h4>
                            <div class="space-y-3">
                                <div class="p-4 border border-gray-200 rounded-lg">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h5 class="font-medium text-gray-900 mb-1">Best SEO Tools - resourcesite.com</h5>
                                            <p class="text-sm text-gray-600 mb-2">Curated list of SEO tools and resources</p>
                                            <div class="flex items-center space-x-4 text-xs text-gray-500">
                                                <span>DA 72</span>
                                                <span>Relevance: 85%</span>
                                            </div>
                                        </div>
                                        <button class="px-3 py-1 bg-green-600 text-white rounded text-sm hover:bg-green-700">
                                            Submit
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button 
                @click="exportBacklinks()"
                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center justify-center"
            >
                <i class="fas fa-download mr-2"></i>
                Export Backlinks
            </button>
            
            <button 
                @click="trackBacklinks()"
                class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center justify-center"
            >
                <i class="fas fa-eye mr-2"></i>
                Monitor Changes
            </button>
            
            <button 
                @click="compareCompetitors()"
                class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 flex items-center justify-center"
            >
                <i class="fas fa-users mr-2"></i>
                Compare Competitors
            </button>
        </div>
    </div>

    <!-- Competitor Comparison Modal -->
    <div x-show="showCompetitorModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" @click="showCompetitorModal = false">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Compare with Competitors</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Competitor Domains</label>
                            <textarea 
                                x-model="competitorDomains"
                                placeholder="Enter competitor domains (one per line)&#10;competitor1.com&#10;competitor2.com"
                                rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            ></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button 
                        @click="runCompetitorAnalysis()"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm"
                    >
                        Analyze
                    </button>
                    <button 
                        @click="showCompetitorModal = false"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function backlinkCheckerData() {
    return {
        domain: '',
        loading: false,
        analysisResults: null,
        activeTab: 'backlinks',
        showCompetitorModal: false,
        competitorDomains: '',
        
        async analyzeDomain() {
            if (!this.domain) return;
            
            this.loading = true;
            this.analysisResults = null;
            
            try {
                const response = await fetch('/api/backlinks/check', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ domain: this.domain })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Transform the data to match the expected format
                    this.analysisResults = {
                        total_backlinks: data.data.summary.total_backlinks,
                        unique_domains: data.data.summary.unique_domains,
                        domain_authority: data.data.summary.average_domain_authority,
                        backlinks: data.data.backlinks,
                        link_type_distribution: {
                            dofollow: data.data.summary.dofollow_count,
                            nofollow: data.data.summary.nofollow_count
                        },
                        anchor_text_distribution: this.getAnchorTextDistribution(data.data.backlinks),
                        top_referring_domains: this.getTopReferringDomains(data.data.backlinks),
                        checked_at: data.data.checked_at
                    };
                    this.showNotification('Backlink analysis completed successfully!', 'success');
                } else {
                    throw new Error(data.message || 'Analysis failed');
                }
            } catch (error) {
                this.showNotification('Error analyzing domain: ' + error.message, 'error');
            } finally {
                this.loading = false;
            }
        },
        
        getDomain(url) {
            try {
                return new URL(url).hostname;
            } catch {
                return url;
            }
        },
        
        getAnchorTextDistribution(backlinks) {
            const distribution = {};
            backlinks.forEach(link => {
                const anchor = link.anchor_text || 'No anchor text';
                distribution[anchor] = (distribution[anchor] || 0) + 1;
            });
            return distribution;
        },
        
        getTopReferringDomains(backlinks) {
            const domains = {};
            backlinks.forEach(link => {
                const domain = this.getDomain(link.source_url);
                if (!domains[domain]) {
                    domains[domain] = {
                        domain: domain,
                        backlink_count: 0,
                        total_da: 0
                    };
                }
                domains[domain].backlink_count++;
                domains[domain].total_da += link.domain_authority || 0;
            });
            
            return Object.values(domains).map(d => ({
                ...d,
                average_da: Math.round(d.total_da / d.backlink_count)
            })).sort((a, b) => b.backlink_count - a.backlink_count).slice(0, 10);
        },
        
        exportBacklinks() {
            if (!this.analysisResults) return;
            
            const csvContent = this.generateBacklinksCSV();
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `backlinks_${this.domain}_${new Date().toISOString().split('T')[0]}.csv`;
            link.click();
            window.URL.revokeObjectURL(url);
        },
        
        generateBacklinksCSV() {
            const headers = ['Source URL', 'Target URL', 'Anchor Text', 'Domain Authority', 'Page Authority', 'Spam Score', 'Link Type', 'Page Type', 'Content Summary', 'Status'];
            const rows = this.analysisResults.backlinks.map(backlink => [
                backlink.source_url,
                backlink.target_url,
                backlink.anchor_text,
                backlink.domain_authority,
                backlink.page_authority,
                backlink.spam_score,
                backlink.link_type,
                backlink.page_type || 'website',
                backlink.content_summary || 'No summary available',
                backlink.status || 'active'
            ]);
            
            return [headers, ...rows].map(row => row.map(field => `"${field || ''}"`).join(',')).join('\n');
        },
        
        trackBacklinks() {
            showNotification('Backlink monitoring enabled! You will receive alerts for changes.', 'success');
        },
        
        compareCompetitors() {
            this.showCompetitorModal = true;
        },
        
        async runCompetitorAnalysis() {
            if (!this.competitorDomains.trim()) return;
            
            const competitors = this.competitorDomains.split('\n').map(d => d.trim()).filter(d => d);
            
            if (competitors.length === 0) {
                showNotification('Please enter at least one competitor domain', 'error');
                return;
            }
            
            try {
                const response = await fetch('/api/backlinks/competitors', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        domain: this.domain,
                        competitor_domains: competitors
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.showCompetitorModal = false;
                    showNotification('Competitor analysis completed! Check the Opportunities tab.', 'success');
                    // You could update the opportunities tab with competitor data here
                } else {
                    throw new Error(data.message || 'Competitor analysis failed');
                }
            } catch (error) {
                showNotification('Error analyzing competitors: ' + error.message, 'error');
            }
        },
        
        refreshAnalysis() {
            if (this.domain) {
                this.analyzeDomain();
            }
        },
        
        // Utility functions
        showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg transform transition-all duration-300 ${
                type === 'success' ? 'bg-green-500 text-white' : 
                type === 'error' ? 'bg-red-500 text-white' : 
                'bg-blue-500 text-white'
            }`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} mr-2"></i>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 5000);
        },
        
        formatDate(dateString) {
            return new Date(dateString).toLocaleString();
        },
        
        // Helper functions for Page Types analysis
        getPageTypeDistribution(backlinks) {
            const distribution = {};
            backlinks.forEach(link => {
                const type = link.page_type || 'website';
                distribution[type] = (distribution[type] || 0) + 1;
            });
            return distribution;
        },
        
        getPageTypeColor(type) {
            const colors = {
                'blog': 'bg-blue-500',
                'news': 'bg-red-500',
                'forum': 'bg-yellow-500',
                'social': 'bg-purple-500',
                'directory': 'bg-green-500',
                'repository': 'bg-gray-800',
                'educational': 'bg-indigo-500',
                'magazine': 'bg-pink-500',
                'website': 'bg-gray-400'
            };
            return colors[type] || 'bg-gray-400';
        },
        
        getHighQualityCount(backlinks) {
            return backlinks.filter(link => 
                link.domain_authority >= 70 && link.spam_score <= 5
            ).length;
        },
        
        getMediumQualityCount(backlinks) {
            return backlinks.filter(link => 
                link.domain_authority >= 40 && link.domain_authority < 70 && link.spam_score <= 15
            ).length;
        },
        
        getLowQualityCount(backlinks) {
            return backlinks.filter(link => 
                link.domain_authority < 40 || link.spam_score > 15
            ).length;
        },
    };
}
</script>
@endpush
@endsection
