@extends('layouts.app')

@section('title', 'Campaigns')

@section('content')
<div class="py-6" x-data="campaignsData()">
    <!-- Header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Campaigns
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Create and manage marketing campaigns with detailed analytics
                </p>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4">
                <a href="{{ route('campaigns.create') }}" 
                   class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                    New Campaign
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Active Campaigns</dt>
                                <dd class="text-lg font-medium text-gray-900" x-text="stats.activeCampaigns">{{ $stats['active_campaigns'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Impressions</dt>
                                <dd class="text-lg font-medium text-gray-900" x-text="formatNumber(stats.totalImpressions)">{{ number_format($stats['total_impressions']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Clicks</dt>
                                <dd class="text-lg font-medium text-gray-900" x-text="formatNumber(stats.totalClicks)">{{ number_format($stats['total_clicks']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Leads</dt>
                                <dd class="text-lg font-medium text-gray-900" x-text="formatNumber(stats.totalLeads)">{{ number_format($stats['total_leads']) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select x-model="filters.status" @change="applyFilters()" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="paused">Paused</option>
                            <option value="draft">Draft</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Campaign Type</label>
                        <select x-model="filters.type" @change="applyFilters()" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">All Types</option>
                            <option value="general">General</option>
                            <option value="email">Email</option>
                            <option value="social">Social Media</option>
                            <option value="affiliate">Affiliate</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date Range</label>
                        <select x-model="filters.dateRange" @change="applyFilters()" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="quarter">This Quarter</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Search</label>
                        <input type="text" x-model="filters.search" @input="applyFilters()" 
                               placeholder="Search campaigns..." 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Campaigns Table -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Your Campaigns</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    <span x-text="filteredCampaigns.length">{{ count($campaigns) }}</span> campaigns found
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campaign</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leads</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                            <th class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($campaigns as $campaign)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if($campaign->image)
                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ Storage::url($campaign->image) }}" alt="{{ $campaign->title }}">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                <svg class="h-6 w-6 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 12a5 5 0 100-10 5 5 0 000 10zm0 2c-5.67 0-11 2.56-11 7v2c0 .55.45 1 1 1h20c.55 0 1-.45 1-1v-2c0-4.44-5.33-7-11-7z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <a href="{{ route('campaigns.show', $campaign) }}" class="hover:text-blue-600">{{ $campaign->title }}</a>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ Str::limit($campaign->description, 50) }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ ucfirst($campaign->campaign_type) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($campaign->status == 'active') bg-green-100 text-green-800 
                                    @elseif($campaign->status == 'paused') bg-yellow-100 text-yellow-800 
                                    @elseif($campaign->status == 'draft') bg-gray-100 text-gray-800 
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($campaign->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ number_format($campaign->total_impressions) }} impressions
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ number_format($campaign->total_clicks) }} clicks ({{ number_format($campaign->ctr, 1) }}%)
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="text-sm text-gray-900">
                                    {{ number_format($campaign->total_conversions) }} leads
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ number_format($campaign->conversion_rate, 1) }}% conversion
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div>{{ $campaign->start_date ? $campaign->start_date->format('M d, Y') : 'Not set' }}</div>
                                <div class="text-xs text-gray-400">to</div>
                                <div>{{ $campaign->end_date ? $campaign->end_date->format('M d, Y') : 'Not set' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end items-center">
                                    <a href="{{ route('campaigns.analytics', $campaign) }}" class="text-blue-600 hover:text-blue-900 mr-4">Analytics</a>
                                    <a href="{{ route('campaigns.edit', $campaign) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 bg-white border-t border-gray-200 sm:px-6">
                {{ $campaigns->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function campaignsData() {
        return {
            stats: {
                activeCampaigns: {{ $stats['active_campaigns'] }},
                totalImpressions: {{ $stats['total_impressions'] }},
                totalClicks: {{ $stats['total_clicks'] }},
                totalLeads: {{ $stats['total_leads'] }}
            },
            filters: {
                status: '',
                type: '',
                dateRange: '',
                search: ''
            },
            campaigns: @json($campaigns->items()),
            filteredCampaigns: [],
            
            init() {
                this.filteredCampaigns = this.campaigns;
            },
            
            applyFilters() {
                let filtered = this.campaigns;
                
                if (this.filters.status) {
                    filtered = filtered.filter(campaign => campaign.status === this.filters.status);
                }
                
                if (this.filters.type) {
                    filtered = filtered.filter(campaign => campaign.campaign_type === this.filters.type);
                }
                
                if (this.filters.search) {
                    const searchLower = this.filters.search.toLowerCase();
                    filtered = filtered.filter(campaign => 
                        campaign.title.toLowerCase().includes(searchLower) || 
                        campaign.description.toLowerCase().includes(searchLower)
                    );
                }
                
                this.filteredCampaigns = filtered;
            },
            
            formatNumber(num) {
                return new Intl.NumberFormat().format(num);
            }
        }
    }
</script>
@endpush
@endsection
