<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Bulk Invite</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test { margin: 20px 0; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .error { color: red; }
        .success { color: green; }
        .info { color: blue; }
        pre { background: #f5f5f5; padding: 10px; overflow-x: auto; border-radius: 4px; }
        form { margin: 10px 0; }
        input[type="file"] { margin: 10px 0; }
        button { padding: 10px 20px; background: #007cba; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #005a87; }
    </style>
</head>
<body>
    <h1>Bulk Invite Email Test</h1>
    
    <div class="test">
        <h3>Current Mail Configuration</h3>
        <p><strong>Mail Driver:</strong> {{ config('mail.default') }}</p>
        @if(config('mail.default') === 'log')
            <p class="info">📝 Emails are being logged to <code>storage/logs/laravel.log</code></p>
            <p class="info">To send actual emails, update your <code>.env</code> file with SMTP settings.</p>
        @else
            <p class="success">✅ Email delivery is configured</p>
        @endif
    </div>
    
    <div class="test">
        <h3>Upload CSV File for Bulk Invite</h3>
        <form id="bulk-form" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div>
                <label for="file">Choose CSV/Excel file:</label><br>
                <input type="file" name="excel_file" accept=".csv,.xlsx,.xls,.txt" required>
            </div>
            <p style="font-size: 14px; color: #666;">
                Your CSV should have emails in any column. Example:<br>
                <code>email<br>test1@example.com<br>test2@example.com</code>
            </p>
            <button type="submit">Test Bulk Invite</button>
        </form>
        <div id="result"></div>
    </div>
    
    <div class="test">
        <h3>Quick Test with Sample CSV</h3>
        <p>Download this sample CSV file and upload it above:</p>
        <a href="/test-bulk-emails.csv" download>📄 Download Sample CSV</a>
    </div>

    <script>
        document.getElementById('bulk-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const resultDiv = document.getElementById('result');
            
            resultDiv.innerHTML = '<p class="info">Processing...</p>';
            
            fetch('/test/actual-bulk-invite', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                return response.text().then(text => {
                    try {
                        const json = JSON.parse(text);
                        return { json, status: response.status };
                    } catch (e) {
                        return { error: `JSON Parse Error: ${e.message}`, rawResponse: text, status: response.status };
                    }
                });
            })
            .then(result => {
                if (result.json) {
                    if (result.json.success) {
                        resultDiv.innerHTML = `
                            <p class="success">✅ ${result.json.message}</p>
                            <pre>${JSON.stringify(result.json.data, null, 2)}</pre>
                        `;
                    } else {
                        resultDiv.innerHTML = `
                            <p class="error">❌ ${result.json.message}</p>
                            ${result.json.errors ? '<pre>' + JSON.stringify(result.json.errors, null, 2) + '</pre>' : ''}
                        `;
                    }
                } else {
                    resultDiv.innerHTML = `
                        <p class="error">❌ ${result.error}</p>
                        <p>Status: ${result.status}</p>
                        <p>Raw Response:</p>
                        <pre>${result.rawResponse}</pre>
                    `;
                }
            })
            .catch(error => {
                resultDiv.innerHTML = `<p class="error">❌ Request failed: ${error.message}</p>`;
            });
        });
    </script>
</body>
</html>
