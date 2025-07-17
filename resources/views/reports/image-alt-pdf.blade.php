<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Image ALT Text Accessibility Report - {{ $audit->formatted_url }}</title>
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
            border-bottom: 3px solid #10b981;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #10b981;
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
            margin-bottom: 20px;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .info-label {
            font-weight: 600;
            color: #374151;
        }
        
        .info-value {
            color: #6b7280;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin: 30px 0;
        }
        
        .stat-card {
            text-align: center;
            padding: 20px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 14px;
            color: #6b7280;
            font-weight: 600;
        }
        
        .stat-card.total { border-color: #3b82f6; }
        .stat-card.total .stat-number { color: #3b82f6; }
        
        .stat-card.good { border-color: #10b981; }
        .stat-card.good .stat-number { color: #10b981; }
        
        .stat-card.missing { border-color: #ef4444; }
        .stat-card.missing .stat-number { color: #ef4444; }
        
        .stat-card.issues { border-color: #f59e0b; }
        .stat-card.issues .stat-number { color: #f59e0b; }
        
        .score-section {
            background: #f9fafb;
            padding: 25px;
            border-radius: 8px;
            margin: 30px 0;
            text-align: center;
        }
        
        .score-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: bold;
            color: white;
        }
        
        .score-excellent { background: #10b981; }
        .score-good { background: #eab308; }
        .score-fair { background: #f97316; }
        .score-poor { background: #ef4444; }
        
        .score-description {
            color: #6b7280;
            font-size: 16px;
        }
        
        .crawl-summary {
            margin: 30px 0;
        }
        
        .crawl-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        .crawl-table th,
        .crawl-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .crawl-table th {
            background: #f9fafb;
            font-weight: 600;
            color: #374151;
        }
        
        .status-success {
            color: #10b981;
            font-weight: 600;
        }
        
        .status-failed {
            color: #ef4444;
            font-weight: 600;
        }
        
        .images-section {
            margin-top: 40px;
        }
        
        .image-item {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            break-inside: avoid;
        }
        
        .image-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .image-filename {
            font-weight: 600;
            color: #1f2937;
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-good {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-needs-improvement {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-poor {
            background: #fed7aa;
            color: #9a3412;
        }
        
        .status-missing {
            background: #fecaca;
            color: #991b1b;
        }
        
        .alt-text {
            background: #f9fafb;
            padding: 12px;
            border-radius: 6px;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
            word-break: break-all;
        }
        
        .issues-list,
        .recommendations-list {
            margin: 15px 0;
        }
        
        .issues-list li {
            color: #dc2626;
            margin: 5px 0;
        }
        
        .recommendations-list li {
            color: #059669;
            margin: 5px 0;
        }
        
        .context-info {
            background: #f3f4f6;
            padding: 12px;
            border-radius: 6px;
            margin-top: 15px;
            font-size: 14px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .summary-section {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #e5e7eb;
        }
        
        .recommendation-box {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .recommendation-box h3 {
            color: #065f46;
            margin-top: 0;
        }
        
        .recommendation-box ul {
            color: #047857;
        }
        
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Image ALT Text Accessibility Report</h1>
        <p>{{ $audit->page_title ?: $audit->formatted_url }}</p>
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>

    <!-- Audit Information -->
    <div class="audit-info">
        <h2>Audit Information</h2>
        <div class="info-grid">
            <div>
                <div class="info-item">
                    <span class="info-label">Website URL:</span>
                    <span class="info-value">{{ $audit->url }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Page Title:</span>
                    <span class="info-value">{{ $audit->page_title ?: 'Not available' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Analysis Date:</span>
                    <span class="info-value">{{ $audit->analyzed_at->format('F j, Y \a\t g:i A') }}</span>
                </div>
            </div>
            <div>
                <div class="info-item">
                    <span class="info-label">Pages Crawled:</span>
                    <span class="info-value">{{ $audit->pages_crawled }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Analysis Type:</span>
                    <span class="info-value">{{ $audit->is_multi_page ? 'Multi-page Crawl' : 'Single Page' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Total Images:</span>
                    <span class="info-value">{{ $audit->total_images }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card total">
            <div class="stat-number">{{ $audit->total_images }}</div>
            <div class="stat-label">Total Images</div>
        </div>
        <div class="stat-card good">
            <div class="stat-number">{{ $audit->images_with_good_alt }}</div>
            <div class="stat-label">Good ALT Text</div>
        </div>
        <div class="stat-card missing">
            <div class="stat-number">{{ $audit->images_without_alt }}</div>
            <div class="stat-label">Missing ALT</div>
        </div>
        <div class="stat-card issues">
            <div class="stat-number">{{ $audit->images_with_issues }}</div>
            <div class="stat-label">Need Improvement</div>
        </div>
    </div>

    <!-- Accessibility Score -->
    <div class="score-section">
        <h2>Overall Accessibility Score</h2>
        <div class="score-circle {{ 
            $audit->accessibility_score >= 90 ? 'score-excellent' : 
            ($audit->accessibility_score >= 70 ? 'score-good' : 
            ($audit->accessibility_score >= 50 ? 'score-fair' : 'score-poor'))
        }}">
            {{ $audit->accessibility_score }}%
        </div>
        <div class="score-description">
            @if($audit->accessibility_score >= 90)
                Excellent accessibility! Most images have proper alt text.
            @elseif($audit->accessibility_score >= 70)
                Good accessibility with room for improvement.
            @elseif($audit->accessibility_score >= 50)
                Fair accessibility. Many images need better alt text.
            @else
                Poor accessibility. Most images are missing or have inadequate alt text.
            @endif
        </div>
    </div>

    <!-- Crawl Summary (for multi-page audits) -->
    @if($audit->is_multi_page && $audit->crawl_summary)
    <div class="crawl-summary">
        <h2>Pages Analyzed</h2>
        <table class="crawl-table">
            <thead>
                <tr>
                    <th>Page URL</th>
                    <th>Page Title</th>
                    <th>Images Found</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($audit->crawl_summary as $page)
                <tr>
                    <td>{{ Str::limit($page['url'], 60) }}</td>
                    <td>{{ $page['page_title'] ?: 'Untitled' }}</td>
                    <td>{{ $page['total_images'] }}</td>
                    <td class="{{ $page['status'] === 'success' ? 'status-success' : 'status-failed' }}">
                        {{ ucfirst($page['status']) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Key Recommendations -->
    <div class="recommendation-box">
        <h3>Key Recommendations</h3>
        <ul>
            @if($audit->images_without_alt > 0)
            <li><strong>Add ALT text to {{ $audit->images_without_alt }} image{{ $audit->images_without_alt > 1 ? 's' : '' }}:</strong> All images should have descriptive alt text for screen reader users.</li>
            @endif
            @if($audit->images_with_issues > 0)
            <li><strong>Improve ALT text quality for {{ $audit->images_with_issues }} image{{ $audit->images_with_issues > 1 ? 's' : '' }}:</strong> Avoid generic terms, keyword stuffing, and filename-based descriptions.</li>
            @endif
            <li><strong>Best Practices:</strong> Keep alt text between 5-125 characters, be descriptive but concise, and avoid redundant phrases like "image of" or "picture of".</li>
            <li><strong>Decorative Images:</strong> Use empty alt="" for purely decorative images that don't add informational value.</li>
        </ul>
    </div>

    <!-- Images Analysis -->
    <div class="images-section page-break">
        <h2>Detailed Image Analysis</h2>
        
        @foreach($audit->images_data as $index => $image)
        <div class="image-item">
            <div class="image-header">
                <div class="image-filename">{{ $image['file_name'] }}</div>
                <span class="status-badge status-{{ str_replace('_', '-', $image['analysis']['status']) }}">
                    {{ 
                        $image['analysis']['status'] === 'good' ? 'Good ALT Text' : 
                        ($image['analysis']['status'] === 'needs_improvement' ? 'Needs Improvement' : 
                        ($image['analysis']['status'] === 'poor' ? 'Poor ALT Text' : 'Missing ALT'))
                    }}
                </span>
            </div>

            <div><strong>Image URL:</strong> {{ Str::limit($image['src'], 80) }}</div>
            
            @if($image['width'] && $image['height'])
            <div><strong>Dimensions:</strong> {{ $image['width'] }}×{{ $image['height'] }}px</div>
            @endif

            @if($image['page_url'] !== $audit->url)
            <div><strong>Found on page:</strong> {{ $image['page_url'] }}</div>
            @endif

            <div><strong>Accessibility Score:</strong> {{ $image['analysis']['score'] }}/100</div>

            <div>
                <strong>Current ALT Text:</strong>
                <div class="alt-text">{{ $image['alt'] ?: '(empty)' }}</div>
                <small>Length: {{ $image['analysis']['length'] }} characters, {{ $image['analysis']['word_count'] }} words</small>
            </div>

            @if(!empty($image['analysis']['issues']))
            <div>
                <strong>Issues Found:</strong>
                <ul class="issues-list">
                    @foreach($image['analysis']['issues'] as $issue)
                    <li>{{ $issue }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div>
                <strong>Recommendations:</strong>
                <ul class="recommendations-list">
                    @foreach($image['analysis']['recommendations'] as $recommendation)
                    <li>{{ $recommendation }}</li>
                    @endforeach
                </ul>
            </div>

            @if(!empty($image['context']['parent_text']) || !empty($image['context']['caption']) || !empty($image['context']['parent_tag']))
            <div class="context-info">
                <strong>Context Information:</strong><br>
                @if(!empty($image['context']['parent_text']))
                <strong>Surrounding text:</strong> {{ Str::limit($image['context']['parent_text'], 100) }}<br>
                @endif
                @if(!empty($image['context']['caption']))
                <strong>Caption:</strong> {{ $image['context']['caption'] }}<br>
                @endif
                @if(!empty($image['context']['parent_tag']))
                <strong>Parent element:</strong> &lt;{{ $image['context']['parent_tag'] }}&gt;
                @endif
            </div>
            @endif
        </div>
        @endforeach
    </div>

    <!-- Summary -->
    <div class="summary-section">
        <h2>Analysis Summary</h2>
        <p>This audit analyzed <strong>{{ $audit->total_images }}</strong> images across <strong>{{ $audit->pages_crawled }}</strong> page{{ $audit->pages_crawled > 1 ? 's' : '' }} and found:</p>
        
        <ul>
            <li><strong>{{ $audit->images_with_good_alt }}</strong> images ({{ round(($audit->images_with_good_alt / max($audit->total_images, 1)) * 100, 1) }}%) have good ALT text</li>
            <li><strong>{{ $audit->images_without_alt }}</strong> images ({{ round(($audit->images_without_alt / max($audit->total_images, 1)) * 100, 1) }}%) are missing ALT text</li>
            <li><strong>{{ $audit->images_with_issues }}</strong> images ({{ round(($audit->images_with_issues / max($audit->total_images, 1)) * 100, 1) }}%) need ALT text improvements</li>
        </ul>

        <p>Regular image accessibility audits help ensure your website is inclusive and accessible to all users, including those using screen readers and assistive technologies.</p>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Report generated by SEO Audit Pro - Image ALT Text Checker</p>
        <p>For more SEO tools and audits, visit our dashboard</p>
    </div>
</body>
</html>
