# Blekkio

[![Join the chat at https://gitter.im/scriptotek/blekkio](https://badges.gitter.im/scriptotek/blekkio.svg)](https://gitter.im/scriptotek/blekkio?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

Simple video database that harvests and connects data from YouTube,
[Vortex](https://www.uio.no/english/services/it/web/vortex/) and other sources.

Updates https://ub.uio.no/live

## Task scheduler

To activate the task scheduler, add this to `/etc/crontab`:

      *  *  *  *  * apache     php /path/to/blekkio/artisan schedule:run 1>> /dev/null 2>&1

## Task queue

To activate the [queue worker](https://laravel.com/docs/5.4/queues), set `QUEUE_DRIVER=database`
in `.env` and add a supervisor configuration file `/etc/supervisord.d/blekkio.ini`:

```
[program:blekkio]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/blekkio/artisan queue:work database --sleep=3 --tries=3
autostart=true
autorestart=true
user=apache
numprocs=1
redirect_stderr=true
stdout_logfile=/path/to/blekkio/storage/logs/worker.log
```

Then

```
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start blekkio:*
```

## Local development

Requirements: PHP + [Composer](https://getcomposer.org), Node + NPM.

Setup:

	git clone https://github.com/scriptotek/blekkio.git
	cd blekkio
	composer install
	npm install
	npm run dev
	cp .env.example .env

Note: php-saml still depends on mcrypt as of 2017-05-09. To avoid having to install it, run

    composer install --ignore-platform-reqs

This is ok since we don't use encrypted SAML messages. See [php-saml#84](https://github.com/aacotroneo/laravel-saml2/issues/84).

Add credentials (SQL, WebDAV, Google APIs) to `.env`.
Local maintainers, see `\\kant\ub-felles\scriptotek\blekkio\README.txt`.

Add to `/etc/hosts`:

	127.0.0.1  blekkio.dev

Start dev server:

	php artisan serve --host=blekkio.dev --port=8000

