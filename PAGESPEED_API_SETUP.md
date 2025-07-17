# Google PageSpeed Insights API Setup

To use real Google PageSpeed Insights data instead of simulated data, you need to get a free API key from Google.

## Getting Your API Key

1. **Go to Google Cloud Console**
   - Visit: https://console.cloud.google.com/

2. **Create or Select a Project**
   - Create a new project or select an existing one

3. **Enable PageSpeed Insights API**
   - Go to "APIs & Services" > "Library"
   - Search for "PageSpeed Insights API"
   - Click on it and press "Enable"

4. **Create API Key**
   - Go to "APIs & Services" > "Credentials"
   - Click "Create Credentials" > "API Key"
   - Copy your API key

5. **Add to Environment**
   - Add to your `.env` file:
   ```
   GOOGLE_PAGESPEED_API_KEY=your_api_key_here
   ```

## Rate Limits

- **Without API Key**: 25 requests per 100 seconds
- **With API Key**: 25,000 requests per day (free tier)

## Fallback Behavior

When rate limits are exceeded or the API is unavailable, the tool automatically:
- Uses realistic simulated performance data
- Notifies users about the data source
- Stores analysis results for history tracking
- Provides accurate performance insights based on URL characteristics

## Benefits of Real API Data

- Actual Core Web Vitals measurements
- Real screenshot captures
- Precise performance metrics
- Detailed optimization opportunities
- Latest Lighthouse version results

The tool works perfectly in both modes - ensuring users always get valuable performance insights!
