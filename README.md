## Deployment 

### Install dependencies
```
composer install
```

### Migration
```
php artisan migrate
```

### Deploy
```
php artisan serve
```

### Run script to scrape the page
```
php artisan app:scrape-pinboard-links
```

## Things to improve
* Allow user to pass in the page they want to scrape
* Check if a link has already been scraped
* Set up an automation to check link status

## Libraries Used
### Fetching HTTP resources 
* symfony/browser-kit
* symfony/http-client
