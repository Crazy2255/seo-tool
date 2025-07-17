<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JSON Response Debug</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test { margin: 20px 0; padding: 10px; border: 1px solid #ddd; }
        .error { color: red; }
        .success { color: green; }
        pre { background: #f5f5f5; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>JSON Response Debug</h1>
    
    <div class="test">
        <h3>Test 1: Clean JSON Response</h3>
        <button onclick="testCleanJson()">Test Clean JSON</button>
        <div id="clean-result"></div>
    </div>
    
    <div class="test">
        <h3>Test 2: Dirty JSON Response (with extra output)</h3>
        <button onclick="testDirtyJson()">Test Dirty JSON</button>
        <div id="dirty-result"></div>
    </div>
    
    <div class="test">
        <h3>Test 2b: Raw JSON Response (direct echo)</h3>
        <button onclick="testRawJson()">Test Raw JSON</button>
        <div id="raw-result"></div>
    </div>
    
    <div class="test">
        <h3>Test 3: Bulk Invite Endpoint</h3>
        <form id="bulk-form" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="file" name="excel_file" accept=".csv,.xlsx,.xls,.txt" required>
            <button type="submit">Test Bulk Invite</button>
        </form>
        <div id="bulk-result"></div>
    </div>
    
    <div class="test">
        <h3>Test 4: Bulk Invite Simulation (Debug)</h3>
        <form id="simulation-form" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="file" name="excel_file" accept=".csv,.xlsx,.xls,.txt">
            <button type="submit">Test Simulation</button>
        </form>
        <div id="simulation-result"></div>
    </div>

    <script>
        function testCleanJson() {
            fetch('/debug/clean-json')
                .then(response => {
                    document.getElementById('clean-result').innerHTML = 
                        `<p class="success">Status: ${response.status}</p>
                         <p>Response Text: <pre>${JSON.stringify(response, null, 2)}</pre></p>`;
                    return response.text();
                })
                .then(text => {
                    document.getElementById('clean-result').innerHTML += 
                        `<p>Raw Response: <pre>${text}</pre></p>`;
                    try {
                        const json = JSON.parse(text);
                        document.getElementById('clean-result').innerHTML += 
                            `<p class="success">JSON Parsed Successfully: <pre>${JSON.stringify(json, null, 2)}</pre></p>`;
                    } catch (e) {
                        document.getElementById('clean-result').innerHTML += 
                            `<p class="error">JSON Parse Error: ${e.message}</p>`;
                    }
                })
                .catch(error => {
                    document.getElementById('clean-result').innerHTML = 
                        `<p class="error">Request Error: ${error.message}</p>`;
                });
        }

        function testDirtyJson() {
            fetch('/debug/dirty-json')
                .then(response => {
                    document.getElementById('dirty-result').innerHTML = 
                        `<p class="success">Status: ${response.status}</p>`;
                    return response.text();
                })
                .then(text => {
                    document.getElementById('dirty-result').innerHTML += 
                        `<p>Raw Response: <pre>${text}</pre></p>`;
                    try {
                        const json = JSON.parse(text);
                        document.getElementById('dirty-result').innerHTML += 
                            `<p class="success">JSON Parsed Successfully: <pre>${JSON.stringify(json, null, 2)}</pre></p>`;
                    } catch (e) {
                        document.getElementById('dirty-result').innerHTML += 
                            `<p class="error">JSON Parse Error: ${e.message}</p>`;
                    }
                })
                .catch(error => {
                    document.getElementById('dirty-result').innerHTML = 
                        `<p class="error">Request Error: ${error.message}</p>`;
                });
        }

        function testRawJson() {
            fetch('/debug/raw-json')
                .then(response => {
                    document.getElementById('raw-result').innerHTML = 
                        `<p class="success">Status: ${response.status}</p>`;
                    return response.text();
                })
                .then(text => {
                    document.getElementById('raw-result').innerHTML += 
                        `<p>Raw Response: <pre>${text}</pre></p>`;
                    try {
                        const json = JSON.parse(text);
                        document.getElementById('raw-result').innerHTML += 
                            `<p class="success">JSON Parsed Successfully: <pre>${JSON.stringify(json, null, 2)}</pre></p>`;
                    } catch (e) {
                        document.getElementById('raw-result').innerHTML += 
                            `<p class="error">JSON Parse Error: ${e.message}</p>`;
                    }
                })
                .catch(error => {
                    document.getElementById('raw-result').innerHTML = 
                        `<p class="error">Request Error: ${error.message}</p>`;
                });
        }

        document.getElementById('bulk-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('/tools/lead-magnets/1/bulk-invite', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                document.getElementById('bulk-result').innerHTML = 
                    `<p class="success">Status: ${response.status}</p>`;
                return response.text();
            })
            .then(text => {
                document.getElementById('bulk-result').innerHTML += 
                    `<p>Raw Response: <pre>${text}</pre></p>`;
                try {
                    const json = JSON.parse(text);
                    document.getElementById('bulk-result').innerHTML += 
                        `<p class="success">JSON Parsed Successfully: <pre>${JSON.stringify(json, null, 2)}</pre></p>`;
                } catch (e) {
                    document.getElementById('bulk-result').innerHTML += 
                        `<p class="error">JSON Parse Error: ${e.message}</p>`;
                }
            })
            .catch(error => {
                document.getElementById('bulk-result').innerHTML = 
                    `<p class="error">Request Error: ${error.message}</p>`;
            });
        });
        
        document.getElementById('simulation-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('/debug/bulk-simulation', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                document.getElementById('simulation-result').innerHTML = 
                    `<p class="success">Status: ${response.status}</p>`;
                return response.text();
            })
            .then(text => {
                document.getElementById('simulation-result').innerHTML += 
                    `<p>Raw Response: <pre>${text}</pre></p>`;
                try {
                    const json = JSON.parse(text);
                    document.getElementById('simulation-result').innerHTML += 
                        `<p class="success">JSON Parsed Successfully: <pre>${JSON.stringify(json, null, 2)}</pre></p>`;
                } catch (e) {
                    document.getElementById('simulation-result').innerHTML += 
                        `<p class="error">JSON Parse Error: ${e.message}</p>`;
                }
            })
            .catch(error => {
                document.getElementById('simulation-result').innerHTML = 
                    `<p class="error">Request Error: ${error.message}</p>`;
            });
        });
    </script>
</body>
</html>
