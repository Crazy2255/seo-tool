<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Working Bulk Invite Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { color: #dc3545; background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { color: #0c5460; background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 10px 0; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
        form { margin: 20px 0; }
        input[type="file"] { margin: 10px 0; padding: 10px; border: 2px dashed #ccc; }
        button { padding: 12px 30px; background: #007cba; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        button:hover { background: #005a87; }
        .test-result { margin: 20px 0; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>✅ Email Configuration Working!</h1>
        
        <div class="success">
            <strong>Great news!</strong> Your SMTP configuration is working correctly with Mailtrap.
        </div>
        
        <div class="info">
            <strong>Email Configuration Status:</strong><br>
            📧 Mail Driver: SMTP<br>
            🖥️ Mail Host: sandbox.smtp.mailtrap.io<br>
            ✅ Test Email: Successfully sent to rramees60@gmail.com<br>
            📋 Lead Magnet: "Seo Checklist" found
        </div>
        
        <h2>🚀 Test Bulk Invite Feature</h2>
        
        <div class="test-result">
            <h3>Quick Email Test</h3>
            <button onclick="testSingleEmail()">Send Test Email</button>
            <div id="email-result"></div>
        </div>
        
        <div class="test-result">
            <h3>Bulk Invite CSV Upload</h3>
            <form id="bulk-form" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div>
                    <label for="file"><strong>Upload your CSV file:</strong></label><br>
                    <input type="file" name="excel_file" accept=".csv,.xlsx,.xls,.txt" required>
                </div>
                <p style="color: #666; font-size: 14px;">
                    📄 Your CSV should contain email addresses. Download <a href="/test-bulk-emails.csv">sample CSV</a> for testing.
                </p>
                <button type="submit">🚀 Send Bulk Invites</button>
            </form>
            <div id="bulk-result"></div>
        </div>
        
        <div class="info">
            <strong>How to Check Your Emails:</strong><br>
            1. Log into your <a href="https://mailtrap.io" target="_blank">Mailtrap account</a><br>
            2. Go to your inbox<br>
            3. You'll see all sent emails there (they won't be delivered to real email addresses)<br>
            4. This is perfect for testing!
        </div>
    </div>

    <script>
        function testSingleEmail() {
            const resultDiv = document.getElementById('email-result');
            resultDiv.innerHTML = '<p style="color: #007cba;">Sending test email...</p>';
            
            fetch('/test/email')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        resultDiv.innerHTML = `
                            <div class="success">
                                <strong>✅ ${data.message}</strong><br>
                                📧 Sent to: ${data.data.test_email}<br>
                                📋 Lead Magnet: ${data.data.lead_magnet}
                            </div>
                        `;
                    } else {
                        resultDiv.innerHTML = `<div class="error">❌ ${data.message}</div>`;
                    }
                })
                .catch(error => {
                    resultDiv.innerHTML = `<div class="error">❌ Error: ${error.message}</div>`;
                });
        }

        document.getElementById('bulk-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const resultDiv = document.getElementById('bulk-result');
            
            resultDiv.innerHTML = '<p style="color: #007cba;">🔄 Processing bulk invites...</p>';
            
            fetch('/test/actual-bulk-invite', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultDiv.innerHTML = `
                        <div class="success">
                            <strong>✅ ${data.message}</strong>
                            <pre>${JSON.stringify(data.data, null, 2)}</pre>
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="error">
                            <strong>❌ ${data.message}</strong>
                            ${data.errors ? '<pre>' + JSON.stringify(data.errors, null, 2) + '</pre>' : ''}
                        </div>
                    `;
                }
            })
            .catch(error => {
                resultDiv.innerHTML = `<div class="error">❌ Request failed: ${error.message}</div>`;
            });
        });
    </script>
</body>
</html>
