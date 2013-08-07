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
 * @version  0.2
 * @link     https://github.com/piqus/bfp4f-limiter
 */

/**
* SQL DB Service Provider
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
	 * @param  string $table        Name of table where logs will be stored in database
	 * @param  string $profile_id   Player Profile ID
	 * @param  int    $soldier_id   Player Soldier ID
	 * @param  string $soldier_name Player Soldier Name
	 * @param  string $reason       Reason for kick
	 * 
	 * @return void
	 */
	public function insertIntoLogs($table, $profile_id, $soldier_id, $soldier_name, $reason)
	{
		$query  = "INSERT INTO " . $table . " ";
		$query .= "(profile_id, soldier_id, soldier_name, date, reason) ";
		$query .= "VALUES ";
		$query .= "(:profile_id, :soldier_id, :soldier, :date, :reason);";

		$stmt = $this->_db->prepare($query);

		$date = date("Y-m-d H:i:s");

		$stmt->bindParam(':profile_id', $profile_id, PDO::PARAM_STR);
		$stmt->bindParam(':soldier_id', $soldier_id, PDO::PARAM_INT);
		$stmt->bindParam(':soldier', $soldier_name, PDO::PARAM_STR);
		$stmt->bindParam(':date', $date, PDO::PARAM_STR);
		$stmt->bindParam(':reason', $reason, PDO::PARAM_STR);

		$stmt->execute();
	}

	/**
	 * selectFromCache
	 * 
	 * @param  string $table      Name of table where is stored cache data
	 * @param  string $profile_id Player Profile ID
	 * @param  string $soldier_id Player Soldier ID
	 * 
	 * @return array Loadout stored in cache table.
	 */
	public function selectFromCache($table, $profile_id, $soldier_id)
	{
    	$query  = "SELECT * FROM " . $table . " ";
    	$query .= "WHERE profile_id = :profile_id AND soldier_id = :soldier_id";

    	$stmt = $this->_db->prepare($query);

		$stmt->bindParam(':profile_id', $profile_id, PDO::PARAM_STR);
		$stmt->bindParam(':soldier_id', $soldier_id, PDO::PARAM_INT);

		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$row['loadout'] = json_decode($row['loadout']);

		return $row;
	}

	/**
	 * insertIntoCache
	 * 
	 * @param  string $table      Name of table where is stored cache data
	 * @param  string $profile_id Player Profile ID
	 * @param  int    $soldier_id Player Soldier ID
	 * @param  string $loadout    Current Loadout of Player
	 * 
	 * @return int
	 */
	public function insertIntoCache($table, $profile_id, $soldier_id, $loadout)
	{
		$query  = "INSERT INTO " . $table . " ";
		$query .= "(profile_id, soldier_id, date, loadout) ";
		$query .= "VALUES ";
		$query .= "(:profile_id, :soldier_id, :date, :loadout);";

		$stmt = $this->_db->prepare($query);

		$date = date("Y-m-d H:i:s");
		$loadout = json_encode($loadout);

		$stmt->bindParam(':profile_id', $profile_id, PDO::PARAM_STR);
		$stmt->bindParam(':soldier_id', $soldier_id, PDO::PARAM_INT);
		$stmt->bindParam(':date', $date, PDO::PARAM_STR);
		$stmt->bindParam(':loadout', $loadout, PDO::PARAM_STR);

		return $stmt->execute();
	}

	/**
	 * updateCache description
	 * 
	 * @param  string $table      Name of table where is stored cache data
	 * @param  string $profile_id Player Profile ID
	 * @param  int    $soldier_id Player Soldier ID
	 * @param  string $loadout    Current Loadout of Player
	 * 
	 * @return int
	 */
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

		$date = date("Y-m-d H:i:s");
		$loadout = json_encode($loadout);

		$stmt->bindParam(':profile_id', $profile_id, PDO::PARAM_STR);
		$stmt->bindParam(':soldier_id', $soldier_id, PDO::PARAM_INT);
		$stmt->bindParam(':date', $date, PDO::PARAM_STR);
		$stmt->bindParam(':loadout', $loadout, PDO::PARAM_STR);

		return $stmt->execute();
	}
}
?>