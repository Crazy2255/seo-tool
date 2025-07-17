<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $leadMagnet->title }} - Free Download</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
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
        [x-cloak] { display: none !important; }
        
        .loading-spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3498db;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">
    <div class="min-h-screen flex items-center justify-center p-4" x-data="leadMagnetLanding()">
        <div class="max-w-2xl w-full">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-primary-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-gift text-2xl text-white"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $leadMagnet->title }}</h1>
                <p class="text-lg text-gray-600">{{ $leadMagnet->description }}</p>
            </div>

            <!-- Main Content Card -->
            <div class="bg-white rounded-xl shadow-lg p-8 fade-in">
                <!-- Success State -->
                <div x-show="submitted" x-cloak class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check text-2xl text-green-600"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Check Your Email!</h2>
                    <p class="text-gray-600 mb-6">
                        We've sent your download link to <strong x-text="submittedEmail"></strong>. 
                        Check your inbox (and spam folder) in the next few minutes.
                    </p>
                    <button @click="submitted = false" 
                            class="text-primary-600 hover:text-primary-700 font-medium">
                        ← Go Back
                    </button>
                </div>

                <!-- Form State -->
                <div x-show="!submitted">
                    <div class="text-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">
                            {{ $leadMagnet->form_title ?: 'Get Your Free Download' }}
                        </h2>
                        @if($leadMagnet->form_description)
                        <p class="text-gray-600">{{ $leadMagnet->form_description }}</p>
                        @endif
                    </div>

                    <!-- File Preview -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file-{{ $leadMagnet->file_type === 'application/pdf' ? 'pdf' : 'alt' }} text-primary-600"></i>
                            </div>
                            <div class="flex-1">
                                <div class="font-medium text-gray-900">{{ $leadMagnet->file_name }}</div>
                                <div class="text-sm text-gray-500">{{ $leadMagnet->formatted_file_size }}</div>
                            </div>
                            <div class="text-primary-600">
                                <i class="fas fa-download"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Lead Form -->
                    <form @submit.prevent="submitForm()" class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                            <input type="text" 
                                   id="name" 
                                   x-model="form.name"
                                   placeholder="Enter your full name"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   required>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email" 
                                   id="email" 
                                   x-model="form.email"
                                   placeholder="Enter your email address"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   required>
                        </div>

                        <button type="submit" 
                                :disabled="loading"
                                class="w-full px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors font-medium">
                            <span x-show="!loading">{{ $leadMagnet->form_button_text ?: 'Download Now' }}</span>
                            <span x-show="loading" class="flex items-center justify-center">
                                <div class="loading-spinner mr-2"></div>
                                Processing...
                            </span>
                        </button>
                    </form>

                    <!-- Trust Indicators -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="flex items-center justify-center space-x-6 text-sm text-gray-500">
                            <div class="flex items-center">
                                <i class="fas fa-shield-alt mr-2"></i>
                                100% Secure
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-envelope mr-2"></i>
                                No Spam
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-times-circle mr-2"></i>
                                Unsubscribe Anytime
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-8">
                <p class="text-sm text-gray-500">
                    Powered by <a href="{{ url('/') }}" class="text-primary-600 hover:text-primary-700">SEO Audit Pro</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        function leadMagnetLanding() {
            return {
                submitted: false,
                submittedEmail: '',
                loading: false,
                form: {
                    name: '',
                    email: ''
                },

                async submitForm() {
                    if (!this.form.name || !this.form.email) {
                        alert('Please fill in all fields');
                        return;
                    }

                    this.loading = true;

                    try {
                        const response = await fetch(`{{ route('lead.submit', $leadMagnet->slug) }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                name: this.form.name,
                                email: this.form.email
                            })
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            this.submittedEmail = this.form.email;
                            this.submitted = true;
                            this.form = { name: '', email: '' };
                            
                            // Redirect to thank you page after a delay
                            setTimeout(() => {
                                window.location.href = data.redirect_url || `{{ route('lead.thank-you', $leadMagnet->slug) }}`;
                            }, 2000);
                        } else {
                            alert(data.message || 'Submission failed. Please try again.');
                        }
                    } catch (error) {
                        console.error('Submission error:', error);
                        alert('Submission failed. Please try again.');
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</body>
</html>
