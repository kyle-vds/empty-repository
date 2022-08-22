<?php
class BallotEditor {

	public static function page() {
		$ballot = new Table("ballot");
		$stage = $ballot->get("stage");
		if (isset($_POST['submit_switch'])) $group_ballot = $_POST['switch_group'];
		elseif (isset($_POST['current_group'])) $group_ballot = $_POST['current_group'];
		elseif ($stage < 4) $group_ballot = 1;
		else $group_ballot = 2;
		
		if (isset($_POST['submit_new'])){
			if(!isset($_POST['new_name']) || $_POST['new_name'] == "") HTML::HTMLerror("You need to enter a name before a student can be added to the ballot;");
			elseif (!isset($_POST['new_id']) || $_POST['new_id'] == "") HTML::HTMLerror("You need to enter a crsid before a student can be added to the ballot;");
			elseif(!isset($_POST['new_priority']) || $_POST['new_priority'] == "") HTML::HTMLerror("You need to select a priority before a student can be added to the ballot;");
			else{
				$values = $_POST['new_id'].",".$_POST['new_name'].",".$_POST['new_priority'].",".$group_ballot;
				if (Table::insert("user", $values)){
					switch($group_ballot){
						case 0:
							HTML::HTMLsuccess("Successfully added student to the ballot!");
							break;
						case 1:
							$values = $_POST['new_id'].",7";
							if (Table::insert("housing_ballot", $values)) HTML::HTMLsuccess("Successfully added student to the ballot!");
							else HTML::HTMLerror("Failed to add student to the ballot");
							break;
						case 2:
							$values = $_POST['new_id'].",";
							switch($_POST['new_priority']){
								case 0:
									$values .= 1;
									break;
								case 1:
									$values .= 1;
									break;
								case 2:
									$values .= 2;
									break;
								case 3:
									$values .= 2;
									break;
								case 4:
									$values .= 3;
									break;
								case 5:
									$values .= 3;
									break;
							}
							if (Table::insert("room_ballot", $values)) HTML::HTMLsuccess("Successfully added student to the ballot!");
							else HTML::HTMLerror("Failed to add student to the ballot");
							break;
					}
				}
				else HTML::HTMLerror("Failed to add student to the ballot");
			}
		}
		if (isset($_POST['remove_user'])){
			if (!isset($_POST['select_member'])) HTML::HTMLerror("You need to select a student before they can be removed");
			else{
				$errors = 0;
				foreach($_POST['select_member'] as $crsid){
					$user = new Table("user", $crsid);
					$ballot_name = NULL;
					if ($group_ballot == 1) $ballot_name = "housing_ballot";
					elseif ($group_ballot == 2) $ballot_name = "room_ballot";
					if ($ballot_name != NULL){
						if (!$user->delete(["group_id", $ballot_name, "crsids"])){
							$errors = 1;
							break;
						}
					}
					elseif (!$user->delete()){
						$errors = 1;
						break;
					}
				}
				if (!$errors) HTML::HTMLsuccess("Successfully removed students from ballot!");
				else HTML::HTMLerror("Failed to remove students from ballot");
			}
		}
		if (isset($_POST['submit_position'])){
			if (!isset($_POST['select_group'])) HTML::HTMLerror("You need to select a group from the table before you can change their position in the ballot");
			elseif (!isset($_POST['new_position'])) HTML::HTMLerror("You need to enter a new position before the groups can be changed");
			else {
				$ballot_name = NULL;
				if ($group_ballot == 1) $ballot_name = "housing_ballot";
				elseif ($group_ballot == 2) $ballot_name = "room_ballot";
				if ($ballot_name != NULL){
					$move_group = new Table($ballot_name, $_POST['select_group']);
					$final_count  = $move_group->get("order");
					$count = $_POST['new_position'];
					if ($move_group->set("order", $count)){
						if ($final_count > $count){
							$condition = "`order` BETWEEN ".($count - 1)." AND ".$final_count. " ORDER BY `order` ASC";
							foreach(Table::get_all($ballot_name, $condition) as $group){
								$count += 1;
								if (!$group->set("order", $count)){
									$errors = 1;
									break;
								}
							}
						}
						else{
							$condition = "`order` BETWEEN ".$count." AND ".($final_count + 1). " ORDER BY `order` DESC";
							foreach(Table::get_all($ballot_name, $condition) as $group){
								$count -= 1;
								if ($group->set("order", $count)){
									$errors = 1;
									break;
								}
							}
						}
					}
					else $errors = 1;
					if ($errors) HTML::HTMLerror("Failed to change position in ballot");
					else HTML::HTMLsuccess("Successfully changed position in ballot!");
					
				}
			}
		}
		if (isset($_POST['submit_date'])){
			if (!isset($_POST['select_group'])) HTML::HTMLerror("You need to select a group from the table before you can change their allocation slot");
			elseif (!isset($_POST['new date'])) HTML::HTMLerror("You need to enter a new time to change one");
			else {
				$ballot_name = NULL;
				if ($group_ballot == 1) $ballot_name = "housing_ballot";
				elseif ($group_ballot == 2) $ballot_name = "room_ballot";
				if ($ballot_name != NULL){
					$group = new Table($ballot_name, $_POST['select_group']);
					if ($group->set("date", $_POST['new_date'])) HTML::HTMLsuccess("Succesfully changed allocation slot!");
					else HTML::HTMLerror("Failed to change allocation slot");
				}
			}
		}
		if (isset($_POST['submit_swap'])){
			if (isset($_POST['select_member']) && count($_POST['select_member']) == 2){
				$member_1 = new Table("user", $_POST['select_member'][0]);
				$member_2 = new Table("user", $_POST['select_member'][1]);
				$room_swap = $member_1->get("room");
				if ($member_1->set("room", $member_2->get("room"), ["room", "allocation"])){
					if ($member_2->set("room", $room_swap, ["room", "allocation"])) HTML::HTMLsuccess("Successfully swapped rooms");
					else HTML::HTMLerror("Failed to swap rooms");
				}
				else HTML::HTMLerror("Failed to swap rooms");
			}
			else HTML::HTMLerror("You need to select TWO students from the table to change their rooms");
		}
		if (isset($_POST['submit_change'])){
			if (!isset($_POST['select_room']) || $_POST['select_room'] == "") HTML::HTMLerror("You need to select a new room before you can give it to a student");
			elseif (!isset($_POST['select_member']) || count($_POST['select_member']) != 1) HTML::HTMLerror("You need to select ONE student before their room can be changed");
			else {
				$user = new Table("user", $_POST['select_member']);
				$old_room = new Table("room", $user->get("room"));
				if (!$old_room->set("allocation", NULL)) HTML::HTMLerror("Failed to remove students current room");
				elseif (!$old_room->set("available", 1)) HTML::HTMLerror("Failed to make old room available");
				elseif (!$user->set("room", $_POST['select_room'], ["room", "allocation"])) HTML::HTMLerror("Failed to change room");
				else HTML::HTMLsuccess("Successfully changed room!");
			}
		}?>
		<div class="container">
		<form action="" method="POST">
		<p>Switch to another ballot or group of students:
		<select name = "switch_group">
			<option value = "">Please Select</option>
			<?if ($group_ballot != 0){?><option value = "0">Not Yet Registered</option><?}?>
			<?if ($group_ballot != 1){?><option value = "1">Housing Ballot</option><?}?>
			<?if ($group_ballot != 2){?><option value = "2">Room Ballot</option><?}?>
			<?if ($group_ballot != 3){?><option value = "3">Opted Out of Balloting</option><?}?>
		</select>
		<input type="submit" name="submit_switch" value="Switch"><input type="hidden" name="current_group" value="<?= $group_ballot ?>">
		<?switch($group_ballot){
			case 0:
				$priorities = ["Maximum Priority" => 0, "Second year or third year abroad" => 1, "Third year with access arrangements" => 2, "Third year" => 3, "First year with access arrangements" => 4, "First year" => 5];
				?>
				<p>Add a new student to the ballot:</p>
				<p>Name: <input type = "text" name = "new_name" maxlength="255"></p>
				<p>CRSID: <input type = "text" name = "new_id" maxlength="7"></p>
				<p>Priority: 
					<select name = "new_priority">
					<option value = "">Please Select</option>
					<option value = "0">Second Year/Third Year Abroad with access arrangements or Maximum Priority</option>
					<option value = "1">Second Year/Third Year Abroad</option>
					<option value = "2">Third Year with access arrangements</option>
					<option value = "3">Third Year</option>
					<option value = "4">First Year with access arrangements</option>
					<option value = "5">First Year</option>
					</select>
				</p>
				<p><input type = "submit" name = "submit_new" value = "Add Student to Housing Ballot"/></p>
				<hr>
				<table>
				<thead><tr>
				<td>Name</td>
				<td>CRSID</td>
				</tr></thead>
				<?foreach($priorities as $priority_name => $priority){
					$condition = "`ballot` = 0 AND `priority` = ".$priority;
					$users = Table::get_all("user", "`ballot` = 0");
					if ($users != NULL){?>
						<tr><td colspan = "2"><?= $priority_name ?></td></tr>
						<?foreach($users as $user){?>
							<tr>
							<td><?= $user->get("name") ?></td>
							<td><?= $user->get("id") ?></td>
							</tr>
						<?}
					}
				}?>
				</table>
				<?break;
			case 3:
				$users = Table::get_all("user", "`ballot` = 3");?>
				<table>
				<thead><tr>
				<td>Name</td>
				<td>CRSID</td>
				<td>Reason</td>
				</tr></thead>
				<?foreach($users as $user){?>
					<tr>
					<td><?= $user->get("name") ?></td>
					<td><?= $user->get("id") ?></td>
					<td><?= $user->get("reason") ?></td>
					</tr>
				<?}?>
				</table>
				<?break;
			case 1:
				$priorities = ["Groups of 9" => 1, "Groups of 8" => 2, "Groups of 7" => 3, "Groups of 6" => 4, "Groups of 5" => 5, "Groups of 4" => 6, "Groups of less than 3" => 7];?>
				<p>Add a new student to the ballot:</p>
				<p>Name: <input type = "text" name = "new_name" maxlength="255"></p>
				<p>CRSID: <input type = "text" name = "new_id" maxlength="7"></p>
				<p>Priority: 
					<select name = "new_priority">
					<option value = "">Please Select</option>
					<option value = "5">First Year</option>
					<option value = "4">First Year with access arrangements</option>
					</select>
				</p>
				<p><input type = "submit" name = "submit_new" value = "Add Student to Housing Ballot"/></p>
				<hr>
				<?if ($stage == 1){?>
					<p>To change a group's position in the ballot, select them from the table below and enter their new position: 
					<input type="text" name="new_position" maxlength="3"> <input type="submit" name="submit_postion" value="Update balloting position"></p>
					<p>To change a group's allocation slot in the ballot, select them from the table below and enter the date: 
					<input type="text" name="new_date" value = "dd/mm hh:mm" maxlength="11"> <input type="submit" name="submit_date" value="Update allocation slot"></p>
				<?}
				elseif ($stage > 1){
					if ($stage == 2){?><p><strong>Current Position in ballot: <?= $ballot->get("position") ?></strong></p><?}?>
					<p>To swap two students rooms or two groups houses, select them both from the table below: <input type="submit" name="submit_swap" value="Swap Rooms/Houses"/></p>
				<?}?>
				<p>To remove a student instead, select them below and press here <input type="submit" name="remove_user" value="Remove Student"></p>
				<table class="table table-condensed table-bordered table-hover">
				<thead><tr>
				<td>Position</td>
				<td>Allocation slot</td>
				<td>Select</td>
				<td>Students</td>
				<td>Room</td>
				</tr></thead>
				<?foreach ($priorities as $priority => $value){
					$condition = "`priority` = ".$value." ORDER BY `order`";
					$groups = Table::get_all("housing_ballot", $condition);
					if ($groups != NULL){?>
						<tr><td colspan = "5"><?= $priority?></td></tr>
						<?foreach($groups as $group){
							$number = $group->count("crsids");
							$first = 1;
							$house_info = $group->get("house");
							if ($house_info != NULL){
								$house = new Table("house", $house_info);
								$house_info = $house->get("name");
							}?>
							<tr>
							<td rowspan = "<?=$number?>"><?= $group->get("order")?></td>
							<td rowspan = "<?=$number?>"><?= $group->get("date")?></td>
							<td rowspan = "<?=$number?>"><?= $house_info?> <input type = "radio" name = "select_group" value = "<?= $group->get("id") ?>"/></td>
							<?foreach($group->get("crsids", 1) as $member_id){
								if ($first) $first = 0;
								else echo("<tr>");
								$member = new Table("user", $member_id);?>
								<td><?= $member->get("name")?> <input type = "checkbox" name = "select_member[]" value = "<?= $member_id ?>"/></td>
								<td><?= $member->get("room")?></td>
								</tr>
							<?}?>
						<?}
					}
				}?>
				</table>
				<?break;
			case 2:
				$priorities = ["Second Years and Third Years Abroad" => 1, "Third Years with confirmed fourth" => 2, "First Years" => 3 ];?>
				<p>Add a new student to the ballot</p>
				<p>Name: <input type = "text" name = "new_name" maxlength="255"></p>
				<p>CRSID: <input type = "text" name = "new_id" maxlength="7"></p>
				<p>Priority: 
					<select name = "new_priority">
					<option value = "">Please Select</option>
					<option value = "0">Second Year/Third Year Abroad with access arrangements or Maximum Priority</option>
					<option value = "1">Second Year/Third Year Abroad</option>
					<option value = "2">Third Year with access arrangements</option>
					<option value = "3">Third Year</option>
					<option value = "4">First Year with access arrangements</option>
					<option value = "5">First Year</option>
					</select>
				</p>
				<p><input type = "submit" name = "submit_new" value = "Add Student to Housing Ballot"/></p>
				<hr>
				<?if ($stage == 4){?>
					<p>To change a group's position in the ballot, select them from the table below and enter their new position: 
					<input type="text" name="new_position" maxlength="3"> <input type="submit" name="submit_postion" value="Update balloting position"></p>
					<p>To change a group's allocation slot in the ballot, select them from the table below and enter the date (24hr clock): 
					<input type="text" name="new_date" value = "dd/mm hh:mm" maxlength="11"> <input type="submit" name="submit_date" value="Update allocation slot"></p>
				<?}
				elseif ($stage > 4){
					if ($stage == 5){?><p><strong>Current Position in ballot: <?= $ballot->get("position") ?></strong></p><?}?>
					<p>To swap two students rooms, select them both from the table below: <input type="submit" name="submit_swap" value="Swap Rooms"/></p>
					<p>To allocate a student a different room, 
					<select name = "select_room">
					<option value = "">please select a room</option>
					<?foreach(Table::get_all("room", "`available` = 1") as $room){?><option value = "<?=$room->get("id")?>"><?= $room->get("name") ?></option><?}?>
					</select> and select the student from the table below: <input type="submit" name="submit_change" value="Allocate Room"/></p>
					
				<?}?>
				<p>To remove a student instead, select them below and press here <input type="submit" name="remove_user" value="Remove Student"></p>
				<table class="table table-condensed table-bordered table-hover">
				<thead><tr>
				<td>Position</td>
				<td>Allocation slot</td>
				<td>House</td>
				<td>Students</td>
				<td>Rooms</td>
				</tr></thead>
				<?foreach ($priorities as $priority => $value){
					$condition = "`size` = ".$value." ORDER BY `order`";
					$groups = Table::get_all("room_ballot", $condition);
					if ($groups != NULL){?>
						<tr><td colspan = "5"><?= $priority?></td></tr>
						<?foreach($groups as $group){
							$number = $group->count("crsids");
							$first = 1;?>
							<tr>
							<td rowspan = "<?=$number?>"><?= $group->get("order")?></td>
							<td rowspan = "<?=$number?>"><?= $group->get("date")?></td>
							<td rowspan = "<?=$number?>"><input type = "radio" name = "select_group" value = "<?= $group->get("id") ?>"/></td>
							<?foreach($group->get("crsids", 1) as $member_id){
								if ($first) $first = 0;
								else echo("<tr>");
								$member = new Table("user", $member_id);?>
								<td><?= $member->get("name")?> <input type = "checkbox" name = "select_member[]" value = "<?= $member_id ?>"/></td>
								<td><?= $member->get("room")?></td>
								</tr>
							<?}?>
						<?}
					}
				}?>
				</table>
				<?break;
		}?>
		
		</form>
		</div>
		<?
		/*$user = new Table();
		if (isset($_POST['submit_switch'])) {
			if (isset($_POST['current_ballot'])) {
				if ($_POST['current_ballot'] == "room_ballot")
					$ballot = new BallotMaker(0);
				else
					$ballot = new BallotMaker(1);
			} else
				throw new Exception("Unable to switch ballot");
		} else
			$ballot = new BallotMaker();
		if (! $user->isadmin()) {
			HTML::HTMLerror("You do not have admin permission");
			return;
		} elseif ($ballot->getStage() == 6) {
			HTML::HTMLerror("The previous ballot has been closed. The ballot editor is only available when a ballot is currently in process");
			return;
		} else {
			if ($ballot->getStage() == 0)
				$ballot->showRemainingGroups();
			if (isset($_POST['submit_remove'])) {
				$selected = 0;
				if (isset($_POST['select_user']))
					$selected = 1;
				if (isset($_POST['select_group']))
					$selected += 2;
				if ($selected == 1) {
					$errors = 0;
					foreach ($_POST['select_user'] as $selected_user) {
						if (! $errors) {
							if ($selected_user == "")
								$errors = 1;
							else {
								$temp_user = new Table($selected_user);
								$temp_group = new Action($temp_user->getGroup(), $temp_user->getBallot());
								if (! $temp_user->destroy_user())
									$errors = 1;
							}
						}
					}
					if (! $errors)
						HTML::HTMLsuccess("Selected users removed from ballot");
					else
						HTML::HTMLerror("An error occured removing a user from the ballot, please contact jcr.website@fitz.cam.ac.uk");
				} elseif ($selected == 2) {
					$errors = 0;
					foreach ($_POST['select_group'] as $selected_group) {
						if (! $errors) {
							if ($selected_group == "")
								$errors = 1;
							else {
								$group = new Action($selected_group, $ballot->getName());
								$remove_members = $group->getMembers();
								array_shift($remove_members);
								if (! $group->remove_members($remove_members))
									$errors = 1;
							}
						}
					}
					if (! $errors)
						HTML::HTMLsuccess("Selected group removed from ballot");
					else
						HTML::HTMLerror("Failed to remove group from ballot");
				} else
					HTML::HTMLerror("Please select either users or groups to be removed from the ballot at a time");
			}
			if (isset($_POST['submit_position'])) {
				$errors = 0;
				if (! isset($_POST['new_position']) || $_POST['new_position'] == "") {
					$errors = 1;
					HTML::HTMLerror("You must enter a new position for the group to recieve");
				} elseif (HTML::IntegerChecker($_POST['new_position'])) {
					$errors = 1;
					HTML::HTMLerror("");
				}
				if (! isset($_POST['select_group']) || $_POST['select_group'] == "" || count($_POST['select_group']) != 1) {
					$errors = 1;
					HTML::HTMLerror("You must select one group to change its position");
				}
				if (! $errors) {
					$group = new Action($_POST['select_group'][0]);
					if ($_POST['new_position'] == $group->getOrder())
						HTML::HTMLerror("A different position needs to be entered for it to be changed");
					else {
						if ($group->newPosition($_POST['new_position']))
							HTML::HTMLsuccess("Position of group adjusted");
						else
							HTML::HTMLerror("Failed to alter group's position");
					}
				}
			}
			if (isset($_POST['submit_swap'])) {
				if (isset($_POST['select_user'])) {
					$user_room = array();
					$user_1 = null;
					foreach ($_POST['select_user'] as $selected_user) {
						if ($user_1 == null)
							$user_1 = $selected_user;
						else
							$user_2 = $selected_user;
						$temp_user = new Table($selected_user);
						$user_room[$temp_user->getCRSID()] = $temp_user->getRoom();
					}
					if (count($user_room) != 2)
						HTML::HTMLerror("You can only select two users from the table below to swap their position");
					else {
						Database::getInstance()->query("begin");
						$query = "UPDATE `users` SET `room` = " . $user_room[$user_1] . " WHERE `crsid` = '" . $user_2 . "'";
						$result = Database::getInstance()->query($query);
						if ($result) {
							$query = "UPDATE `users` SET `room` = " . $user_room[$user_2] . " WHERE `crsid` = '" . $user_1 . "'";
							$result = Database::getInstance()->query($query);
							if ($result) {
								Database::getInstance()->query("commit");
								HTML::HTMLsuccess("Successfully swapped users rooms");
							} else {
								Database::getInstance()->query("rollback");
								HTML::HTMLerror("An error occured swapping the users rooms, please contact jcr.website@fitz.cam.ac.uk");
							}
						} else {
							Database::getInstance()->query("rollback");
							HTML::HTMLerror("An error occured swapping the users rooms, please contact jcr.website@fitz.cam.ac.uk");
						}
					}
				} else
					HTML::HTMLerror("You must select two users from the table below to swap their rooms");
			}		
}*/
	}
}