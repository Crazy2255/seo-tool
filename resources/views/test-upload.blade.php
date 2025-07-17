<!DOCTYPE html>
<html>
<head>
    <title>Test Bulk Invite Upload</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>Test File Upload Validation</h1>
    
    <form id="testForm" enctype="multipart/form-data">
        <label for="excel_file">Select CSV/Excel file:</label>
        <input type="file" name="excel_file" id="excel_file" accept=".csv,.xlsx,.xls,.txt" required>
        <br><br>
        <button type="submit">Test Upload</button>
    </form>
    
    <div id="result" style="margin-top: 20px; padding: 10px; border: 1px solid #ccc;"></div>
    
    <script>
        document.getElementById('testForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            const fileInput = document.getElementById('excel_file');
            const file = fileInput.files[0];
            
            if (!file) {
                alert('Please select a file');
                return;
            }
            
            formData.append('excel_file', file);
            
            try {
                const response = await fetch('/test/bulk-invite', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                const data = await response.json();
                document.getElementById('result').innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
                
            } catch (error) {
                document.getElementById('result').innerHTML = 'Error: ' + error.message;
            }
        });
    </script>
</body>
</html>
