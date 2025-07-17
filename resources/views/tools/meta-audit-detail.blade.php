@extends('layouts.app')

@section('title', 'Meta Tag Audit Detail - SEO Audit Pro')
@section('page-title', 'Meta Tag Audit Detail')

@section('content')
<div>
    <!-- Header -->
    <div class="mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center mb-2">
                        <a href="{{ route('meta-analyzer.history') }}" class="text-primary-600 hover:text-primary-800 mr-3">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <h1 class="text-2xl font-bold text-gray-900">Meta Tag Audit Detail</h1>
                    </div>
                    <p class="text-gray-600 break-all">{{ $audit->url }}</p>
                    <p class="text-sm text-gray-500 mt-1">
                        Analyzed on {{ $audit->analyzed_at->format('F j, Y \a\t g:i A') }}
                    </p>
                </div>
                <div class="hidden lg:block">
                    <div class="w-16 h-16 bg-purple-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-search text-purple-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-4 mb-8">
        <a href="{{ route('meta-analyzer.pdf', $audit->id) }}" 
           class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center justify-center">
            <i class="fas fa-download mr-2"></i>
            Download PDF Report
        </a>
        <a href="{{ route('tools.meta-analyzer') }}?url={{ urlencode($audit->url) }}" 
           class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center justify-center">
            <i class="fas fa-sync mr-2"></i>
            Re-analyze
        </a>
        <button onclick="deleteAudit({{ $audit->id }})" 
                class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center justify-center">
            <i class="fas fa-trash mr-2"></i>
            Delete Audit
        </button>
    </div>

    <!-- Score Overview -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">SEO Score</h3>
            <div class="text-sm text-gray-500">
                Score calculated based on meta tag optimization
            </div>
        </div>
        
        <div class="flex items-center space-x-6">
            <div class="relative w-24 h-24">
                <div class="w-24 h-24 rounded-full border-8 flex items-center justify-center
                    @if($audit->score >= 80) border-green-500
                    @elseif($audit->score >= 60) border-yellow-500
                    @else border-red-500
                    @endif">
                    <span class="text-2xl font-bold text-gray-900">{{ $audit->score }}</span>
                </div>
            </div>
            <div>
                <h4 class="text-xl font-semibold 
                    @if($audit->score >= 80) text-green-600
                    @elseif($audit->score >= 60) text-yellow-600
                    @else text-red-600
                    @endif">
                    {{ $audit->getStatusText() }}
                </h4>
                <p class="text-gray-600 mt-1">
                    @if($audit->score >= 80) Your meta tags are well optimized for SEO
                    @elseif($audit->score >= 60) Your meta tags are fairly good but could be improved
                    @elseif($audit->score >= 40) Your meta tags need some attention
                    @else Your meta tags need significant improvement
                    @endif
                </p>
                <div class="flex items-center mt-2 space-x-4">
                    <span class="text-sm text-red-600">Issues: {{ count($audit->issues) }}</span>
                    <span class="text-sm text-blue-600">Recommendations: {{ count($audit->recommendations) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Meta Tags Analysis -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Title Tag -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Title Tag</h3>
                <span class="px-3 py-1 rounded-full text-sm font-medium
                    @if($audit->title && $audit->title_length >= 30 && $audit->title_length <= 60) 
                        bg-green-100 text-green-800
                    @elseif($audit->title)
                        bg-yellow-100 text-yellow-800
                    @else
                        bg-red-100 text-red-800
                    @endif">
                    <i class="fas 
                        @if($audit->title && $audit->title_length >= 30 && $audit->title_length <= 60) fa-check
                        @elseif($audit->title) fa-exclamation-triangle
                        @else fa-times
                        @endif mr-1"></i>
                    @if($audit->title && $audit->title_length >= 30 && $audit->title_length <= 60) Good
                    @elseif($audit->title) Needs Improvement
                    @else Missing
                    @endif
                </span>
            </div>
            
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Title:</label>
                    <p class="text-gray-900 bg-gray-50 p-3 rounded-lg text-sm">
                        {{ $audit->title ?: 'No title found' }}
                    </p>
                </div>
                
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Length:</span>
                    <span class="font-medium">{{ $audit->title_length ?? 0 }} characters</span>
                </div>
                
                <div class="bg-blue-50 p-3 rounded-lg">
                    <p class="text-xs text-blue-700">
                        <i class="fas fa-info-circle mr-1"></i>
                        Recommended: 30-60 characters for optimal display in search results
                    </p>
                </div>
            </div>
        </div>

        <!-- Meta Description -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Meta Description</h3>
                <span class="px-3 py-1 rounded-full text-sm font-medium
                    @if($audit->meta_description && $audit->meta_description_length >= 120 && $audit->meta_description_length <= 160) 
                        bg-green-100 text-green-800
                    @elseif($audit->meta_description)
                        bg-yellow-100 text-yellow-800
                    @else
                        bg-red-100 text-red-800
                    @endif">
                    <i class="fas 
                        @if($audit->meta_description && $audit->meta_description_length >= 120 && $audit->meta_description_length <= 160) fa-check
                        @elseif($audit->meta_description) fa-exclamation-triangle
                        @else fa-times
                        @endif mr-1"></i>
                    @if($audit->meta_description && $audit->meta_description_length >= 120 && $audit->meta_description_length <= 160) Good
                    @elseif($audit->meta_description) Needs Improvement
                    @else Missing
                    @endif
                </span>
            </div>
            
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Description:</label>
                    <p class="text-gray-900 bg-gray-50 p-3 rounded-lg text-sm">
                        {{ $audit->meta_description ?: 'No meta description found' }}
                    </p>
                </div>
                
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Length:</span>
                    <span class="font-medium">{{ $audit->meta_description_length ?? 0 }} characters</span>
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
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Technical Meta Tags</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Viewport -->
            <div class="border rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-medium text-gray-700">Viewport</span>
                    <span class="px-2 py-1 rounded text-xs font-medium {{ $audit->viewport ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        <i class="fas {{ $audit->viewport ? 'fa-check' : 'fa-times' }} mr-1"></i>
                        {{ $audit->viewport ? 'Present' : 'Missing' }}
                    </span>
                </div>
                <p class="text-sm text-gray-600">{{ $audit->viewport ?: 'Not found' }}</p>
            </div>

            <!-- Robots -->
            <div class="border rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-medium text-gray-700">Robots</span>
                    <span class="px-2 py-1 rounded text-xs font-medium {{ $audit->robots ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        <i class="fas {{ $audit->robots ? 'fa-check' : 'fa-exclamation-triangle' }} mr-1"></i>
                        {{ $audit->robots ? 'Present' : 'Not Set' }}
                    </span>
                </div>
                <p class="text-sm text-gray-600">{{ $audit->robots ?: 'Default behavior' }}</p>
            </div>

            <!-- Canonical -->
            <div class="border rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-medium text-gray-700">Canonical URL</span>
                    <span class="px-2 py-1 rounded text-xs font-medium {{ $audit->canonical_url ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        <i class="fas {{ $audit->canonical_url ? 'fa-check' : 'fa-exclamation-triangle' }} mr-1"></i>
                        {{ $audit->canonical_url ? 'Present' : 'Missing' }}
                    </span>
                </div>
                <p class="text-sm text-gray-600 break-all">{{ $audit->canonical_url ?: 'Not found' }}</p>
            </div>

            <!-- Character Set -->
            <div class="border rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-medium text-gray-700">Character Set</span>
                    <span class="px-2 py-1 rounded text-xs font-medium {{ $audit->charset ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        <i class="fas {{ $audit->charset ? 'fa-check' : 'fa-exclamation-triangle' }} mr-1"></i>
                        {{ $audit->charset ? 'Present' : 'Missing' }}
                    </span>
                </div>
                <p class="text-sm text-gray-600">{{ $audit->charset ?: 'Not found' }}</p>
            </div>

            <!-- Language -->
            <div class="border rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-medium text-gray-700">Language</span>
                    <span class="px-2 py-1 rounded text-xs font-medium {{ $audit->language ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        <i class="fas {{ $audit->language ? 'fa-check' : 'fa-exclamation-triangle' }} mr-1"></i>
                        {{ $audit->language ? 'Present' : 'Missing' }}
                    </span>
                </div>
                <p class="text-sm text-gray-600">{{ $audit->language ?: 'Not found' }}</p>
            </div>

            <!-- Author -->
            <div class="border rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-medium text-gray-700">Author</span>
                    <span class="px-2 py-1 rounded text-xs font-medium {{ $audit->author ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        <i class="fas {{ $audit->author ? 'fa-check' : 'fa-exclamation-triangle' }} mr-1"></i>
                        {{ $audit->author ? 'Present' : 'Missing' }}
                    </span>
                </div>
                <p class="text-sm text-gray-600">{{ $audit->author ?: 'Not found' }}</p>
            </div>
        </div>
    </div>

    <!-- Open Graph & Social Media Tags -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Open Graph -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Open Graph Tags</h3>
            
            <div class="space-y-4">
                @foreach(['og_title' => 'OG Title', 'og_description' => 'OG Description', 'og_image' => 'OG Image', 'og_type' => 'OG Type', 'og_url' => 'OG URL'] as $field => $label)
                    <div class="border rounded-lg p-3">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-medium text-gray-700">{{ $label }}</span>
                            <span class="px-2 py-1 rounded text-xs font-medium {{ $audit->$field ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                <i class="fas {{ $audit->$field ? 'fa-check' : 'fa-exclamation-triangle' }} mr-1"></i>
                                {{ $audit->$field ? 'Present' : 'Missing' }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 break-all">{{ $audit->$field ?: 'Not found' }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Twitter Cards -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Twitter Card Tags</h3>
            
            <div class="space-y-4">
                @foreach(['twitter_card' => 'Twitter Card', 'twitter_title' => 'Twitter Title', 'twitter_description' => 'Twitter Description', 'twitter_image' => 'Twitter Image'] as $field => $label)
                    <div class="border rounded-lg p-3">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-medium text-gray-700">{{ $label }}</span>
                            <span class="px-2 py-1 rounded text-xs font-medium {{ $audit->$field ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                <i class="fas {{ $audit->$field ? 'fa-check' : 'fa-exclamation-triangle' }} mr-1"></i>
                                {{ $audit->$field ? 'Present' : 'Missing' }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 break-all">{{ $audit->$field ?: 'Not found' }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Issues and Recommendations -->
    @if(count($audit->issues) > 0 || count($audit->recommendations) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Issues -->
            @if(count($audit->issues) > 0)
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Issues Found</h3>
                    
                    <div class="space-y-3">
                        @foreach($audit->issues as $issue)
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                                    <span class="text-red-800 font-medium">
                                        {{ ucfirst(str_replace('_', ' ', $issue)) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Recommendations -->
            @if(count($audit->recommendations) > 0)
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Recommendations</h3>
                    
                    <div class="space-y-3">
                        @foreach($audit->recommendations as $recommendation)
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <i class="fas fa-lightbulb text-blue-500 mr-2"></i>
                                    <span class="text-blue-800 font-medium">
                                        {{ ucfirst(str_replace('_', ' ', $recommendation)) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>

<script>
async function deleteAudit(auditId) {
    if (!confirm('Are you sure you want to delete this audit? This action cannot be undone.')) {
        return;
    }
    
    try {
        const response = await fetch(`/api/meta-analyzer/audit/${auditId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Redirect to history page
            window.location.href = '{{ route("meta-analyzer.history") }}';
        } else {
            alert('Error deleting audit: ' + (data.message || 'Unknown error'));
        }
    } catch (error) {
        alert('Error deleting audit: ' + error.message);
    }
}
</script>
@endsection
