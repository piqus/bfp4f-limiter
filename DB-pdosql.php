<?php
/**
 * Weapon Limiter - SQL Database Service provider
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

/**
* DB Service Provider
*/
class DB
{
	/**
	 * Stores PDO object.
	 * 
	 * @var object
	 */
	private $_db;
	
	/**
	 * Contructor
	 *
	 * Initializes connection and instantiate PDO.
	 */
	public function __construct()
	{
		$dsn = 'mysql:host='.DB_HOST.';port='.DB_PORT.';dbname='.DB_NAME;
		try {
			$this->_db = new PDO($dsn, DB_USER, DB_PASS);
		} catch (PDOException $e) {
			echo 'Connection failed: ' . $e->getMessage();
		}

	}

	/**
	 * Insert into Logs table
	 * 
	 * @param  string $table   [description]
	 * @param  string $pid          [description]
	 * @param  int    $sid          [description]
	 * @param  string $soldier_name [description]
	 * @param  string $reason       [description]
	 * 
	 * @return void
	 */
	public function insertIntoLogs($table, $pid, $sid, $soldier_name, $reason, $autokickType)
	{
		$query  = "INSERT INTO " . $table . " ";
		$query .= "(profile_id, soldier_id, soldier_name, date, reason) ";
		$query .= "VALUES ";
		$query .= "(:profile_id, :soldier_id, :soldier, :date, :reason);";

		$stmt = $this->_db->prepare($query);

		$stmt->bindParam(':profile_id', $pid);
		$stmt->bindParam(':soldier_id', $sid);
		$stmt->bindParam(':soldier', $soldier_name);
		$stmt->bindParam(':date', date("Y-m-d H:i:s"));
		$stmt->bindParam(':reason', $reason);

		$stmt->execute();
	}

	public function selectFromCache($table, $profile_id, $soldier_id)
	{
    	$query  = "SELECT * FROM " . $table . " ";
    	$query .= "WHERE profile_id = :profile_id AND soldier_id = :soldier_id";

    	$stmt = $this->_db->prepare($query);

		$stmt->bindParam(':profile_id', $pid);
		$stmt->bindParam(':soldier_id', $sid);

		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$row['loadout'] = json_decode($row['loadout'])

		return $row;
	}

	public function insertIntoCache($table, $profile_id, $soldier_id, $loadout)
	{
		$query  = "INSERT INTO " . $table . " ";
		$query .= "(profile_id, soldier_id, date, loadout) ";
		$query .= "VALUES ";
		$query .= "(:profile_id, :soldier_id, :date, :loadout);";

		$stmt = $this->_db->prepare($query);

		$stmt->bindParam(':profile_id', $pid);
		$stmt->bindParam(':soldier_id', $sid);
		$stmt->bindParam(':date', date("Y-m-d H:i:s"));
		$stmt->bindParam(':loadout', json_encode($loadout));

		$stmt->execute();
	}

	public function updateCache($table, $profile_id, $soldier_id, $loadout)
	{
		$query  = "UPDATE " . $table . " SET ";
		$query .= "profile_id = :profile_id, ";
		$query .= "soldier_id = :soldier_id, ";
		$query .= "date = :date, ";
		$query .= "loadout = :loadout ";
		$query .= "WHERE ";
		$query .= "profile_id = :profile_id, soldier_id = :soldier_id;";

		$stmt = $this->_db->prepare($query);

		$stmt->bindParam(':profile_id', $pid);
		$stmt->bindParam(':soldier_id', $sid);
		$stmt->bindParam(':date', date("Y-m-d H:i:s"));
		$stmt->bindParam(':loadout', json_encode($loadout));

		$stmt->execute();
	}
}
?>