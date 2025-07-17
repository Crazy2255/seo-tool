@extends('layouts.app')

@section('title', 'Lead Magnet Builder - SEO Audit Pro')
@section('page-title', 'Lead Magnet Builder')

@section('content')
<div class="space-y-6" x-data="leadMagnetBuilder()">
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-xl text-white p-6 lg:p-8">
            <div class="max-w-6xl mx-auto">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center space-x-4 mb-6 lg:mb-0">
                        <div class="w-12 h-12 lg:w-16 lg:h-16 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-magnet text-xl lg:text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl lg:text-3xl font-bold">Lead Magnet Builder</h1>
                            <p class="text-purple-100 text-sm lg:text-lg">Create irresistible lead magnets to grow your email list</p>
                        </div>
                    </div>
                </div>

                @if($isDemo)
                <div class="bg-amber-500/20 border border-amber-300/30 rounded-lg p-4 mb-6">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-info-circle text-amber-200"></i>
                        <p class="text-amber-100 text-sm lg:text-base">
                            <strong>Demo Mode:</strong> You're viewing sample data. 
                            <a href="{{ route('register') }}" class="underline hover:text-white">Sign up free</a> 
                            to create real lead magnets and capture leads.
                        </p>
                    </div>
                </div>
                @endif

                <!-- Statistics -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3 lg:p-4">
                        <div class="text-xl lg:text-2xl font-bold">{{ $totalMagnets }}</div>
                        <div class="text-xs lg:text-sm text-purple-200">Lead Magnets</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3 lg:p-4">
                        <div class="text-xl lg:text-2xl font-bold">{{ $totalLeads }}</div>
                        <div class="text-xs lg:text-sm text-purple-200">Total Leads</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3 lg:p-4">
                        <div class="text-xl lg:text-2xl font-bold">{{ $totalDownloads }}</div>
                        <div class="text-xs lg:text-sm text-purple-200">Downloads</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3 lg:p-4">
                        <div class="text-xl lg:text-2xl font-bold">{{ $avgConversionRate }}%</div>
                        <div class="text-xs lg:text-sm text-purple-200">Avg. Conversion</div>
                    </div>
                </div>
            </div>
        </div>

    <!-- Create New Lead Magnet -->
    <div class="bg-white rounded-xl shadow-lg p-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Create New Lead Magnet</h2>
                <p class="text-gray-600">Upload a file and create a landing page to capture leads</p>
            </div>
            <button @click="showCreateForm = !showCreateForm" 
                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                <span x-text="showCreateForm ? 'Cancel' : 'Create Lead Magnet'"></span>
            </button>
        </div>

        <!-- Create Form -->
        <div x-show="showCreateForm" x-collapse class="border-t pt-6">
            <form @submit.prevent="createLeadMagnet()" enctype="multipart/form-data">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Basic Information -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900">Basic Information</h3>
                        
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                            <input type="text" 
                                   id="title" 
                                   x-model="form.title"
                                   placeholder="e.g., Ultimate SEO Checklist"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   required>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea id="description" 
                                      x-model="form.description"
                                      rows="4"
                                      placeholder="Describe what this lead magnet offers and its value..."
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                      required></textarea>
                        </div>

                        <div>
                            <label for="file" class="block text-sm font-medium text-gray-700 mb-2">Upload File</label>
                            <input type="file" 
                                   id="file" 
                                   @change="handleFileSelect($event)"
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.zip"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   required>
                            <p class="text-sm text-gray-500 mt-1">Supported: PDF, DOC, DOCX, XLS, XLSX, ZIP (Max: 10MB)</p>
                        </div>
                    </div>

                    <!-- Form Customization -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900">Form Customization</h3>
                        
                        <div>
                            <label for="form_title" class="block text-sm font-medium text-gray-700 mb-2">Form Title</label>
                            <input type="text" 
                                   id="form_title" 
                                   x-model="form.form_title"
                                   placeholder="Get Your Free Download"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>

                        <div>
                            <label for="form_description" class="block text-sm font-medium text-gray-700 mb-2">Form Description</label>
                            <textarea id="form_description" 
                                      x-model="form.form_description"
                                      rows="3"
                                      placeholder="Enter your details below to get instant access..."
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"></textarea>
                        </div>

                        <div>
                            <label for="form_button_text" class="block text-sm font-medium text-gray-700 mb-2">Button Text</label>
                            <input type="text" 
                                   id="form_button_text" 
                                   x-model="form.form_button_text"
                                   placeholder="Download Now"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" 
                            :disabled="loading"
                            class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        <span x-show="!loading">Create Lead Magnet</span>
                        <span x-show="loading" class="flex items-center">
                            <div class="loading-spinner mr-2"></div>
                            Creating...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Existing Lead Magnets -->
    <div class="bg-white rounded-xl shadow-lg p-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Your Lead Magnets</h2>
            @if(!$isDemo && $totalLeads > 0)
            <a href="{{ route('lead-magnets.export-all') }}" 
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-file-csv mr-2"></i>Export All Leads
            </a>
            @endif
        </div>

        @if($leadMagnets->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($leadMagnets as $magnet)
            <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $magnet->title }}</h3>
                        <p class="text-sm text-gray-600 mb-3">{{ Str::limit($magnet->description, 100) }}</p>
                        
                        <!-- Stats -->
                        <div class="grid grid-cols-3 gap-2 text-center mb-4">
                            <div class="bg-blue-50 rounded p-2">
                                <div class="text-lg font-semibold text-blue-600">{{ $magnet->view_count }}</div>
                                <div class="text-xs text-blue-500">Views</div>
                            </div>
                            <div class="bg-green-50 rounded p-2">
                                <div class="text-lg font-semibold text-green-600">{{ $magnet->leads->count() }}</div>
                                <div class="text-xs text-green-500">Leads</div>
                            </div>
                            <div class="bg-purple-50 rounded p-2">
                                <div class="text-lg font-semibold text-purple-600">{{ $magnet->conversion_rate }}%</div>
                                <div class="text-xs text-purple-500">Conv.</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="ml-4">
                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $magnet->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $magnet->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>

                <!-- File Info -->
                <div class="bg-gray-50 rounded p-3 mb-4">
                    <div class="flex items-center space-x-2 text-sm text-gray-600">
                        <i class="fas fa-file"></i>
                        <span>{{ $magnet->file_name }}</span>
                        <span class="text-gray-400">•</span>
                        <span>{{ $magnet->formatted_file_size }}</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between">
                    <div class="flex space-x-2">
                        <a href="{{ route('lead-magnets.show', $magnet) }}" 
                           class="text-purple-600 hover:text-purple-700 text-sm font-medium">
                            <i class="fas fa-chart-bar mr-1"></i>Analytics
                        </a>
                        <a href="{{ $magnet->landing_url }}" 
                           target="_blank"
                           class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                            <i class="fas fa-external-link-alt mr-1"></i>View Page
                        </a>
                        <button @click="openBulkInviteModal({{ $magnet->id }}, '{{ $magnet->title }}')"
                                class="text-green-600 hover:text-green-700 text-sm font-medium">
                            <i class="fas fa-envelope mr-1"></i>Invite
                        </button>
                    </div>
                    
                    <div class="flex space-x-1">
                        <button @click="editMagnet({{ $magnet }})" 
                                class="p-2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button @click="deleteMagnet({{ $magnet->id }})" 
                                class="p-2 text-red-400 hover:text-red-600">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12">
            <div class="w-24 h-24 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-magnet text-3xl text-purple-600"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No lead magnets yet</h3>
            <p class="text-gray-600 mb-4">Create your first lead magnet to start capturing leads</p>
            <button @click="showCreateForm = true" 
                    class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>Create Your First Lead Magnet
            </button>
        </div>
        @endif
    </div>

    <!-- Bulk Invite Modal -->
    <div x-show="showBulkInviteModal" 
         x-cloak
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
         @click.self="closeBulkInviteModal()">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Send Bulk Invites</h3>
                    <button @click="closeBulkInviteModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <p class="text-sm text-gray-600 mb-4">
                    Upload a CSV or Excel file containing email addresses to send invitation emails for 
                    "<span x-text="selectedMagnetTitle"></span>".
                </p>
                
                <form @submit.prevent="sendBulkInvites()" class="space-y-4">
                    <div>
                        <label for="excel_file" class="block text-sm font-medium text-gray-700 mb-2">
                            CSV or Excel File with Email Addresses
                        </label>
                        <input type="file" 
                               id="excel_file" 
                               @change="handleExcelFileSelect($event)"
                               accept=".csv,.xlsx,.xls,.txt"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               required>
                        <p class="text-xs text-gray-500 mt-1">
                            Supported formats: CSV (.csv), Excel (.xlsx, .xls), or Text (.txt) files. Max size: 5MB.
                        </p>
                    </div>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-2"></i>
                            <div class="text-sm text-blue-700">
                                <p class="font-medium mb-1">How it works:</p>
                                <ul class="text-xs space-y-1 list-disc list-inside ml-2">
                                    <li>Upload a CSV or Excel file with email addresses in any column</li>
                                    <li>We'll send an invitation email to each valid address</li>
                                    <li>Recipients can click the link to access your lead magnet form</li>
                                    <li>All submissions will be tracked in your analytics</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" 
                                @click="closeBulkInviteModal()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">
                            Cancel
                        </button>
                        <button type="submit" 
                                :disabled="bulkInviteLoading || !bulkInviteFile"
                                class="px-4 py-2 text-sm font-medium text-white bg-purple-600 border border-transparent rounded-md hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!bulkInviteLoading">Send Invites</span>
                            <span x-show="bulkInviteLoading" class="flex items-center">
                                <div class="loading-spinner mr-2"></div>
                                Sending...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.loading-spinner {
    border: 2px solid #f3f3f3;
    border-top: 2px solid #9333ea;
    border-radius: 50%;
    width: 16px;
    height: 16px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

[x-cloak] { display: none !important; }
</style>

<script>
function leadMagnetBuilder() {
    return {
        showCreateForm: false,
        loading: false,
        showBulkInviteModal: false,
        bulkInviteLoading: false,
        selectedMagnetId: null,
        selectedMagnetTitle: '',
        bulkInviteFile: null,
        form: {
            title: '',
            description: '',
            file: null,
            form_title: '',
            form_description: '',
            form_button_text: ''
        },

        handleFileSelect(event) {
            this.form.file = event.target.files[0];
        },

        async createLeadMagnet() {
            if (!this.form.title || !this.form.description || !this.form.file) {
                this.showNotification('Please fill in all required fields', 'error');
                return;
            }

            this.loading = true;

            try {
                const formData = new FormData();
                formData.append('title', this.form.title);
                formData.append('description', this.form.description);
                formData.append('file', this.form.file);
                formData.append('form_title', this.form.form_title);
                formData.append('form_description', this.form.form_description);
                formData.append('form_button_text', this.form.form_button_text);

                const response = await fetch('/api/lead-magnets', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Server returned non-JSON response. Please check if you are logged in.');
                }

                const data = await response.json();

                if (response.ok && data.success) {
                    this.showNotification(data.message || 'Lead magnet created successfully!', 'success');
                    this.resetForm();
                    this.showCreateForm = false;
                    // Reload page to show new magnet
                    setTimeout(() => location.reload(), 1000);
                } else {
                    this.showNotification(data.message || 'Creation failed', 'error');
                }
            } catch (error) {
                console.error('Creation error:', error);
                this.showNotification('Creation failed: ' + error.message, 'error');
            } finally {
                this.loading = false;
            }
        },

        async deleteMagnet(id) {
            if (!confirm('Are you sure you want to delete this lead magnet? This action cannot be undone.')) {
                return;
            }

            try {
                const response = await fetch(`/api/lead-magnets/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Server returned non-JSON response. Please check if you are logged in.');
                }

                const data = await response.json();

                if (response.ok && data.success) {
                    this.showNotification(data.message || 'Lead magnet deleted successfully!', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    this.showNotification(data.message || 'Deletion failed', 'error');
                }
            } catch (error) {
                console.error('Deletion error:', error);
                this.showNotification('Deletion failed: ' + error.message, 'error');
            }
        },

        editMagnet(magnet) {
            // For now, just show an alert. You can implement an edit modal later
            alert('Edit functionality coming soon! For now, you can create a new lead magnet.');
        },

        openBulkInviteModal(magnetId, magnetTitle) {
            this.selectedMagnetId = magnetId;
            this.selectedMagnetTitle = magnetTitle;
            this.showBulkInviteModal = true;
            this.bulkInviteFile = null;
            // Reset file input
            const fileInput = document.getElementById('excel_file');
            if (fileInput) fileInput.value = '';
        },

        closeBulkInviteModal() {
            this.showBulkInviteModal = false;
            this.selectedMagnetId = null;
            this.selectedMagnetTitle = '';
            this.bulkInviteFile = null;
            this.bulkInviteLoading = false;
        },

        handleExcelFileSelect(event) {
            this.bulkInviteFile = event.target.files[0];
        },

        async sendBulkInvites() {
            if (!this.bulkInviteFile || !this.selectedMagnetId) {
                this.showNotification('Please select a file and try again', 'error');
                return;
            }

            // Client-side file validation
            const file = this.bulkInviteFile;
            const allowedExtensions = ['csv', 'xlsx', 'xls', 'txt'];
            const fileExtension = file.name.split('.').pop().toLowerCase();
            
            if (!allowedExtensions.includes(fileExtension)) {
                this.showNotification('Please upload a CSV, Excel, or Text file only', 'error');
                return;
            }
            
            if (file.size > 5 * 1024 * 1024) { // 5MB
                this.showNotification('File size must be less than 5MB', 'error');
                return;
            }

            this.bulkInviteLoading = true;

            try {
                const formData = new FormData();
                formData.append('excel_file', this.bulkInviteFile);

                const response = await fetch(`/lead-magnets/${this.selectedMagnetId}/bulk-invite`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                // Debug: log response details
                console.log('Response status:', response.status);
                console.log('Response headers:', Object.fromEntries(response.headers.entries()));

                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    // Get response text for debugging
                    const responseText = await response.text();
                    console.error('Non-JSON response received:', responseText);
                    throw new Error('Server returned non-JSON response. Please check server logs and ensure you are logged in.');
                }

                // Get the response text first for debugging
                const responseText = await response.text();
                console.log('Raw response:', responseText);

                // Parse JSON
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (parseError) {
                    console.error('JSON parse error:', parseError);
                    console.error('Response that failed to parse:', responseText);
                    throw new Error(`Invalid JSON response: ${parseError.message}. Response: ${responseText.substring(0, 200)}...`);
                }

                if (response.ok && data.success) {
                    let message = data.message;
                    if (data.data) {
                        const details = data.data;
                        message += `\n\nSummary:\n• Emails sent: ${details.emails_sent}\n• Failed: ${details.failed_emails}\n• Duplicates: ${details.duplicate_emails}\n• Invalid rows: ${details.invalid_rows}`;
                        
                        if (details.details) {
                            if (details.details.duplicates && details.details.duplicates.length > 0) {
                                message += `\n\nSome duplicate emails (already subscribed): ${details.details.duplicates.slice(0, 3).join(', ')}${details.details.duplicates.length > 3 ? '...' : ''}`;
                            }
                            if (details.details.failed && details.details.failed.length > 0) {
                                message += `\n\nSome failed emails: ${details.details.failed.slice(0, 3).join(', ')}${details.details.failed.length > 3 ? '...' : ''}`;
                            }
                        }
                    }
                    
                    this.showNotification(message, 'success');
                    this.closeBulkInviteModal();
                } else {
                    let errorMessage = data.message || 'Bulk invite failed';
                    
                    // Show validation errors if present
                    if (data.errors && Array.isArray(data.errors)) {
                        errorMessage += '\n\nValidation errors:\n• ' + data.errors.join('\n• ');
                    } else if (data.details) {
                        errorMessage += `\n\nDetails:\n• Invalid rows: ${data.details.total_invalid || 0}`;
                        if (data.details.invalid_emails && data.details.invalid_emails.length > 0) {
                            errorMessage += `\n• Some invalid rows: ${data.details.invalid_emails.slice(0, 3).join(', ')}${data.details.invalid_emails.length > 3 ? '...' : ''}`;
                        }
                    }
                    this.showNotification(errorMessage, 'error');
                }
            } catch (error) {
                console.error('Bulk invite error:', error);
                this.showNotification('Bulk invite failed: ' + error.message, 'error');
            } finally {
                this.bulkInviteLoading = false;
            }
        },

        resetForm() {
            this.form = {
                title: '',
                description: '',
                file: null,
                form_title: '',
                form_description: '',
                form_button_text: ''
            };
            document.getElementById('file').value = '';
        },

        showNotification(message, type) {
            // Use the global notification system from app.blade.php
            if (typeof showNotification === 'function') {
                showNotification(message, type);
            } else {
                // Fallback to alert if global function is not available
                alert(message);
            }
        }
    }
}
</script>
@endsection
