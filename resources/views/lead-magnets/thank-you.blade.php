<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - {{ $leadMagnet->title }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        .fade-in {
            animation: fadeIn 0.8s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .bounce-in {
            animation: bounceIn 0.8s ease-out;
        }
        
        @keyframes bounceIn {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-primary-50 to-blue-50 font-sans antialiased min-h-screen">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-2xl w-full text-center">
            <!-- Success Icon -->
            <div class="bounce-in mb-8">
                <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-check-circle text-4xl text-green-600"></i>
                </div>
            </div>

            <!-- Main Content -->
            <div class="fade-in">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">
                    {{ $leadMagnet->thank_you_page_title ?: 'Thank You!' }}
                </h1>
                
                <div class="text-lg text-gray-600 mb-8 max-w-lg mx-auto">
                    @if($leadMagnet->thank_you_page_content)
                        {!! nl2br(e($leadMagnet->thank_you_page_content)) !!}
                    @else
                        <p class="mb-4">Your download link has been sent to your email address.</p>
                        <p>Please check your inbox (and spam folder) for the download link to <strong>{{ $leadMagnet->title }}</strong>.</p>
                    @endif
                </div>

                <!-- Download Card -->
                <div class="bg-white rounded-xl shadow-lg p-8 mb-8 max-w-md mx-auto">
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-file-{{ $leadMagnet->file_type === 'application/pdf' ? 'pdf' : 'alt' }} text-primary-600"></i>
                        </div>
                        <div class="flex-1 text-left">
                            <div class="font-semibold text-gray-900">{{ $leadMagnet->title }}</div>
                            <div class="text-sm text-gray-500">{{ $leadMagnet->formatted_file_size }}</div>
                        </div>
                    </div>
                    
                    <div class="text-sm text-gray-600 mb-4">
                        <i class="fas fa-clock mr-2"></i>
                        Download link sent via email
                    </div>
                    
                    <div class="text-xs text-gray-500">
                        Didn't receive the email? Check your spam folder or contact support.
                    </div>
                </div>

                <!-- What's Next -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">What's Next?</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-2">
                                <i class="fas fa-envelope text-blue-600"></i>
                            </div>
                            <div class="font-medium text-gray-900">Check Email</div>
                            <div class="text-gray-600">Look for our email with your download link</div>
                        </div>
                        <div class="flex flex-col items-center text-center">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-2">
                                <i class="fas fa-download text-green-600"></i>
                            </div>
                            <div class="font-medium text-gray-900">Download</div>
                            <div class="text-gray-600">Click the link to get your free resource</div>
                        </div>
                        <div class="flex flex-col items-center text-center">
                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mb-2">
                                <i class="fas fa-rocket text-purple-600"></i>
                            </div>
                            <div class="font-medium text-gray-900">Implement</div>
                            <div class="text-gray-600">Use the resource to improve your results</div>
                        </div>
                    </div>
                </div>

                <!-- CTA Section -->
                <div class="bg-gradient-to-r from-primary-600 to-blue-600 rounded-xl text-white p-8">
                    <h2 class="text-2xl font-bold mb-4">Want More SEO Resources?</h2>
                    <p class="text-primary-100 mb-6">
                        Discover more tools and resources to boost your website's performance
                    </p>
                    <a href="{{ url('/') }}" 
                       class="inline-flex items-center px-6 py-3 bg-white text-primary-600 rounded-lg hover:bg-gray-100 transition-colors font-medium">
                        <i class="fas fa-tools mr-2"></i>
                        Explore SEO Tools
                    </a>
                </div>

                <!-- Footer -->
                <div class="mt-8 text-sm text-gray-500">
                    <p>
                        Powered by <a href="{{ url('/') }}" class="text-primary-600 hover:text-primary-700">SEO Audit Pro</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Auto-refresh reminder script -->
    <script>
        // Remind user to check email after 30 seconds
        setTimeout(() => {
            const reminder = document.createElement('div');
            reminder.className = 'fixed top-4 right-4 bg-amber-100 border border-amber-300 text-amber-800 px-4 py-3 rounded-lg shadow-lg max-w-sm';
            reminder.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-envelope mr-2"></i>
                    <div>
                        <div class="font-medium">Check your email!</div>
                        <div class="text-sm">Your download link should arrive shortly.</div>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-amber-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            document.body.appendChild(reminder);
            
            // Auto-remove after 10 seconds
            setTimeout(() => {
                if (reminder.parentElement) {
                    reminder.remove();
                }
            }, 10000);
        }, 30000);
    </script>
</body>
</html>
