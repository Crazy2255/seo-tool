@extends('layouts.app')

@section('title', 'Edit Campaign')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    Edit Campaign: {{ $campaign->title }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Update your campaign details and settings
                </p>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4">
                <a href="{{ route('campaigns.show', $campaign) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Back to Campaign
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
        <form action="{{ route('campaigns.update', $campaign) }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-sm rounded-lg">
            @csrf
            @method('PUT')
            <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 gap-6">
                    <!-- Campaign Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Campaign Title</label>
                        <input type="text" name="title" id="title" value="{{ old('title', $campaign->title) }}" required 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="Enter a descriptive title">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Campaign Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <div class="mt-1">
                            <textarea id="description" name="description" rows="4"
                                      class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                      placeholder="Describe your campaign">{{ old('description', $campaign->description) }}</textarea>
                        </div>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Campaign Type -->
                    <div>
                        <label for="campaign_type" class="block text-sm font-medium text-gray-700">Campaign Type</label>
                        <select id="campaign_type" name="campaign_type"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="general" {{ old('campaign_type', $campaign->campaign_type) == 'general' ? 'selected' : '' }}>General</option>
                            <option value="email" {{ old('campaign_type', $campaign->campaign_type) == 'email' ? 'selected' : '' }}>Email</option>
                            <option value="social" {{ old('campaign_type', $campaign->campaign_type) == 'social' ? 'selected' : '' }}>Social Media</option>
                            <option value="affiliate" {{ old('campaign_type', $campaign->campaign_type) == 'affiliate' ? 'selected' : '' }}>Affiliate</option>
                            <option value="custom" {{ old('campaign_type', $campaign->campaign_type) == 'custom' ? 'selected' : '' }}>Custom</option>
                        </select>
                        @error('campaign_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Campaign Image -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Campaign Image (1000×1000px recommended)</label>
                        
                        @if($campaign->image)
                            <div class="mt-2 mb-4">
                                <img src="{{ Storage::url($campaign->image) }}" alt="{{ $campaign->title }}" class="h-32 w-32 object-cover rounded-md border border-gray-200">
                                <p class="mt-1 text-xs text-gray-500">Current image</p>
                            </div>
                        @endif
                        
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                        <span>Upload a new image</span>
                                        <input id="image" name="image" type="file" class="sr-only" accept="image/*">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB (1000×1000px optimal size)</p>
                            </div>
                        </div>
                        <div id="image-preview" class="mt-2 hidden">
                            <img src="#" alt="Image Preview" class="h-32 w-32 object-cover rounded-md">
                        </div>
                        @error('image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Website URL -->
                    <div>
                        <label for="website_url" class="block text-sm font-medium text-gray-700">Website URL (Optional)</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                https://
                            </span>
                            <input type="text" name="website_url" id="website_url" value="{{ old('website_url', str_replace('https://', '', $campaign->website_url)) }}"
                                   class="flex-1 block w-full border-gray-300 rounded-none rounded-r-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="www.example.com">
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Leave blank if you want leads to stay on the campaign landing page</p>
                        @error('website_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Campaign Dates -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $campaign->start_date ? $campaign->start_date->format('Y-m-d') : '') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('start_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">End Date (Optional)</label>
                            <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $campaign->end_date ? $campaign->end_date->format('Y-m-d') : '') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('end_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 rounded-b-lg">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Update Campaign
                </button>
            </div>
        </form>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
        <div class="bg-white shadow-sm rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 text-red-600">
                    Danger Zone
                </h3>
                <div class="mt-2 max-w-xl text-sm text-gray-500">
                    <p>Once you delete a campaign, it cannot be recovered along with its analytics data.</p>
                </div>
                <div class="mt-5">
                    <form action="{{ route('campaigns.destroy', $campaign) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this campaign? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                            Delete Campaign
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Image preview functionality
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            const preview = document.getElementById('image-preview');
            const previewImage = preview.querySelector('img');
            
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                preview.classList.remove('hidden');
            }
            
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
@endsection
