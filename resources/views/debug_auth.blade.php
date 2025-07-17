<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication Debug</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Authentication Debug</h1>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Authentication Status -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Authentication Status</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="font-medium">Is Authenticated:</span>
                            <span class="px-2 py-1 rounded text-sm {{ Auth::check() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ Auth::check() ? 'Yes' : 'No' }}
                            </span>
                        </div>
                        
                        @if(Auth::check())
                            <div class="flex justify-between">
                                <span class="font-medium">User ID:</span>
                                <span>{{ Auth::id() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">User Email:</span>
                                <span>{{ Auth::user()->email }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">User Name:</span>
                                <span>{{ Auth::user()->name }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Session Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Session Information</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="font-medium">Session ID:</span>
                            <span class="text-sm font-mono">{{ session()->getId() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Session Token:</span>
                            <span class="text-sm font-mono">{{ session()->token() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Has Session:</span>
                            <span class="px-2 py-1 rounded text-sm {{ session()->has('_token') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ session()->has('_token') ? 'Yes' : 'No' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Route Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Route Information</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="font-medium">Current Route:</span>
                            <span>{{ request()->route()->getName() ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">URL:</span>
                            <span class="text-sm">{{ request()->url() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Method:</span>
                            <span>{{ request()->method() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Config Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Configuration</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="font-medium">Session Driver:</span>
                            <span>{{ config('session.driver') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Session Lifetime:</span>
                            <span>{{ config('session.lifetime') }} minutes</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Expire on Close:</span>
                            <span>{{ config('session.expire_on_close') ? 'Yes' : 'No' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Session Encrypt:</span>
                            <span>{{ config('session.encrypt') ? 'Yes' : 'No' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex space-x-4">
                <a href="{{ route('login') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Go to Login
                </a>
                <a href="{{ route('dashboard') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    Go to Dashboard
                </a>
                <a href="{{ route('competitor-analysis.index') }}" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                    Go to Competitor Analysis
                </a>
                <button onclick="location.reload()" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                    Refresh
                </button>
            </div>

            <!-- Test Session -->
            <div class="mt-8 bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Test Session</h2>
                <div class="space-y-4">
                    <div>
                        <p class="text-gray-600 mb-2">Test if the session is working properly by setting and retrieving a test value:</p>
                        @php
                            session(['test_auth_debug' => now()->toString()]);
                            $testValue = session('test_auth_debug');
                        @endphp
                        <div class="flex justify-between items-center">
                            <span class="font-medium">Test Session Value:</span>
                            <span class="text-sm font-mono bg-gray-100 px-2 py-1 rounded">{{ $testValue }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Raw Session Data -->
            <div class="mt-8 bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Raw Session Data</h2>
                <div class="bg-gray-100 p-4 rounded overflow-auto">
                    <pre class="text-sm">{{ json_encode(session()->all(), JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
