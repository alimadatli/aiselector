# AI Selector API Documentation

## Overview

AI Selector is an intelligent web scraping management system that helps maintain and automatically repair CSS selectors when website structures change. This documentation covers the API endpoints available for integrating with your web scraping system.

## Base URL

```
https://your-domain.com/api
```

## Authentication

All API requests require a valid API token sent in the header:

```
Authorization: Bearer your-api-token
```

## API Endpoints

### Website Management

#### List Websites

```http
GET /websites
```

Response:
```json
{
    "data": [
        {
            "id": 1,
            "name": "Example Website",
            "url": "https://example.com",
            "is_active": true,
            "created_at": "2025-03-03T14:00:00Z",
            "updated_at": "2025-03-03T14:00:00Z"
        }
    ]
}
```

#### Create Website

```http
POST /websites
```

Request Body:
```json
{
    "name": "Example Website",
    "url": "https://example.com",
    "is_active": true
}
```

### Selector Management

#### List Selectors for Website

```http
GET /websites/{website_id}/selectors
```

Response:
```json
{
    "data": [
        {
            "id": 1,
            "name": "Product Price",
            "description": "Extracts the product price in USD format",
            "selector": ".price-value",
            "is_active": true,
            "last_validated": "2025-03-03T14:00:00Z"
        }
    ]
}
```

#### Create Selector

```http
POST /websites/{website_id}/selectors
```

Request Body:
```json
{
    "name": "Product Price",
    "description": "Extracts the product price in USD format",
    "selector": ".price-value",
    "is_active": true
}
```

### Scraper Integration

#### Validate Scraped Data

```http
POST /scraper/validate
```

Request Body:
```json
{
    "website_id": 1,
    "url": "https://example.com/product",
    "data": {
        "price": "$99.99",
        "title": "Example Product"
    },
    "selectors_used": {
        "price": ".price-value",
        "title": ".product-title"
    }
}
```

Response:
```json
{
    "is_valid": true,
    "missing_fields": [],
    "invalid_fields": [],
    "updated_selectors": {}
}
```

#### Analyze HTML for New Selectors

```http
POST /scraper/analyze
```

Request Body:
```json
{
    "website_id": 1,
    "url": "https://example.com/product",
    "html": "<html>...</html>",
    "failed_selectors": [
        {
            "id": 1,
            "name": "Product Price",
            "current_selector": ".price-value"
        }
    ]
}
```

Response:
```json
{
    "updated_selectors": {
        "1": {
            "old_selector": ".price-value",
            "new_selector": ".new-price-class",
            "confidence": 0.95
        }
    }
}
```

## Error Handling

The API uses standard HTTP status codes and returns detailed error messages:

```json
{
    "error": {
        "code": "validation_error",
        "message": "The provided data was invalid",
        "details": {
            "url": ["The url field is required"]
        }
    }
}
```

Common Status Codes:
- 200: Success
- 201: Created
- 400: Bad Request
- 401: Unauthorized
- 403: Forbidden
- 404: Not Found
- 422: Validation Error
- 429: Too Many Requests
- 500: Server Error

## Rate Limiting

The API implements rate limiting to ensure system stability:
- 60 requests per minute for validation endpoints
- 1000 requests per day for analysis endpoints

Rate limit headers are included in responses:
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1583234567
```

## Integration Example

Here's a Python example of integrating with the API:

```python
import requests

class AISelectorClient:
    def __init__(self, api_key, base_url):
        self.api_key = api_key
        self.base_url = base_url
        self.headers = {
            'Authorization': f'Bearer {api_key}',
            'Content-Type': 'application/json'
        }
    
    def get_selectors(self, website_id):
        response = requests.get(
            f'{self.base_url}/websites/{website_id}/selectors',
            headers=self.headers
        )
        return response.json()
    
    def validate_data(self, website_id, url, data, selectors):
        payload = {
            'website_id': website_id,
            'url': url,
            'data': data,
            'selectors_used': selectors
        }
        response = requests.post(
            f'{self.base_url}/scraper/validate',
            headers=self.headers,
            json=payload
        )
        return response.json()
    
    def analyze_html(self, website_id, url, html, failed_selectors):
        payload = {
            'website_id': website_id,
            'url': url,
            'html': html,
            'failed_selectors': failed_selectors
        }
        response = requests.post(
            f'{self.base_url}/scraper/analyze',
            headers=self.headers,
            json=payload
        )
        return response.json()
```

## Best Practices

1. **Selector Description Quality**
   - Provide detailed descriptions of what the selector should extract
   - Include expected data format and validation rules
   - Mention any specific patterns or identifiers

2. **Error Handling**
   - Implement exponential backoff for rate limits
   - Cache selector results to reduce API calls
   - Handle validation failures gracefully

3. **Performance**
   - Batch validate multiple selectors when possible
   - Only send failed selectors for analysis
   - Compress HTML content when sending for analysis

4. **Security**
   - Never expose your API key in client-side code
   - Validate and sanitize all input data
   - Use HTTPS for all API requests

## Support

For technical support or feature requests, please contact:
- Email: support@aiselector.com
- Documentation: https://docs.aiselector.com
- GitHub Issues: https://github.com/aiselector/issues
