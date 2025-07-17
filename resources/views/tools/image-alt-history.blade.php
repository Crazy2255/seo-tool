@extends('layouts.app')

@section('title', 'Image ALT Text Audit History - SEO Audit Pro')
@section('page-title', 'Image ALT Text Audit History')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-lg p-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Image ALT Text Audit History</h1>
                <p class="text-gray-600 mt-2">Track your website's image accessibility improvements over time</p>
            </div>
            <a href="{{ route('tools.image-alt-checker') }}" 
               class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>New Analysis
            </a>
        </div>

        <!-- Summary Stats -->
        @if($audits->total() > 0)
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <div class="text-2xl font-bold text-blue-600">{{ $audits->total() }}</div>
                <div class="text-sm text-blue-800 font-medium">Total Audits</div>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <div class="text-2xl font-bold text-green-600">{{ $audits->sum('total_images') }}</div>
                <div class="text-sm text-green-800 font-medium">Images Analyzed</div>
            </div>
            <div class="text-center p-4 bg-purple-50 rounded-lg">
                <div class="text-2xl font-bold text-purple-600">{{ $audits->sum('pages_crawled') }}</div>
                <div class="text-sm text-purple-800 font-medium">Pages Crawled</div>
            </div>
            <div class="text-center p-4 bg-amber-50 rounded-lg">
                <div class="text-2xl font-bold text-amber-600">{{ number_format($audits->avg('accessibility_score'), 1) }}%</div>
                <div class="text-sm text-amber-800 font-medium">Avg. Accessibility</div>
            </div>
        </div>
        @endif
    </div>

    <!-- Audits Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        @if($audits->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Website</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Images</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Accessibility Score</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Issues</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($audits as $audit)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $audit->formatted_url }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ Str::limit($audit->page_title, 50) }}
                                    </div>
                                    @if($audit->is_multi_page)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 mt-1">
                                        <i class="fas fa-sitemap mr-1"></i>Multi-page
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $audit->total_images }} images</div>
                            <div class="text-sm text-gray-500">{{ $audit->pages_crawled }} page{{ $audit->pages_crawled > 1 ? 's' : '' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-1 mr-3">
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="h-2 rounded-full {{ 
                                            $audit->accessibility_score >= 90 ? 'bg-green-500' : 
                                            ($audit->accessibility_score >= 70 ? 'bg-yellow-500' : 
                                            ($audit->accessibility_score >= 50 ? 'bg-orange-500' : 'bg-red-500'))
                                        }}" style="width: {{ $audit->accessibility_score }}%"></div>
                                    </div>
                                </div>
                                <span class="text-sm font-medium {{ 
                                    $audit->accessibility_score >= 90 ? 'text-green-600' : 
                                    ($audit->accessibility_score >= 70 ? 'text-yellow-600' : 
                                    ($audit->accessibility_score >= 50 ? 'text-orange-600' : 'text-red-600'))
                                }}">{{ $audit->accessibility_score }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col space-y-1">
                                @if($audit->images_without_alt > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                    {{ $audit->images_without_alt }} missing ALT
                                </span>
                                @endif
                                @if($audit->images_with_issues > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">
                                    {{ $audit->images_with_issues }} need improvement
                                </span>
                                @endif
                                @if($audit->images_without_alt === 0 && $audit->images_with_issues === 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i>All good
                                </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <div>{{ $audit->analyzed_at->format('M j, Y') }}</div>
                            <div class="text-xs">{{ $audit->analyzed_at->format('g:i A') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('tools.image-alt.show', $audit->id) }}" 
                                   class="text-green-600 hover:text-green-700 text-sm font-medium">
                                    <i class="fas fa-eye mr-1"></i>View
                                </a>
                                <a href="{{ route('tools.image-alt.pdf', $audit->id) }}" 
                                   class="text-red-600 hover:text-red-700 text-sm font-medium">
                                    <i class="fas fa-file-pdf mr-1"></i>PDF
                                </a>
                                <button onclick="deleteAudit({{ $audit->id }})" 
                                        class="text-red-600 hover:text-red-700 text-sm font-medium">
                                    <i class="fas fa-trash mr-1"></i>Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($audits->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $audits->links() }}
        </div>
        @endif

        @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-images text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Image Audits Yet</h3>
            <p class="text-gray-600 mb-6">Start analyzing your website's image accessibility to see your audit history here.</p>
            <a href="{{ route('tools.image-alt-checker') }}" 
               class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>Create Your First Audit
            </a>
        </div>
        @endif
    </div>
</div>

<script>
async function deleteAudit(auditId) {
    if (!confirm('Are you sure you want to delete this audit? This action cannot be undone.')) {
        return;
    }

    try {
        const response = await fetch(`/api/image-alt/${auditId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Failed to delete audit');
        }
    } catch (error) {
        console.error('Delete error:', error);
        alert('Failed to delete audit');
    }
}
</script>
@endsection
