<?php
/**
* MongoDB service provider
*/
class DB
{
	private $mdb;
	
	public function __construct()
	{
		$dsn = 'mongodb://'.DB_USER.':'.DB_PASS.'@'.DB_HOST.':'.DB_PORT.'/'.DB_NAME;
		$m = new MongoClient(DB_NAME, array());
		$this->_mdb = $m->selectDB($configs['db']);
	}

	public function insertIntoLogs($collection, $pid, $sid, $soldier_name, $reason)
	{
		$c = $this->_mdb->selectCollection($collection);
        $c->insert(
            array(
                'pid' => (string) $player->nucleusId,
                'sid' => $player->cdKeyHash,
                'soldier' => $player->name,
                'date' => date("Y-m-d H:i:s"),
                'reason' => $decision['reason'],
            )
        );
	}

	public function selectFromCache($collection, $profile_id, $soldier_id)
	{
		$c = $this->_mdb->selectCollection($collection);
    	$cache = $c->findOne(array('pid' => (string) $profile_id, 'sid' => $soldier_id));
	}

	public function insertIntoCache($collection, $profile_id, $soldier_id, $loadout)
	{
		$c = $this->_mdb->selectCollection($collection);
		$c->insert(
            array(
            'pid' => (string) $profile_id,
            'sid' => $soldier_id,
            'date' => date("Y-m-d H:i:s"),
            'loadout' => $loadout,
            )
        );
	}

	public function updateCache($collection, $profile_id, $soldier_id, $loadout)
	{
		$c = $this->_mdb->selectCollection($collection);
		$c->update(
            array(
                'pid' => (string) $profile_id,
                'sid' => $soldier_id
            ),
            array(
                'pid' => (string) $profile_id,
                'sid' => $soldier_id,
                'date' => date("Y-m-d H:i:s"),
                'loadout' => $loadout,
            )
        ); 
	}
}
?>