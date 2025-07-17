<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Meta Tag Audit Report - {{ $audit->url }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #0ea5e9;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #0ea5e9;
            font-size: 28px;
            margin: 0;
        }
        
        .header p {
            color: #666;
            margin: 5px 0 0 0;
        }
        
        .audit-info {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .audit-info h2 {
            color: #1e293b;
            font-size: 20px;
            margin: 0 0 15px 0;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .info-label {
            font-weight: bold;
            color: #475569;
        }
        
        .info-value {
            color: #1e293b;
        }
        
        .score-section {
            text-align: center;
            background: linear-gradient(135deg, #0ea5e9, #0284c7);
            color: white;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .score-number {
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .score-label {
            font-size: 18px;
            opacity: 0.9;
        }
        
        .meta-tags-section {
            margin-bottom: 30px;
        }
        
        .section-title {
            color: #1e293b;
            font-size: 22px;
            margin-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
        }
        
        .meta-tag {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .meta-tag-name {
            font-weight: bold;
            color: #0ea5e9;
            margin-bottom: 5px;
        }
        
        .meta-tag-value {
            color: #475569;
            margin-bottom: 10px;
            word-wrap: break-word;
        }
        
        .meta-tag-status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-good {
            background: #dcfce7;
            color: #166534;
        }
        
        .status-warning {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-error {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .issues-section {
            margin-bottom: 30px;
        }
        
        .issue-item {
            background: #fef2f2;
            border-left: 4px solid #ef4444;
            padding: 15px;
            margin-bottom: 10px;
        }
        
        .issue-title {
            font-weight: bold;
            color: #991b1b;
            margin-bottom: 5px;
        }
        
        .issue-description {
            color: #475569;
        }
        
        .recommendations-section {
            margin-bottom: 30px;
        }
        
        .recommendation-item {
            background: #f0f9ff;
            border-left: 4px solid #0ea5e9;
            padding: 15px;
            margin-bottom: 10px;
        }
        
        .recommendation-title {
            font-weight: bold;
            color: #0c4a6e;
            margin-bottom: 5px;
        }
        
        .recommendation-description {
            color: #475569;
        }
        
        .footer {
            text-align: center;
            color: #666;
            font-size: 12px;
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Meta Tag Audit Report</h1>
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>

    <div class="audit-info">
        <h2>Audit Information</h2>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">URL:</span>
                <span class="info-value">{{ $audit->url }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Date:</span>
                <span class="info-value">{{ $audit->created_at->format('F j, Y \a\t g:i A') }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Status:</span>
                <span class="info-value">{{ $audit->getStatusLabel() }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Total Issues:</span>
                <span class="info-value">{{ count($audit->issues) }}</span>
            </div>
        </div>
    </div>

    <div class="score-section">
        <div class="score-number">{{ $audit->score }}/100</div>
        <div class="score-label">Overall Meta Tag Score</div>
    </div>

    <div class="meta-tags-section">
        <h2 class="section-title">Meta Tags Analysis</h2>
        
        @if($audit->title)
            <div class="meta-tag">
                <div class="meta-tag-name">Title Tag</div>
                <div class="meta-tag-value">{{ $audit->title }}</div>
                <div class="meta-tag-status {{ $audit->title && strlen($audit->title) >= 30 && strlen($audit->title) <= 60 ? 'status-good' : 'status-warning' }}">
                    {{ $audit->title && strlen($audit->title) >= 30 && strlen($audit->title) <= 60 ? 'Good' : 'Needs Attention' }}
                </div>
            </div>
        @endif

        @if($audit->meta_description)
            <div class="meta-tag">
                <div class="meta-tag-name">Meta Description</div>
                <div class="meta-tag-value">{{ $audit->meta_description }}</div>
                <div class="meta-tag-status {{ $audit->meta_description && strlen($audit->meta_description) >= 120 && strlen($audit->meta_description) <= 160 ? 'status-good' : 'status-warning' }}">
                    {{ $audit->meta_description && strlen($audit->meta_description) >= 120 && strlen($audit->meta_description) <= 160 ? 'Good' : 'Needs Attention' }}
                </div>
            </div>
        @endif

        @if($audit->meta_keywords)
            <div class="meta-tag">
                <div class="meta-tag-name">Meta Keywords</div>
                <div class="meta-tag-value">{{ $audit->meta_keywords }}</div>
                <div class="meta-tag-status status-warning">
                    Note: Meta keywords are not used by search engines
                </div>
            </div>
        @endif

        @if($audit->canonical_url)
            <div class="meta-tag">
                <div class="meta-tag-name">Canonical URL</div>
                <div class="meta-tag-value">{{ $audit->canonical_url }}</div>
                <div class="meta-tag-status status-good">Good</div>
            </div>
        @endif

        @if($audit->robots)
            <div class="meta-tag">
                <div class="meta-tag-name">Robots Meta Tag</div>
                <div class="meta-tag-value">{{ $audit->robots }}</div>
                <div class="meta-tag-status status-good">Present</div>
            </div>
        @endif

        @if($audit->og_title)
            <div class="meta-tag">
                <div class="meta-tag-name">Open Graph Title</div>
                <div class="meta-tag-value">{{ $audit->og_title }}</div>
                <div class="meta-tag-status status-good">Good</div>
            </div>
        @endif

        @if($audit->og_description)
            <div class="meta-tag">
                <div class="meta-tag-name">Open Graph Description</div>
                <div class="meta-tag-value">{{ $audit->og_description }}</div>
                <div class="meta-tag-status status-good">Good</div>
            </div>
        @endif

        @if($audit->og_image)
            <div class="meta-tag">
                <div class="meta-tag-name">Open Graph Image</div>
                <div class="meta-tag-value">{{ $audit->og_image }}</div>
                <div class="meta-tag-status status-good">Good</div>
            </div>
        @endif

        @if($audit->twitter_card)
            <div class="meta-tag">
                <div class="meta-tag-name">Twitter Card</div>
                <div class="meta-tag-value">{{ $audit->twitter_card }}</div>
                <div class="meta-tag-status status-good">Good</div>
            </div>
        @endif

        @if($audit->twitter_title)
            <div class="meta-tag">
                <div class="meta-tag-name">Twitter Title</div>
                <div class="meta-tag-value">{{ $audit->twitter_title }}</div>
                <div class="meta-tag-status status-good">Good</div>
            </div>
        @endif

        @if($audit->twitter_description)
            <div class="meta-tag">
                <div class="meta-tag-name">Twitter Description</div>
                <div class="meta-tag-value">{{ $audit->twitter_description }}</div>
                <div class="meta-tag-status status-good">Good</div>
            </div>
        @endif

        @if($audit->twitter_image)
            <div class="meta-tag">
                <div class="meta-tag-name">Twitter Image</div>
                <div class="meta-tag-value">{{ $audit->twitter_image }}</div>
                <div class="meta-tag-status status-good">Good</div>
            </div>
        @endif

        @if($audit->viewport)
            <div class="meta-tag">
                <div class="meta-tag-name">Viewport</div>
                <div class="meta-tag-value">{{ $audit->viewport }}</div>
                <div class="meta-tag-status status-good">Good</div>
            </div>
        @endif

        @if($audit->charset)
            <div class="meta-tag">
                <div class="meta-tag-name">Character Set</div>
                <div class="meta-tag-value">{{ $audit->charset }}</div>
                <div class="meta-tag-status status-good">Good</div>
            </div>
        @endif

        @if($audit->language)
            <div class="meta-tag">
                <div class="meta-tag-name">Language</div>
                <div class="meta-tag-value">{{ $audit->language }}</div>
                <div class="meta-tag-status status-good">Good</div>
            </div>
        @endif

        @if($audit->author)
            <div class="meta-tag">
                <div class="meta-tag-name">Author</div>
                <div class="meta-tag-value">{{ $audit->author }}</div>
                <div class="meta-tag-status status-good">Good</div>
            </div>
        @endif
    </div>

    @if(count($audit->issues) > 0)
        <div class="issues-section">
            <h2 class="section-title">Issues Found</h2>
            @foreach($audit->issues as $issue)
                <div class="issue-item">
                    <div class="issue-title">{{ ucfirst(str_replace('_', ' ', $issue)) }}</div>
                    <div class="issue-description">This issue needs attention to improve SEO performance.</div>
                </div>
            @endforeach
        </div>
    @endif

    @if(count($audit->recommendations) > 0)
        <div class="recommendations-section">
            <h2 class="section-title">Recommendations</h2>
            @foreach($audit->recommendations as $recommendation)
                <div class="recommendation-item">
                    <div class="recommendation-title">{{ ucfirst(str_replace('_', ' ', $recommendation)) }}</div>
                    <div class="recommendation-description">Implement this recommendation to improve SEO.</div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="footer">
        <p>This report was generated by the SEO Meta Tag Analyzer tool.</p>
        <p>For more information about improving your website's SEO, consult with SEO professionals.</p>
    </div>
</body>
</html>
