bfp4f-limiter
=============

Battlefield Play4Free weapon limiter script.

## Requirements ##

Weapon limiter script needs some goodies to work:

If you are using SQL database:

* PHP >= 5.3
* PHP PDO extension (included in PHP v5.3)

Likely all LAMP/WAMPs have MySQL database include, so I don't think so you need links to external databases.

If you are using MongoDB: 

* [http://www.mongodb.org/](MongoDB) or SQL database like **MySQL**.
* PHP mongo extension (I recommend to download it from [http://php.net/manual/en/mongo.installation.php](PECL)). If you are using MongoDB as database.

You can get MongoDB database from [https://www.mongohq.com/home](MongoLab) or [https://mongolab.com/welcome/](MongoHQ). 

## Installation ##

### Composer ###

Run via console:

```
composer create-project piqus/bfp4f-limiter path/to/where/should/be/limiter -s dev
```

### By extracting .ZIPs ###

Download `.ZIP` file of bfp4f-limiter project. You can find "Download" button on right sidebar of github.

This project (bfp4f-limiter) requires `piqus/bfp4f-rcon` so you also you need to download .ZIP from bfp4f-rcon repo.

## Use ##

It depends on *environment* and operating system.

### Windows ###

If you are using Windows and you haven't installed any WAMP package. You should try [http://sourceforge.net/projects/winginx/](winginx). It has all features what you need (except downloaded composer). Move piqus/bfp4f-limiter (and bfp4f-rcon) to you winginx `public_html` directory. 

Next steps:

* If you are using MySQL database:
	* Open *phpmyadmin*, 
	* Create new database called `limiter`, 
	* Open *limiter* database viewport,
	* Click *Import*,
	* *Import from file*: `limiter.sql`.
* Personalize (open limiter-config.php) it via code editor like Notepad++ or SublimeText or even standard Notepad. 
* Open in your browser [http://localhost/limiter-browser.php](http://localhost/limiter-browser.php).

## Linux ##

On Linux It is much easier to make it working. 
Of course depends on your linux distro distribution installation of MongoDB or LAMP may be a little bit different. 

Step-by-step:

* Install LAMP - Apache (*nix), MySQL/MongoDB (if mongo -> install mongo-ext for php from pecl).
* Download by composer or do it manually by downloading .zips and unpacking it to one folder. Remember about path to vendors.
* If you are using MySQL database:
	* Open *phpmyadmin*, 
	* Create new database called `limiter`, 
	* Open *limiter* database viewport,
	* Click *Import*,
	* *Import from file*: `limiter.sql`.
* Edit limiter-config.php
	* Console: `sh loop-limiter.sh` or `php limiter-console.php`
	* CRON: Paste to `minutely/` directory
	* Browser: Open [http://localhost/limiter-browser.php](http://localhost/limiter-browser.php) 
	(or other path to this file according to your webhost/vhost configuration)

