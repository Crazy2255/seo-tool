<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Free Download</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .download-button {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
        }
        .download-button:hover {
            background: #5a6fd8;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎉 Your Free Download is Ready!</h1>
        <p>Thank you for signing up, {{ $lead->name }}!</p>
    </div>

    <div class="content">
        <h2>{{ $leadMagnet->title }}</h2>
        <p>{{ $leadMagnet->description }}</p>

        <p>Click the button below to download your free resource:</p>

        <div style="text-align: center;">
            <a href="{{ $downloadUrl }}" class="download-button">
                📥 Download Now
            </a>
        </div>

        <p><strong>Direct Link:</strong><br>
        <a href="{{ $downloadUrl }}">{{ $downloadUrl }}</a></p>

        <div style="background: #fff; padding: 20px; border-radius: 5px; margin: 20px 0;">
            <h3>📋 Resource Details:</h3>
            <ul>
                <li><strong>Title:</strong> {{ $leadMagnet->title }}</li>
                <li><strong>File Type:</strong> {{ strtoupper(pathinfo($leadMagnet->file_name, PATHINFO_EXTENSION)) }}</li>
                <li><strong>File Size:</strong> {{ $leadMagnet->formatted_file_size }}</li>
            </ul>
        </div>

        <p><strong>Note:</strong> This download link is valid for 7 days. Please save the file to your device.</p>
    </div>

    <div class="footer">
        <p>This email was sent to {{ $lead->email }}</p>
        <p>If you have any questions, please contact us.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>
</html>
