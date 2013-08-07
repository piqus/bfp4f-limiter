<?php
/**
 * Weapon Limiter 
 * 
 * Configuration File
 * 
 * Console script for Battlefield Play4Free community.
 * Kicks players with forbidden weapons like shotguns on selected server.
 * 
 * @category BFP4F
 * @package  limiter
 * @author   piqus <ovirendo@gmail.com>
 * @license  MIT http://opensource.org/licenses/MIT
 * @version  0.2
 * @link     https://github.com/piqus/bfp4f-limiter
 */

/* Environment Configuration
 ***************************/

ini_set('max_execution_time', 20);
date_default_timezone_set('Europe/London');

// Load composer vendors 
define('VENDOR_DIR', __DIR__ . '/vendor');

/* Load Classes for COMPOSER
 ***************************/
require_once VENDOR_DIR.'/autoload.php';

//# or if you don't have composer #//
// require_once "src/T4G/BFP4F/Rcon/Base.php";
// require_once "src/T4G/BFP4F/Rcon/Players.php";
// require_once "src/T4G/BFP4F/Rcon/Chat.php";
// require_once "src/T4G/BFP4F/Rcon/Server.php";
// require_once "src/T4G/BFP4F/Rcon/Support.php";
// require_once "src/T4G/BFP4F/Rcon/Stats.php";

/* Connect to DB 
 ********************/
define('DB_TYPE', 'mysql');

define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_USER', 'root');
define('DB_PASS', 'password');
define('DB_NAME', 'limiter');

if (DB_TYPE=="mongodb") {
    require_once __DIR__ . '/DB-mongo.php';
} else {
    require_once __DIR__ . '/DB-pdosql.php';
}

$db = new DB();


/* Limiter Configuration
 ***********************/

$configs = array(

    // If selected player was kicked how many minutes he won't be able to join server
    'cacheThres' => 30,

    // Collection/Table of Logs
    'colLogs' => 'logs',

    // Collection/Table of Cache
    'colCache' => 'cache',

    // Ignore VIPs
    "ignVIP" => true,

    // Ignore selected player to check their weapon slots?
    'ignored_members_enabled' => false,

    // List of ignored players:
    'ignored_members' => array(
        // pid stands for profile_id
        // sid stands for soldier_id
        array('pid' => "2627733530", 'sid' => "609452444"),
        array('pid' => "2627733530", 'sid' => "611528041"),
    ),

    // Kick for prebuy?
    'prebuy_enabled' => false,

    // Which weapons are checking for prebuy thingie
    'prebuy_restricted' => array(
        3000, 3008
    ),

    // Weapon limiter enabled?
    "enabled" => true,

    // Which weapons are disallowed on server
    "restrGuns"  => array(
        3000, 3024
    ),

    // Custom Autokick Message
    "cstMessage" => "%player you are being autokicked for %weapon",

    // Server IP
    'server_ip' => "127.0.0.0",

    //Server PORT
    'server_port' => "27100",

    // Server PASSWORD
    'server_password' => "password",
);

?>