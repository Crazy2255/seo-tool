@extends('layouts.app')

@section('title', $campaign->title)

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    {{ $campaign->title }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Created on {{ $campaign->created_at->format('M d, Y') }} • {{ ucfirst($campaign->campaign_type) }} Campaign
                </p>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4">
                <a href="{{ route('campaigns.analytics', $campaign) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" clip-rule="evenodd" />
                    </svg>
                    Analytics
                </a>
                <a href="{{ route('campaigns.edit', $campaign) }}" class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                    Edit
                </a>
                @if($campaign->status === 'draft' || $campaign->status === 'paused')
                <form action="{{ route('campaigns.activate', $campaign) }}" method="POST" class="inline-block">
                    @csrf
                    <button type="submit" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                        </svg>
                        Activate
                    </button>
                </form>
                @elseif($campaign->status === 'active')
                <form action="{{ route('campaigns.pause', $campaign) }}" method="POST" class="inline-block">
                    @csrf
                    <button type="submit" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        Pause
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Campaign Details</h3>
            </div>
            <div class="border-t border-gray-200">
                <dl>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($campaign->status == 'active') bg-green-100 text-green-800 
                                @elseif($campaign->status == 'paused') bg-yellow-100 text-yellow-800 
                                @elseif($campaign->status == 'draft') bg-gray-100 text-gray-800 
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($campaign->status) }}
                            </span>
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $campaign->description }}</dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Campaign Period</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $campaign->start_date ? $campaign->start_date->format('M d, Y') : 'Not set' }} -
                            {{ $campaign->end_date ? $campaign->end_date->format('M d, Y') : 'No end date' }}
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Campaign Image</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            @if($campaign->image)
                                <img src="{{ Storage::url($campaign->image) }}" alt="{{ $campaign->title }}" class="h-48 w-auto rounded-md border border-gray-200">
                            @else
                                <span class="text-gray-500">No image uploaded</span>
                            @endif
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Website URL</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            @if($campaign->website_url)
                                <a href="{{ $campaign->website_url }}" target="_blank" class="text-blue-600 hover:text-blue-900">{{ $campaign->website_url }}</a>
                            @else
                                <span class="text-gray-500">No website URL specified</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- Campaign Performance Summary -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Performance Summary</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x">
                <div class="px-6 py-5 text-center">
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($campaign->total_impressions) }}</p>
                    <p class="text-sm font-medium text-gray-500">Total Impressions</p>
                </div>
                <div class="px-6 py-5 text-center">
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($campaign->total_clicks) }}</p>
                    <p class="text-sm font-medium text-gray-500">Total Clicks</p>
                </div>
                <div class="px-6 py-5 text-center">
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($campaign->total_conversions) }}</p>
                    <p class="text-sm font-medium text-gray-500">Total Leads</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Share Campaign -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
        <div class="bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Share Your Campaign</h3>
                <div class="mt-2 max-w-xl text-sm text-gray-500">
                    <p>Share this campaign via email or with a direct link</p>
                </div>
                <div class="mt-5">
                    <div class="rounded-md shadow-sm">
                        <div class="flex">
                            <input type="text" id="share-link" readonly value="{{ $campaign->share_url }}" 
                                   class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-l-md border-gray-300 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <button type="button" onclick="copyShareLink()" 
                                    class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-gray-50 text-gray-500 hover:bg-gray-100">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z"></path>
                                    <path d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Email sharing form -->
                <div class="mt-5 border-t border-gray-200 pt-5">
                    <h4 class="text-md font-medium text-gray-900">Send Bulk Emails</h4>
                    <form action="{{ route('campaigns.bulk-emails', $campaign) }}" method="POST" class="mt-3">
                        @csrf
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label for="emails" class="block text-sm font-medium text-gray-700">Email Addresses</label>
                                <div class="mt-1">
                                    <textarea id="emails" name="emails" rows="3" 
                                              class="shadow-sm block w-full sm:text-sm border-gray-300 rounded-md"
                                              placeholder="Enter email addresses separated by commas"></textarea>
                                    <p class="mt-1 text-xs text-gray-500">Enter multiple email addresses separated by commas</p>
                                </div>
                            </div>
                            <div>
                                <label for="subject" class="block text-sm font-medium text-gray-700">Email Subject</label>
                                <input type="text" name="subject" id="subject"  
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                       placeholder="Enter email subject">
                            </div>
                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700">Email Message</label>
                                <div class="mt-1">
                                    <textarea id="message" name="message" rows="4" 
                                              class="shadow-sm block w-full sm:text-sm border-gray-300 rounded-md"
                                              placeholder="Enter your message"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                </svg>
                                Send Emails
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function copyShareLink() {
        const shareLink = document.getElementById('share-link');
        shareLink.select();
        document.execCommand('copy');
        
        // Show feedback
        const button = shareLink.nextElementSibling;
        const originalHTML = button.innerHTML;
        button.innerHTML = `
            <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
            </svg>
        `;
        
        setTimeout(() => {
            button.innerHTML = originalHTML;
        }, 2000);
    }
</script>
@endpush
@endsection
