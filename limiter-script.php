<?php
/**
 * Weapon Limiter
 *
 * Script
 * 
 * Console script for Battlefield Play4Free community.
 * Kicks players with forbidden weapons like shotguns on selected server.
 * 
 * @category BFP4F
 * @package  limiter
 * @author   piqus <ovirendo@gmail.com>
 * @license  MIT http://opensource.org/licenses/MIT
 * @version  0.3.2
 * @link     https://github.com/piqus/bfp4f-limiter
 */

/* Instantiate namespace
 ***********************/
use T4G\BFP4F\Rcon as rcon;
$rc = new rcon\Base();

/* Is limiter off? 
 ********************/
if ($configs['general.script_enabled'] === false) {
    echo('Limiter is switched off'.PHP_EOL);
    exit(1);
}

/* Connect to server 
 ********************/
$rc->ip = $configs['general.server_ip'];
$rc->port = (int) $configs['general.server_port'];
$rc->pwd = $configs['general.server_password'];

$rc->connect($cn, $cs);

if ($cn !== 0) {
    $err = "E: Game server is not responding;" . PHP_EOL.
           "E: Invalid credentials or server is down;" . PHP_EOL.
           "E: $cs ($cn)" . PHP_EOL;
    error_log($err);
    echo($err);
    exit(1);
}

$rc->init();

/* Retrieve data from game server 
 ********************************/

// Create Chat Object
$chat = new rcon\Chat();

// Create Player Object
$rcp = new rcon\Players();

// Get Players Info
$players = $rcp->fetch();

// Toolbox
$sup = new rcon\Support();

/* DO NOT CHANGE!
 * Declare counters used to class and player limiter
 ***************************************************/
$classCounter[1] = array('assaults' => 0, 'engineers' => 0, 'medics' => 0, 'recons' => 0, );
$classCounter[2] = array('assaults' => 0, 'engineers' => 0, 'medics' => 0, 'recons' => 0, );

/* Run Limiter
 *************/

foreach ($players as $player) {

    // Temporary variable to detect is (not) used data from cache
    $usesCache = false;

    /* Skip player which has loading screen
     **************************************/
    if ($player->connected != '1') {
        continue;
    }

    /* Skip players with VIP status
     ******************************/
    if ($configs['general.ignore_vips'] === true) {
        if ($player->vip == '1') {
            continue;
        }
    }

    // Get class name (inspired from roennel test_live example)
    switch(true)
    {
        case strpos($player->kit, 'Medic') !== false:
            $kit = "medic";
            break;
    
        case strpos($player->kit, 'Assault') !== false:
            $kit = "assault";
            break;
    
        case strpos($player->kit, 'Recon') !== false:
            $kit = "recon";
            break;
    
        case strpos($player->kit, 'Engineer') !== false:
            $kit = "engineer";
            break;

        default:
            //"none" - soldier is dead
            $kit = "dead"; 
            break;
    }

    /* Declare tmp variable
     **********************/
    $decision = array(
        'kick' => false,
        'weapon_id' => "",
        'level' => $player->level,
        'class' => $kit,
        'reason' => "autokick",
        'script' => "",
    );


    /* Test - Ignore Selected players (Not VIPs, but also not managed to leave)
     ***************************************************************************/
    if ($configs['general.ignored_players_enabled'] === true) {
        foreach ($configs['general.ignored_players'] as $ignored) {
            if ($ignored == $player->nucleusId) {
                continue;
            }
        }
    }

    /* Weapon Limiter and Prebuy Limiter
     ***********************************/
    if ($decision['kick']!==true 
        && ($configs['weaponLimiter.weapon_limiter_enabled'] === true 
            || $configs['weaponLimiter.prebuy_limiter_enabled'] === true )) {
            
        $cache = $db->selectFromCache($configs['colCache'], (string) $player->nucleusId, $player->cdKeyHash);

        if (empty($cache) || empty($cache['loadout'])) {
            $playerLoadout = new rcon\Stats((string) $player->nucleusId, $player->cdKeyHash);
            $loadout = $playerLoadout->retrieveLoadout();

            // Didn't received JSON -> skip.
            if (empty($loadout) === true) {
                continue;
            }
            
            // Prepare loadout for storage into DB.
            foreach ($loadout['data']['equipment'] as $key => $value) {
                $loadout['storage'][$key] = $value['id'];
            }

            // Insert into cache collection
            $db->insertIntoCache($configs['colCache'], (string) $player->nucleusId, $player->cdKeyHash, $loadout['storage']);

        } else {
            // Time Difference
            $start_date = new DateTime('now');
            $since_start = $start_date->diff(new DateTime($cache['date']));
            $minutes = $since_start->days * 24 * 60;
            $minutes += $since_start->h * 60;
            $minutes += $since_start->i;

            // May I update data in DB?
            if ($minutes >= $configs['weaponLimiter.cache_threshold']) {
                $playerLoadout = new rcon\Stats((string) $player->nucleusId, $player->cdKeyHash);
                $loadout = $playerLoadout->retrieveLoadout();

                // Prepare loadout for storage into DB.
                foreach ($loadout['data']['equipment'] as $key => $value) {
                    $loadout['storage'][$key] = $value['id'];
                }

                // Update cache collection
                $db->updateCache($configs['colCache'], (string) $player->nucleusId, $player->cdKeyHash, $loadout['storage']);
            
            } else {
                // Cached - Just load it from DB
                $loadout['storage'] = $cache['loadout'];
                $usesCache = true;
            }
        }

        /* Look ma! I am *Valid* 
         ***********************/
        foreach ($loadout['storage'] as $weapon) {

            /* I haz too much monies?
             ************************/
            if ($configs['weaponLimiter.prebuy_limiter_enabled']===true) {
                if (($sup->weaponGetReqLvl($weapon) > $player->level) && in_array($weapon, $configs['weaponLimiter.prebuy_limiter_restricted_guns']) ) {
                    $configs['cstMessage'] = $configs['weaponLimiter.custom_message'];
                    $decision['kick'] = true;
                    $decision['weapon_id'] = $weapon;
                    $decision['type'] = "Prebuy Limiter";
                    $decision['script'] = $decision['type'];
                    $decision['reason'] = "Prebuy Limiter: ".$sup->weaponGetName($weapon)." (".$player->level."/".$sup->weaponGetReqLvl($weapon).")";
                }
            }

            /* I haz too big gun?
             ********************/
            if ($configs['weaponLimiter.weapon_limiter_enabled']===true) {
                if (in_array($weapon, $configs['weaponLimiter.weapon_limiter_restricted_guns'])) {
                    $configs['cstMessage'] = $configs['weaponLimiter.custom_message'];
                    $decision['kick'] = true;
                    $decision['weapon_id'] = $weapon;
                    $decision['type'] = "Weapon Limiter";
                    $decision['script'] = $decision['type'];
                    $decision['reason'] = "Weapon Limiter: ".$sup->weaponGetName($weapon);
                }
            }
        }
    }

    /* Level Limiter
     ***************/
    if ($decision['kick']!==true && $configs['levelLimiter.script_enabled']===true) {

        // Is Below Required?
        if ($player->level < $configs['levelLimiter.kick_below_level']) {
            $configs['cstMessage'] = $configs['levelLimiter.custom_message'];
            $decision['kick'] = true;
            $decision['type'] = "below";
            $decision['reason'] = "Level Limiter: {$player->level}";
            $decision['script'] = "Level Limiter";
        } 

        // Is Above Required?
        if ($player->level > $configs['levelLimiter.kick_above_level']) {
            $configs['cstMessage'] = $configs['levelLimiter.custom_message'];
            $decision['kick'] = true;
            $decision['type'] = "above";
            $decision['reason'] = "Level Limiter: {$player->level}";
            $decision['script'] = "Level Limiter";
        }
    }

    /* Class Limiter
     ***************/
    if ($decision['kick']!==true && $configs['classLimiter.script_enabled']===true) {
        $team = $player->team;
        if ($kit!="dead") {
            $kit = $kit . "s";
            ++$classCounter[$team][$kit];
        }

        // You are too boring!
        if ($kit!=="dead" && ($classCounter[$team][$kit] > $configs['classLimiter.max_'.$kit])) {
            $configs['cstMessage'] = $configs['classLimiter.custom_message'];
            $decision['kick'] = true;
            $decision['type'] = "";
            $decision['reason'] = "Class Limiter: ".$decision['class'];
            $decision['script'] = "Class Limiter";
        }
    }

    /* Bai!
     ******/
    if ($decision['kick'] === true) {
        if ($usesCache===true && isset($configs['weapon.cache_msg']) && !empty($configs['weapon.cache_msg']) && is_string($configs['weapon.cache_msg'])) {
            $information = $configs['weapon.cache_msg'];
            $reason = preg_replace('/%player%/', $player->name, $information);
            $reason = preg_replace('/%weapon%/', $sup->weaponGetName($decision['weapon_id']), $information);
            $chat->say($information);
            usleep(200);
        }
        $reason = preg_replace('/%player%/', $player->name, $configs['cstMessage']);
        $reason = preg_replace('/%weapon%/', $sup->weaponGetName($decision['weapon_id']), $reason);
        $reason = preg_replace('/%type%/', $decision['type'], $reason);
        $reason = preg_replace('/%kit%/', $decision['class'], $reason);
        $reason = preg_replace('/%script%/', $decision['script'], $reason);
        $reason = preg_replace('/%level%/', $decision['level'], $reason);
        $rcp->kick($player->name, $reason);
        
        $db->insertIntoLogs($configs['colLogs'], (string) $player->nucleusId, $player->cdKeyHash, $player->name, $decision['reason']);
    }
}

// Notice to stdout
echo "Completed. " . date("Y-m-d H:i:s") . PHP_EOL;

