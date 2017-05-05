# Blekkio

[![Join the chat at https://gitter.im/blekkio/Lobby](https://badges.gitter.im/blekkio/Lobby.svg)](https://gitter.im/blekkio/Lobby?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

Simple video database that harvests and connects data from YouTube,
[Vortex](https://www.uio.no/english/services/it/web/vortex/) and other sources.

Updates https://ub.uio.no/live

## Task scheduler

To activate the task scheduler, add this to `/etc/crontab`:

      *  *  *  *  * apache     php /path/to/blekkio/artisan schedule:run 1>> /dev/null 2>&1

## Local development

Requirements: PHP + [Composer](https://getcomposer.org), Node + NPM.

Setup:

	git clone https://github.com/scriptotek/blekkio.git
	cd blekkio
	composer install
	npm install
	npm run dev
	cp .env.example .env

Add credentials (SQL, WebDAV, Google APIs) to `.env`.
Local maintainers, see `\\kant\ub-felles\scriptotek\blekkio\README.txt`.

Add to `/etc/hosts`:

	127.0.0.1  blekkio.dev

Start dev server:

	php artisan serve --host=blekkio.dev --port=8000

