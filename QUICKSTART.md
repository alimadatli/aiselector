# AI Selector Quick Start Guide

## Introduction

AI Selector is an intelligent system that helps maintain web scraping selectors using AI. This guide will help you get started quickly.

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/aiselector.git
cd aiselector
```

2. Install dependencies:
```bash
composer install
npm install
```

3. Set up environment:
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure your database in `.env`:
```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

5. Run migrations:
```bash
php artisan migrate
```

## Basic Usage

### 1. Adding a Website

1. Log into the dashboard
2. Click "Add Website"
3. Enter website details:
   - Name: Descriptive name
   - URL: Base URL of the website
   - Status: Active/Inactive

### 2. Creating Selectors

For each data point you want to extract:

1. Select your website
2. Click "Add Selector"
3. Enter selector details:
   - Name: What this selector extracts
   - Description: Detailed description of the data
   - CSS Selector: Initial CSS selector
   - Status: Active/Inactive

Example selector:
```json
{
    "name": "Product Price",
    "description": "Extracts the main product price. Should be a numeric value with currency symbol ($). Located near the product title, usually in larger font.",
    "selector": ".price-main",
    "is_active": true
}
```

### 3. Integrating with Your Scraper

1. Get your API key from the dashboard
2. Use our API client or make direct API calls:

```python
from aiselector import AISelectorClient

client = AISelectorClient(
    api_key='your-api-key',
    base_url='https://your-domain.com/api'
)

# Get latest selectors
selectors = client.get_selectors(website_id=1)

# Validate scraped data
validation = client.validate_data(
    website_id=1,
    url='https://example.com/product',
    data={'price': '$99.99'},
    selectors={
        'price': '.price-main'
    }
)

# If validation fails, send HTML for analysis
if not validation['is_valid']:
    analysis = client.analyze_html(
        website_id=1,
        url='https://example.com/product',
        html=page_html,
        failed_selectors=validation['failed_selectors']
    )
```

### 4. Monitoring

1. Check the dashboard for:
   - Selector health status
   - Recent changes
   - Validation success rates
   - AI analysis logs

2. Set up alerts for:
   - Selector failures
   - Validation errors
   - Rate limit warnings

## Best Practices

1. **Selector Descriptions**
   - Be specific about expected data format
   - Mention visual or structural context
   - Include example values

2. **Validation**
   - Validate data as soon as it's scraped
   - Send full HTML only when necessary
   - Cache successful selectors

3. **Error Handling**
   - Implement retry logic
   - Use exponential backoff
   - Log all failures

## Common Issues

1. **Selector Not Found**
   - Check if website structure changed
   - Verify selector in browser
   - Review recent changes in dashboard

2. **Invalid Data**
   - Compare against description
   - Check for format changes
   - Update validation rules

3. **Rate Limits**
   - Implement caching
   - Batch requests when possible
   - Monitor usage in dashboard

## Getting Help

- Documentation: https://docs.aiselector.com
- API Reference: See API.md
- Support: support@aiselector.com
- Issues: https://github.com/aiselector/issues
