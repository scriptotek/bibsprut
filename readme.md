# Blekkio

Simple video database that harvests and connects data from YouTube,
[Vortex](https://www.uio.no/english/services/it/web/vortex/) and other sources.

Updates https://ub.uio.no/live

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

