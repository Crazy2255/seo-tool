<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $campaign->title }}</title>
    <meta name="description" content="{{ Str::limit($campaign->description, 160) }}">
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        .bg-gradient {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center">
                    <h1 class="text-xl font-bold text-gray-900 truncate">
                        {{ $campaign->title }}
                    </h1>
                </div>
            </div>
        </header>
        
        <!-- Main Content -->
        <main class="flex-grow">
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div class="md:flex md:items-center md:justify-between md:space-x-5 bg-white shadow rounded-lg overflow-hidden">
                    @if($campaign->image)
                    <div class="md:w-1/2">
                        <img src="{{ Storage::url($campaign->image) }}" alt="{{ $campaign->title }}" class="w-full h-full object-cover">
                    </div>
                    @endif
                    
                    <div class="p-6 md:p-8 @if(!$campaign->image) w-full @else md:w-1/2 @endif">
                        <h2 class="text-2xl font-bold text-gray-900 md:text-3xl mb-4">
                            {{ $campaign->title }}
                        </h2>
                        
                        <div class="prose max-w-none text-gray-600 mb-8">
                            <p>{{ $campaign->description }}</p>
                        </div>
                        
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Get Access Now</h3>
                            
                            <form action="{{ route('campaigns.store-lead', $campaign->share_link) }}" method="POST">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                                        <input type="text" name="name" id="name" 
                                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                               placeholder="Your name">
                                    </div>
                                    
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address <span class="text-red-500">*</span></label>
                                        <input type="email" name="email" id="email" required
                                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                               placeholder="your@email.com">
                                        @error('email')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                        <button type="submit" 
                                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-gradient hover:opacity-90">
                                            Submit
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        
        <!-- Footer -->
        <footer class="bg-white">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <p class="text-sm text-gray-500 text-center">
                    © {{ date('Y') }} | {{ $campaign->title }} | All Rights Reserved
                </p>
            </div>
        </footer>
    </div>
    
    <!-- Track page view -->
    <script>
        // This would typically be replaced with actual tracking code
        console.log('Campaign page view tracked');
    </script>
</body>
</html>
