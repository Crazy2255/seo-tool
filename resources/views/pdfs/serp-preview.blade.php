<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>SERP Preview Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #4F46E5;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #4F46E5;
            margin: 0;
            font-size: 28px;
        }
        .header p {
            color: #666;
            margin: 5px 0 0 0;
        }
        .section {
            margin-bottom: 30px;
        }
        .section h2 {
            color: #374151;
            border-bottom: 1px solid #E5E7EB;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .preview-container {
            border: 2px solid #E5E7EB;
            border-radius: 8px;
            padding: 20px;
            background-color: #F9FAFB;
            margin: 20px 0;
        }
        .google-mockup {
            background-color: white;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .search-bar {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .google-logo {
            background-color: #4285F4;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
        }
        .search-input {
            flex: 1;
            border: 1px solid #E5E7EB;
            border-radius: 24px;
            padding: 8px 16px;
            background-color: #F8F9FA;
            color: #666;
        }
        .search-results-info {
            color: #666;
            font-size: 14px;
        }
        .search-result {
            border-left: 4px solid #1A73E8;
            padding-left: 15px;
            background-color: #F8F9FA;
            padding: 15px;
            border-radius: 0 8px 8px 0;
        }
        .result-url {
            color: #006621;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .result-title {
            color: #1A0DAB;
            font-size: 20px;
            font-weight: normal;
            margin-bottom: 8px;
            text-decoration: underline;
        }
        .result-description {
            color: #545454;
            font-size: 14px;
            line-height: 1.4;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin: 20px 0;
        }
        .stats-row {
            display: table-row;
        }
        .stats-cell {
            display: table-cell;
            padding: 10px;
            border: 1px solid #E5E7EB;
            background-color: white;
        }
        .stats-header {
            background-color: #F3F4F6;
            font-weight: bold;
        }
        .status-good {
            color: #059669;
            font-weight: bold;
        }
        .status-warning {
            color: #D97706;
            font-weight: bold;
        }
        .status-error {
            color: #DC2626;
            font-weight: bold;
        }
        .recommendations {
            background-color: #FEF3C7;
            border: 1px solid #F59E0B;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
        .recommendations h3 {
            color: #92400E;
            margin-top: 0;
        }
        .recommendations ul {
            margin: 0;
            padding-left: 20px;
        }
        .recommendations li {
            color: #92400E;
            margin-bottom: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #E5E7EB;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SERP Preview Report</h1>
        <p>Generated on {{ $generated_at }}</p>
    </div>

    <div class="section">
        <h2>Page Information</h2>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stats-cell stats-header">Page Title</div>
                <div class="stats-cell">{{ $page_title }}</div>
            </div>
            <div class="stats-row">
                <div class="stats-cell stats-header">Target URL</div>
                <div class="stats-cell">{{ $target_url }}</div>
            </div>
            <div class="stats-row">
                <div class="stats-cell stats-header">Meta Description</div>
                <div class="stats-cell">{{ $meta_description }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Search Result Preview</h2>
        <div class="preview-container">
            <div class="google-mockup">
                <div class="search-bar">
                    <div class="google-logo">G</div>
                    <div class="search-input">your search query</div>
                </div>
                <div class="search-results-info">About 1,234,567 results (0.45 seconds)</div>
            </div>
            
            <div class="search-result">
                <div class="result-url">{{ $formatted_url }}</div>
                <div class="result-title">
                    @if(strlen($page_title) > 60)
                        {{ substr($page_title, 0, 60) }}...
                    @else
                        {{ $page_title }}
                    @endif
                </div>
                <div class="result-description">
                    @if(strlen($meta_description) > 160)
                        {{ substr($meta_description, 0, 160) }}...
                    @else
                        {{ $meta_description }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>SEO Analysis</h2>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stats-cell stats-header">Title Length</div>
                <div class="stats-cell">
                    {{ $title_length }} characters
                    @if($title_length >= 30 && $title_length <= 60)
                        <span class="status-good">✓ Optimal</span>
                    @elseif($title_length > 60)
                        <span class="status-error">⚠ Too Long</span>
                    @else
                        <span class="status-warning">⚠ Too Short</span>
                    @endif
                </div>
            </div>
            <div class="stats-row">
                <div class="stats-cell stats-header">Description Length</div>
                <div class="stats-cell">
                    {{ $description_length }} characters
                    @if($description_length >= 120 && $description_length <= 160)
                        <span class="status-good">✓ Optimal</span>
                    @elseif($description_length > 160)
                        <span class="status-error">⚠ Too Long</span>
                    @else
                        <span class="status-warning">⚠ Too Short</span>
                    @endif
                </div>
            </div>
            <div class="stats-row">
                <div class="stats-cell stats-header">URL Format</div>
                <div class="stats-cell">
                    @if(filter_var($target_url, FILTER_VALIDATE_URL))
                        <span class="status-good">✓ Valid URL</span>
                    @else
                        <span class="status-error">⚠ Invalid URL</span>
                    @endif
                </div>
            </div>
        </div>

        @php
            $recommendations = [];
            if($title_length < 30) $recommendations[] = "Consider making your title longer and more descriptive";
            if($title_length > 60) $recommendations[] = "Shorten your title to avoid truncation in search results";
            if($description_length < 120) $recommendations[] = "Add more details to your meta description";
            if($description_length > 160) $recommendations[] = "Shorten your meta description to avoid truncation";
            if(!strpos($page_title, '|') && !strpos($page_title, '-')) $recommendations[] = "Consider adding your brand name to the title";
        @endphp

        @if(count($recommendations) > 0)
        <div class="recommendations">
            <h3>🚀 Optimization Recommendations</h3>
            <ul>
                @foreach($recommendations as $recommendation)
                    <li>{{ $recommendation }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    <div class="section">
        <h2>Best Practices</h2>
        <div style="background-color: #ECFDF5; border: 1px solid #10B981; border-radius: 8px; padding: 15px;">
            <h3 style="color: #065F46; margin-top: 0;">✅ SEO Guidelines</h3>
            <ul style="color: #065F46; margin: 0; padding-left: 20px;">
                <li>Keep titles between 30-60 characters for optimal display</li>
                <li>Write meta descriptions between 120-160 characters</li>
                <li>Include your primary keyword in both title and description</li>
                <li>Make titles compelling to encourage clicks</li>
                <li>Ensure descriptions accurately summarize page content</li>
                <li>Include your brand name in the title when possible</li>
                <li>Use action words and emotional triggers in descriptions</li>
            </ul>
        </div>
    </div>

    <div class="footer">
        <p>This report was generated by SEO Audit Pro | {{ now()->format('Y') }} | For more SEO tools, visit your dashboard</p>
    </div>
</body>
</html>
