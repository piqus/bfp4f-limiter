bfp4f-limiter
=============

Battlefield Play4Free limiter script based on [bfp4f-rcon](https://github.com/piqus/bfp4f-rcon) class.

## Requirements ##

* PHP Interpreter 
* Database (Script supports 2 types of DB)
   *  MySQL, PostgreSQL or other database possible to connect via PDO.
   *  ... or MongoDB (You need to install [ext-mongo](http://pecl.php.net/package/mongo) for PHP)

### Optional

* Webserver (if you would like to run it continuously via web-browser instead of running in console/cmd/terminal)

## Installation ##

You may install bfp4f-limiter in two different ways. With [Composer](http://getcomposer.org/) (package dependency manager for php) or just manually.

Installtion guide was made for Windows OS. It may be also reproduced on LAMP or MAMP easily. If you have installed or you owned already webserver with php you may skip first 2 steps.

### Manual Installation ###

1. Download EasyPHP (php development) or other webserver with php interperter and mysql database (googlit: *wamp php mysql*).
2. Install it.
3. Download [bfp4f-rcon](https://github.com/piqus/bfp4f-rcon) ("Download .zip" button on right sidebar). Extract it.
4. Download [bfp4f-limiter](https://github.com/piqus/bfp4f-limiter/tree/stable) from *stable* branch ("Download .zip" button on right sidebar). Extract it.
5. Merge it to one folder. 
```
├── composer.json
├── DB-mongo.php
├── DB-pdosql.php
├── limiter-browser.php
├── limiter-config.php
├── limiter-console.php
├── limiter-script.php
├── limiter.sql
├── loop-limiter.sh
└── src
    └── T4G
        └── BFP4F
            └── Rcon
                ├── Base.php
                ├── Chat.php
                ├── Players.php
                ├── Server.php
                ├── Stats.php
                └── Support.php
```
6. Open limiter-config.php in your text/code editor.
7. Comment (add two slashes `//`) at the beginnig of the [line 29](https://github.com/piqus/bfp4f-limiter/blob/stable/limiter-config.php#L29)
8. Uncomment (delete two slashes) on [lines 31-35](https://github.com/piqus/bfp4f-limiter/blob/stable/limiter-config.php#L31-L35).
9. [Configure connection to database on lines 39-45](https://github.com/piqus/bfp4f-limiter/blob/stable/limiter-config.php#L39-L45). Password is supposed to be empty as default on many WAMPs. If you are sure (googlit) you don't have password to your mysql database leave `""` as no password.
10. Configure RCON conection
	* [On line 67](https://github.com/piqus/bfp4f-limiter/blob/stable/limiter-config.php#L67) type address to your gameserver (rcon).
    * [On line 70](https://github.com/piqus/bfp4f-limiter/blob/stable/limiter-config.php#L70) type port address (rcon).
    * [on line 76](https://github.com/piqus/bfp4f-limiter/blob/stable/limiter-config.php#L76) type rcon password.
11. Open phpmyadmin (look for it in easyphp's tray menu).
12. Create new database called "limiter".
13. Enter this database.
14. Import "limiter.sql" (Tab called import > upload .sql).
15. Move this directory to your `ProgramFiles/EasyPHP/htdocs` (web) folder.
16. Start Apache and MySQL services .
17. Run it in browser typing `http://localhost/limiter-browser.php`.

Installation finished. Now look up for configuration.

===

## Configuration ##

In `limiter-config.php` you have very long list what you could change and configure it as you prefer.

Please notice that mm_iga/mm_kicker has usually predefined default kick message (usually it is `piqus you are being kicked (reason: Unknown)`). Each kick message in `limiter-config.php` will **only replace** word *Unknown*.

===

## Run ##

You may run script continously running via browser or terminal. There are some webhostings which allows to run CRON script minutely, also this method is supported and recomended.

Running via browser. If you installed completely limiter script you may type in address bar http://localhost/path/to/limiter-browser.php

Running via console is much easier if you have ever used it to run php scripts. If you have exported to `$PATH` (System Env. Variable) location to your php(.exe) binary you may enter folder where you have installed limiter then execute `php limiter-console.php` or `sh  loop-limiter.sh` in terminal/console/cmd.exe/powershell. You don't have to export this variable. You could just type "global" path to php(.exe) i.e `C:/ProgramFiles/PHP/5.4/php.exe C:/Users/Admin/Webdocs/bfp4f-limiter/limiter-console.php`. 

It is worth noting that `sh` command is not supported out of the box in Windows OS. You could install GIT Bash (Mingw) form http://git-scm.com/ which includes many useful commands like `sh`, `ssh` over openssh, `grep`, `find`, `git` etc. etc.

===

## Composer

Project has been added to [Packagist](https://packagist.org/packages/piqus/bfp4f-limiter). You may invoke `composer create-project piqus/bfp4f-limiter:dev-stable`

***

## Contribution

Feel free to modify code. Each patch is moderated so don't be shy and share your idea! ;-)
