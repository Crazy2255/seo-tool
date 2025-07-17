@extends('layouts.app')

@section('title', 'Analytics - ' . $leadMagnet->title . ' - SEO Audit Pro')
@section('page-title', 'Lead Magnet Analytics')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('tools.lead-magnet-builder') }}" 
                   class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $leadMagnet->title }}</h1>
                    <p class="text-gray-600">Lead Magnet Analytics</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <span class="px-3 py-1 text-sm font-medium rounded-full {{ $leadMagnet->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $leadMagnet->is_active ? 'Active' : 'Inactive' }}
                </span>
                <a href="{{ $leadMagnet->landing_url }}" 
                   target="_blank"
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-external-link-alt mr-2"></i>View Landing Page
                </a>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Views</p>
                    <p class="text-3xl font-bold text-blue-600">{{ number_format($analytics['total_views']) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-eye text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Leads</p>
                    <p class="text-3xl font-bold text-green-600">{{ number_format($analytics['total_leads']) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Downloads</p>
                    <p class="text-3xl font-bold text-purple-600">{{ number_format($analytics['total_downloads']) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-download text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Conversion Rate</p>
                    <p class="text-3xl font-bold text-orange-600">{{ $analytics['conversion_rate'] }}%</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-chart-line text-orange-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Lead Magnet Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Lead Magnet Info -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Lead Magnet Details</h2>
            
            <div class="space-y-4">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file text-gray-600"></i>
                    </div>
                    <div>
                        <div class="font-medium text-gray-900">{{ $leadMagnet->file_name }}</div>
                        <div class="text-sm text-gray-500">{{ $leadMagnet->formatted_file_size }}</div>
                    </div>
                </div>
                
                <div class="pt-4 border-t border-gray-200">
                    <div class="text-sm text-gray-600 space-y-2">
                        <div><strong>Created:</strong> {{ $leadMagnet->created_at->format('M j, Y g:i A') }}</div>
                        <div><strong>Last Updated:</strong> {{ $leadMagnet->updated_at->format('M j, Y g:i A') }}</div>
                        <div><strong>Slug:</strong> <code class="bg-gray-100 px-2 py-1 rounded">{{ $leadMagnet->slug }}</code></div>
                    </div>
                </div>
                
                <div class="pt-4 border-t border-gray-200">
                    <a href="{{ route('lead-magnets.export-leads', $leadMagnet) }}" 
                       class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-file-csv mr-2"></i>Export Leads CSV
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Leads -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Recent Leads</h2>
                <span class="text-sm text-gray-500">Last 10 submissions</span>
            </div>
            
            @if($analytics['recent_leads']->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-900">Name</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-900">Email</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-900">Date</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-900">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($analytics['recent_leads'] as $lead)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $lead->name }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $lead->email }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $lead->created_at->format('M j, Y') }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-2">
                                    @if($lead->email_sent)
                                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                        <span class="text-green-600 text-xs">Email Sent</span>
                                    @else
                                        <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                                        <span class="text-yellow-600 text-xs">Pending</span>
                                    @endif
                                    
                                    @if($lead->downloaded_at)
                                        <span class="w-2 h-2 bg-blue-500 rounded-full ml-2"></span>
                                        <span class="text-blue-600 text-xs">Downloaded</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users text-gray-400 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No leads yet</h3>
                <p class="text-gray-600">Share your landing page to start capturing leads.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Performance Insights -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Performance Insights</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Conversion Rate Analysis -->
            <div class="text-center">
                <div class="w-20 h-20 mx-auto mb-4 relative">
                    <svg class="w-20 h-20 transform -rotate-90" viewBox="0 0 36 36">
                        <path class="text-gray-200" stroke="currentColor" stroke-width="3" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"></path>
                        <path class="text-green-600" stroke="currentColor" stroke-width="3" fill="none" stroke-dasharray="{{ $analytics['conversion_rate'] }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"></path>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-xl font-bold text-gray-900">{{ $analytics['conversion_rate'] }}%</span>
                    </div>
                </div>
                <h3 class="font-medium text-gray-900 mb-2">Conversion Rate</h3>
                <p class="text-sm text-gray-600">
                    @if($analytics['conversion_rate'] >= 15)
                        Excellent performance! 🎉
                    @elseif($analytics['conversion_rate'] >= 10)
                        Good conversion rate 👍
                    @elseif($analytics['conversion_rate'] >= 5)
                        Average performance 📈
                    @else
                        Room for improvement 🔧
                    @endif
                </p>
            </div>

            <!-- Tips -->
            <div class="md:col-span-2">
                <h3 class="font-medium text-gray-900 mb-3">Optimization Tips</h3>
                <div class="space-y-2 text-sm text-gray-600">
                    @if($analytics['conversion_rate'] < 10)
                        <div class="flex items-start space-x-2">
                            <i class="fas fa-lightbulb text-yellow-500 mt-0.5"></i>
                            <span>Try A/B testing different form titles and descriptions</span>
                        </div>
                        <div class="flex items-start space-x-2">
                            <i class="fas fa-lightbulb text-yellow-500 mt-0.5"></i>
                            <span>Make sure your value proposition is clear and compelling</span>
                        </div>
                    @endif
                    @if($analytics['total_views'] > 0 && $analytics['total_leads'] == 0)
                        <div class="flex items-start space-x-2">
                            <i class="fas fa-exclamation-triangle text-orange-500 mt-0.5"></i>
                            <span>You have views but no leads. Check your form functionality.</span>
                        </div>
                    @endif
                    <div class="flex items-start space-x-2">
                        <i class="fas fa-share text-blue-500 mt-0.5"></i>
                        <span>Share your landing page URL: <code class="bg-gray-100 px-1 rounded">{{ $leadMagnet->landing_url }}</code></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
