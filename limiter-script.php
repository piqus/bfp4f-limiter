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
 * @version  0.2
 * @link     https://github.com/piqus/bfp4f-limiter
 */

/* Instantiate namespace
 ***********************/
use T4G\BFP4F\Rcon as rcon;
$rc = new rcon\Base();

/* Is limiter off? 
 ********************/
if ($configs['enabled'] === false) {
    echo('Limiter is switched off');
    exit(1);
}

/* Connect to server 
 ********************/
$rc->ip = $configs['server_ip'];
$rc->port = (int) $configs['server_port'];
$rc->pwd = $configs['server_password'];

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

// Create Player Object
$rcp = new rcon\Players();

// Get Players Info
$players = $rcp->fetch();

// Toolbox
$sup = new rcon\Support();

foreach ($players as $player) {

    /* Skip player which has loading screen
     **************************************/
    if ($player->connected != '1') {
        continue;
    }

    /* Skip players with VIP status
     ******************************/
    if ($configs['ignVIP'] === true) {
        if ($player->vip == 1) {
            continue;
        }
    }

    /* Devlare tmp variable
     **********************/
    $decision = array(
        'kick' => false,
        'weapon_id' => '0',
        'reason' => "autokick"
    );

    /* Test - Ignore Selected soldiers (Not VIPs, but also not managed to leave)
     ***************************************************************************/
    if ($configs['ignored_members_enabled'] === true) {
        foreach ($configs['ignored_members'] as $ignored) {
            if ($ignored['pid'] == $player->nucleusId && $ignored['sid'] == $player->cdKeyHash) {
                continue;
            }
        }
    }

    /* Retrieve Loadout from cache collection or website.
     ****************************************************/
    $cache = $db->selectFromCache($configs['colCache'], (string) $player->nucleusId, $player->cdKeyHash);

    if (empty($cache) || empty($cache['loadout'])) {
        $playerLoadout = new rcon\Stats((string) $player->nucleusId, $player->cdKeyHash);
        $loadout = $playerLoadout->retrieveLoadout();

        // Didn't recived JSON -> skip.
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
        if ($minutes >= $configs['cacheThres']) {
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
        }
    }

    /* Look ma! I am *Valid* 
     ***********************/
    foreach ($loadout['storage'] as $weapon) {

        /* I haz too much monies?
         ************************/
        if ($configs['prebuy_enabled']===true) {
            if (($sup->weaponGetReqLvl($weapon) < $player->level) && in_array($weapon, $configs['prebuy_restricted']) ) {
                $decision['kick'] = true;
                $decision['weapon_id'] = $weapon;
                $decision['type'] = "Prebuy";
                $decision['reason'] = "Prebought gun: ".$sup->weaponGetName($weapon).". Already on ".$player->level." lvl";
            }
        }

        /* I haz too big gun?
         ********************/
        if (in_array($weapon, $configs['restrGuns'])) {
            $decision['kick'] = true;
            $decision['weapon_id'] = $weapon;
            $decision['type'] = "Weapon Limiter";
            $decision['reason'] = "Disallowed gun: ".$sup->weaponGetName($weapon);
        }
    }

    /* For sure. Bai
     ***************/
    if ($decision['kick'] === true) {
        $reason = preg_replace('/%player/', $player->name, $configs['cstMessage']);
        $reason = preg_replace('/%weapon/', $sup->weaponGetName($decision['weapon_id']), $reason);
        $reason = preg_replace('/%kick_type/', $decision['type'], $reason);
        $rcp->kick($player->name, $reason);

        $db->insertIntoLogs($configs['colLogs'], (string) $player->nucleusId, $player->cdKeyHash, $player->name, $decision['reason']);
    }
}

// Notice to stdout
echo "Completed. " . date("Y-m-d H:i:s") . PHP_EOL;

