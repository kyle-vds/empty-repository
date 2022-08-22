<?php
class ControlPanel{

	public static function page(){
		$ballot = new Table("ballot");
		$stage = $ballot->get("stage");
		if (isset($_POST['push_ballot'])){
			$new_stage = $stage + 1;
			if ($ballot->set("stage",$new_stage)){
				switch($new_stage){
					case 1: #Draw Housing Ballot
						break;
					case 2: #Start House Allocation
						break;
					case 3: #Switch students with no house to Room Ballot
						break;
					case 4: #Draw Room Ballot
						break;
					case 5: #Start Room Allocation
						break;
					case 6: #Close Ballot, filling spreadsheet
						break;
				}
			}
			else HTML::HTMLerror("Failed to push ballot stage");	
		}
		if (isset($_POST['start_ballot'])){
			
		}
		if (isset($_POST['submit_date'])){
			if (!isset($_POST['select_date'])) HTML::HTMLerror("You need to select a date from the table before you can change it");
			elseif (!isset($_POST['date'])) HTML::HTMLerror("You need to enter a new date to chaneg one");
			else {
				if ($ballot->set($_POST['select_date'], $_POST['date'])) HTML::HTMLsuccess("Succesfully changed date!");
				else HTML::HTMLerror("Failed to change date");
			}
		}
		if (isset($_POST['submit_admin'])){
			if (isset($_POST['remove_admin'])){
				$errors = 0;
				foreach ($_POST['remove_admin'] as $admin_id) {
					if (!$errors){
						$admin = new Table("admin", $admin_id);
						if (!$admin->delete()){
							HTML::HTMLerror("Failed to remove admin");
							$errors = 1;
						}
					}
				}
				if (!$errors) HTML::HTMLsuccess("Successfully removed admin!");
			}
			if (isset($_POST['name'])){
				if (isset($_POST['admin_crsid'])){
					$values = $_POST['admin_crsid'].", ".$_POST['name'];
					if (Table::insert("admin", $values)) HTML::HTMLsuccess("Successfully added admin!");
					else HTML::HTMLerror("Failed to add admin");
				}
				else HTML::HTMLerror("You need to enter a crsid too in order to add an admin");
			}
			elseif (isset($_POST['admin_crsid'])) HTML::HTMLerror("You need to enter a name too in order to add an admin");
		}
		if (isset($_POST['submit_access'])){
			if (isset($_POST['remove_access'])){
				$errors = 0;
				foreach ($_POST['remove_access'] as $access_id) {
					if (!$errors){
						$user = new Table("user", $access_id);
						$new_priority = $user->get("priority") + 1;
						if (!$user->set("priority", $new_priority)){
							HTML::HTMLerror("Failed to take away access arrangement");
							$errors = 1;
						}
					}
				}
				if (!$errors) HTML::HTMLsuccess("Successfully taken away access arrangements!");
			}
			if (isset($_POST['priority']) && $_POST['priority'] != ""){
				if (isset($_POST['access_crsid'])){
					$user = new Table("user", $_POST['access_crsid']);
					if ($user->set("priority", $_POST['priority'])) HTML::HTMLsuccess("Successfully given access arrangement!");
					else HTML::HTMLerror("Failed to give access arrangement");
				}
				else HTML::HTMLerror("You need to enter a crsid too in order to give an access arrangement");
			}
			elseif (isset($_POST['admin_crsid'])) HTML::HTMLerror("You need to select a priority too in order to give an access arrangement");
		}
		?>
		<div class="container">
		<video width="640" height="340" controls><source src="include/Ballot_images/Videos/system_walkthrough.mp4" type="video/mp4"></video>
		<form method="POST">
		<?switch ($stage){
			case 0:
				$stage_name = "Registration Open";
				$next_stage = "Draw Housing Ballot";
				break;
			case 1:
				$stage_name = "Housing Ballot Drawn";
				$next_stage = "Lock Housing Ballot Dates";
				break;
			case 2:
				$stage_name = "Housing Ballot Dates Locked";
				$next_stage = "Close the Housing Ballot";
				break;
			case 3:
				$stage_name = "Housing Ballot Closed";
				$next_stage = "Draw Room Ballot";
				break;
			case 4:
				$stage_name = "Room Ballot Drawn";
				$next_stage = "Lock Room Ballot Dates";
				break;
			case 5:
				$stage_name = "Room Ballot Dates Locked";
				$next_stage = "Close the Room Ballot";
				break;
			case 6:
				$stage_name = "Room Ballot Closed";
				break;
		}?>
		<p>Current Balloting Stage: <strong><?= $stage_name?></strong></p>
		<?if ($stage < 6){
			$dates = ["Registration Opens" => "reg_open", "Housing Ballot Drawn" => "hb_drawn", "Housing Ballot Deadline" => "hb_dead", "Room Ballot Drawn" => "rb_drawn", "Room Ballot Deadline" => "rb_dead", "Contract Deadline" => "contract_dead"];?>
			<p>Push Ballot to the next stage: <input type="submit" name="push_ballot" value="<?= $next_stage?>"></p>
			<hr>
			<h2>Edit Ballot Dates</h2>
			<table class="table table-condensed table-bordered table-hover">
			<thead><tr>
			<td>Event</td>
			<td>Date</td>
			<td>Select</td>
			</tr></thead>
			<?foreach ($dates as $date => $col){?>
				<tr>
				<td class="col-md-4"><?= $date ?>
				<td class="col-md-4"><?= $ballot->get($col) ?>
				<td class="col-md-1"><input type="radio" name="select_date" value="<?= $ballot->get($col)?>"></td>
				</tr>
			<?}?>
			</table>
			<p>Edit Dates:</p>
			<p>Enter new date: <input type = "text" name = "date" maxlength = "10"/></p>
			<p>To edit a date, it must be selected from the table above <input type="submit" name="submit_date" value="Change Date"></p>
		<?}
		else {?>
			<p>Upload a CSV file to setup a new ballot: <input type="file" name="my_file"/></p>
			<p><input type="submit" name="start_ballot" value="Upload"></p>
		<?}?>
		<hr>
		<h2>Manage Admin Access</h2>
		<table class="table table-condensed table-bordered table-hover">
		<thead><tr>
		<td>Admin</td>
		<td>CRSID</td>
		<td>Remove</td>
		</tr></thead>
		<?$admins = Table::get_all("admin");
		foreach ($admins as $admin){?>
			<tr>
			<td class="col-md-4"><?= $admin->get("name")?></td>
			<td class="col-md-4"><?= $admin->get("id")?></td>
			<td class="col-md-1"><input type="checkbox" name="remove_admin[]" value="<?= $admin->get("id")?>"/></td>
			</tr>
		<?}?>
		</table>
		<p>Add a new admin:</p>
		<p>Enter Name: <input type="text" name="name" maxlength="255"></p>
		<p>Enter CRSID: <input type="text" name="admin_crsid" maxlength="7"></p>
		<p>To remove an admin, they must be selected from the table above <input type="submit" name="submit_admin" value="Add/Remove Admin"></p>
		<hr>
		<h2>Manage Access Arrangements</h2>
		<table class="table table-condensed table-bordered table-hover">
		<thead><tr>
		<td>Student</td>
		<td>CRSID</td>
		<td>Remove</td>
		</tr></thead>
		<?
		$priorities = ["Second Years and Third Years Abroad (or maximum priority)" => 0, "Third Years with confirmed fourth" => 2, "First Years" => 4 ];
		foreach($priorities as $title => $priority){
			$condition = "`priority` = ".$priority;
			$access_arrangements = Table::get_all("user", $condition);
			if ($access_arrangements != NULL){?>
				<tr><td colspan="2"><h3><?= $title ?></h3></td></tr>
				<?foreach ($access_arrangements as $access_arrangement){?>
					<tr>
					<td class="col-md-4"><?= $access_arrangement->get("name")?></td>
					<td class="col-md-4"><?= $admin->get("id")?></td>
					<td class="col-md-1"><input type="checkbox" name="remove_access[]" value="<?= $admin->get("id")?>"></td>
					</tr>
				<?}
			}
		}?>
		</table>
		<p>Give an access arrangement to a student:
		<select name="priority">
				<option value="">Please select</option>
				<option value="0">Second Years and Third Years Abroad (or maximum priority)</option>
				<option value="2">Third Years with confirmed fourth</option>
				<option value="4">First Years</option>
		</select>
		</p>
		<p>Enter CRSID: <input type="text" name="access_crsid" maxlength="7"></p>
		<p>To take away an access arrangement from a student, they must be selected from the table above <input type="submit" name="submit_access" value="Give/Take Access Arrangement"></p>
		<hr>
		</form>
		</div>
		<?
		/*
			if (isset($_POST['push_ballot'])) {
				if (isset($_POST['year']) && $_POST['year'] != "") {
					if (HTML::IntegerChecker($_POST['year'])) {
						if ($ballot->PushBallot($_POST['year'])) {
							HTML::HTMLsuccess("Ballot pushed to next stage");
							$ballot = new BallotMaker();
						} else
							HTML::HTMLerror("Error creating new ballot, please email jcr.website@fitz.cam.ac.uk");
					} else
						HTML::HTMLerror("Please enter a valid year");
				} elseif ($ballot->PushBallot()) {
					HTML::HTMLsuccess("Ballot pushed to next stage");
					$ballot = new BallotMaker();
				} else
					HTML::HTMLerror("Error pushing ballot to next stage, please email jcr.website@fitz.cam.ac.uk");
			}
			if (isset($_POST['draw_seed'])) {
				if ($ballot->drawSeed()) {
					HTML::HTMLsuccess("Successfully updated balloting seed");
					$ballot = new BallotMaker();
				} else
					HTML::HTMLerror("Error updating balloting seed, please email jcr.website@fitz.cam.ac.uk");
			}
			if (isset($_POST['draw_order'])) {
				if ($ballot->drawOrder()) {
					HTML::HTMLsuccess("Successfully updated balloting order");
					$ballot = new BallotMaker();
				} else
					HTML::HTMLerror("Error updating balloting order, please email jcr.website@fitz.cam.ac.uk");
			}


class BallotMaker
{

	protected $year = NULL;

	protected $stage = NULL;

	protected $name = NULL;

	protected $seed = NULL;

	protected $ballotPriorities = array();

	protected $criteria_col = NULL;

	protected $position = NULL;

	protected $proxy = NULL;

	private static function fetchSeed()
	{
		$session = curl_init("https://www.random.org/integers/?num=1&min=100000000&max=1000000000&col=5&base=10&format=plain&rnd=new");
		curl_setopt($session, CURLOPT_HTTPGET, true);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($session);
		curl_close($session);
		return $response;
	}

	public function drawSeed()
	{
		$this->seed = self::fetchSeed();
		$query = "UPDATE `ballot_log` SET `";
		if ($this->stage == 1)
			$query .= "hb_seed";
		elseif ($this->stage == 4)
			$query .= "rb_seed";
		else
			return false;
		$query .= "` = " . $this->seed . " WHERE `year` = " . $this->year;
		$result = Database::getInstance()->query($query);
		return ($result);
	}

	public function drawOrder()
	{
		Database::getInstance()->query("begin");
		if ($this->seed != NULL && $this->seed != "") {
			mt_srand($this->seed);
			$order = array();
			foreach ($this->ballotPriorities as $ballotPriority => $criteria) {
				$shuffling = array();
				$query = "SELECT `group_id` FROM `" . $this->name . "` WHERE " . $this->criteria_col . $criteria;
				$result = Database::getInstance()->query($query);
				if ($result) {
					$size = 0;
					while (($row = $result->fetch_assoc()) != false) {
						array_push($shuffling, $row['group_id']);
						$size += 1;
					}
					for ($n = $size; $n > 0; $n --) {
						if ($n == 1)
							array_push($order, $shuffling[0]);
						else {
							$index = mt_rand(0, $n - 1);
							array_push($order, $shuffling[$index]);
							array_splice($shuffling, $index, 1);
						}
					}
				} else {
					Database::getInstance()->query("rollback");
					return false;
				}
			}
			$position = 1;
			$errors = 0;
			foreach ($order as $group_id) {
				$query = "UPDATE `" . $this->name . "` SET `order` = " . $position . " WHERE `group_id` = " . $group_id;
				$position += 1;
				$result = Database::getInstance()->query($query);
				if (! $result)
					$errors = 1;
			}
			if ($errors) {
				Database::getInstance()->query("rollback");
				return false;
			} else {
				Database::getInstance()->query("commit");
				return true;
			}
		} else {
			Database::getInstance()->query("rollback");
			return false;
		}
	}

	public function PushBallot($year = null)
	{
		Database::getInstance()->query("begin");
		if ($this->getStage() == 0) {
			$query = "UPDATE `users` SET `searching` = 0 WHERE `room_ballot` = 0";
			$result = Database::getInstance()->query($query);
			if (! $result) {
				Database::getInstance()->query("rollback");
				return false;
			}
		} elseif ($this->getStage() == 1 || $this->getStage() == 4) {
			$query = "UPDATE `ballot_log` SET `position` = 1 WHERE `year` = " . $this->year;
			$result = Database::getInstance()->query($query);
			if (! $result) {
				Database::getInstance()->query("rollback");
				return false;
			} else {
				$query = "SELECT `group_id` FROM `" . $this->name . "` WHERE `order` = 1";
				$result = Database::getInstance()->query($query);
				if ($result) {
					$row = $result->fetch_assoc();
					$group = new Group($row['group_id'], $this->name);
					HTML::sendEmail($group->getAdmin(), "Your turn in the ballot!");
				} else
					return false;
			}
		} elseif ($this->getStage() == 2) {
			$query = "SELECT `crsid` FROM `users` WHERE `room` IS NULL AND `room_ballot` = 0";
			$result = Database::getInstance()->query($query);
			if ($result) {
				while (($row = $result->fetch_assoc()) != false) {
					$user = new User($row['crsid']);
					if (! $user->swapBallot()) {
						Database::getInstance()->query("rollback");
						return false;
					}
				}
			} else {
				Database::getInstance()->query("rollback");
				return false;
			}
		} elseif ($this->getStage() == 3) {
			$query = "UPDATE `users` SET `searching` = 0 WHERE `room_ballot` = 1";
			$result = Database::getInstance()->query($query);
			if (! $result) {
				Database::getInstance()->query("rollback");
				return false;
			}
		} elseif ($this->getStage() == 6) {
			if (isset($year) && $year != "") {
				$query = "INSERT INTO `ballot_log` (`year`, `stage`) VALUES ('" . $year . "', 0)";
				$result = Database::getInstance()->query($query);
				if ($result) {
					$db_delete = array(
						'users',
						'housing_ballot',
						'room_ballot'
					);
					$errors = 0;
					foreach ($db_delete as $delete) {
						if (! $errors) {
							$query = "DELETE FROM `" . $delete . "`";
							$result = Database::getInstance()->query($query);
							if (! $result)
								$errors = 1;
						}
					}
					if (! $errors) {
						$query = "UPDATE `access` SET `users` = NULL";
						$result = Database::getInstance()->query($query);
						if ($result) {
							$db_update = array(
								'rooms',
								'houses'
							);
							$errors = 0;
							foreach ($db_update as $update) {
								if (! $errors) {
									$query = "UPDATE `" . $update . "` SET `available` = 1";
									$result = Database::getInstance()->query($query);
									if (! $result)
										$errors = 1;
								}
							}
							if (! $errors) {
								Database::getInstance()->query("commit");
								return true;
							} else {
								Database::getInstance()->query("rollback");
								return false;
							}
						} else {
							Database::getInstance()->query("rollback");
							return false;
						}
					} else {
						Database::getInstance()->query("rollback");
						return false;
					}
				} else {
					Database::getInstance()->query("rollback");
					return false;
				}
			} else {
				Database::getInstance()->query("rollback");
				return false;
			}
		}
		
		$new_stage = $this->getStage() + 1;
		$query = "UPDATE `ballot_log` SET `stage` = " . $new_stage . " WHERE `year` = " . $this->getYear();
		$result = Database::getInstance()->query($query);
		if ($result) {
			Database::getInstance()->query("commit");
			return true;
		} else {
			Database::getInstance()->query("rollback");
			return false;
		}
	}
}

*/
	}
}