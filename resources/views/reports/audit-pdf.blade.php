<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>SEO Audit Report - {{ $audit->url }}</title>
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
        
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .metric-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
        }
        
        .metric-number {
            font-size: 24px;
            font-weight: bold;
            color: #0ea5e9;
            margin-bottom: 5px;
        }
        
        .metric-label {
            color: #64748b;
            font-size: 14px;
        }
        
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        
        .section h3 {
            color: #1e293b;
            font-size: 18px;
            margin-bottom: 15px;
            border-left: 4px solid #0ea5e9;
            padding-left: 15px;
        }
        
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .content-table th,
        .content-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .content-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            color: #475569;
        }
        
        .recommendations {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 20px;
        }
        
        .recommendations h3 {
            color: #92400e;
            margin-top: 0;
            border-left: 4px solid #f59e0b;
        }
        
        .recommendation-item {
            margin-bottom: 10px;
            padding: 10px;
            background: white;
            border-radius: 5px;
            border-left: 3px solid #f59e0b;
        }
        
        .status-good {
            color: #059669;
            font-weight: bold;
        }
        
        .status-warning {
            color: #d97706;
            font-weight: bold;
        }
        
        .status-error {
            color: #dc2626;
            font-weight: bold;
        }
        
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 12px;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>SEO Audit Report</h1>
        <p>Comprehensive SEO Analysis for {{ $audit->url }}</p>
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>
    
    <!-- Audit Score -->
    <div class="score-section">
        <div class="score-number">{{ $audit->audit_score ?? 0 }}/100</div>
        <div class="score-label">Overall SEO Score</div>
    </div>
    
    <!-- Key Metrics -->
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-number">{{ $audit->page_load_speed ?? 0 }}ms</div>
            <div class="metric-label">Page Load Speed</div>
        </div>
        
        <div class="metric-card">
            <div class="metric-number">{{ count($audit->h1_tags ?? []) }}</div>
            <div class="metric-label">H1 Tags</div>
        </div>
        
        <div class="metric-card">
            <div class="metric-number">{{ $audit->internal_links_count ?? 0 }}</div>
            <div class="metric-label">Internal Links</div>
        </div>
        
        <div class="metric-card">
            <div class="metric-number">{{ $audit->external_links_count ?? 0 }}</div>
            <div class="metric-label">External Links</div>
        </div>
        
        <div class="metric-card">
            <div class="metric-number">{{ $audit->images_count ?? 0 }}</div>
            <div class="metric-label">Total Images</div>
        </div>
        
        <div class="metric-card">
            <div class="metric-number">{{ $audit->images_without_alt ?? 0 }}</div>
            <div class="metric-label">Images without Alt</div>
        </div>
    </div>
    
    <!-- Basic Information -->
    <div class="section">
        <h3>Basic Information</h3>
        <div class="audit-info">
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Website URL:</span>
                    <span class="info-value">{{ $audit->url }}</span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Status Code:</span>
                    <span class="info-value">{{ $audit->status_code ?? 'Unknown' }}</span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Page Title:</span>
                    <span class="info-value">{{ $audit->title ?: 'Not found' }}</span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">SSL Certificate:</span>
                    <span class="info-value {{ $audit->ssl_certificate ? 'status-good' : 'status-error' }}">
                        {{ $audit->ssl_certificate ? 'Valid' : 'Missing' }}
                    </span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Mobile Friendly:</span>
                    <span class="info-value {{ $audit->mobile_friendly ? 'status-good' : 'status-error' }}">
                        {{ $audit->mobile_friendly ? 'Yes' : 'No' }}
                    </span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Word Count:</span>
                    <span class="info-value">{{ $audit->word_count ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Meta Information -->
    <div class="section">
        <h3>Meta Information</h3>
        <table class="content-table">
            <thead>
                <tr>
                    <th>Element</th>
                    <th>Content</th>
                    <th>Length</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Title Tag</td>
                    <td>{{ $audit->title ?: 'Not found' }}</td>
                    <td>{{ strlen($audit->title ?? '') }} characters</td>
                    <td class="{{ strlen($audit->title ?? '') >= 30 && strlen($audit->title ?? '') <= 60 ? 'status-good' : 'status-warning' }}">
                        {{ strlen($audit->title ?? '') >= 30 && strlen($audit->title ?? '') <= 60 ? 'Optimal' : 'Needs optimization' }}
                    </td>
                </tr>
                
                <tr>
                    <td>Meta Description</td>
                    <td>{{ $audit->meta_description ?: 'Not found' }}</td>
                    <td>{{ strlen($audit->meta_description ?? '') }} characters</td>
                    <td class="{{ strlen($audit->meta_description ?? '') >= 120 && strlen($audit->meta_description ?? '') <= 160 ? 'status-good' : 'status-warning' }}">
                        {{ strlen($audit->meta_description ?? '') >= 120 && strlen($audit->meta_description ?? '') <= 160 ? 'Optimal' : 'Needs optimization' }}
                    </td>
                </tr>
                
                <tr>
                    <td>Meta Keywords</td>
                    <td>{{ $audit->meta_keywords ?: 'Not found' }}</td>
                    <td>{{ strlen($audit->meta_keywords ?? '') }} characters</td>
                    <td class="status-warning">Legacy</td>
                </tr>
                
                <tr>
                    <td>Canonical URL</td>
                    <td>{{ $audit->canonical_url ?: 'Not found' }}</td>
                    <td>-</td>
                    <td class="{{ $audit->canonical_url ? 'status-good' : 'status-error' }}">
                        {{ $audit->canonical_url ? 'Present' : 'Missing' }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Content Structure -->
    <div class="section page-break">
        <h3>Content Structure</h3>
        
        @if(!empty($audit->h1_tags))
        <h4>H1 Tags ({{ count($audit->h1_tags) }})</h4>
        <ul>
            @foreach($audit->h1_tags as $h1)
            <li>{{ $h1 }}</li>
            @endforeach
        </ul>
        @endif
        
        @if(!empty($audit->h2_tags))
        <h4>H2 Tags ({{ count($audit->h2_tags) }})</h4>
        <ul>
            @foreach(array_slice($audit->h2_tags, 0, 10) as $h2)
            <li>{{ $h2 }}</li>
            @endforeach
            @if(count($audit->h2_tags) > 10)
            <li><em>... and {{ count($audit->h2_tags) - 10 }} more</em></li>
            @endif
        </ul>
        @endif
    </div>
    
    <!-- Technical SEO -->
    <div class="section">
        <h3>Technical SEO</h3>
        <table class="content-table">
            <thead>
                <tr>
                    <th>Check</th>
                    <th>Status</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>XML Sitemap</td>
                    <td class="{{ $audit->sitemap_url ? 'status-good' : 'status-error' }}">
                        {{ $audit->sitemap_url ? 'Found' : 'Not Found' }}
                    </td>
                    <td>{{ $audit->sitemap_url ?: 'No sitemap detected' }}</td>
                </tr>
                
                <tr>
                    <td>Robots.txt</td>
                    <td class="{{ $audit->robots_txt_status === 'found' ? 'status-good' : 'status-error' }}">
                        {{ $audit->robots_txt_status === 'found' ? 'Found' : 'Not Found' }}
                    </td>
                    <td>{{ $audit->robots_txt_status === 'found' ? 'Robots.txt file is accessible' : 'No robots.txt file found' }}</td>
                </tr>
                
                <tr>
                    <td>Schema Markup</td>
                    <td class="{{ !empty($audit->schema_markup) ? 'status-good' : 'status-warning' }}">
                        {{ !empty($audit->schema_markup) ? 'Found' : 'Not Found' }}
                    </td>
                    <td>
                        @if(!empty($audit->schema_markup))
                            {{ implode(', ', $audit->schema_markup) }}
                        @else
                            No structured data detected
                        @endif
                    </td>
                </tr>
                
                <tr>
                    <td>Robots Meta</td>
                    <td class="{{ $audit->robots_meta ? 'status-good' : 'status-warning' }}">
                        {{ $audit->robots_meta ? 'Set' : 'Not Set' }}
                    </td>
                    <td>{{ $audit->robots_meta ?: 'No robots meta tag found' }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Performance Analysis -->
    <div class="section">
        <h3>Performance Analysis</h3>
        <table class="content-table">
            <thead>
                <tr>
                    <th>Metric</th>
                    <th>Value</th>
                    <th>Rating</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Page Load Speed</td>
                    <td>{{ $audit->page_load_speed ?? 0 }}ms</td>
                    <td class="{{ ($audit->page_load_speed ?? 0) <= 3000 ? 'status-good' : (($audit->page_load_speed ?? 0) <= 5000 ? 'status-warning' : 'status-error') }}">
                        {{ ($audit->page_load_speed ?? 0) <= 3000 ? 'Good' : (($audit->page_load_speed ?? 0) <= 5000 ? 'Needs Improvement' : 'Poor') }}
                    </td>
                </tr>
                
                <tr>
                    <td>Internal Links</td>
                    <td>{{ $audit->internal_links_count ?? 0 }}</td>
                    <td class="{{ ($audit->internal_links_count ?? 0) > 0 ? 'status-good' : 'status-warning' }}">
                        {{ ($audit->internal_links_count ?? 0) > 0 ? 'Good' : 'Needs Improvement' }}
                    </td>
                </tr>
                
                <tr>
                    <td>External Links</td>
                    <td>{{ $audit->external_links_count ?? 0 }}</td>
                    <td class="status-good">Normal</td>
                </tr>
                
                <tr>
                    <td>Images with Alt Text</td>
                    <td>{{ ($audit->images_count ?? 0) - ($audit->images_without_alt ?? 0) }}/{{ $audit->images_count ?? 0 }}</td>
                    <td class="{{ ($audit->images_without_alt ?? 0) === 0 ? 'status-good' : 'status-warning' }}">
                        {{ ($audit->images_without_alt ?? 0) === 0 ? 'All images have alt text' : ($audit->images_without_alt ?? 0) . ' images missing alt text' }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Keywords -->
    @if($audit->keywords && $audit->keywords->count() > 0)
    <div class="section page-break">
        <h3>Tracked Keywords</h3>
        <table class="content-table">
            <thead>
                <tr>
                    <th>Keyword</th>
                    <th>Current Position</th>
                    <th>Previous Position</th>
                    <th>Change</th>
                    <th>Search Volume</th>
                </tr>
            </thead>
            <tbody>
                @foreach($audit->keywords as $keyword)
                <tr>
                    <td>{{ $keyword->keyword }}</td>
                    <td>{{ $keyword->current_position ?: 'N/A' }}</td>
                    <td>{{ $keyword->previous_position ?: 'N/A' }}</td>
                    <td class="{{ $keyword->position_change > 0 ? 'status-good' : ($keyword->position_change < 0 ? 'status-error' : '') }}">
                        @if($keyword->position_change > 0)
                            +{{ $keyword->position_change }}
                        @elseif($keyword->position_change < 0)
                            {{ $keyword->position_change }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ number_format($keyword->search_volume) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    
    <!-- Backlinks -->
    @if($audit->backlinks && $audit->backlinks->count() > 0)
    <div class="section">
        <h3>Top Backlinks</h3>
        <table class="content-table">
            <thead>
                <tr>
                    <th>Source Domain</th>
                    <th>Anchor Text</th>
                    <th>Domain Authority</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($audit->backlinks->take(10) as $backlink)
                <tr>
                    <td>{{ parse_url($backlink->source_url, PHP_URL_HOST) }}</td>
                    <td>{{ $backlink->anchor_text }}</td>
                    <td>{{ $backlink->domain_authority }}</td>
                    <td class="{{ $backlink->status === 'active' ? 'status-good' : 'status-warning' }}">
                        {{ ucfirst($backlink->status) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    
    <!-- Recommendations -->
    @if(!empty($audit->recommendations))
    <div class="section page-break">
        <div class="recommendations">
            <h3>SEO Recommendations</h3>
            @foreach($audit->recommendations as $recommendation)
            <div class="recommendation-item">
                <strong>•</strong> {{ $recommendation }}
            </div>
            @endforeach
        </div>
    </div>
    @endif
    
    <!-- Footer -->
    <div class="footer">
        <p>This report was generated by SEO Audit Pro on {{ now()->format('F j, Y \a\t g:i A') }}</p>
        <p>For more detailed analysis and ongoing monitoring, visit our dashboard.</p>
    </div>
</body>
</html>
