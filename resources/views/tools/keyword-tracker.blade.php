@extends('layouts.app')

@section('title', 'Keyword Rank Tracker - SEO Audit Pro')
@section('page-title', 'Keyword Rank Tracker')

@section('content')
<div x-data="keywordTrackerData()">
    @guest
    <!-- Demo Mode Banner for Guests -->
    <div class="mb-6 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-600 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Demo Mode</h3>
                <p class="text-sm text-blue-700 mt-1">
                    You're viewing demo data. Sign up to track your own keywords and get real-time rankings!
                </p>
            </div>
            <div class="ml-auto">
                <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition-colors">
                    Sign Up Free
                </a>
            </div>
        </div>
    </div>
    @else
    <!-- Welcome Banner for Authenticated Users -->
    <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-green-800">Real Data Tracking</h3>
                <p class="text-sm text-green-700 mt-1">
                    Welcome {{ auth()->user()->name }}! You're now tracking real keyword data.
                </p>
            </div>
            <div class="ml-auto flex items-center space-x-2">
                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                    <i class="fas fa-shield-alt mr-1"></i>
                    Live Data
                </span>
                <button @click="refreshAuthStatus(); loadKeywords();" class="bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700" title="Refresh authentication and data">
                    <i class="fas fa-refresh"></i>
                </button>
            </div>
        </div>
    </div>
    @endguest

    <!-- Debug Info (only show in development) -->
    <div class="mb-6 bg-gray-100 border border-gray-300 rounded-xl p-4" x-show="true">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-medium text-gray-800">Debug Info</h3>
                <p class="text-xs text-gray-600">
                    Auth Status: <span x-text="isAuthenticated ? 'Authenticated' : 'Not Authenticated'"></span> |
                    Keywords: <span x-text="keywords.length"></span> |
                    PHP Auth: {{ auth()->check() ? 'true' : 'false' }}
                </p>
            </div>
            <div class="flex space-x-2">
                <button @click="refreshAuthStatus(); loadKeywords();" class="bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700">
                    Refresh Auth & Data
                </button>
            </div>
        </div>
    </div>

    <!-- Header -->
    <div class="mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Keyword Rank Tracker</h2>
                    <p class="text-gray-600">Monitor your website's Google ranking for important keywords and track changes over time.</p>
                </div>
                <div class="hidden lg:block">
                    <div class="w-16 h-16 bg-green-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-chart-line text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add New Keyword Form -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Add New Keyword</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Keyword</label>
                <input 
                    type="text" 
                    x-model="newKeyword.keyword"
                    placeholder="Enter keyword"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                >
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Target URL</label>
                <input 
                    type="url" 
                    x-model="newKeyword.url"
                    placeholder="https://example.com"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                >
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                <select x-model="newKeyword.country" @change="onCountryChange()" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="US">United States</option>
                    <option value="UK">United Kingdom</option>
                    <option value="CA">Canada</option>
                    <option value="AU">Australia</option>
                    <option value="DE">Germany</option>
                    <option value="FR">France</option>
                    <option value="ES">Spain</option>
                    <option value="IT">Italy</option>
                    <option value="AE">United Arab Emirates</option>
                    <option value="SA">Saudi Arabia</option>
                    <option value="IN">India</option>
                    <option value="JP">Japan</option>
                    <option value="CN">China</option>
                    <option value="BR">Brazil</option>
                    <option value="MX">Mexico</option>
                    <option value="AR">Argentina</option>
                    <option value="NL">Netherlands</option>
                    <option value="BE">Belgium</option>
                    <option value="CH">Switzerland</option>
                    <option value="AT">Austria</option>
                    <option value="SE">Sweden</option>
                    <option value="NO">Norway</option>
                    <option value="DK">Denmark</option>
                    <option value="FI">Finland</option>
                    <option value="PL">Poland</option>
                    <option value="CZ">Czech Republic</option>
                    <option value="HU">Hungary</option>
                    <option value="RO">Romania</option>
                    <option value="BG">Bulgaria</option>
                    <option value="HR">Croatia</option>
                    <option value="SI">Slovenia</option>
                    <option value="SK">Slovakia</option>
                    <option value="LT">Lithuania</option>
                    <option value="LV">Latvia</option>
                    <option value="EE">Estonia</option>
                    <option value="PT">Portugal</option>
                    <option value="GR">Greece</option>
                    <option value="TR">Turkey</option>
                    <option value="IL">Israel</option>
                    <option value="EG">Egypt</option>
                    <option value="ZA">South Africa</option>
                    <option value="NG">Nigeria</option>
                    <option value="KE">Kenya</option>
                    <option value="MA">Morocco</option>
                    <option value="DZ">Algeria</option>
                    <option value="TN">Tunisia</option>
                    <option value="JO">Jordan</option>
                    <option value="LB">Lebanon</option>
                    <option value="KW">Kuwait</option>
                    <option value="QA">Qatar</option>
                    <option value="BH">Bahrain</option>
                    <option value="OM">Oman</option>
                    <option value="KR">South Korea</option>
                    <option value="TH">Thailand</option>
                    <option value="VN">Vietnam</option>
                    <option value="MY">Malaysia</option>
                    <option value="SG">Singapore</option>
                    <option value="ID">Indonesia</option>
                    <option value="PH">Philippines</option>
                    <option value="TW">Taiwan</option>
                    <option value="HK">Hong Kong</option>
                    <option value="NZ">New Zealand</option>
                    <option value="RU">Russia</option>
                    <option value="UA">Ukraine</option>
                    <option value="BY">Belarus</option>
                    <option value="KZ">Kazakhstan</option>
                    <option value="UZ">Uzbekistan</option>
                    <option value="AM">Armenia</option>
                    <option value="GE">Georgia</option>
                    <option value="AZ">Azerbaijan</option>
                    <option value="IR">Iran</option>
                    <option value="IQ">Iraq</option>
                    <option value="AF">Afghanistan</option>
                    <option value="PK">Pakistan</option>
                    <option value="BD">Bangladesh</option>
                    <option value="LK">Sri Lanka</option>
                    <option value="NP">Nepal</option>
                    <option value="MM">Myanmar</option>
                    <option value="LA">Laos</option>
                    <option value="KH">Cambodia</option>
                    <option value="BN">Brunei</option>
                    <option value="MV">Maldives</option>
                    <option value="CL">Chile</option>
                    <option value="PE">Peru</option>
                    <option value="CO">Colombia</option>
                    <option value="VE">Venezuela</option>
                    <option value="EC">Ecuador</option>
                    <option value="BO">Bolivia</option>
                    <option value="PY">Paraguay</option>
                    <option value="UY">Uruguay</option>
                    <option value="GY">Guyana</option>
                    <option value="SR">Suriname</option>
                    <option value="CR">Costa Rica</option>
                    <option value="PA">Panama</option>
                    <option value="GT">Guatemala</option>
                    <option value="HN">Honduras</option>
                    <option value="SV">El Salvador</option>
                    <option value="NI">Nicaragua</option>
                    <option value="BZ">Belize</option>
                    <option value="JM">Jamaica</option>
                    <option value="TT">Trinidad and Tobago</option>
                    <option value="BB">Barbados</option>
                    <option value="BS">Bahamas</option>
                    <option value="CU">Cuba</option>
                    <option value="DO">Dominican Republic</option>
                    <option value="HT">Haiti</option>
                    <option value="PR">Puerto Rico</option>
                </select>
            </div>
            
            <!-- City Selection (shown when UAE or other supported countries are selected) -->
            <div x-show="showCitySelector" x-cloak>
                <label class="block text-sm font-medium text-gray-700 mb-2">City/Region</label>
                <select x-model="newKeyword.city" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <template x-for="city in availableCities" :key="city.value">
                        <option :value="city.value" x-text="city.label"></option>
                    </template>
                </select>
            </div>
            
            <div class="flex items-end">
                <button 
                    @click="addKeyword()"
                    :disabled="loading || !newKeyword.keyword || !newKeyword.url"
                    class="w-full px-4 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
                >
                    <span x-show="!loading">
                        <i class="fas fa-plus mr-2"></i>
                        Add Keyword
                    </span>
                    <span x-show="loading" x-cloak>
                        <div class="loading-spinner mr-2"></div>
                        Adding...
                    </span>
                </button>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 mb-8">
        <div class="flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
            <div class="flex items-center space-x-4">
                <h3 class="text-lg font-semibold text-gray-900">Tracked Keywords</h3>
                <span class="bg-primary-100 text-primary-800 px-3 py-1 rounded-full text-sm font-medium" x-text="keywords.length + ' keywords'"></span>
            </div>
            
            <div class="flex items-center space-x-4">
                <button 
                    @click="bulkUpdate()"
                    :disabled="bulkLoading"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 flex items-center"
                >
                    <span x-show="!bulkLoading">
                        <i class="fas fa-sync mr-2"></i>
                        Update All
                    </span>
                    <span x-show="bulkLoading" x-cloak>
                        <div class="loading-spinner mr-2"></div>
                        Updating...
                    </span>
                </button>
                
                <button 
                    @click="exportData()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center"
                >
                    <i class="fas fa-download mr-2"></i>
                    Export
                </button>
            </div>
        </div>
    </div>

    <!-- Keywords Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left py-4 px-6 font-semibold text-gray-900">Keyword</th>
                        <th class="text-left py-4 px-6 font-semibold text-gray-900">URL</th>
                        <th class="text-center py-4 px-6 font-semibold text-gray-900">Position</th>
                        <th class="text-center py-4 px-6 font-semibold text-gray-900">Change</th>
                        <th class="text-center py-4 px-6 font-semibold text-gray-900">Search Volume</th>
                        <th class="text-center py-4 px-6 font-semibold text-gray-900">Difficulty</th>
                        <th class="text-center py-4 px-6 font-semibold text-gray-900">Last Updated</th>
                        <th class="text-center py-4 px-6 font-semibold text-gray-900">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <template x-for="keyword in keywords" :key="keyword.id">
                        <tr class="hover:bg-gray-50">
                            <td class="py-4 px-6">
                                <div class="flex items-center">
                                    <div>
                                        <div class="font-medium text-gray-900" x-text="keyword.keyword"></div>
                                        <div class="text-sm text-gray-500">
                                            <span x-text="keyword.country"></span>
                                            <template x-if="keyword.city">
                                                <span>, <span x-text="keyword.city"></span></span>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="py-4 px-6">
                                <a :href="keyword.url" target="_blank" class="text-primary-600 hover:text-primary-800 text-sm" x-text="keyword.url.substring(0, 40) + '...'"></a>
                            </td>
                            
                            <td class="py-4 px-6 text-center">
                                <span class="text-lg font-semibold" 
                                      :class="keyword.current_position <= 10 ? 'text-green-600' : keyword.current_position <= 20 ? 'text-yellow-600' : 'text-red-600'"
                                      x-text="keyword.current_position || 'N/A'">
                                </span>
                            </td>
                            
                            <td class="py-4 px-6 text-center">
                                <div class="flex items-center justify-center">
                                    <template x-if="getPositionChange(keyword) > 0">
                                        <div class="flex items-center text-green-600">
                                            <i class="fas fa-arrow-up mr-1"></i>
                                            <span x-text="'+' + getPositionChange(keyword)"></span>
                                        </div>
                                    </template>
                                    
                                    <template x-if="getPositionChange(keyword) < 0">
                                        <div class="flex items-center text-red-600">
                                            <i class="fas fa-arrow-down mr-1"></i>
                                            <span x-text="getPositionChange(keyword)"></span>
                                        </div>
                                    </template>
                                    
                                    <template x-if="getPositionChange(keyword) === 0">
                                        <span class="text-gray-500">-</span>
                                    </template>
                                </div>
                            </td>
                            
                            <td class="py-4 px-6 text-center">
                                <span class="text-gray-900" x-text="formatNumber(keyword.search_volume)"></span>
                            </td>
                            
                            <td class="py-4 px-6 text-center">
                                <span class="px-2 py-1 text-xs rounded-full"
                                      :class="keyword.difficulty <= 30 ? 'bg-green-100 text-green-800' : keyword.difficulty <= 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'"
                                      x-text="keyword.difficulty + '%'">
                                </span>
                            </td>
                            
                            <td class="py-4 px-6 text-center text-sm text-gray-500">
                                <span x-text="formatDate(keyword.tracked_date)"></span>
                            </td>
                            
                            <td class="py-4 px-6 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <button 
                                        @click="updateKeyword(keyword.id)"
                                        class="p-2 text-blue-600 hover:text-blue-800"
                                        title="Update ranking"
                                    >
                                        <i class="fas fa-sync"></i>
                                    </button>
                                    
                                    <button 
                                        @click="deleteKeyword(keyword.id)"
                                        class="p-2 text-red-600 hover:text-red-800"
                                        title="Delete keyword"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    
                    <tr x-show="keywords.length === 0">
                        <td colspan="8" class="py-8 text-center text-gray-500">
                            <i class="fas fa-search text-3xl mb-2"></i>
                            <p>No keywords tracked yet. Add your first keyword above!</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Analytics -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Stats</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="text-2xl font-bold text-primary-600" x-text="keywords.length"></div>
                <div class="text-sm text-gray-600">Keywords Tracked</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600" x-text="getAveragePosition()"></div>
                <div class="text-sm text-gray-600">Avg. Position</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600" x-text="getImprovedCount()"></div>
                <div class="text-sm text-gray-600">Improved</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-orange-600" x-text="formatNumber(getTotalSearchVolume())"></div>
                <div class="text-sm text-gray-600">Total Volume</div>
            </div>
        </div>
    </div>

    <!-- Keyword Suggestions -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Keyword Suggestions</h3>
        <div class="flex space-x-4 mb-4">
            <input 
                type="text" 
                x-model="seedKeyword"
                placeholder="Enter seed keyword"
                class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
            >
            <button 
                @click="getSuggestions()"
                :disabled="suggestionsLoading || !seedKeyword"
                class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50"
            >
                <span x-show="!suggestionsLoading">Get Suggestions</span>
                <span x-show="suggestionsLoading" x-cloak>Loading...</span>
            </button>
        </div>
        
        <div x-show="suggestions.length > 0" x-cloak class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <template x-for="suggestion in suggestions" :key="suggestion.keyword">
                <div class="p-4 border border-gray-200 rounded-lg hover:border-primary-300 transition-colors">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium text-gray-900" x-text="suggestion.keyword"></h4>
                        <button 
                            @click="addSuggestedKeyword(suggestion)"
                            class="text-primary-600 hover:text-primary-800 text-sm"
                        >
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>Volume: <span x-text="formatNumber(suggestion.search_volume)"></span></span>
                        <span>Difficulty: <span x-text="suggestion.difficulty + '%'"></span></span>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

@push('scripts')
<script>
function keywordTrackerData() {
    return {
        keywords: [],
        newKeyword: {
            keyword: '',
            url: '',
            country: 'US',
            city: ''
        },
        loading: false,
        bulkLoading: false,
        suggestionsLoading: false,
        seedKeyword: '',
        suggestions: [],
        showCitySelector: false,
        availableCities: [],
        isAuthenticated: {{ auth()->check() ? 'true' : 'false' }}, // Pass auth status to JavaScript
        
        // City data for different countries
        cityData: {
            'AE': [
                { value: 'dubai', label: 'Dubai' },
                { value: 'abu-dhabi', label: 'Abu Dhabi' },
                { value: 'sharjah', label: 'Sharjah' },
                { value: 'ajman', label: 'Ajman' },
                { value: 'ras-al-khaimah', label: 'Ras Al Khaimah' },
                { value: 'fujairah', label: 'Fujairah' },
                { value: 'umm-al-quwain', label: 'Umm Al Quwain' }
            ],
            'US': [
                { value: 'new-york', label: 'New York' },
                { value: 'los-angeles', label: 'Los Angeles' },
                { value: 'chicago', label: 'Chicago' },
                { value: 'houston', label: 'Houston' },
                { value: 'miami', label: 'Miami' },
                { value: 'san-francisco', label: 'San Francisco' },
                { value: 'seattle', label: 'Seattle' },
                { value: 'boston', label: 'Boston' },
                { value: 'atlanta', label: 'Atlanta' },
                { value: 'dallas', label: 'Dallas' }
            ],
            'UK': [
                { value: 'london', label: 'London' },
                { value: 'manchester', label: 'Manchester' },
                { value: 'birmingham', label: 'Birmingham' },
                { value: 'glasgow', label: 'Glasgow' },
                { value: 'edinburgh', label: 'Edinburgh' },
                { value: 'liverpool', label: 'Liverpool' },
                { value: 'bristol', label: 'Bristol' },
                { value: 'leeds', label: 'Leeds' }
            ],
            'IN': [
                { value: 'mumbai', label: 'Mumbai' },
                { value: 'delhi', label: 'Delhi' },
                { value: 'bangalore', label: 'Bangalore' },
                { value: 'hyderabad', label: 'Hyderabad' },
                { value: 'chennai', label: 'Chennai' },
                { value: 'kolkata', label: 'Kolkata' },
                { value: 'pune', label: 'Pune' },
                { value: 'ahmedabad', label: 'Ahmedabad' }
            ],
            'CA': [
                { value: 'toronto', label: 'Toronto' },
                { value: 'vancouver', label: 'Vancouver' },
                { value: 'montreal', label: 'Montreal' },
                { value: 'calgary', label: 'Calgary' },
                { value: 'ottawa', label: 'Ottawa' },
                { value: 'edmonton', label: 'Edmonton' }
            ],
            'AU': [
                { value: 'sydney', label: 'Sydney' },
                { value: 'melbourne', label: 'Melbourne' },
                { value: 'brisbane', label: 'Brisbane' },
                { value: 'perth', label: 'Perth' },
                { value: 'adelaide', label: 'Adelaide' },
                { value: 'canberra', label: 'Canberra' }
            ],
            'SA': [
                { value: 'riyadh', label: 'Riyadh' },
                { value: 'jeddah', label: 'Jeddah' },
                { value: 'mecca', label: 'Mecca' },
                { value: 'medina', label: 'Medina' },
                { value: 'dammam', label: 'Dammam' },
                { value: 'khobar', label: 'Khobar' }
            ]
        },
        
        async init() {
            console.log('Initializing keyword tracker...');
            console.log('Authentication Status (blade):', this.isAuthenticated);
            console.log('User authenticated via PHP:', {{ auth()->check() ? 'true' : 'false' }});
            
            // Refresh auth status to ensure we have the latest state
            await this.refreshAuthStatus();
            
            await this.loadKeywords();
            this.onCountryChange(); // Initialize city selector
            console.log('Keyword tracker initialized with', this.keywords.length, 'keywords');
        },
        
        onCountryChange() {
            if (this.cityData[this.newKeyword.country]) {
                this.availableCities = this.cityData[this.newKeyword.country];
                this.showCitySelector = true;
                this.newKeyword.city = this.availableCities[0]?.value || '';
            } else {
                this.showCitySelector = false;
                this.newKeyword.city = '';
            }
        },
        
        async loadKeywords() {
            try {
                console.log('Making API request to /api/keywords...');
                const response = await apiRequest('/api/keywords');
                console.log('API response received:', response);
                
                if (response.success) {
                    // Handle both simple array and paginated response
                    this.keywords = Array.isArray(response.data) ? response.data : response.data.data || [];
                    console.log('Keywords loaded:', this.keywords.length, 'items');
                    
                    // Check if we got demo data even though we're authenticated
                    if (this.isAuthenticated && this.keywords.length > 0) {
                        // Check if the first keyword has a demo-like ID or structure
                        const firstKeyword = this.keywords[0];
                        if (firstKeyword.id === 1 && firstKeyword.keyword === 'SEO tools') {
                            console.warn('Received demo data for authenticated user - this might be an auth issue');
                            showNotification('Warning: You may need to refresh the page after login', 'warning');
                        }
                    }
                    
                    // Sort keywords by most recent first
                    this.keywords.sort((a, b) => new Date(b.tracked_date) - new Date(a.tracked_date));
                } else {
                    console.error('API returned error:', response.message);
                    // Only show demo data for unauthenticated users
                    if (!this.isAuthenticated) {
                        console.log('Loading demo keywords for unauthenticated user');
                        this.loadDemoKeywords();
                    } else {
                        // For authenticated users, keep empty array if there's an error
                        console.log('Keeping empty array for authenticated user with error');
                        this.keywords = [];
                        showNotification('Unable to load keywords. Please try again.', 'error');
                    }
                }
            } catch (error) {
                console.error('Error loading keywords:', error);
                // Only show demo data for unauthenticated users
                if (!this.isAuthenticated) {
                    console.log('Loading demo keywords for unauthenticated user (catch block)');
                    this.loadDemoKeywords();
                } else {
                    // For authenticated users, keep empty array if there's an error
                    console.log('Keeping empty array for authenticated user with error (catch block)');
                    this.keywords = [];
                    showNotification('Unable to load keywords. Please try again.', 'error');
                }
            }
        },
        
        loadDemoKeywords() {
            this.keywords = [
                {
                    id: 1,
                    keyword: 'SEO tools',
                    url: 'https://example.com',
                    current_position: 15,
                    previous_position: 18,
                    search_volume: 12000,
                    difficulty: 65,
                    cpc: 2.45,
                    country: 'US',
                    city: 'new-york',
                    language: 'en',
                    tracked_date: new Date().toISOString()
                },
                {
                    id: 2,
                    keyword: 'keyword rank tracker',
                    url: 'https://example.com/keyword-tracker',
                    current_position: 8,
                    previous_position: 12,
                    search_volume: 3200,
                    difficulty: 45,
                    cpc: 3.80,
                    country: 'AE',
                    city: 'dubai',
                    language: 'en',
                    tracked_date: new Date().toISOString()
                }
            ];
        },
        
        async addKeyword() {
            if (!this.newKeyword.keyword || !this.newKeyword.url) return;
            
            this.loading = true;
            
            try {
                const requestData = {
                    keyword: this.newKeyword.keyword,
                    url: this.newKeyword.url,
                    country: this.newKeyword.country,
                    language: 'en'
                };
                
                // Add city if selected
                if (this.newKeyword.city) {
                    requestData.city = this.newKeyword.city;
                }
                
                console.log('Sending request data:', requestData);
                console.log('Authentication Status:', this.isAuthenticated);
                
                const response = await apiRequest('/api/keywords', {
                    body: requestData
                });
                
                console.log('API response:', response);
                
                if (response.success) {
                    // For authenticated users, reload all keywords to get fresh data
                    if (this.isAuthenticated) {
                        await this.loadKeywords();
                        showNotification(response.message || 'Keyword added successfully!', 'success');
                    } else {
                        // For demo mode, just add to the existing array
                        if (Array.isArray(response.data)) {
                            this.keywords = [...response.data, ...this.keywords];
                        } else {
                            this.keywords.unshift(response.data);
                        }
                        showNotification(response.message || 'Demo keyword added!', 'info');
                    }
                    this.resetNewKeywordForm();
                } else {
                    throw new Error(response.message || 'Failed to add keyword');
                }
            } catch (error) {
                console.error('Add keyword error:', error);
                
                if (this.isAuthenticated) {
                    // For authenticated users, show the actual error
                    showNotification('Error adding keyword: ' + error.message, 'error');
                } else {
                    // Only show demo mode for unauthenticated users
                    const demoKeyword = {
                        id: Date.now(),
                        keyword: this.newKeyword.keyword,
                        url: this.newKeyword.url,
                        current_position: Math.floor(Math.random() * 50) + 1,
                        previous_position: Math.floor(Math.random() * 50) + 1,
                        search_volume: Math.floor(Math.random() * 10000) + 1000,
                        difficulty: Math.floor(Math.random() * 100),
                        cpc: (Math.random() * 5 + 0.5).toFixed(2),
                        country: this.newKeyword.country,
                        city: this.newKeyword.city || '',
                        language: 'en',
                        tracked_date: new Date().toISOString()
                    };
                    
                    this.keywords.unshift(demoKeyword);
                    this.resetNewKeywordForm();
                    showNotification('Demo keyword added! Sign up to track real rankings.', 'info');
                }
            } finally {
                this.loading = false;
            }
        },
        
        resetNewKeywordForm() {
            this.newKeyword = { 
                keyword: '', 
                url: '', 
                country: 'US',
                city: ''
            };
            this.onCountryChange(); // Reset city selector
        },
        
        async updateKeyword(id) {
            try {
                if (!this.isAuthenticated) {
                    // Demo mode - simulate update
                    const index = this.keywords.findIndex(k => k.id === id);
                    if (index !== -1) {
                        this.keywords[index].previous_position = this.keywords[index].current_position;
                        this.keywords[index].current_position = Math.floor(Math.random() * 50) + 1;
                        this.keywords[index].tracked_date = new Date().toISOString();
                    }
                    showNotification('Demo keyword updated! Sign up for real tracking.', 'info');
                    return;
                }
                
                const response = await apiRequest(`/api/keywords/${id}`, {
                    method: 'PUT'
                });
                
                if (response.success) {
                    // Reload all keywords to get the latest data
                    await this.loadKeywords();
                    showNotification('Keyword updated successfully!', 'success');
                } else {
                    throw new Error(response.message || 'Failed to update keyword');
                }
            } catch (error) {
                console.error('Update keyword error:', error);
                showNotification('Error updating keyword: ' + error.message, 'error');
            }
        },
        
        async deleteKeyword(id) {
            if (!confirm('Are you sure you want to delete this keyword?')) return;
            
            try {
                if (!this.isAuthenticated) {
                    // Demo mode - just remove from array
                    this.keywords = this.keywords.filter(k => k.id !== id);
                    showNotification('Demo keyword removed! Sign up to manage real keywords.', 'info');
                    return;
                }
                
                const response = await apiRequest(`/api/keywords/${id}`, {
                    method: 'DELETE'
                });
                
                if (response.success) {
                    this.keywords = this.keywords.filter(k => k.id !== id);
                    showNotification('Keyword deleted successfully!', 'success');
                } else {
                    throw new Error(response.message || 'Failed to delete keyword');
                }
            } catch (error) {
                console.error('Delete keyword error:', error);
                showNotification('Error deleting keyword: ' + error.message, 'error');
            }
        },
        
        async bulkUpdate() {
            console.log('Starting bulk update...');
            console.log('User authenticated:', this.isAuthenticated);
            console.log('Keywords to update:', this.keywords.length);
            
            this.bulkLoading = true;
            
            try {
                if (!this.isAuthenticated) {
                    console.log('Running demo mode bulk update');
                    // Demo mode simulation
                    await new Promise(resolve => setTimeout(resolve, 2000)); // Simulate API delay
                    
                    // Update some demo data
                    this.keywords.forEach(keyword => {
                        keyword.previous_position = keyword.current_position;
                        keyword.current_position = Math.floor(Math.random() * 50) + 1;
                        keyword.tracked_date = new Date().toISOString();
                    });
                    
                    showNotification('Demo: Updated ' + this.keywords.length + ' keywords! Sign up for real tracking.', 'info');
                    return;
                }
                
                console.log('Making API request to /api/keywords/bulk-update');
                // For authenticated users, make the API call
                const response = await apiRequest('/api/keywords/bulk-update', {
                    method: 'POST'
                });
                
                console.log('Bulk update response:', response);
                
                if (response.success) {
                    console.log('Bulk update successful, reloading keywords...');
                    // Reload all keywords to get the fresh data
                    await this.loadKeywords();
                    showNotification(`Updated ${response.updated_count} keywords successfully!`, 'success');
                    
                    // Show errors if any
                    if (response.errors && response.errors.length > 0) {
                        console.warn('Bulk update errors:', response.errors);
                        showNotification('Some keywords had errors. Check console for details.', 'warning');
                    }
                } else {
                    throw new Error(response.message || 'Bulk update failed');
                }
            } catch (error) {
                console.error('Bulk update error:', error);
                showNotification('Error updating keywords: ' + error.message, 'error');
            } finally {
                this.bulkLoading = false;
                console.log('Bulk update finished');
            }
        },
        
        async getSuggestions() {
            if (!this.seedKeyword) return;
            
            this.suggestionsLoading = true;
            this.suggestions = [];
            
            try {
                const response = await apiRequest('/api/keywords/suggestions', {
                    method: 'POST',
                    body: { seed_keyword: this.seedKeyword }
                });
                
                if (response.success) {
                    this.suggestions = response.data;
                    if (this.isAuthenticated) {
                        showNotification('Real keyword suggestions loaded!', 'success');
                    } else {
                        showNotification('Demo suggestions generated! Sign up for real keyword data.', 'info');
                    }
                } else {
                    throw new Error(response.message || 'Failed to get suggestions');
                }
            } catch (error) {
                // Always provide sample suggestions, but with different messaging
                this.suggestions = [
                    {
                        keyword: this.seedKeyword + ' tools',
                        search_volume: Math.floor(Math.random() * 5000) + 1000,
                        difficulty: Math.floor(Math.random() * 100),
                        cpc: (Math.random() * 3 + 0.5).toFixed(2)
                    },
                    {
                        keyword: this.seedKeyword + ' software',
                        search_volume: Math.floor(Math.random() * 5000) + 1000,
                        difficulty: Math.floor(Math.random() * 100),
                        cpc: (Math.random() * 3 + 0.5).toFixed(2)
                    },
                    {
                        keyword: 'best ' + this.seedKeyword,
                        search_volume: Math.floor(Math.random() * 5000) + 1000,
                        difficulty: Math.floor(Math.random() * 100),
                        cpc: (Math.random() * 3 + 0.5).toFixed(2)
                    },
                    {
                        keyword: this.seedKeyword + ' free',
                        search_volume: Math.floor(Math.random() * 5000) + 1000,
                        difficulty: Math.floor(Math.random() * 100),
                        cpc: (Math.random() * 3 + 0.5).toFixed(2)
                    }
                ];
                
                if (this.isAuthenticated) {
                    showNotification('Error getting suggestions: ' + error.message + ' (showing sample data)', 'error');
                } else {
                    showNotification('Demo suggestions generated! Sign up for real keyword data.', 'info');
                }
            } finally {
                this.suggestionsLoading = false;
            }
        },
        
        addSuggestedKeyword(suggestion) {
            this.newKeyword = {
                keyword: suggestion.keyword,
                url: this.newKeyword.url || '',
                country: this.newKeyword.country || 'US',
                city: this.newKeyword.city || ''
            };
            
            // Ensure city selector is properly updated
            this.onCountryChange();
            
            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        
        exportData() {
            const csvContent = this.generateCSV();
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `keywords_${new Date().toISOString().split('T')[0]}.csv`;
            link.click();
            window.URL.revokeObjectURL(url);
        },
        
        generateCSV() {
            const headers = ['Keyword', 'URL', 'Position', 'Previous Position', 'Change', 'Search Volume', 'Difficulty', 'Country', 'Last Updated'];
            const rows = this.keywords.map(keyword => [
                keyword.keyword,
                keyword.url,
                keyword.current_position || 'N/A',
                keyword.previous_position || 'N/A',
                this.getPositionChange(keyword),
                keyword.search_volume || 0,
                keyword.difficulty || 0,
                keyword.country,
                this.formatDate(keyword.tracked_date)
            ]);
            
            return [headers, ...rows].map(row => row.map(field => `"${field}"`).join(',')).join('\n');
        },
        
        getPositionChange(keyword) {
            if (!keyword.previous_position || !keyword.current_position) return 0;
            return keyword.previous_position - keyword.current_position;
        },
        
        formatNumber(num) {
            if (!num) return '0';
            return new Intl.NumberFormat().format(num);
        },
        
        formatDate(date) {
            if (!date) return 'Never';
            return new Date(date).toLocaleDateString();
        },
        
        // Analytics helper functions
        getAveragePosition() {
            if (this.keywords.length === 0) return '0';
            const validPositions = this.keywords.filter(k => k.current_position).map(k => k.current_position);
            if (validPositions.length === 0) return '0';
            const avg = validPositions.reduce((a, b) => a + b, 0) / validPositions.length;
            return avg.toFixed(1);
        },
        
        getImprovedCount() {
            return this.keywords.filter(keyword => {
                const change = this.getPositionChange(keyword);
                return change > 0; // Positive change means improvement (lower position number)
            }).length;
        },
        
        getTotalSearchVolume() {
            return this.keywords.reduce((total, keyword) => {
                return total + (keyword.search_volume || 0);
            }, 0);
        },
        
        async refreshAuthStatus() {
            try {
                const response = await apiRequest('/api/user');
                if (response && response.id) {
                    this.isAuthenticated = true;
                    console.log('Authentication status refreshed: authenticated as', response.name);
                } else {
                    this.isAuthenticated = false;
                    console.log('Authentication status refreshed: not authenticated (no user data)');
                }
            } catch (error) {
                this.isAuthenticated = false;
                console.log('Authentication status refreshed: not authenticated (error:', error.message, ')');
            }
        }
    };
}
</script>
@endpush
@endsection
