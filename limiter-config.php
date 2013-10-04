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
 * @version  0.3
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
// foreach (glob("src/T4G/BFP4F/Rcon/*.php") as $class)
// {
//     require_once $class;
// }

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

    /* ------------------------------------
     * GENERAL
     * ------------------------------------
     */
    
    // Server IP
    'general.server_ip' => "127.0.0.1",

    //Server PORT
    'general.server_port' => (int) 1337,

    // Server PASSWORD
    'general.server_password' => "rcon_password",

    // Is a whole script enabled (force disable script)? 
    'general.script_enabled' => true,

    // Ignore VIPs
    'general.ignore_vips' => false,

    // Ignore selected player to check their weapon slots?
    'general.ignored_players_enabled' => false,

    // List of ignored players
    // # Profile IDs spearated by commas
    // # Use strings  (values in quotes) instead of numeric
    'general.ignored_players' => array(
            "2627733530",
        ),

    /* ------------------------------------
     * DATABASE
     * ------------------------------------
     */

    // Collection/Table of Logs
    'db.colLogs' => 'logs',

    // Collection/Table of Cache
    'db.colCache' => 'cache',
    
    /* ------------------------------------
     * WEAPON LIMITER SCRIPT
     * ------------------------------------
     * + Weapon Limiter
     * + Prebuy Limiter
     */

    // Kick for carrying disallowed weapon
    'weaponLimiter.weapon_limiter_enabled' => true,

    // Which weapons are disallowed on server
    // # You may find IDs of weapon in (vendor/piqus/bfp4f-rcon/)src/T4G/BFP4F/Rcon/Support.php
    'weaponLimiter.weapon_limiter_restricted_guns'  => array(
            3048, // MG36
            3120, // FAMAS
        ),

    // Kick for prebuy?
    'weaponLimiter.prebuy_limiter_enabled' => true,

    // Which weapons are checking for prebuy thingie
    'weaponLimiter.prebuy_limiter_restricted_guns' => array(
            3127, // L85A2
            3128, // PKP PECHENEG
            3122, // AS-VAL
        ),

    // If selected player was kicked how long (in minutes) he won't be able to rejoin server
    'weaponLimiter.cache_threshold' => (int) 15,

    // Custom Autokick Message
    // # %player% - person managed to leave, soldier name
    // # %weapon% - found gun which violates Weapon or Prebuy Limiter
    // # %type% - "Prebuy Limiter" or "Weapon Limiter"
    // # %script% - "Prebuy Limiter" or "Weapon Limiter"
    // # %level% - soldier level
    // # %kit% - soldier class
    'weaponLimiter.custom_message' => "%player% you are being autokicked (%script% - %weapon%)",

    
    // Custom Global Message triggered when player is being kicked (when script uses loadout cache from DB)
    // # %player% - person managed to leave, soldier name
    // # %weapon% - found gun which violates Weapon or Prebuy Limiter
    // ! Set up as empty string ("") or type (bool) false if you would like to disable this feature
    'weapon.cache_msg' => "|ccc| %player% |ccc| please back in next X minutes because we are using cache",

    /* ------------------------------------
     * LEVEL LIMITER SCRIPT
     * ------------------------------------
     */
    
    // Enable Level Limiter?
    'levelLimiter.script_enabled' => true,

    // Kick soldiers below X level (not equal)
    // # -1 to disable
    // #  0 to kick -1 glitchers
    'levelLimiter.kick_below_level' => (int) 0,

    // Kick soldiers above X level (not equal)
    // # 30 to disable
    'levelLimiter.kick_above_level' => (int) 30,

    // Custom Autokick Message
    // # %player% - person managed to leave, soldier name
    // # %type% - "Below" or "Above"
    // # %script% - "Level Limiter"
    // # %level% - soldier level
    // # %kit% - soldier class
    // # Note: You may concatenate required levels using . (dot) operator.
    'levelLimiter.custom_message' => "%player% you are being autokicked (level required: 0-30)",

    /* ------------------------------------
     * CLASS LIMITER SCRIPT
     * ------------------------------------
     */
    
    // Enable Class Limiter?
    'classLimiter.script_enabled' => false,

    // How many assaults can be on server per one team?
    'classLimiter.max_assaults' => (int) 16,

    // How many engineers can be on server per one team?
    'classLimiter.max_engineers' => (int) 16,

    // How many medics can be on server per one team?
    'classLimiter.max_medics' => (int) 16,

    // How many recons can be on server per one team?
    'classLimiter.max_recons' => (int) 16,

    // Custom Autokick Message
    // # %player% - person managed to leave, soldier name
    // # %script% - "Class Limiter"
    // # %level% - soldier level
    // # %kit% - soldier class
    'classLimiter.custom_message' => "%player% you are being autokicked (%script%; %kit%)",

);

?>