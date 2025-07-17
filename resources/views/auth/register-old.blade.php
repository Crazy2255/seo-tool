<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign Up - SEO Audit Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <div class="mx-auto h-16 w-16 bg-primary-600 rounded-xl flex items-center justify-center mb-4">
                <i class="fas fa-chart-line text-2xl text-white"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-900">Create your account</h2>
            <p class="mt-2 text-sm text-gray-600">
                Start tracking your keywords and get real SEO insights
            </p>
        </div>

        <!-- Registration Form -->
        <form class="mt-8 space-y-6" action="{{ route('register') }}" method="POST" x-data="registrationForm()" @submit="handleSubmit">
            @csrf
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            
            <!-- Error Messages -->
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex">
                        <i class="fas fa-exclamation-circle text-red-400 mr-2 mt-0.5"></i>
                        <div>
                            <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div class="space-y-4">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input 
                        id="name" 
                        name="name" 
                        type="text" 
                        required 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                        placeholder="Enter your full name"
                        value="{{ old('name') }}"
                    >
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        required 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                        placeholder="Enter your email"
                        value="{{ old('email') }}"
                    >
                </div>

                <!-- Company (Optional) -->
                <div>
                    <label for="company" class="block text-sm font-medium text-gray-700 mb-1">Company Name (Optional)</label>
                    <input 
                        id="company" 
                        name="company" 
                        type="text" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                        placeholder="Enter your company name"
                        value="{{ old('company') }}"
                    >
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <input 
                            id="password" 
                            name="password" 
                            :type="showPassword ? 'text' : 'password'"
                            required 
                            class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="Create a strong password"
                            x-model="password"
                            @input="checkPasswordStrength()"
                        >
                        <button 
                            type="button" 
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center"
                        >
                            <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-gray-400 hover:text-gray-600"></i>
                        </button>
                    </div>
                    
                    <!-- Password Strength Indicator -->
                    <div class="mt-2" x-show="password.length > 0">
                        <div class="flex items-center space-x-2">
                            <div class="flex-1 bg-gray-200 rounded-full h-2">
                                <div 
                                    class="h-2 rounded-full transition-all duration-300"
                                    :class="passwordStrengthColor"
                                    :style="`width: ${passwordStrength}%`"
                                ></div>
                            </div>
                            <span class="text-xs font-medium" :class="passwordStrengthTextColor" x-text="passwordStrengthText"></span>
                        </div>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <input 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        type="password" 
                        required 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                        placeholder="Confirm your password"
                    >
                </div>
            </div>

            <!-- Terms and Privacy -->
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input 
                        id="terms" 
                        name="terms" 
                        type="checkbox" 
                        required
                        class="w-4 h-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                    >
                </div>
                <div class="ml-3 text-sm">
                    <label for="terms" class="text-gray-700">
                        I agree to the 
                        <a href="#" class="text-primary-600 hover:text-primary-500 font-medium">Terms of Service</a> 
                        and 
                        <a href="#" class="text-primary-600 hover:text-primary-500 font-medium">Privacy Policy</a>
                    </label>
                </div>
            </div>

            <!-- Submit Button -->
            <div>
                <button 
                    type="submit" 
                    :disabled="isSubmitting"
                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3" x-show="!isSubmitting">
                        <i class="fas fa-user-plus text-primary-500 group-hover:text-primary-400"></i>
                    </span>
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3" x-show="isSubmitting" x-cloak>
                        <i class="fas fa-spinner fa-spin text-primary-500"></i>
                    </span>
                    <span x-text="isSubmitting ? 'Creating Account...' : 'Create Account'"></span>
                </button>
            </div>

            <!-- Sign In Link -->
            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="font-medium text-primary-600 hover:text-primary-500">
                        Sign in here
                    </a>
                </p>
            </div>
        </form>

        <!-- Features -->
        <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">What you'll get:</h3>
            <ul class="space-y-3">
                <li class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                    <span class="text-sm text-gray-700">Real-time keyword ranking tracking</span>
                </li>
                <li class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                    <span class="text-sm text-gray-700">Comprehensive SEO audits</span>
                </li>
                <li class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                    <span class="text-sm text-gray-700">Competitor analysis tools</span>
                </li>
                <li class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                    <span class="text-sm text-gray-700">Export reports and data</span>
                </li>
                <li class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                    <span class="text-sm text-gray-700">Email alerts for ranking changes</span>
                </li>
            </ul>
        </div>
    </div>

    <script>
        function registrationForm() {
            return {
                showPassword: false,
                password: '',
                passwordStrength: 0,
                passwordStrengthText: '',
                passwordStrengthColor: '',
                passwordStrengthTextColor: '',
                isSubmitting: false,

                handleSubmit(event) {
                    if (this.isSubmitting) {
                        event.preventDefault();
                        return false;
                    }
                    this.isSubmitting = true;
                    // Let the form submit naturally
                },

                checkPasswordStrength() {
                    const password = this.password;
                    let strength = 0;
                    let text = '';
                    let color = '';
                    let textColor = '';

                    if (password.length >= 8) strength += 25;
                    if (/[a-z]/.test(password)) strength += 25;
                    if (/[A-Z]/.test(password)) strength += 25;
                    if (/[0-9]/.test(password)) strength += 25;

                    if (strength <= 25) {
                        text = 'Weak';
                        color = 'bg-red-500';
                        textColor = 'text-red-600';
                    } else if (strength <= 50) {
                        text = 'Fair';
                        color = 'bg-yellow-500';
                        textColor = 'text-yellow-600';
                    } else if (strength <= 75) {
                        text = 'Good';
                        color = 'bg-blue-500';
                        textColor = 'text-blue-600';
                    } else {
                        text = 'Strong';
                        color = 'bg-green-500';
                        textColor = 'text-green-600';
                    }

                    this.passwordStrength = strength;
                    this.passwordStrengthText = text;
                    this.passwordStrengthColor = color;
                    this.passwordStrengthTextColor = textColor;
                }
            }
        }
    </script>
</body>
</html>
