<?php
class YourPage{

	public static function register($priority, $stage){?>
		<h3>Register:</h3><?
		if ($stage > 3) echo ("<p>Sorry, all ballots are currently closed for registration</p>");
		else {?>
			<form action="" method="POST">
			<p>Ballot: <select name="select_ballot">
			<option value="">Please select</option>
			<option value="0">Opt out of either ballot</option>
			<option value="2">Room Ballot</option>
   			<?if ($stage == 0 && $priority > 3) echo('<option value = "1">Housing Ballot</option>');?>
    		</select></p>
			<input type="checkbox" name="consent" /> <label for="consent">I consent to my data being used for the Fitzwilliam JCR Room Ballot</label><br />
			<input type="submit" name="submit_register" value="Submit" />
			</form>
		<?}
	}

	public static function group_editor($user, $user_ballot, $ballot_name){
		$group = new Table($ballot_name, $user->get("group_id"));
		$members = $group->get("crsids", 1);
		if ($user->get("id") == $members[0])$group_admin = 1;
		else $group_admin = 0;
		if ($group->count("requesting") > 0) $requesting = 1;
		else $requesting = 0;
		if ($user->get("searching")) $searching = 1;
		else $searching = 0?>
		<form action="" method="POST">
		<table class="table table-condensed table-bordered table-hover">
		<thead><tr>
		<td>Your Group:</td>
		<?if ($group_admin) echo("<td>Remove</td>");  ?>
		</tr></thead>
		<?if ($requesting){?>
			<tr>
			<td <?if ($group_admin) echo('colspan = "2"'); ?>><strong>Members</strong></td>
			</tr>
		<?}
		$first_member = 1;
		foreach ($members as $member_crsid) {
			$member = new Table("user", $member_crsid);?>
			<tr>
			<td><?= $member->get("name") ?></td>
			<?if ($group_admin) {
				if ($first_member) {
					$first_member = 0;
					echo ("<td></td>");
				}
				else echo ('<td><input type = "checkbox" name = "members[]" value = "' . $member_crsid . '"></td>');
			}?>
 			</tr>
 		<?}
		if ($requesting){?>
			<tr>
			<td <?if ($group_admin) echo('colspan = "2"'); ?>><strong>Requests</strong></td>
			</tr>
 			<?foreach ($group->get("requesting", 1) as $requesting_crsid) {
				$requesting_user = new Table("user", $requesting_crsid);?>
 				<tr>
				<td><?=$requesting_user->get("name")?></td>
 				<?if ($group_admin) echo('<td><input type = "checkbox" name = "requesting[]" value = "'.$requesting.'"></td>');?>
 				</tr>
 			<?}
		}?>	
		</table>	
 		<?if ($group_admin) echo ('<p><input type = "submit" name = "submit_remove" value = "Remove selected members/requests from group"></p>');
		if ($group->get("size") > 1 && $searching) echo ('<input type = "submit" name = "submit_leave" value = "Leave Group and Ballot Alone"></p>');
		if ($searching) {?>
			<p>
			<input type="submit" name="submit_lock" value="Lock in with whom you are balloting">
			</p>
			<?if ($user->count("requests") > 0){?>
				<table class="table table-condensed table-bordered table-hover">
				<thead><tr>
				<td>Group and Current Members</td>
				<td>Others Requested</td>
				<td>Select</td>
				</tr></thead>
				<?foreach ($user->get("requests", 1) as $group_id) {
					$requested_group = new Table($ballot_name, $group_id);?>
					<tr>
					<td class="col-md-4">
					<?$names = array();
					foreach ($requested_group->get("crsids", 1) as $member_crsid) {
						$member = new Table("user", $member_crsid);
						array_push($names, $member->get("name"));
					}
					echo (implode("<br>", $names));?>
					</td>
					<td class="col-md-4"><?
					if ($requested_group->count("requesting") > 0) {
						$requesting_names = array();
						foreach ($requested_group->get("requesting", 1) as $requesting_crsid) {
							$requesting_user = new Table("user", $requesting_crsid);
							array_push($requesting_names, $requesting_user->get("name"));
						}
						echo (implode("<br>", $requesting_names));
					}?>
					</td>
					<td class="col-md-1" style="text-align: left;"><input type="checkbox" name="group_select[]" value="<?= $group_id?>"></td>
					</tr>
				<?}?>
				</table>
				<p>
				<input type="submit" name="submit_join" value="Join Selected Group" />
				<input type="submit" name="submit_decline" value="Decline Selected Group" />
				</p>
			<?}
			if ($group_admin && $group->get("size") < 9) {?>
				<table class="table table-condensed table-bordered table-hover">
				<thead><tr>
				<td>Request others to join your group</td>
				<td>Select</td>
				</tr></thead>
				<?$other_crsids = $members;
				if ($requesting) array_merge($other_crsids, $group->get("requesting", 1));
				$all_users = Table::get_all("user", "`searching` = 1 AND `ballot` = " . $user_ballot . " AND `crsid` NOT IN ('" . implode("', '", $other_crsids) . "')");
				foreach ($all_users as $other_user) {?>
					<tr>
					<td class="col-md-8"><?= $other_user->get("name")?></td>
					<td class="col-md-1" style="text-align: left;"><input type="checkbox" name="requests[]" value="<?= $other_user->get("id")?>"></td>
					</tr>
				<?}?>
				</table>
				<p>
				<input type="submit" name="submit_request" value="Request Selected to Join Group" />
				</p>
			<?}
		}
		else {?>
			<p>You are locked in with whom you are balloting, this prevents you from being sent requests and sending requests to join groups.
 			However, you are still able to become another student's proxy. Note, if balloting within a group, the remaining members may still not be locked in. 
 			Therefore, it is still possible for your balloting group to change. If you wish to leave or alter your group you can unlock yourself below.</p>
			<input type="submit" name="submit_unlock" value="Unlock with whom you are balloting">
		<?}?>
 		</form>
 	<?}
 	
 	public static function room_selector($user, $user_ballot, $ballot_name, $ballot, $stage){
 		$group = new Table($ballot_name, $user->get("group_id"));
 		$members = $group->get("crsids", 1);
 		if ($user->get("id") == $members[0]) $group_admin = 1;
		else $group_admin = 0;
		if ($group->count("proxy") > 0) $is_proxy = 1;
		else $is_proxy = 0;
		if ($user->get("room") != NULL) $allocated = 1;
		else $allocated = 0;?>
		<form action="" method="POST">
		<p><strong>Proxy</strong>: 
		<? $proxy = $group->get("proxy");
		if ($proxy != NULL) echo($proxy);
		else echo("no proxy");?>
		</p>
		<?if ($group_admin){?>
			<p>If you would like to change your group's proxy: <select name="proxy">
				<option value="">Please select</option>
   				<? $condition = "`id` != '".$user->get("id")."' AND `ballot` = ".$user_ballot;
   				foreach(Table::get_all("user", $condition) as $proxy) echo('<option value = "'.$proxy->get("id").'">'.$proxy->get("name").'</option>');?>
    		</select> <input type="submit" name="submit_proxy" value="Submit" /></p>
    	<?}?>
		<table class="table table-condensed table-bordered table-hover">
		<thead><tr>
		<td>Position</td>
		<td>Group Members</td>
		</tr></thead>
		<tr><td colspan="2"><h3>Your Group</h3></td></tr>
        <tr>
		<td class="col-md-1" style="text-align: right;"><?= $group->get("order")?></td>
		<td class="col-md-8"><?
		$names = array();
		foreach ($members as $member_crsid) {
			$member = new Table("user", $member_crsid);
			array_push($names, $member->get("name"));
		}
		echo (implode("<br>", $names));?>
		</td>
		</tr>
		<?if ($is_proxy){
			$proxies = $user->get("proxies", 1);?>
			<tr><td colspan="2"><h3>Groups you are proxy for</h3></td></tr>
  			<?foreach($proxies as $proxy_id){
  				$proxy_group = new Table($ballot_name, $proxy_id);?>
  				<tr>
				<td class="col-md-1" style="text-align: right;"><?= $proxy_group->get("order")?></td>
				<td class="col-md-8"><?
				$names = array();
				foreach ($proxy_group->get("crsids", 1) as $member_crsid) {
					$member = new Table("user", $member_crsid);
					array_push($names, $member->get("name"));
				}
				echo (implode("<br>", $names));?>
				</td>
				</tr>
  			<?}
  		}?>
   		</table>
		<?if ($user_ballot == 1){?>
			<h3>House Selection:</h3>
			<?if ($stage == 1){?>
				<p>The ballot is not yet ready for house selection to begin</p>
			<?}
			else {
				if ($user->get("id") == $ballot->get("proxy")){
					$group_id = $ballot->get("group");
					$group = new Table("housing_ballot", $group_id);
					$group_admin_id = $group->get("crsids", 1)[0];
					$group_admin_o = new Table("user", $group_admin_id);?>
					<input type="hidden" name="group_id" value="<?= $group_id?>">
					<p>Please select a house for the group containing <?=$group_admin_o->get("name")?>, from the following:</p>
					<table class="table table-condensed table-bordered table-hover">
					<thead><tr><td>House:</td></tr></thead>
					<?$condition = "`house` = 1 AND `available` = 1 AND `size` = ".$group->get("size");
					foreach(Table::get_all("house", $condition) as $house){?>
						<tr><td><input type = "radio" name = "select_house" value = "<?=$house->get("id")?>"> <?=$house->get("name")?></td></tr>
					<?}?>
  					</table>
					<p>Please confirm you have selected the above house for their group <input type="checkbox" name="confirm_choice" value="confirm"></p>
					<input type="submit" name="submit_house" value="Submit your choice">
				<?}
				elseif ($allocated){
					$room = new Table("room", $user->get("room"));
					$house_id = $room->get("house");
					$house = new Table("house", $house_id);?>
					<h3>Your Room:</h3>
					<p><strong><?= $room->get("name")?></strong>: <?= $house->get("name")?>, <?=$house->get("floor")?></p>
					<p><strong>Previous rent</strong>: <?= $room->get("price")?> pounds/week</p>
   					<hr>
   					<p>Others in your house:</p>
					<table class="table table-condensed table-bordered table-hover">
					<thead><tr>
					<td>Room</td>
					<td>Housemate</td>
					</tr></thead>
   					<?$condition = "`room` IN (SELECT `id` FROM `rooms` WHERE `house` = ".$house_id.") ORDER BY `room` ASC";
   					$flatmates = Table::get_all("user", $condition);
   					foreach ($flatmates as $flatmate) {
   						$room = new Table("room", $flatmate->get("room"));?>
						<tr>
						<td><?= $room->get("name")?></td>
						<td><?= $flatmate->get("name")?></td>
						</tr>
					<?}?>
   					</table>
				<?}
				elseif ($group->get("position") < $ballot->get("position")){?>
					<p>It is not yet your turn to pick a house in the ballot, please wait for now.</p>
  					<p><strong>Current position in the ballot</strong>: <?= $ballot->get("position")?></p>
				<?}
				elseif ($group->get("position") == $ballot->get("position")){
					if ($group_admin){?>
  						<p>Please select a house for your group, from the following:</p>
						<table class="table table-condensed table-bordered table-hover">
						<thead><tr><td>House:</td></tr></thead>
							<?$condition = "`house` = 1 AND `available` = 1 AND `size` = ".$group->get("size");
						foreach(Table::get_all("house", $condition) as $house){?>
							<tr><td><input type = "radio" name = "select_house" value = "<?=$house->get("id")?>"> <?=$house->get("name")?></td></tr>
						<?}?>
  						</table>
						<p>Please confirm you have selected the above house for your group <input type="checkbox" name="confirm_choice" value="confirm"></p>
						<input type="submit" name="submit_house" value="Submit your choice">
  					<?}
  					else {?>
  						<p>Your group admin has not yet allocated your selected rooms between your group members</p>
  					<?}
				}
  				else {
  					if ($group_admin){
  						$house = new Table("house", $group->get("house"))?>
  						<p>Assign specific rooms to specific group members:</p>
  						<table class="table table-condensed table-bordered table-hover">
  						<thead><tr>
  						<td>Rooms:</td>
  						<td>Group Members:</td>
  						</tr></thead>
  						<?foreach($house->get("rooms", 1) as $room_id){
   							$room = new Table("room", $room_id);?>
   							<tr>
							<td><?= $room->get("name")?></td>
							<td><select name="select_<?= $room_id ?>">
								<option value="">Please select</option>
   								<?foreach ($group->get("crsids") as $member_id) {
									$member = new Table("user", $member_id);
									echo ('<option value = "'.$member_id.'">'.$member->get("id").'</option>');
								}?>
   							</select></td>
							</tr>
   						<?}?>
   						</table>
						<p>Please confirm the above room allocations <input type="checkbox" name="confirm_allocations" value="confirm"></p>
						<input type="submit" name="submit_allocations" value="Submit Room Allocations">
  					<?}
  					else {?>
  						<p>Your group admin has not yet allocated your selected rooms between your group members</p>
  					<?}
  				}
			}
  		}
		else {?>
			<h3>Room Selection:</h3>
			<?if ($stage == 4){?>
				<p>The ballot is not yet ready for room selection to begin</p>
			<?}
			else {
				if ($user->get("id") == $ballot->get("proxy")){
					$group_id = $ballot->get("group");
					$group = new Table("room_ballot", $group_id);
					$group_admin_id = $group->get("crsids", 1)[0];
					$group_admin_o = new Table("user", $group_admin_id);?>
					<input type="hidden" name="group_id" value="<?= $group_id?>">
					<p>Please select <?= $group->get("size")?> room(s) for the group containing <?=$group_admin_o->get("name")?>, from the following:</p>
					<input type="hidden" name="group_size" value="<?= $group->get("size")?>">
					<table class="table table-condensed table-bordered table-hover">
					<thead><tr>
					<td>Floor:</td>
					<td>Rooms:</td>
					</tr></thead>
					<?foreach(Table::get_all("house", "`house` = 0") as $house){?>
						<tr><td colspan="2"><h3><?= $house->get("name")?></h3></td></tr>
						<?$floors = ["Basement" => 0, "Ground Floor" => 1, "First Floor" => 2, "Second Floor" => 3, "Third Floor" => 4, "Attic" => 5];
						foreach($floors as $name => $value){
							$condition = "`id` IN (".$house->get("rooms").") AND `available` = 1 AND `floor` = ".$value;
							$rooms = Table::get_all("room", $condition);
							if ($rooms != NULL){?>
								<tr>
								<td><?=$name?></td>
								<td>
								<?$inputs = array();
								foreach ($rooms as $room_id) {
									$room = new Table("room", $room_id);
									$input = '<input type = "checkbox" name = "select_room[]" value = "'.$room_id.'"> '.$room->get("name");
									array_push($inputs, $input);
								}
								echo (implode("<br>", $inputs));?>
								</td>
								</tr>
							<?}
						}
					}?>
  					</table>
					<p>Please confirm you have selected the above room(s) for their group <input type="checkbox" name="confirm_choice" value="confirm"></p>
					<input type="submit" name="submit_room" value="Submit your choice">
				<?}
				elseif ($allocated){
					$room = new Table("room", $user->get("room"));
					$house_id = $room->get("house");
					$house = new Table("house", $house_id);
					$floor_id = $room->get("floor");
					switch($floor_id){
						case 0:
							$floor = "Basement";
							break;
						case 1:
							$floor = "Ground floor";
							break;
						case 2:
							$floor = "First floor";
							break;
						case 3:
							$floor = "Second floor";
							break;
						case 4:
							$floor = "Third floor";
							break;
						case 5:
							$floor = "Attic";
							break;
					}?>
					<h3>Your Room:</h3>
					<p><strong><?= $room->get("name")?></strong>: <?= $house->get("name")?>, <?=$floor?></p>
					<p><strong>Previous rent</strong>: <?= $room->get("price")?> pounds/week</p>
   					<hr>
   					<p>Others in your flat:</p>
					<table class="table table-condensed table-bordered table-hover">
					<thead><tr>
					<td>Room</td>
					<td>Flatmate</td>
					</tr></thead>
   					<?$condition = "`house` = ".$house_id." AND `floor` = ".$floor_id;
   					$rooms = Table::get_all("room", $condition);
   					foreach ($rooms as $room) {
   						$flatmate = new Table("user", $room->get("allocation"));?>
						<tr>
						<td><?= $room->get("name")?></td>
						<td><?= $flatmate->get("name")?></td>
						</tr>
					<?}?>
   					</table>
				<?}
				elseif ($group->get("position") < $ballot->get("position")){?>
					<p>It is not yet your turn to pick a house in the ballot, please wait for now.</p>
  					<p><strong>Current position in the ballot</strong>: <?= $ballot->get("position")?></p>
				<?}
				elseif ($group->get("position") == $ballot->get("position")){
					if ($group_admin){?>
  						<p>Please select <?= $group->get("size")?> room(s) for your group (or yourself), from the following:</p>
						<input type="hidden" name="group_size" value="<?= $group->get("size")?>">
						<table class="table table-condensed table-bordered table-hover">
						<thead><tr>
						<td>Floor:</td>
						<td>Rooms:</td>
						</tr></thead>
						<?foreach(Table::get_all("house", "`house` = 0") as $house){?>
							<tr><td colspan="2"><h3><?= $house->get("name")?></h3></td></tr>
							<?$floors = ["Basement" => 0, "Ground Floor" => 1, "First Floor" => 2, "Second Floor" => 3, "Third Floor" => 4, "Attic" => 5];
							foreach($floors as $name => $value){
								$condition = "`id` IN (".$house->get("rooms").") AND `available` = 1 AND `floor` = ".$value;
								$rooms = Table::get_all("room", $condition);
								if ($rooms != NULL){?>
									<tr>
									<td><?=$name?></td>
									<td>
									<?$inputs = array();
									foreach ($rooms as $room_id) {
										$room = new Table("room", $room_id);
										$input = '<input type = "checkbox" name = "select_room[]" value = "'.$room_id.'"> '.$room->get("name");
										array_push($inputs, $input);
									}
									echo (implode("<br>", $inputs));?>
									</td>
									</tr>
								<?}
							}
						}?>
  						</table>
						<p>Please confirm you have selected the above room(s) for your group (or yourself)<input type="checkbox" name="confirm_choice" value="confirm"></p>
						<input type="submit" name="submit_room" value="Submit your choice">
					<?}
  					else {?>
  						<p>Your group admin has not yet selected the rooms for your group</p>
  					<?}	
				}
  				else {
  					if ($group_admin){?>
  						<p>Assign specific rooms to specific group members:</p>
						<table class="table table-condensed table-bordered table-hover">
						<thead><tr>
							<td>Rooms:</td>
							<td>Group Members:</td>
						</tr></thead>
   						<?foreach($group->get("rooms", 1) as $room_id){
   							$room = new Table("room", $room_id);?>
   							<tr>
							<td><?= $room->get("name")?></td>
							<td><select name="select_<?= $room_id ?>">
								<option value="">Please select</option>
   								<?foreach ($group->get("crsids") as $member_id) {
									$member = new Table("user", $member_id);
									echo ('<option value = "'.$member_id.'">'.$member->get("id").'</option>');
								}?>
   							</select></td>
							</tr>
   						<?}?>
   						</table>
						<p>Please confirm the above room allocations <input type="checkbox" name="confirm_allocations" value="confirm"></p>
						<input type="submit" name="submit_allocations" value="Submit Room Allocations">
  					<?}
  					else {?>
  						<p>Your group admin has not yet allocated your selected rooms between your group members</p>
  					<?}
  				}
			}
		}?>
		</form>
		<?
 	}

	public static function page(){
		$user = new Table("user");
		$user_ballot = $user->get("ballot");
		$ballot = new Table("ballot");
		$stage = $ballot->get("stage");
		switch($user_ballot){
			case 1:
				$ballot_name = "housing_ballot";
				break;
			case 2:
				$ballot_name = "room_ballot";
				break;
			default:
				if (isset($_POST['submit_register'])){
					if (!isset($_POST['select_ballot']) || $_POST['select_ballot'] == "") HTML::HTMLerror("You need to select a ballot option before you can register");
					elseif (!isset($_POST['consent'])) HTML::HTMLerror("You must agree to the terms of the ballot before you can use this system");
					else{
						if ($user->set("ballot", $_POST['select_ballot'])) HTML::HTMLsuccess("Successfully changed ballot option!");
						else HTML::HTMLerror("Failed to change ballot option");
					}
				}
				break;
		}
		if ($user_ballot == 1 || $user_ballot == 2){
			if (isset($_POST['submit_remove'])){
				if (!isset($_POST['members']) && !isset($_POST['requesting'])) HTML::HTMLerror("You need to select a group member or request to remove them");
				else{
					$group = new Table($ballot_name, $user->get("group_id"));
					if (isset($_POST['members'])){
						$errors = 0;
						foreach ($_POST['members'] as $member){
							if (!$errors){
								if (!$group->remove("crsids", $member, ["user", "group_id"])){
									HTML::HTMLerror("Failed to remove member from group");
									$errors = 1;
								}
								elseif (!Table::insert($ballot_name, $member)){
									HTML::HTMLerror("Failed to add member back into ballot, please contact jcr.website@fitz.cam.ac.uk");
									$errors = 1;
								}
							}
						}
						if (!$errors) HTML::HTMLsuccess("Successfully removed member(s)!");
					}
					if (isset($_POST['requesting'])){
						$errors = 0;
						foreach ($_POST['requesting'] as $member){
							if (!$errors){
								if (!$group->remove("crsids", $member, ["user", "requests"])){
									HTML::HTMLerror("Failed to remove request");
									$errors = 1;
								}
								
							}
						}
						if (!$errors) HTML::HTMLsuccess("Successfully removed request(s)!");
					}
				}
			}
			if (isset($_POST['submit_leave'])){
				$group = new Table($ballot_name, $user->get("group_id"));
				$crsid = $user->get("id");
				if (!$group->remove("crsids", $crsid, ["user", "group_id"]))HTML::HTMLerror("Failed to remove self from group");
				elseif (!Table::insert($ballot_name, $crsid)) HTML::HTMLerror("Failed to add self back into ballot, please contact jcr.website@fitz.cam.ac.uk");
				else HTML::HTMLsuccess("Successfully left own group");
			}
			if (isset($_POST['submit_lock'])){
				if ($user->set("searching", 0)) HTML::HTMLsuccess("Successfully locked in with whom you are balloting");
				else HTML::HTMLerror("Failed to lock in with whom you are balloting");
			}
			if (isset($_POST['submit_join'])){
				if (isset($_POST['group_select']) && count($_POST['group_select']) == 1){
					$group = new Table($ballot_name, $_POST['group_select'][0]);
					$crsid = $user->get("id");
					if (!$group->add("crsids", $crsid, ["user", "group_id"])) HTML::HTMLerror("Failed to join group");
					elseif(!$group->remove("requesting", $crsid, ["user", "requests"])) HTML::HTMLerror("Failed to remove request");
					else HTML::HTMLsuccess("Successfully joined group!");
				}
				else HTML::HTMLerror("You need to select a single group before you can join it");
			}
			if (isset($_POST['submit_decline'])){
				if (isset($_POST['group_select'])){
					$crsid = $user->get("id");
					$errors = 0;
					foreach ($_POST['group_select'] as $group_id){
						if (!$errors){
							$group = new Table($ballot_name, $group_id);
							if (!$group->remove("requesting", $crsid, ["user", "requests"])){
								HTML::HTMLerror("Failed to remove group request");
								$errors = 1;
							}
						}
					}
					if (!$errors) HTML::HTMLsuccess("Successfully declined request(s)!");
				}
				else HTML::HTMLerror("You need to select a group before you can decline its request");
			}
			if (isset($_POST['submit_request'])){
				if (isset($_POST['requests']) && (count($_POST['requests']) + $group->get("size") <= 9)){
					$group = new Table($ballot_name, $user->get("group_id"));
					$errors = 0;
					foreach($_POST['requests'] as $request){
						if (!$errors){
							if (!$group->add("requesting", $request, ["user", "requests"])){
								HTML::HTMLerror("Failed to make group request");
								$errors = 1;
							}
						}
					}
					if (!$errors) HTML::HTMLsuccess("Successfully sent request(s)!");
				}
				else HTML::HTMLerror("You need to select at least one user, but less than will bring your group size above the max, before you can then send them requests");
			}
			if (isset($_POST['submit_unlock'])){
				if ($user->set("searching", 1)) HTML::HTMLsuccess("Successfully unlocked with whom you are balloting");
				else HTML::HTMLerror("Failed to unlock with whom you are balloting");
			}
			if (isset($_POST['submit_proxy'])){
				$group = new Table($ballot_name, $user->get("group_id"));
				if (!isset($_POST['proxy']) || $_POST['proxy'] == "") HTML::HTMLerror("You need to select someone to make your proxy before you can change it");
				elseif (!$group->set("proxy", $_POST['proxy'])) HTML::HTMLerror("Failed to change proxy");
				else HTML::HTMLsuccess("Successfully changed proxy!");
			}
			if (isset($_POST['submit_house'])){
				if (isset($_POST['group_id']) && $_POST['group_id'] != "") $group = new Table($ballot_name, $_POST['group_id']);
				else $group = new Table($ballot_name, $user->get("group_id"));
				if (!isset($_POST['confirm_choice'])) HTML::HTMLerror("You need to confirm your choice");
				elseif (!isset($_POST['select_house'])) HTML::HTMLerror("You need to select a house before you can be allocated it");
				else {
					$house_id = $_POST['select_house'];
					$house = new Table("house", $house_id);
					if (!$group->set("house", $_POST['select_house'])) HTML::HTMLerror("Failed to allocate house");
					elseif (!$house->set("available", 0)) HTML::HTMLerror("Failed to make house unavailable");
					else HTML::HTMLsuccess("Successfully allocated house to your group!");
				}
			}
			if (isset($_POST['submit_allocations'])){
				if (!isset($_POST['confirm_allocations'])) HTML::HTMLerror("You need to confirm your allocations");
				else {
					$group = new Table($ballot_name, $user->get("group_id"));
					$allocations = array();
					foreach($group->get("crsids", 1) as $crsid) $allocations[$crsid] = 0;
					$errors = 0;
					if ($ballot_name == "room_ballot") $rooms = $group->get("rooms", 1);
					else{
						$house = new Table("house", $group->get("house"));
						$rooms = $house->get("rooms", 1);
					}
					foreach($rooms as $room_id){
						if (!$errors){
							$room_post = "select_".$room_id;
							if (!isset($_POST[$room_post]) || $_POST[$room_post] == ""){
								HTML::HTMLerror("You need to select one member per room without repeats");
								$errors = 1;
							}
							else {
								$crsid = $_POST[$room_post];
								$allocations[$crsid] = $allocations[$crsid] + 1;
								$user = new Table("user", $crsid);
								if (!$user->set("room", $room_id)) HTML::HTMLerror("Failed to allocate room to user");
							}
						}
					}
					if (!$errors){
						foreach($allocations as $number){
							if (!$errors){
								if ($number != 1){
									HTML::HTMLerror("You need to select one member per room without repeats");
									$errors = 1;
								}
							}
						}
					}
					if (!$errors) HTML::HTMLsuccess("Successfully allocated rooms to group members");
				}
			}
			if (isset($_POST['submit_room'])){
				if (isset($_POST['group_id']) && $_POST['group_id'] != "") $group = new Table($ballot_name, $_POST['group_id']);
				else $group = new Table($ballot_name, $user->get("group_id"));
				if (!isset($_POST['confirm_choice'])) HTML::HTMLerror("You need to confirm your choice");
				elseif (!isset($_POST['select_room']) || (count($_POST['select_room']) != $group->get("size"))) HTML::HTMLerror("You need to select as many rooms as there are group members before they can be allocated");
				else {
					$errors = 0;
					foreach($_POST['select_room'] as $room_id){
						if (!$errors){
							$room = new Table("room", $room_id);
							if (!$group->add("rooms", $room_id)){
								HTML::HTMLerror("Failed to allocate room to group");
								$errors = 1;
							}
							elseif (!$room->set("available", 0)){
								HTML::HTMLerror("Failed to make room unavailable");
								$errors = 1;
							}
						}
						if (!$errors) HTML::HTMLsuccess("Successfully allocated rooms to your group!");
					}
				}
			}?>
			<div class="container">
			<h3>Your Details:</h3>
			<p><strong>Name</strong>: <?= $user->get("name")?></p>
			<p><strong>Current year/priority</strong>: 
			<?$priority = $user->get("priority");
			switch ($priority) {
				case 0:
					echo ("Due to access arrangements, you have been given highest priority");
					break;
				case 1:
					echo ("Second year or third year abroad, this gives you top priority");
					break;
				case 2:
					echo ("Due to access arrangements, you have been given priority above all other third years");
					break;
				case 3:
					echo ("Third year, this gives you middle priority");
					break;
				case 4:
					echo ("Due to access arrangements, you have been given prioritiy above all other first years (this only applies for the room ballot, not the housing ballot)");
					break;
				case 5:
					echo ("First year, this gives you bottom priority within the room ballot but eligible for the housing ballot");
					break;
			}?>
			</p>
			<p><strong>Ballot</strong>:
			<?switch ($user_ballot) {
				case 1:
					?>Housing ballot</p>
					<?$ballot_name = "housing_ballot";
					if ($stage == 0) YourPage::group_editor($user, $user_ballot, $ballot_name);
					else YourPage::room_selector($user, $user_ballot, $ballot_name, $ballot, $stage);
					break;
				case 2:
					?>Room ballot</p>
					<?$ballot_name = "room_ballot";
					if ($stage < 4) YourPage::group_editor($user, $user_ballot, $ballot_name);
					else YourPage::room_selector($user, $user_ballot, $ballot_name, $ballot, $stage);
					break;
				case 3:
					?>You have opted out from both/either ballot, however, if you change your mind you are still able to register</p>
					<?YourPage::register($priority, $stage);
					break;
				case 0:
					?>You have not yet registered for a ballot</p>
    				<?YourPage::register($priority, $stage);
					break;
			}?>
			</div>
		<?}
	}
}	