bfp4f-limiter
=============

Battlefield Play4Free weapon limiter script.

## Requirements ##

Weapon limiter script needs some goodies to work:

If you are using MongoDB:

* PHP >= 5.3
* (MongoDB)[http://www.mongodb.org/] or SQL database like **MySQL**.
* PHP mongo extension (I recommend to download it from (PECL)[http://php.net/manual/en/mongo.installation.php]). If you are using MongoDB as database.
* PHP PDO extension (included in PHP v5.3)

You can get MongoDB database from (MongoLab)[https://www.mongohq.com/home] or (MongoHQ)[https://mongolab.com/welcome/]. 

## Installation ##

### Composer ###

Create `composer.json`. Copy this snippet (below \/). 

```js
{
	"require": {
		"piqus/bfp4f-limiter": "dev-master"
	}
}
```

Run `composer install` (or `php composer.phar install` if you don't have already installed composer globally"

### By extracting .ZIPs ###

Download `.ZIP` file of bfp4f-limiter project. You can find "Download" button on right sidebar of github.

This project (bfp4f-limiter) requires `piqus/bfp4f-rcon` so you also you need to download .ZIP from bfp4f-rcon repo.

## Use ##

It depends on *environment* and operating system.

### Windows ###

If you are using Windows and you haven't installed any WAMP package. You should try (winginx)[http://sourceforge.net/projects/winginx/]. It has all features what you need (except downloaded composer). Move piqus/bfp4f-limiter (and bfp4f-rcon) to you winginx `public_html` directory. 

Next steps:

* Personalize (open limiter-config.php) it via code editor like Notepad++ or SublimeText or even standard Notepad. 
* Open in your browser `(http://localhost/limiter-browser.php)[http://localhost/limiter-browser.php]`.

## Linux ##

On Linux It is much easier to make it working. 
Of course depends on your linux distro distribution installation of MongoDB or LAMP may be a little bit different. 

Step-by-step:

* Install LAMP - Apache (*nix), MySQL/MongoDB (if mongo -> install mongo-ext for php from pecl).
* Copy source files to apache *web* folder (/var/www/ or to your own vhost).
* Edit limiter-config.php
* Run via
	* Console: `sh loop-limiter.sh` or `php limiter-console.php`
	* CRON: Paste to `minutely/` directory
	* Browser: Open `http://localhost/limiter-browser.php` (or other path to this file according to your webhost/vhost configuration)

