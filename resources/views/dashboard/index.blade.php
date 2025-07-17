@extends('layouts.app')

@section('title', 'Dashboard - SEO Audit Pro')
@section('page-title', 'Dashboard Overview')

@section('content')
<div x-data="dashboardData()">
    <!-- Welcome Section -->
    <div class="mb-8">
        @auth
        <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold mb-2">Welcome back, {{ auth()->user()->name }}!</h2>
                    <p class="text-primary-100">Here's what's happening with your SEO performance today.</p>
                    @if(auth()->user()->company)
                        <p class="text-primary-200 text-sm mt-1">{{ auth()->user()->company }}</p>
                    @endif
                </div>
                <div class="hidden lg:block">
                    <div class="w-24 h-24 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <i class="fas fa-chart-line text-3xl text-white"></i>
                    </div>
                </div>
            </div>
            
            @if(auth()->user()->created_at->diffInDays(now()) < 1)
                <!-- New User Welcome -->
                <div class="mt-4 p-4 bg-white bg-opacity-20 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-rocket text-white mr-3"></i>
                        <div>
                            <h3 class="font-semibold text-white">🎉 Account Created Successfully!</h3>
                            <p class="text-primary-100 text-sm">
                                Get started by tracking your first keyword or running an SEO audit.
                            </p>
                        </div>
                    </div>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <a href="{{ route('tools.keyword-tracker') }}" class="bg-white text-primary-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-100 transition-colors">
                            Track Keywords
                        </a>
                        <a href="{{ route('tools.site-audit') }}" class="border border-white text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-white hover:bg-opacity-20 transition-colors">
                            Run SEO Audit
                        </a>
                    </div>
                </div>
            @endif
        </div>
        @else
        <div class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold mb-2">Exploring SEO Audit Pro</h2>
                    <p class="text-amber-100">You're viewing demo data. Sign up to unlock real SEO tracking!</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('register') }}" class="bg-white text-amber-600 px-6 py-3 rounded-lg font-medium hover:bg-gray-100 transition-colors">
                        Sign Up Free
                    </a>
                    <a href="{{ route('login') }}" class="border border-white text-white px-6 py-3 rounded-lg font-medium hover:bg-white hover:bg-opacity-20 transition-colors">
                        Sign In
                    </a>
                </div>
            </div>
        </div>
        @endauth
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Audits -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Audits</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_audits'] }}</p>
                    <p class="text-xs text-green-600 flex items-center mt-1">
                        <i class="fas fa-arrow-up mr-1"></i>
                        12% from last month
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-search text-blue-600"></i>
                </div>
            </div>
        </div>

        <!-- Keywords Tracked -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Keywords Tracked</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_keywords'] }}</p>
                    <p class="text-xs text-green-600 flex items-center mt-1">
                        <i class="fas fa-arrow-up mr-1"></i>
                        8% from last month
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-green-600"></i>
                </div>
            </div>
        </div>

        <!-- Total Backlinks -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Backlinks</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_backlinks'] }}</p>
                    <p class="text-xs text-green-600 flex items-center mt-1">
                        <i class="fas fa-arrow-up mr-1"></i>
                        15% from last month
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-link text-purple-600"></i>
                </div>
            </div>
        </div>

        <!-- Average Score -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Avg. Audit Score</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['avg_audit_score'] }}%</p>
                    <p class="text-xs text-green-600 flex items-center mt-1">
                        <i class="fas fa-arrow-up mr-1"></i>
                        5% from last month
                    </p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-trophy text-orange-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Audit Section -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick SEO Audit</h3>
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input 
                    type="url" 
                    x-model="auditUrl"
                    placeholder="Enter website URL (e.g., https://example.com)"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                >
            </div>
            <button 
                @click="runQuickAudit()"
                :disabled="loading || !auditUrl"
                class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center min-w-[120px]"
            >
                <span x-show="!loading">
                    <i class="fas fa-search mr-2"></i>
                    Audit Now
                </span>
                <span x-show="loading" x-cloak>
                    <div class="loading-spinner mr-2"></div>
                    Analyzing...
                </span>
            </button>
        </div>
        
        <!-- Quick Audit Results -->
        <div x-show="auditResults" x-cloak class="mt-6 p-4 bg-gray-50 rounded-lg">
            <h4 class="font-semibold text-gray-900 mb-3">Audit Results</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4" x-show="auditResults">
                <div class="text-center">
                    <div class="text-2xl font-bold text-primary-600" x-text="auditResults?.audit_score || 0"></div>
                    <div class="text-sm text-gray-500">Overall Score</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600" x-text="auditResults?.page_load_speed || 0"></div>
                    <div class="text-sm text-gray-500">Load Time (ms)</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600" x-text="auditResults?.internal_links_count || 0"></div>
                    <div class="text-sm text-gray-500">Internal Links</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600" x-text="auditResults?.external_links_count || 0"></div>
                    <div class="text-sm text-gray-500">External Links</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Recent Audits -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Audits</h3>
                        <div class="flex space-x-2">
                            <a href="{{ route('meta-analyzer.history') }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                                Meta Audits
                            </a>
                            <span class="text-gray-400">|</span>
                            <a href="{{ route('site-audit.history') }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                                Site Audits
                            </a>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    @if(count($stats['recent_audits']) > 0)
                        <div class="space-y-4">
                            @foreach($stats['recent_audits'] as $audit)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center">
                                        @if($audit['type'] === 'meta')
                                            <i class="fas fa-tags text-primary-600"></i>
                                        @else
                                            <i class="fas fa-globe text-primary-600"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ $audit['url'] }}</h4>
                                        <p class="text-sm text-gray-500">
                                            {{ $audit['created_at'] }} 
                                            @if($audit['type'] === 'meta')
                                                <span class="text-primary-600">• Meta Audit</span>
                                            @else
                                                <span class="text-blue-600">• Site Audit</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <div class="text-right">
                                        <div class="text-lg font-semibold text-gray-900">{{ $audit['audit_score'] }}%</div>
                                        <div class="text-xs text-gray-500">Score</div>
                                    </div>
                                    @if($audit['type'] === 'meta')
                                        <a href="{{ route('meta-analyzer.show', $audit['id']) }}" class="p-2 text-gray-400 hover:text-gray-600">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    @else
                                        <a href="{{ route('site-audit.show', $audit['id']) }}" class="p-2 text-gray-400 hover:text-gray-600">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-search text-gray-400 text-xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No audits yet</h3>
                            <p class="text-gray-500 mb-4">Start by running your first SEO audit to see results here.</p>
                            <div class="flex justify-center space-x-3">
                                <a href="{{ route('tools.meta-analyzer') }}" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors">
                                    Meta Analyzer
                                </a>
                                <a href="{{ route('tools.site-audit') }}" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors">
                                    Site Audit
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
             <!-- Quick Tools -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 mt-5">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Quick Tools</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-4 gap-3">
                        <a href="{{ route('tools.keyword-tracker') }}" class="p-3 bg-blue-50 rounded-lg text-center hover:bg-blue-100 transition-colors">
                            <i class="fas fa-chart-line text-blue-600 text-lg mb-2"></i>
                            <div class="text-sm font-medium text-gray-900">Keywords</div>
                        </a>
                        <a href="{{ route('tools.backlink-checker') }}" class="p-3 bg-purple-50 rounded-lg text-center hover:bg-purple-100 transition-colors">
                            <i class="fas fa-link text-purple-600 text-lg mb-2"></i>
                            <div class="text-sm font-medium text-gray-900">Backlinks</div>
                        </a>
                        <a href="{{ route('tools.meta-analyzer') }}" class="p-3 bg-green-50 rounded-lg text-center hover:bg-green-100 transition-colors">
                            <i class="fas fa-tags text-green-600 text-lg mb-2"></i>
                            <div class="text-sm font-medium text-gray-900">Meta Tags</div>
                        </a>
                        <a href="{{ route('tools.reports') }}" class="p-3 bg-orange-50 rounded-lg text-center hover:bg-orange-100 transition-colors">
                            <i class="fas fa-file-alt text-orange-600 text-lg mb-2"></i>
                            <div class="text-sm font-medium text-gray-900">Reports</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Widgets -->
        <div class="space-y-6">
            <!-- Top Keywords -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Top Keywords</h3>
                </div>
                <div class="p-6">
                    @if(count($stats['top_keywords']) > 0)
                        <div class="space-y-4">
                            @foreach($stats['top_keywords'] as $keyword)
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $keyword['keyword'] }}</h4>
                                    <p class="text-sm text-gray-500">Position {{ $keyword['position'] }}</p>
                                </div>
                                <div class="flex items-center space-x-1">
                                    @if($keyword['change'] > 0)
                                        <i class="fas fa-arrow-up text-green-500 text-xs"></i>
                                        <span class="text-green-600 text-sm">+{{ $keyword['change'] }}</span>
                                    @elseif($keyword['change'] < 0)
                                        <i class="fas fa-arrow-down text-red-500 text-xs"></i>
                                        <span class="text-red-600 text-sm">{{ $keyword['change'] }}</span>
                                    @else
                                        <span class="text-gray-500 text-sm">-</span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-chart-line text-gray-400"></i>
                            </div>
                            <h4 class="font-medium text-gray-900 mb-2">No keywords tracked</h4>
                            <p class="text-sm text-gray-500 mb-3">Start tracking keywords to see your rankings.</p>
                            <a href="{{ route('tools.keyword-tracker') }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                                Track Keywords
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            

            <!-- Recent Backlinks -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Backlinks</h3>
                </div>
                <div class="p-6">
                    @if(count($stats['recent_backlinks']) > 0)
                        <div class="space-y-4">
                            @foreach($stats['recent_backlinks'] as $backlink)
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $backlink['source_domain'] }}</h4>
                                    <p class="text-sm text-gray-500">{{ $backlink['discovered_date'] }}</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-semibold text-gray-900">DA {{ $backlink['domain_authority'] }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-link text-gray-400"></i>
                            </div>
                            <h4 class="font-medium text-gray-900 mb-2">No backlinks found</h4>
                            <p class="text-sm text-gray-500 mb-3">Check your backlink profile to see who's linking to you.</p>
                            <a href="{{ route('tools.backlink-checker') }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                                Check Backlinks
                            </a>
                        </div>
                    @endif
                </div>
            </div>

           
        </div>
    </div>
</div>

@push('scripts')
<script>
function dashboardData() {
    return {
        auditUrl: '',
        loading: false,
        auditResults: null,
        
        async runQuickAudit() {
            if (!this.auditUrl) return;
            
            this.loading = true;
            this.auditResults = null;
            
            try {
                const response = await apiRequest('/api/audit', {
                    method: 'POST',
                    body: {
                        url: this.auditUrl
                    }
                });
                
                if (response.success) {
                    this.auditResults = response.data;
                    showNotification('SEO audit completed successfully!', 'success');
                } else {
                    throw new Error(response.message || 'Audit failed');
                }
            } catch (error) {
                showNotification('Error running audit: ' + error.message, 'error');
                console.error('Audit error:', error);
            } finally {
                this.loading = false;
            }
        }
    };
}
</script>
@endpush
@endsection
