# AI Web Scraping Selector System

An intelligent web scraping management system that dynamically updates and maintains CSS selectors using AI analysis.

## Features

- Automatic selector repair using AI
- Selector change history tracking
- Rate limiting on API endpoints
- Robust error handling and logging

## Tech Stack

- Backend: Laravel 12
- Frontend: Alpine.js + Tailwind CSS
- Database: SQLite (configurable)
- AI Integration: DeepSeek AI

## Deployment Guide

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js 16 or higher
- npm
- Web server (Apache/Nginx)

### Server Setup

1. Clone the repository:
   ```bash
   git clone <repository-url>
   cd aiselector
   ```

2. Install PHP dependencies:
   ```bash
   composer install --optimize-autoloader --no-dev
   ```

3. Install Node.js dependencies and build assets:
   ```bash
   npm install
   npm run build
   ```

4. Environment setup:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. Configure your `.env` file:
   ```
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-domain.com
   
   DB_CONNECTION=sqlite
   DB_DATABASE=/absolute/path/to/database.sqlite
   
   DEEPSEEK_API_KEY=your-api-key
   ```

6. Setup the database:
   ```bash
   touch database/database.sqlite
   php artisan migrate
   ```

7. Set proper permissions:
   ```bash
   chmod -R 755 storage bootstrap/cache
   ```

### Web Server Configuration

#### Nginx Configuration

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/aiselector/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### Apache Configuration

Make sure the `.htaccess` file in the `public` directory is present and has proper permissions.

### Post-Deployment

1. Clear all caches:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

2. Setup SSL (recommended):
   ```bash
   certbot --nginx -d your-domain.com
   ```

3. Setup a cron job for Laravel's scheduler:
   ```bash
   * * * * * cd /path/to/aiselector && php artisan schedule:run >> /dev/null 2>&1
   ```

### Maintenance

- Monitor the Laravel log files in `storage/logs`
- Regularly backup the SQLite database
- Keep dependencies updated:
  ```bash
  composer update --no-dev
  npm update
  ```

## Security Considerations

1. Ensure proper file permissions
2. Keep Laravel and all dependencies updated
3. Use HTTPS
4. Implement rate limiting
5. Store sensitive data in `.env`

## Troubleshooting

1. If the site is down:
   ```bash
   php artisan down
   # make fixes
   php artisan up
   ```

2. If assets are not loading:
   ```bash
   npm run build
   php artisan cache:clear
   ```

3. For database issues:
   ```bash
   php artisan migrate:status
   php artisan migrate:fresh --seed # Warning: This will reset the database
   ```

## Support

For issues and support, please create an issue in the repository or contact the development team.

## License

[MIT License](LICENSE.md)
