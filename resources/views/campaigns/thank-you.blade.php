<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - {{ $campaign->title }}</title>
    <meta name="description" content="Thank you for submitting your information for {{ $campaign->title }}">
    
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
        <main class="flex-grow flex items-center justify-center">
            <div class="max-w-md mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100">
                        <svg class="h-12 w-12 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    
                    <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                        Thank You!
                    </h2>
                    
                    <p class="mt-2 text-lg text-gray-600">
                        Your submission has been received successfully.
                    </p>
                    
                    @if($campaign->website_url)
                    <div class="mt-8">
                        <a href="{{ $campaign->website_url }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-gradient hover:opacity-90">
                            Continue to Website
                        </a>
                    </div>
                    @endif
                    
                    <div class="mt-6">
                        <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Share with others</h3>
                            
                            <div class="flex justify-center space-x-4">
                                <!-- Facebook -->
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('campaigns.landing', $campaign->share_link)) }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                    <span class="sr-only">Share on Facebook</span>
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                                
                                <!-- Twitter -->
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('campaigns.landing', $campaign->share_link)) }}&text={{ urlencode($campaign->title) }}" target="_blank" class="text-blue-400 hover:text-blue-600">
                                    <span class="sr-only">Share on Twitter</span>
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                                    </svg>
                                </a>
                                
                                <!-- LinkedIn -->
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(route('campaigns.landing', $campaign->share_link)) }}" target="_blank" class="text-blue-700 hover:text-blue-900">
                                    <span class="sr-only">Share on LinkedIn</span>
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                                
                                <!-- Email -->
                                <a href="mailto:?subject={{ urlencode($campaign->title) }}&body={{ urlencode('Check out this campaign: ' . route('campaigns.landing', $campaign->share_link)) }}" class="text-gray-600 hover:text-gray-800">
                                    <span class="sr-only">Share via Email</span>
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                    </svg>
                                </a>
                            </div>
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
    
    <!-- Track conversion -->
    <script>
        // This would typically be replaced with actual conversion tracking code
        console.log('Campaign conversion tracked');
    </script>
</body>
</html>
