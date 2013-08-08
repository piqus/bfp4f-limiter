<?php
/**
 * Weapon Limiter - MongoDB Database Service provider
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

/**
* MongoDB service provider
*/
class DB
{
    /**
     * Stores MongoClient object.
     * 
     * @var object
     */    
	private $_mdb;

    /**
     * Contructor
     *
     * Initializes connection and instantiate MongoClient.
     */	
	public function __construct()
	{
		$dsn = 'mongodb://'.DB_USER.':'.DB_PASS.'@'.DB_HOST.':'.DB_PORT.'/'.DB_NAME;
		$m = new MongoClient(DB_NAME, array());
		$this->_mdb = $m->selectDB(DB_NAME);
	}

    /**
     * Insert into Logs table
     * 
     * @param  string $table        Name of table where logs will be stored in database
     * @param  string $profile_id   Player Profile ID
     * @param  int    $soldier_id   Player Soldier ID
     * @param  string $soldier_name Player Soldier Name
     * @param  string $reason       Reason for kick
     * 
     * @return void
     */
	public function insertIntoLogs($collection, $profile_id, $soldier_id, $soldier_name, $reason)
	{
		$c = $this->_mdb->selectCollection($collection);
        $c->insert(
            array(
                'profile_id' => (string) $profile_id,
                'soldier_id' => $soldier_id,
                'soldier' => $soldier_name,
                'date' => date("Y-m-d H:i:s"),
                'reason' => $reason,
            )
        );
	}

    /**
     * selectFromCache
     * 
     * @param  string $collection        Name of collection where is stored cache data
     * @param  string $profile_id Player Profile ID
     * @param  string $soldier_id Player Soldier ID
     * 
     * @return array Loadout stored in cache collection.
     */
	public function selectFromCache($collection, $profile_id, $soldier_id)
	{
		$c = $this->_mdb->selectCollection($collection);
    	$cache = $c->findOne(array('profile_id' => (string) $profile_id, 'soldier_id' => $soldier_id));
        return $cache;
	}

    /**
     * insertIntoCache
     * 
     * @param  string $collection Name of collection where is stored cache data
     * @param  string $profile_id Player Profile ID
     * @param  int    $soldier_id Player Soldier ID
     * @param  string $loadout    Current Loadout of Player
     * 
     * @return void
     */
	public function insertIntoCache($collection, $profile_id, $soldier_id, $loadout)
	{
		$c = $this->_mdb->selectCollection($collection);
		$c->insert(
            array(
            'profile_id' => (string) $profile_id,
            'soldier_id' => $soldier_id,
            'date' => date("Y-m-d H:i:s"),
            'loadout' => $loadout,
            )
        );
	}

    /**
     * updateCache description
     * 
     * @param  string $collection Name of collection where is stored cache data
     * @param  string $profile_id Player Profile ID
     * @param  int    $soldier_id Player Soldier ID
     * @param  string $loadout    Current Loadout of Player
     * 
     * @return void
     */
	public function updateCache($collection, $profile_id, $soldier_id, $loadout)
	{
		$c = $this->_mdb->selectCollection($collection);
		$c->update(
            array(
                'profile_id' => (string) $profile_id,
                'soldier_id' => $soldier_id
            ),
            array(
                'profile_id' => (string) $profile_id,
                'soldier_id' => $soldier_id,
                'date' => date("Y-m-d H:i:s"),
                'loadout' => $loadout,
            )
        ); 
	}
}
?>
