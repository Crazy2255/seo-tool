<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Competitor Analysis Report - {{ $analysis->domain }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #3B82F6;
        }
        .header h1 {
            color: #3B82F6;
            font-size: 24px;
            margin: 0;
        }
        .header p {
            color: #666;
            font-size: 14px;
            margin: 5px 0;
        }
        .section {
            margin-bottom: 25px;
        }
        .section h2 {
            color: #1F2937;
            font-size: 16px;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #E5E7EB;
        }
        .metrics {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .metric {
            text-align: center;
            padding: 10px;
            background: #F9FAFB;
            border-radius: 8px;
            width: 22%;
        }
        .metric-value {
            font-size: 20px;
            font-weight: bold;
            color: #3B82F6;
        }
        .metric-label {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }
        .tools-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }
        .tool-item {
            padding: 8px;
            background: #F3F4F6;
            border-radius: 4px;
            font-size: 10px;
        }
        .tool-category {
            font-weight: bold;
            color: #374151;
            margin-bottom: 5px;
        }
        .recommendations {
            background: #FEF3C7;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .recommendations h3 {
            color: #92400E;
            margin-top: 0;
            font-size: 14px;
        }
        .recommendations ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .recommendations li {
            margin-bottom: 5px;
            font-size: 11px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #E5E7EB;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .two-column {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }
        .column {
            flex: 1;
        }
        .score-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #3B82F6;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Competitor Analysis Report</h1>
        <p><strong>Domain:</strong> {{ $analysis->domain }}</p>
        <p><strong>Generated:</strong> {{ now()->format('M d, Y \a\t g:i A') }}</p>
        <p><strong>Last Scanned:</strong> {{ $analysis->last_scanned_at ? $analysis->last_scanned_at->format('M d, Y \a\t g:i A') : 'N/A' }}</p>
    </div>

    <div class="section">
        <h2>Overview</h2>
        <div class="metrics">
            <div class="metric">
                <div class="metric-value">{{ $analysis->strategy_score }}</div>
                <div class="metric-label">Strategy Score</div>
            </div>
            <div class="metric">
                <div class="metric-value">{{ $analysis->total_tools_detected }}</div>
                <div class="metric-label">Tools Detected</div>
            </div>
            <div class="metric">
                <div class="metric-value">{{ $analysis->strategy_summary_count }}</div>
                <div class="metric-label">Strategies</div>
            </div>
            <div class="metric">
                <div class="metric-value">{{ $analysis->scan_status === 'completed' ? 'Complete' : 'Incomplete' }}</div>
                <div class="metric-label">Scan Status</div>
            </div>
        </div>
    </div>

    @if($analysis->detected_tools && count($analysis->detected_tools) > 0)
    <div class="section">
        <h2>Detected Tools & Technologies</h2>
        <div class="tools-grid">
            @foreach($analysis->detected_tools as $category => $tools)
            <div class="tool-item">
                <div class="tool-category">{{ ucfirst(str_replace('_', ' ', $category)) }}</div>
                @if(is_array($tools))
                    @foreach($tools as $tool)
                    <div>• {{ $tool }}</div>
                    @endforeach
                @else
                    <div>• {{ $tools }}</div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($analysis->strategy_insights && count($analysis->strategy_insights) > 0)
    <div class="section">
        <h2>Strategy Insights</h2>
        <div class="two-column">
            @foreach($analysis->strategy_insights as $insight)
            <div class="column">
                <h3 style="color: #3B82F6; font-size: 12px; margin-bottom: 8px;">{{ $insight['category'] ?? 'General' }}</h3>
                <p style="font-size: 11px; margin-bottom: 15px;">{{ $insight['description'] ?? $insight['insight'] ?? 'No description available' }}</p>
                @if(isset($insight['priority']))
                <p style="font-size: 10px; color: #666;"><strong>Priority:</strong> {{ $insight['priority'] }}</p>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($analysis->actionable_recommendations && count($analysis->actionable_recommendations) > 0)
    <div class="recommendations">
        <h3>Actionable Recommendations</h3>
        <ul>
            @foreach($analysis->actionable_recommendations as $recommendation)
            <li>{{ is_array($recommendation) ? $recommendation['title'] ?? $recommendation['recommendation'] ?? 'No recommendation available' : $recommendation }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if($analysis->competitive_advantages && count($analysis->competitive_advantages) > 0)
    <div class="section">
        <h2>Competitive Advantages Identified</h2>
        <ul style="padding-left: 20px;">
            @foreach($analysis->competitive_advantages as $advantage)
            <li style="margin-bottom: 8px; font-size: 11px;">{{ is_array($advantage) ? $advantage['advantage'] ?? json_encode($advantage) : $advantage }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="footer">
        <p>This report was generated by SEO Tools Platform on {{ now()->format('M d, Y \a\t g:i A') }}</p>
        <p>For internal use only. Data is based on publicly available information.</p>
    </div>
</body>
</html>
