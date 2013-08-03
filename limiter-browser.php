<?php
/**
 * Weapon Limiter
 *
 * Browser Version
 * 
 * Console script for Battlefield Play4Free community.
 * Kicks players with forbidden weapons like shotguns on selected server.
 * 
 * @category BFP4F
 * @package  limiter
 * @author   piqus <ovirendo@gmail.com>
 * @license  MIT http://opensource.org/licenses/MIT
 * @version  0.1
 * @link     https://github.com/piqus/bfp4f-limiter
 */

header('refresh: 60'); // Value = Seconds
require_once __DIR__ . 'limter-config.php';
require_once __DIR__ . 'limiter-script.php';

?>