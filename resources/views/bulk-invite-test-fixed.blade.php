<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Invite Email Test - Fixed</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .status-good { color: #059669; background: #d1fae5; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .status-warning { color: #d97706; background: #fef3c7; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .status-error { color: #dc2626; background: #fee2e2; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .test-section { margin: 20px 0; padding: 20px; border: 1px solid #e5e7eb; border-radius: 8px; }
        pre { background: #f3f4f6; padding: 10px; overflow-x: auto; border-radius: 4px; font-size: 12px; }
        button { padding: 12px 24px; background: #3b82f6; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; }
        button:hover { background: #2563eb; }
        button:disabled { background: #9ca3af; cursor: not-allowed; }
        input[type="file"] { margin: 10px 0; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px; width: 100%; }
        .progress { display: none; color: #3b82f6; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Bulk Invite Email Test - Fixed Version</h1>
        
        <div class="test-section">
            <h3>📧 Current Email Configuration</h3>
            <div class="status-good">
                ✅ <strong>Mail Driver:</strong> {{ config('mail.default') }}<br>
                ✅ <strong>SMTP Host:</strong> {{ config('mail.mailers.smtp.host') }}<br>
                ✅ <strong>Mail Port:</strong> {{ config('mail.mailers.smtp.port') }}
            </div>
            <p><strong>Status:</strong> Email sending is properly configured with Mailtrap SMTP!</p>
        </div>

        <div class="test-section">
            <h3>🧪 Quick Email Test</h3>
            <p>Test if email sending works with a single email:</p>
            <button onclick="testSingleEmail()" id="single-email-btn">Send Test Email</button>
            <div id="single-email-result"></div>
        </div>

        <div class="test-section">
            <h3>📁 Bulk Invite Test</h3>
            <p>Upload a CSV file to test bulk email invitations:</p>
            
            <form id="bulk-form" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div>
                    <label for="file"><strong>Choose CSV/Excel file:</strong></label><br>
                    <input type="file" name="excel_file" accept=".csv,.xlsx,.xls,.txt" required>
                </div>
                <p style="font-size: 14px; color: #6b7280;">
                    📝 <strong>CSV Format Example:</strong><br>
                    <code>email,name<br>user1@example.com,John Doe<br>user2@example.com,Jane Smith</code>
                </p>
                <button type="submit" id="bulk-submit-btn">Process Bulk Invites</button>
                <div class="progress" id="bulk-progress">Processing... Please wait...</div>
            </form>
            <div id="bulk-result"></div>
        </div>

        <div class="test-section">
            <h3>📋 Sample CSV Download</h3>
            <p>Download a sample CSV file for testing:</p>
            <a href="/test-bulk-emails.csv" download style="color: #3b82f6; text-decoration: none;">
                📄 <strong>Download Sample CSV</strong>
            </a>
        </div>

        <div class="test-section">
            <h3>🔍 Recent Logs</h3>
            <button onclick="showLogs()" id="logs-btn">Show Recent Email Logs</button>
            <div id="logs-result"></div>
        </div>
    </div>

    <script>
        function testSingleEmail() {
            const btn = document.getElementById('single-email-btn');
            const result = document.getElementById('single-email-result');
            
            btn.disabled = true;
            btn.textContent = 'Sending...';
            result.innerHTML = '<div class="progress">Sending test email...</div>';
            
            fetch('/test/email', {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(text => {
                try {
                    const data = JSON.parse(text.replace(/^\\/, '')); // Remove leading backslash if present
                    if (data.success) {
                        result.innerHTML = `
                            <div class="status-good">
                                ✅ ${data.message}<br>
                                📧 Test Email: ${data.data.test_email}<br>
                                📮 Lead Magnet: ${data.data.lead_magnet}<br>
                                🔧 Mail Driver: ${data.data.mail_driver}
                            </div>
                        `;
                    } else {
                        result.innerHTML = `<div class="status-error">❌ ${data.message}</div>`;
                    }
                } catch (e) {
                    result.innerHTML = `
                        <div class="status-error">
                            ❌ JSON Parse Error: ${e.message}<br>
                            Raw Response: <pre>${text}</pre>
                        </div>
                    `;
                }
            })
            .catch(error => {
                result.innerHTML = `<div class="status-error">❌ Request Error: ${error.message}</div>`;
            })
            .finally(() => {
                btn.disabled = false;
                btn.textContent = 'Send Test Email';
            });
        }

        document.getElementById('bulk-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const result = document.getElementById('bulk-result');
            const btn = document.getElementById('bulk-submit-btn');
            const progress = document.getElementById('bulk-progress');
            
            btn.disabled = true;
            btn.textContent = 'Processing...';
            progress.style.display = 'block';
            result.innerHTML = '';
            
            fetch('/test/actual-bulk-invite', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(text => {
                try {
                    const data = JSON.parse(text.replace(/^\\/, '')); // Remove leading backslash if present
                    if (data.success) {
                        result.innerHTML = `
                            <div class="status-good">
                                ✅ ${data.message}<br>
                                📊 <strong>Results Summary:</strong><br>
                                • Lead Magnet: ${data.data.lead_magnet}<br>
                                • Emails Found: ${data.data.emails_found}<br>
                                • Leads Created: ${data.data.leads_created}<br>
                                • Emails Sent: ${data.data.emails_sent}<br>
                                • Mail Driver: ${data.data.mail_driver}
                            </div>
                            <pre>${JSON.stringify(data.data, null, 2)}</pre>
                        `;
                    } else {
                        result.innerHTML = `
                            <div class="status-error">
                                ❌ ${data.message}<br>
                                ${data.errors ? data.errors.join('<br>') : ''}
                            </div>
                        `;
                    }
                } catch (e) {
                    result.innerHTML = `
                        <div class="status-error">
                            ❌ JSON Parse Error: ${e.message}<br>
                            Raw Response: <pre>${text}</pre>
                        </div>
                    `;
                }
            })
            .catch(error => {
                result.innerHTML = `<div class="status-error">❌ Request Error: ${error.message}</div>`;
            })
            .finally(() => {
                btn.disabled = false;
                btn.textContent = 'Process Bulk Invites';
                progress.style.display = 'none';
            });
        });

        function showLogs() {
            const btn = document.getElementById('logs-btn');
            const result = document.getElementById('logs-result');
            
            result.innerHTML = `
                <div class="status-warning">
                    📝 <strong>Email Logs Location:</strong><br>
                    Since you're using Mailtrap, emails are captured in your Mailtrap inbox.<br>
                    <strong>Mailtrap Dashboard:</strong> <a href="https://mailtrap.io/inboxes" target="_blank">https://mailtrap.io/inboxes</a><br><br>
                    Laravel logs are saved to: <code>storage/logs/laravel.log</code>
                </div>
            `;
        }
    </script>
</body>
</html>
