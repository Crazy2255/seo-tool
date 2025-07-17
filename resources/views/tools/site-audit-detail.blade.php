@extends('layouts.app')

@section('title', 'Site Audit Details - SEO Audit Pro')
@section('page-title', 'Site Audit Details')

@section('content')
<div>
    <!-- Header -->
    <div class="mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Site Audit Details</h1>
                    <p class="text-gray-600">Detailed analysis results for {{ $audit->url }}</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('tools.site-audit') }}?url={{ urlencode($audit->url) }}" 
                       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-redo mr-2"></i>
                        Re-audit
                    </a>
                    <a href="{{ route('site-audit.history') }}" 
                       class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to History
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Audit Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Overall Score</p>
                    <p class="text-3xl font-bold 
                        @if($audit->audit_score >= 80) text-green-600
                        @elseif($audit->audit_score >= 60) text-yellow-600
                        @else text-red-600
                        @endif">
                        {{ $audit->audit_score }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">out of 100</p>
                </div>
                <div class="w-12 h-12 rounded-lg flex items-center justify-center
                    @if($audit->audit_score >= 80) bg-green-100
                    @elseif($audit->audit_score >= 60) bg-yellow-100
                    @else bg-red-100
                    @endif">
                    <i class="fas fa-trophy text-lg
                        @if($audit->audit_score >= 80) text-green-600
                        @elseif($audit->audit_score >= 60) text-yellow-600
                        @else text-red-600
                        @endif"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Load Speed</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $audit->page_load_speed }}</p>
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
                    <p class="text-3xl font-bold text-red-600">{{ is_array($audit->recommendations) ? count($audit->recommendations) : 0 }}</p>
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
                    <p class="text-3xl font-bold text-gray-900">{{ ($audit->internal_links_count + $audit->external_links_count) }}</p>
                    <p class="text-xs text-gray-500 mt-1">internal + external</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-link text-blue-600 text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Basic Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Basic Information</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">URL</label>
                        <p class="text-gray-900 break-all">{{ $audit->url }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Title</label>
                        <p class="text-gray-900">{{ $audit->title ?: 'No title found' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Meta Description</label>
                        <p class="text-gray-900">{{ $audit->meta_description ?: 'No meta description found' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Status Code</label>
                        <span class="px-3 py-1 rounded-full text-xs font-medium
                            @if($audit->status_code == 200) bg-green-100 text-green-800
                            @elseif($audit->status_code >= 300 && $audit->status_code < 400) bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ $audit->status_code }}
                        </span>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Audit Date</label>
                        <p class="text-gray-900">{{ $audit->created_at->format('M j, Y g:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Technical SEO -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Technical SEO</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">SSL Certificate</span>
                        <span class="px-3 py-1 rounded-full text-xs font-medium
                            @if($audit->ssl_certificate) bg-green-100 text-green-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ $audit->ssl_certificate ? 'Secure' : 'Not Secure' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">Mobile Friendly</span>
                        <span class="px-3 py-1 rounded-full text-xs font-medium
                            @if($audit->mobile_friendly) bg-green-100 text-green-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ $audit->mobile_friendly ? 'Yes' : 'No' }}
                        </span>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Canonical URL</label>
                        <p class="text-gray-900 break-all">{{ $audit->canonical_url ?: 'Not found' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Robots Meta</label>
                        <p class="text-gray-900">{{ $audit->robots_meta ?: 'Not specified' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Sitemap</label>
                        <p class="text-gray-900">{{ $audit->sitemap_url ?: 'Not found' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Robots.txt</label>
                        <p class="text-gray-900">{{ $audit->robots_txt_status ?: 'Not found' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Analysis -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Content Analysis</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600">{{ $audit->word_count }}</div>
                    <div class="text-sm text-gray-500">Words</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600">{{ $audit->internal_links_count }}</div>
                    <div class="text-sm text-gray-500">Internal Links</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-purple-600">{{ $audit->external_links_count }}</div>
                    <div class="text-sm text-gray-500">External Links</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-orange-600">{{ $audit->images_count }}</div>
                    <div class="text-sm text-gray-500">Images</div>
                </div>
            </div>

            @if($audit->h1_tags || $audit->h2_tags)
                <div class="mt-6">
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Heading Structure</h4>
                    <div class="bg-gray-50 rounded-lg p-4">
                        @if($audit->h1_tags)
                            <div class="mb-3">
                                <span class="text-sm font-medium text-gray-700">H1 Tags:</span>
                                <ul class="mt-1 space-y-1">
                                    @foreach($audit->h1_tags as $h1)
                                        <li class="text-sm text-gray-600">• {{ $h1 }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if($audit->h2_tags)
                            <div>
                                <span class="text-sm font-medium text-gray-700">H2 Tags:</span>
                                <ul class="mt-1 space-y-1">
                                    @foreach($audit->h2_tags as $h2)
                                        <li class="text-sm text-gray-600">• {{ $h2 }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Recommendations -->
    @if($audit->recommendations && count($audit->recommendations) > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">SEO Recommendations</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($audit->recommendations as $recommendation)
                        <div class="flex items-start space-x-3 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex-shrink-0">
                                <i class="fas fa-lightbulb text-yellow-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-gray-800">{{ $recommendation }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">SEO Recommendations</h3>
            </div>
            <div class="p-6">
                <div class="text-center py-8">
                    <i class="fas fa-check-circle text-green-500 text-3xl mb-2"></i>
                    <p class="text-gray-600">Great! No major SEO issues found.</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
