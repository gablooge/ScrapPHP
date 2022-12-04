# ScrapPHP
Web Scraper Application that allows to take URL (input box on front-end page), validate URL, show on front-end and save into database the following information: URL, duration and time of the current request,  count of html tags for current request, domain name of current request; show on front-end historical info: count of html tags by domain for all requests with domain name (summary information).

### Build and run local mysql with docker
`docker-compose up -d --build database`

### make migration
`php bin/console make:migration`

### make entity
`php bin/console make:entity`

### migrate database
`php bin/console doctrine:migrations:migrate`

### make controller
`php bin/console make:controller UrlRequestController`

### run server
`symfony server:start`

### List all existing routes
`php bin/console debug:router`

### Run Lint
- Command: `./vendor/bin/phplint`
- More: https://github.com/overtrue/phplint
