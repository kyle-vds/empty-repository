<?php
require_once "Database.php";
class Table
{

	protected $table = NULL;

	protected $id = NULL;

	protected $name = NULL;

	protected $data = NULL;

	public function __construct($name, $id = /*$_SERVER['REMOTE_USER']*/ "kv330")
	{ // id necessary for house, room, image or alternative user, admin or group
		if (! HTML::IntegerChecker($id))
			$id = "'" . $id . "'";
		$this->id = $id;
		$this->name = $name;
		switch ($name) {
			case "admin":
				$this->table = "`admin`";
				break;
			case "ballot":
				$this->table = "`ballot_log`";
				break;
			case "housing_ballot":
				$this->table = "`housing_ballot`";
				break;
			case "room_ballot":
				$this->table = "`room_ballot`";
				break;
			case "house":
				$this->table = "`houses`";
				break;
			case "image":
				$this->table = "`images`";
				break;
			case "room":
				$this->table = "`rooms`";
				break;
			case "user":
				$this->table = "`users`";
				break;
		}
		if (! $this->retrieve())
			throw new Exception("Failed to create table object");
	}

	private function retrieve()
	{
		switch ($this->table) {
			case "`admin`":
				$query = "SELECT * FROM `admin` WHERE `id` = " . $this->id;
				$result = Database::getInstance()->query($query);
				if ($result)
					$this->data = $result->fetch_assoc();
				else
					$this->data = NULL;
				return true;
			case "`ballot_log`":
				$query = "SELECT * FROM `ballot_log` ORDER BY `id` DESC LIMIT 1";
				$result = Database::getInstance()->query($query);
				if ($result) {
					$this->data = $result->fetch_assoc();
					$this->id = $this->data['id'];
					return true;
				} else
					return false;
			default:
				$query = "SELECT * FROM " . $this->table . " WHERE `id` = " . $this->id;
				$result = Database::getInstance()->query($query);
				if ($result) {
					$this->data = $result->fetch_assoc();
					return true;
				} else
					return false;
		}
	}
	
	public static function get_all($table_object, $conditions = NULL)
	{
		switch ($table_object) {
			case "housing_ballot":
				$table = "`housing_ballot`";
				break;
			case "room_ballot":
				$table = "`room_ballot`";
				break;
			case "admin":
				$table = "`admin`";
				break;
			case "house":
				$table = "`houses`";
				break;
			case "image":
				$table = "`images`";
				break;
			case "room":
				$table = "`rooms`";
				break;
			case "user":
				$table = "`users`";
				break;
		}
		$query = "SELECT `id` FROM " . $table;
		if ($conditions != NULL)
			$query .= " WHERE " . $conditions;
			$result = Database::getInstance()->query($query);
			if ($result) {
				$a_table_objects = array();
				while (($row = $result->fetch_assoc()) != false) {
					$new_table_object = new Table($table_object, $row['id']);
					array_push($a_table_objects, $new_table_object);
				}
				return $a_table_objects;
			} else
				throw new Exception("Failed to get everything");
	}

	public function get($item, $array = 0)
	{
		if ($this->table == "`admin`")
			if ($this->data == NULL)
				return NULL;
		if ($array)
			return explode(",", $this->data[$item]);
		else
			return $this->data[$item];
	}

	public function set($item, $value, $item_chain = NULL)
	{// array of [object being chained to aka. link, column of links table indexing back to object]
		if ($item_chain != NULL) {
			$object = new Table($item_chain[0], $value);
			$object->set($item_chain[1], $this->data['id']);
		}
		if (! HTML::IntegerChecker($value) && $value != "NULL")
			$value = "'" . $value . "'";
			$query = "UPDATE " . $this->table . " SET `" . $item . "` = " . $value . " WHERE `id` = " . $this->data['id'];
		$result = Database::getInstance()->query($query);
		if ($result) {
			$this->retrieve();
			return true;
		} else
			return false; // Failed to set
	}

	public function add($item, $value, $item_chain = NULL)
	{ // array of [object being chained to aka. link, column of links table indexing back to object]
		if ($item_chain != NULL) {
			$object = new Table($item_chain[0], $value);
			$object->add($item_chain[1], $this->data['id']);
		}
		$old_values = $this->get($item);
		if ($old_values != NULL) $new_values = $old_values . ",". $value;
		else $new_values = $value;
		return $this->set($item, $new_values);
	}

	public function count($item)
	{
		return count(explode(",", $this->get($item)));
	}

	public function remove($item, $value, $item_chain = NULL)
	{ // array of [object being chained to aka. link, column of links table indexing back to object]
		if ($item_chain != NULL) {
			$object = new Table($item_chain[0], $value);
			$object->remove($item_chain[1], $this->data['id']);
		}
		$new_values = array();
		foreach (explode(",", $this->get($item)) as $old_value) {
			$add = 1;
			if ($old_value == $value)
				$add = 0;
			if ($add)
				array_push($new_values, $old_value);
		}
		if (empty($new_values))
			$value = "NULL";
		else
			$value = implode(",", $new_values);
		return $this->set($item, $value);
	}

	public function delete($item_chains = NULL)
	{ // array of arrays [column of objects own table, object being chained to aka. link, column of links table indexing back to object]
		if ($item_chains != NULL) {
			foreach ($item_chains as $item_chain) {
				foreach ($this->get($item_chain[0], 1) as $object_id) {
					$object = new Table($item_chain[1], $object_id);
					$object->remove($item_chain[2], $this->id);
				}
			}
		}
		$query = "DELETE FROM " . $this->table . " WHERE `id` = " . $this->id;
		$result = Database::getInstance()->query($query);
		if ($result)
			return true;
		else
			return false; // Failed to delete table object
	}
	
	public function get_minmax($objects, $item, $get_min){
		switch($objects){
			case "rooms":
				$object_name = "room";
				break;
		}
		$first = 1;
		$min = 0;
		$max = 0;
		foreach($this->get($objects, 1) as $object_id){
			$object = new Table($object_name, $object_id);
			$value = $object->get($item);
			if ($first){
				$first = 0;
				$min = $value;
				$max = $value;
			}
			else{
				if ($value > $max) $max = $value;
				if ($value < $min) $min = $value;
			}
		}
		if ($get_min) return $min;
		else return $max;
	}
	
	public static function insert($table_object, $values)
	{
		switch ($table_object) {
			case "admin":
				$query = "INSERT INTO `admin` VALUES (".$values.")";
				$result = Database::getInstance()->query($query);
				if ($result) return true;
				else return false;
			case "ballot":
				$table = "`ballot_log`";
				break;
			case "housing_ballot":
				$query = "INSERT INTO `housing_ballot` (`crsids`, `priority`) VALUES (".$values.")";
				$result = Database::getInstance()->query($query);
				if ($result){
					$query = "SELECT * FROM `housing_ballot` ORDER BY `id` DESC LIMIT 1";
					$result = Database::getInstance()->query($query);
					if ($result){
						$group_id = $result->fetch_assoc()['id'];
						$crsid = explode(",",$values);
						$user = new Table("user", $crsid[0]);
						if ($user->set("group_id", $group_id)) return true;
						else return false;
					}
					else return false;
				}
				else return false;
			case "room_ballot":
				$query = "INSERT INTO `room_ballot` (`crsids`, `priority`) VALUES (".$values.")";
				$result = Database::getInstance()->query($query);
				if ($result){
					$query = "SELECT * FROM `room_ballot` ORDER BY `id` DESC LIMIT 1";
					$result = Database::getInstance()->query($query);
					if ($result){
						$group_id = $result->fetch_assoc()['id'];
						$crsid = explode(",",$values);
						$user = new Table("user", $crsid[0]);
						if ($user->set("group_id", $group_id)) return true;
						else return false;
					}
					else return false;
				}
				else return false;
			case "house":
				$query = "INSERT INTO `houses` (`name`,`house`) VALUES (".$values.")";
				$result = Database::getInstance()->query($query);
				if ($result) {
					$query = "SELECT `id` FROM `houses` ORDER BY `id` DESC LIMIT 1";
					$result = Database::getInstance()->query($query);
					if ($result){
						return $result->fetch_assoc()['id'];
					}
					else return NULL;
				}
				else return NULL;
			case "image":
				$query = "INSERT INTO `images` (`src`) VALUES (".$values.")";
				$result = Database::getInstance()->query($query);
				if ($result) {
					$query = "SELECT `id` FROM `images` ORDER BY `id` DESC LIMIT 1";
					$result = Database::getInstance()->query($query);
					if ($result){
						return $result->fetch_assoc()['id'];
					}
					else return NULL;
				}
				else return NULL;
			case "room":
				$query = "INSERT INTO `rooms` (`name`,`house`) VALUES (".$values.")";
				$result = Database::getInstance()->query($query);
				if ($result) {
					$query = "SELECT `id` FROM `rooms` ORDER BY `id` DESC LIMIT 1";
					$result = Database::getInstance()->query($query);
					if ($result){
						return new Table("room", $result->fetch_assoc()['id']);
					}
					else return NULL;
				}
				else return NULL;
			case "user":
				$query = "INSERT INTO `user` (`id`,`name`,`priority`,`ballot`) VALUES (".$values.")";
				$result = Database::getInstance()->query($query);
				if ($result) return true;
				else return false;
		}
	}
}
