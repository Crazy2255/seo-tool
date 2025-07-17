<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Page Speed Report - {{ $audit->url }}</title>
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
        
        .category-scores {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-top: 20px;
        }
        
        .category-score {
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
            padding: 15px;
            border-radius: 8px;
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
            margin-bottom: 5px;
        }
        
        .metric-label {
            color: #1e293b;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .metric-description {
            color: #64748b;
            font-size: 12px;
        }
        
        .rating-good { color: #059669; }
        .rating-needs-improvement { color: #d97706; }
        .rating-poor { color: #dc2626; }
        
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
        
        .recommendations {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .recommendations h3 {
            color: #92400e;
            margin-top: 0;
            border-left: 4px solid #f59e0b;
        }
        
        .recommendation-item {
            margin-bottom: 15px;
            padding: 15px;
            background: white;
            border-radius: 5px;
            border-left: 3px solid #f59e0b;
        }
        
        .recommendation-title {
            font-weight: bold;
            color: #92400e;
            margin-bottom: 5px;
        }
        
        .recommendation-description {
            color: #1e293b;
            font-size: 14px;
        }
        
        .priority-high {
            border-left-color: #dc2626;
        }
        
        .priority-medium {
            border-left-color: #d97706;
        }
        
        .priority-low {
            border-left-color: #059669;
        }
        
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 12px;
        }
        
        .vitals-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .vital-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
        }
        
        .vital-value {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .vital-name {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 5px;
        }
        
        .vital-description {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 10px;
        }
        
        .vital-rating {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .rating-good-bg {
            background: #dcfce7;
            color: #166534;
        }
        
        .rating-needs-improvement-bg {
            background: #fef3c7;
            color: #92400e;
        }
        
        .rating-poor-bg {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Page Speed Report</h1>
        <p>Performance Analysis for {{ $audit->url }}</p>
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }} • {{ ucfirst($audit->strategy) }} Analysis</p>
    </div>
    
    <!-- Overall Performance Score -->
    <div class="score-section">
        <div class="score-number">{{ $audit->performance_score ?? 0 }}/100</div>
        <div class="score-label">Performance Score</div>
        
        <div class="category-scores">
            <div class="category-score">
                <div style="font-size: 24px; font-weight: bold;">{{ $audit->performance_score ?? 0 }}</div>
                <div style="font-size: 12px; opacity: 0.9;">Performance</div>
            </div>
            <div class="category-score">
                <div style="font-size: 24px; font-weight: bold;">{{ $audit->accessibility_score ?? 0 }}</div>
                <div style="font-size: 12px; opacity: 0.9;">Accessibility</div>
            </div>
            <div class="category-score">
                <div style="font-size: 24px; font-weight: bold;">{{ $audit->best_practices_score ?? 0 }}</div>
                <div style="font-size: 12px; opacity: 0.9;">Best Practices</div>
            </div>
            <div class="category-score">
                <div style="font-size: 24px; font-weight: bold;">{{ $audit->seo_score ?? 0 }}</div>
                <div style="font-size: 12px; opacity: 0.9;">SEO</div>
            </div>
        </div>
    </div>
    
    <!-- Basic Information -->
    <div class="section">
        <h3>Analysis Information</h3>
        <div class="audit-info">
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Website URL:</span>
                    <span class="info-value">{{ $audit->url }}</span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Analysis Date:</span>
                    <span class="info-value">{{ $audit->analyzed_at->format('M j, Y g:i A') }}</span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Device Type:</span>
                    <span class="info-value">{{ ucfirst($audit->strategy) }}</span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Lighthouse Version:</span>
                    <span class="info-value">{{ $audit->lighthouse_version ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Core Web Vitals -->
    <div class="section">
        <h3>Core Web Vitals</h3>
        <div class="vitals-grid">
            <div class="vital-card">
                <div class="vital-value {{ $audit->fcp_rating === 'good' ? 'rating-good' : ($audit->fcp_rating === 'needs-improvement' ? 'rating-needs-improvement' : 'rating-poor') }}">
                    {{ $audit->formatted_fcp }}
                </div>
                <div class="vital-name">First Contentful Paint</div>
                <div class="vital-description">Time until first content appears</div>
                <div class="vital-rating {{ $audit->fcp_rating === 'good' ? 'rating-good-bg' : ($audit->fcp_rating === 'needs-improvement' ? 'rating-needs-improvement-bg' : 'rating-poor-bg') }}">
                    {{ $audit->fcp_rating === 'good' ? 'Good' : ($audit->fcp_rating === 'needs-improvement' ? 'Needs Improvement' : 'Poor') }}
                </div>
            </div>
            
            <div class="vital-card">
                <div class="vital-value {{ $audit->lcp_rating === 'good' ? 'rating-good' : ($audit->lcp_rating === 'needs-improvement' ? 'rating-needs-improvement' : 'rating-poor') }}">
                    {{ $audit->formatted_lcp }}
                </div>
                <div class="vital-name">Largest Contentful Paint</div>
                <div class="vital-description">Time until largest content loads</div>
                <div class="vital-rating {{ $audit->lcp_rating === 'good' ? 'rating-good-bg' : ($audit->lcp_rating === 'needs-improvement' ? 'rating-needs-improvement-bg' : 'rating-poor-bg') }}">
                    {{ $audit->lcp_rating === 'good' ? 'Good' : ($audit->lcp_rating === 'needs-improvement' ? 'Needs Improvement' : 'Poor') }}
                </div>
            </div>
            
            <div class="vital-card">
                <div class="vital-value {{ $audit->tbt_rating === 'good' ? 'rating-good' : ($audit->tbt_rating === 'needs-improvement' ? 'rating-needs-improvement' : 'rating-poor') }}">
                    {{ $audit->formatted_tbt }}
                </div>
                <div class="vital-name">Total Blocking Time</div>
                <div class="vital-description">Time page is blocked from input</div>
                <div class="vital-rating {{ $audit->tbt_rating === 'good' ? 'rating-good-bg' : ($audit->tbt_rating === 'needs-improvement' ? 'rating-needs-improvement-bg' : 'rating-poor-bg') }}">
                    {{ $audit->tbt_rating === 'good' ? 'Good' : ($audit->tbt_rating === 'needs-improvement' ? 'Needs Improvement' : 'Poor') }}
                </div>
            </div>
            
            <div class="vital-card">
                <div class="vital-value {{ $audit->cls_rating === 'good' ? 'rating-good' : ($audit->cls_rating === 'needs-improvement' ? 'rating-needs-improvement' : 'rating-poor') }}">
                    {{ $audit->formatted_cls }}
                </div>
                <div class="vital-name">Cumulative Layout Shift</div>
                <div class="vital-description">Amount of unexpected layout shift</div>
                <div class="vital-rating {{ $audit->cls_rating === 'good' ? 'rating-good-bg' : ($audit->cls_rating === 'needs-improvement' ? 'rating-needs-improvement-bg' : 'rating-poor-bg') }}">
                    {{ $audit->cls_rating === 'good' ? 'Good' : ($audit->cls_rating === 'needs-improvement' ? 'Needs Improvement' : 'Poor') }}
                </div>
            </div>
        </div>
    </div>
    
    <!-- Additional Metrics -->
    <div class="section page-break">
        <h3>Additional Metrics</h3>
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-number rating-good">{{ $audit->formatted_speed_index }}</div>
                <div class="metric-label">Speed Index</div>
                <div class="metric-description">How quickly content appears</div>
            </div>
            
            <div class="metric-card">
                <div class="metric-number rating-good">{{ number_format($audit->time_to_interactive, 1) }}s</div>
                <div class="metric-label">Time to Interactive</div>
                <div class="metric-description">When page becomes fully interactive</div>
            </div>
            
            <div class="metric-card">
                <div class="metric-number rating-good">{{ number_format($audit->first_meaningful_paint, 1) }}s</div>
                <div class="metric-label">First Meaningful Paint</div>
                <div class="metric-description">Time until primary content appears</div>
            </div>
            
            <div class="metric-card">
                <div class="metric-number rating-good">{{ number_format($audit->max_potential_fid, 0) }}ms</div>
                <div class="metric-label">Max Potential FID</div>
                <div class="metric-description">Maximum input delay</div>
            </div>
        </div>
    </div>
    
    <!-- Performance Recommendations -->
    @if(!empty($recommendations))
    <div class="section">
        <div class="recommendations">
            <h3>Performance Recommendations</h3>
            @foreach($recommendations as $recommendation)
            <div class="recommendation-item priority-{{ $recommendation['priority'] }}">
                <div class="recommendation-title">
                    {{ $recommendation['title'] }}
                    <span style="float: right; font-size: 12px; color: #64748b;">Impact: {{ $recommendation['impact'] }}</span>
                </div>
                <div class="recommendation-description">
                    {{ $recommendation['description'] }}
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    
    <!-- Performance Summary -->
    <div class="section">
        <h3>Performance Summary</h3>
        <div style="background: #f8fafc; padding: 20px; border-radius: 8px;">
            <div style="margin-bottom: 15px;">
                <strong>Overall Assessment:</strong>
                @if($audit->performance_score >= 90)
                    <span class="rating-good">Excellent performance! Your site loads quickly and provides a great user experience.</span>
                @elseif($audit->performance_score >= 50)
                    <span class="rating-needs-improvement">Good performance with room for improvement. Consider optimizing the areas highlighted in the recommendations.</span>
                @else
                    <span class="rating-poor">Performance needs significant improvement. Focus on the critical issues identified in the recommendations.</span>
                @endif
            </div>
            
            <div style="margin-bottom: 15px;">
                <strong>Core Web Vitals Status:</strong>
                @php
                    $goodVitals = 0;
                    $goodVitals += ($audit->fcp_rating === 'good') ? 1 : 0;
                    $goodVitals += ($audit->lcp_rating === 'good') ? 1 : 0;
                    $goodVitals += ($audit->tbt_rating === 'good') ? 1 : 0;
                    $goodVitals += ($audit->cls_rating === 'good') ? 1 : 0;
                @endphp
                
                @if($goodVitals >= 3)
                    <span class="rating-good">{{ $goodVitals }}/4 Core Web Vitals are in the "Good" range.</span>
                @elseif($goodVitals >= 2)
                    <span class="rating-needs-improvement">{{ $goodVitals }}/4 Core Web Vitals are in the "Good" range.</span>
                @else
                    <span class="rating-poor">Only {{ $goodVitals }}/4 Core Web Vitals are in the "Good" range.</span>
                @endif
            </div>
            
            <div>
                <strong>Key Strengths:</strong>
                <ul style="margin: 5px 0 0 20px;">
                    @if($audit->performance_score >= 90)
                        <li>Excellent overall performance score</li>
                    @endif
                    @if($audit->fcp_rating === 'good')
                        <li>Fast First Contentful Paint</li>
                    @endif
                    @if($audit->lcp_rating === 'good')
                        <li>Optimized Largest Contentful Paint</li>
                    @endif
                    @if($audit->cls_rating === 'good')
                        <li>Minimal layout shifts</li>
                    @endif
                    @if($audit->seo_score >= 90)
                        <li>Strong SEO optimization</li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <p>This report was generated by SEO Audit Pro on {{ now()->format('F j, Y \a\t g:i A') }}</p>
        <p>Performance data provided by Google PageSpeed Insights API</p>
        <p>For ongoing performance monitoring and optimization tips, visit our dashboard.</p>
    </div>
</body>
</html>
