@extends('layouts.app')

@section('title', 'Image ALT Text Report - SEO Audit Pro')
@section('page-title', 'Image ALT Text Report')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-lg p-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Image Accessibility Report</h1>
                <p class="text-gray-600 mt-2">{{ $audit->page_title ?: $audit->formatted_url }}</p>
                <p class="text-sm text-gray-500">Analyzed on {{ $audit->analyzed_at->format('F j, Y \a\t g:i A') }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('tools.image-alt.pdf', $audit->id) }}" 
                   class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-file-pdf mr-2"></i>Export PDF
                </a>
                <a href="{{ route('tools.image-alt.csv', $audit->id) }}" 
                   class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-file-csv mr-2"></i>Export CSV
                </a>
                <a href="{{ route('tools.image-alt-checker') }}" 
                   class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>New Analysis
                </a>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <div class="text-2xl font-bold text-blue-600">{{ $audit->total_images }}</div>
                <div class="text-sm text-blue-800 font-medium">Total Images</div>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <div class="text-2xl font-bold text-green-600">{{ $audit->images_with_good_alt }}</div>
                <div class="text-sm text-green-800 font-medium">Good ALT Text</div>
            </div>
            <div class="text-center p-4 bg-red-50 rounded-lg">
                <div class="text-2xl font-bold text-red-600">{{ $audit->images_without_alt }}</div>
                <div class="text-sm text-red-800 font-medium">Missing ALT</div>
            </div>
            <div class="text-center p-4 bg-amber-50 rounded-lg">
                <div class="text-2xl font-bold text-amber-600">{{ $audit->images_with_issues }}</div>
                <div class="text-sm text-amber-800 font-medium">Need Improvement</div>
            </div>
            <div class="text-center p-4 bg-purple-50 rounded-lg">
                <div class="text-2xl font-bold text-purple-600">{{ $audit->pages_crawled }}</div>
                <div class="text-sm text-purple-800 font-medium">Pages Crawled</div>
            </div>
        </div>

        <!-- Accessibility Score -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Overall Accessibility Score</h3>
            <div class="flex items-center space-x-4">
                <div class="flex-1">
                    <div class="w-full bg-gray-200 rounded-full h-4">
                        <div class="h-4 rounded-full {{ 
                            $audit->accessibility_score >= 90 ? 'bg-green-500' : 
                            ($audit->accessibility_score >= 70 ? 'bg-yellow-500' : 
                            ($audit->accessibility_score >= 50 ? 'bg-orange-500' : 'bg-red-500'))
                        }}" style="width: {{ $audit->accessibility_score }}%"></div>
                    </div>
                </div>
                <div class="text-3xl font-bold {{ 
                    $audit->accessibility_score >= 90 ? 'text-green-600' : 
                    ($audit->accessibility_score >= 70 ? 'text-yellow-600' : 
                    ($audit->accessibility_score >= 50 ? 'text-orange-600' : 'text-red-600'))
                }}">{{ $audit->accessibility_score }}%</div>
            </div>
            <p class="text-sm text-gray-600 mt-2">
                @if($audit->accessibility_score >= 90)
                    Excellent accessibility! Most images have proper alt text.
                @elseif($audit->accessibility_score >= 70)
                    Good accessibility with room for improvement.
                @elseif($audit->accessibility_score >= 50)
                    Fair accessibility. Many images need better alt text.
                @else
                    Poor accessibility. Most images are missing or have inadequate alt text.
                @endif
            </p>
        </div>
    </div>

    <!-- Crawl Summary (for multi-page audits) -->
    @if($audit->is_multi_page && $audit->crawl_summary)
    <div class="bg-white rounded-xl shadow-lg p-8">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Pages Analyzed</h2>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Page URL</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Page Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Images Found</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($audit->crawl_summary as $page)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ Str::limit($page['url'], 60) }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $page['page_title'] ?: 'Untitled' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $page['total_images'] }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ 
                                $page['status'] === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                            }}">
                                {{ ucfirst($page['status']) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Images Analysis -->
    <div class="bg-white rounded-xl shadow-lg p-8" x-data="imageReport()">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Image Analysis Details</h2>
            <div class="flex space-x-2">
                <button @click="filterStatus = 'all'" 
                        :class="filterStatus === 'all' ? 'bg-gray-900 text-white' : 'bg-gray-200 text-gray-700'"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    All ({{ count($audit->images_data) }})
                </button>
                <button @click="filterStatus = 'missing'" 
                        :class="filterStatus === 'missing' ? 'bg-red-600 text-white' : 'bg-red-100 text-red-700'"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Missing ({{ collect($audit->images_data)->where('analysis.status', 'missing')->count() }})
                </button>
                <button @click="filterStatus = 'issues'" 
                        :class="filterStatus === 'issues' ? 'bg-amber-600 text-white' : 'bg-amber-100 text-amber-700'"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Issues ({{ collect($audit->images_data)->whereIn('analysis.status', ['needs_improvement', 'poor'])->count() }})
                </button>
                <button @click="filterStatus = 'good'" 
                        :class="filterStatus === 'good' ? 'bg-green-600 text-white' : 'bg-green-100 text-green-700'"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    Good ({{ collect($audit->images_data)->where('analysis.status', 'good')->count() }})
                </button>
            </div>
        </div>

        <div class="space-y-6">
            @foreach($audit->images_data as $index => $image)
            <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow"
                 x-show="shouldShowImage('{{ $image['analysis']['status'] }}')"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100">
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Image Preview -->
                    <div class="space-y-4">
                        <div class="aspect-video bg-gray-100 rounded-lg overflow-hidden">
                            <img src="{{ $image['src'] }}" alt="{{ $image['alt'] ?: 'Image preview' }}" 
                                 class="w-full h-full object-cover"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
                            <div class="w-full h-full flex items-center justify-center text-gray-400" style="display: none;">
                                <i class="fas fa-image text-4xl"></i>
                            </div>
                        </div>
                        <div class="text-sm text-gray-600 space-y-1">
                            <p><strong>File:</strong> {{ $image['file_name'] }}</p>
                            @if($image['width'] && $image['height'])
                            <p><strong>Size:</strong> {{ $image['width'] }}×{{ $image['height'] }}</p>
                            @endif
                            <p><strong>Type:</strong> {{ strtoupper($image['file_extension']) }}</p>
                            @if($image['page_url'] !== $audit->url)
                            <p><strong>Page:</strong> <a href="{{ $image['page_url'] }}" class="text-blue-600 hover:underline" target="_blank">{{ Str::limit($image['page_url'], 40) }}</a></p>
                            @endif
                        </div>
                    </div>

                    <!-- Image Analysis -->
                    <div class="lg:col-span-2 space-y-4">
                        <!-- Status & Score -->
                        <div class="flex items-center justify-between">
                            <span class="px-3 py-1 rounded-full text-sm font-medium {{ 
                                $image['analysis']['status'] === 'good' ? 'bg-green-100 text-green-800' : 
                                ($image['analysis']['status'] === 'needs_improvement' ? 'bg-amber-100 text-amber-800' : 
                                ($image['analysis']['status'] === 'poor' ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800'))
                            }}">
                                {{ 
                                    $image['analysis']['status'] === 'good' ? 'Good ALT Text' : 
                                    ($image['analysis']['status'] === 'needs_improvement' ? 'Needs Improvement' : 
                                    ($image['analysis']['status'] === 'poor' ? 'Poor ALT Text' : 'Missing ALT'))
                                }}
                            </span>
                            <span class="text-sm text-gray-500">Score: {{ $image['analysis']['score'] }}/100</span>
                        </div>

                        <!-- Current ALT Text -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current ALT Text</label>
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <code class="text-sm">{{ $image['alt'] ?: '(empty)' }}</code>
                                <div class="text-xs text-gray-500 mt-1">
                                    Length: {{ $image['analysis']['length'] }} characters, {{ $image['analysis']['word_count'] }} words
                                </div>
                            </div>
                        </div>

                        <!-- Issues -->
                        @if(!empty($image['analysis']['issues']))
                        <div>
                            <label class="block text-sm font-medium text-red-700 mb-2">Issues Found</label>
                            <ul class="space-y-1">
                                @foreach($image['analysis']['issues'] as $issue)
                                <li class="flex items-center space-x-2 text-sm text-red-600">
                                    <i class="fas fa-exclamation-triangle text-xs"></i>
                                    <span>{{ $issue }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <!-- Recommendations -->
                        <div>
                            <label class="block text-sm font-medium text-green-700 mb-2">Recommendations</label>
                            <ul class="space-y-1">
                                @foreach($image['analysis']['recommendations'] as $recommendation)
                                <li class="flex items-center space-x-2 text-sm text-green-600">
                                    <i class="fas fa-lightbulb text-xs"></i>
                                    <span>{{ $recommendation }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Context (if available) -->
                        @if(!empty($image['context']['parent_text']) || !empty($image['context']['caption']))
                        <div class="pt-4 border-t border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Context Information</label>
                            <div class="text-sm text-gray-600 space-y-1">
                                @if(!empty($image['context']['parent_text']))
                                <div>
                                    <strong>Surrounding text:</strong> {{ Str::limit($image['context']['parent_text'], 100) }}
                                </div>
                                @endif
                                @if(!empty($image['context']['caption']))
                                <div>
                                    <strong>Caption:</strong> {{ $image['context']['caption'] }}
                                </div>
                                @endif
                                @if(!empty($image['context']['parent_tag']))
                                <div>
                                    <strong>Parent element:</strong> &lt;{{ $image['context']['parent_tag'] }}&gt;
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<script>
function imageReport() {
    return {
        filterStatus: 'all',
        
        shouldShowImage(status) {
            if (this.filterStatus === 'all') return true;
            if (this.filterStatus === 'missing') return status === 'missing';
            if (this.filterStatus === 'issues') return status === 'needs_improvement' || status === 'poor';
            if (this.filterStatus === 'good') return status === 'good';
            return true;
        }
    }
}
</script>
@endsection
