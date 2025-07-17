<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $leadMagnet->title }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 40px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
        }
        .title {
            font-size: 28px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 20px;
            line-height: 1.3;
        }
        .description {
            font-size: 16px;
            color: #6b7280;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            margin: 20px 0;
            transition: transform 0.2s;
        }
        .cta-button:hover {
            transform: translateY(-2px);
        }
        .features {
            background-color: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            margin: 30px 0;
        }
        .feature {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .feature:last-child {
            margin-bottom: 0;
        }
        .feature-icon {
            width: 20px;
            height: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            margin-right: 12px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .feature-icon::before {
            content: "✓";
            color: white;
            font-size: 12px;
            font-weight: bold;
        }
        .feature-text {
            color: #374151;
            font-size: 14px;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #9ca3af;
            font-size: 12px;
        }
        .unsubscribe {
            color: #9ca3af;
            text-decoration: none;
            font-size: 12px;
        }
        .preview-text {
            display: none;
            font-size: 1px;
            color: #ffffff;
            line-height: 1px;
            max-height: 0px;
            max-width: 0px;
            opacity: 0;
            overflow: hidden;
        }
        @media only screen and (max-width: 600px) {
            .container {
                padding: 20px 15px;
            }
            .title {
                font-size: 24px;
            }
            .cta-button {
                display: block;
                width: 100%;
                box-sizing: border-box;
            }
        }
    </style>
</head>
<body>
    <div class="preview-text">
        Get your free {{ $leadMagnet->title }} - exclusive resource for better SEO results
    </div>
    
    <div class="container">
        <div class="header">
            <div class="logo">SEO Tool</div>
        </div>
        
        <h1 class="title">{{ $leadMagnet->title }}</h1>
        
        <div class="description">
            <p>Hi there!</p>
            
            <p>We're excited to share an exclusive resource that can help you improve your SEO strategy and get better results for your website.</p>
            
            <p>{{ $leadMagnet->description }}</p>
        </div>
        
        <div style="text-align: center;">
            <a href="{{ $landingUrl }}" class="cta-button">
                Get Your Free Download
            </a>
        </div>
        
        <div class="features">
            <div class="feature">
                <div class="feature-icon"></div>
                <div class="feature-text">Completely free - no hidden costs</div>
            </div>
            <div class="feature">
                <div class="feature-icon"></div>
                <div class="feature-text">Instant download after signup</div>
            </div>
            <div class="feature">
                <div class="feature-icon"></div>
                <div class="feature-text">Expert-crafted content</div>
            </div>
            <div class="feature">
                <div class="feature-icon"></div>
                <div class="feature-text">Actionable tips you can implement today</div>
            </div>
        </div>
        
        <p style="color: #6b7280; font-size: 14px; text-align: center; margin-top: 30px;">
            Simply click the button above to access your free download. You'll just need to provide your name and email address to get instant access.
        </p>
        
        <div class="footer">
            <p>This email was sent to {{ $recipientEmail }}.</p>
            <p>If you no longer wish to receive these emails, you can unsubscribe at any time.</p>
        </div>
    </div>
</body>
</html>
