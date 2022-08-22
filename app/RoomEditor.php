<?php
class RoomEditor{

	public static function page(){
		$ballot = new Table("ballot");
		if (isset($_POST['submit_switch'])) $houses_or_blocks = ($_POST['current_house_block'] + 1) % 2;
		elseif ($ballot->get("stage") < 4) $houses_or_blocks = 1;
		else $houses_or_blocks = 0;
		if (isset($_POST['submit_house'])){
			if (isset($_POST['house_name']) && $_POST['house_name'] != ""){
				if (isset($_POST['select_house'])) HTML::HTMLerror("You need to enter a new house/block name OR select a house/block from the table, not both");
				else {
					$values = $_POST['house_name'].",".$houses_or_blocks;
					$house_id = Table::insert("house", $values);
					if ($house_id == NULL) HTML::HTMLerror("Failed to create new house/block");
				}
			}
			elseif (isset($_POST['select_house'])) $house_id = $_POST['select_house'];
			else {
				$house_id = NULL;
				HTML::HTMLerror("You need to enter a new house/block name or select a house/block from the table");
			}
			if ($house_id != NULL){
				$house = new Table("house", $house_id);
				if (isset($_POST['description'])){
					if ($house->set("description", $_POST['description'])) HTML::HTMLsuccess("Successfully updated house/block description!");
					else HTML::HTMLerror("Failed to update house/block description");
				}
				if ($houses_or_blocks){
					if (isset($_POST['available'])){
						if ($house->set("available", $_POST['available'])) HTML::HTMLsuccess("Successfully change house availability!");
						else HTML::HTMLerror("Failed to change house availability");
					}
				}
			}
		}
		if (isset($_POST['remove_house'])){
			if (!isset($_POST['select_house'])) HTML::HTMLerror("You need to select a house before it can be removed");
			else {
				$house = new Table("house", $_POST['select_house']);
				$errors = 0;
				foreach($house->get("rooms", 1) as $room_id){
					$room = new Table("room", $room_id);
					if (!$room->delete(["house","house","rooms"])){
						$errors = 1;
						break;
					}
				}
				if (!$errors){
					if ($house->delete()) HTML::HTMLsuccess("Successfully removed house!");
					else HTML::HTMLerror("Failed to remove house");
				}
				else HTML::HTMLerror("Failed to remove rooms");
			}
		}
		if (isset($_POST['submit_room'])){
			if (isset($_POST['room_name']) && $_POST['room_name'] != ""){
				if (isset($_POST['select_room'])) HTML::HTMLerror("You need to enter a new room name OR select ONE from the table, not both");
				elseif (!isset($_POST['select_house'])) HTML::HTMLerror("You need to select a house which the room is to be added to");
				else{
					$values = $_POST['room_name'].",".$_POST['select_house'];
					$room = Table::insert("room", $values);
					if ($room == NULL) HTML::HTMLerror("Failed to create new room");
				}
			}
			elseif (isset($_POST['select_room']) && count($_POST['select_room']) == 1) {
				$room = new Table("room", $_POST['select_room'][0]);
				if (isset($_POST['select_house'])){
					if ($room->set("house", $_POST['select_house'])) HTML::HTMLsuccess("Successfully updated room's house!");
					else HTML::HTMLerror("Failed to update room's house");
				}
			}
			else {
				$room = NULL;
				HTML::HTMLerror("You need to enter a new room or select ONE room from the table");
			}
			if ($room != NULL){
				if (isset($_POST['floor'])){
					if ($room->set("floor", $_POST['floor'])) HTML::HTMLsuccess("Successfully updated room floor!");
					else HTML::HTMLerror("Failed to update room floor");
				}
				if (isset($_POST['room_rent'])){
					if ($room->set("price", $_POST['room_rent'])) HTML::HTMLsuccess("Successfully updated room rent!");
					else HTML::HTMLerror("Failed to update room rent");
				}
				if (!$houses_or_blocks){
					if (isset($_POST['available'])){
						if ($room->set("available", $_POST['available'])) HTML::HTMLsuccess("Successfully changed room availability!");
						else HTML::HTMLerror("Failed to change room availability");
					}
				}
			}
		}
		if (isset($_POST['remove_room'])){
			if (!isset($_POST['select_room'])) HTML::HTMLerror("You need to select a room before it can be removed");
			else {
				$errors = 0;
				foreach($_POST['select_room'] as $room_id){
					$room = new Table("room", $room_id);
					if (!$room->delete(["house","house","rooms"])){
						$errors = 1;
						break;
					}
				}
				if (!$errors) HTML::HTMLsuccess("Successfully removed rooms!");
				else HTML::HTMLerror("Failed to remove rooms");
			}
		}
		if ($houses_or_blocks){
			$title_cap = "House";
			$title = "house";
			$not_title = "Block";
		}
		else {
			$title_cap = "Block";
			$title = "block";
			$not_title = "House";
		}
		?>
		<div class="container">
		<form action="" method="POST">
		<input type="submit" name="submit_switch" value="Switch to <?= $not_title ?>s"><input type="hidden" name="current_house_block" value="<?= $houses_or_blocks ?>">
		<h2>Manage <?= $title_cap ?>s</h2>
		<p>Please select a <?= $title?> from those below or enter a new name for one: <input type="text" name="house_name" maxlength="255"></p>
		<p>Add a description of the <?= $title ?>: <input type="text" name="description" maxlength="255"></p>
		<?if ($houses_or_blocks) {?>
			<p>Change availability: <select name="available">
				<option value="">Please select</option>
				<option value="1">Yes</option>
				<option value="0">No</option>
			</select></p>
		<?}?>
		<p><input type="submit" name="submit_house" value="Add/Update <?= $title_cap ?>"></p>
		<p>To remove a <?= $title ?> instead, select it below and press here <input type="submit" name="remove_house" value="Remove <?= $title_cap ?>"></p>
		<hr>
		<h2>Manage Rooms</h2>
		<p>Please select a Room from those below or enter a new name for one: <input type="text" name="room_name" maxlength="255"></p>
		<p>Please also select a <?= $title ?> to add the room to
		<?if (!$houses_or_blocks){?>
				as well as a floor: <select name="floor">
					<option value="">Please select</option>
					<option value="0">Ground Floor</option>
					<option value="1">First Floor</option>
					<option value="2">Second Floor</option>
				</select>
		<?}?>
		</p>
		<p>Add a rent price: <input type="text" name="room_rent" maxlength="6"></p>
		<?if (!$houses_or_blocks) {?>
			<p>Change availability: <select name="available">
				<option value="">Please select</option>
				<option value="1">Yes</option>
				<option value="0">No</option>
			</select></p>
		<?}?>
		<p><input type="submit" name="submit_room" value="Add/Update Room"></p>
		<p>To remove a room instead, select it below and press here <input type="submit" name="remove_room" value="Remove Room"></p>
		<hr>
		<h2>Rooms</h2>
		<table class="table table-condensed table-bordered table-hover">
		<thead><tr>
			<td>Name</td>
			<td>Description</td>
			<td>Rooms</td>
			<?if (!$houses_or_blocks){?><td>Floor</td><?}?>
			<td>Rents</td>
			<td>Allocation</td>
		</tr></thead>
		<?$condition = "`house` = ".$houses_or_blocks;
		$houses = Table::get_all("house", $condition);
		foreach($houses as $house){
			$number = $house->count("rooms");
			$first = 1;?>
			<tr>
			<td rowspan = "<?= $number?>"><input type = "checkbox" name = "select_house[]" value = "<?= $house->get("id")?>" /> <?= $house->get("name")?></td>
			<td rowspan = "<?= $number?>"><?= $house->get("description")?></td>
			<?if ($house->get("rooms") != NULL){
				foreach($house->get("rooms", 1) as $room_id){
					$room = new Table("room", $room_id);
					if ($first) $first = 0;
					else echo("<tr>");?>
					<td><input type = "checkbox" name = "select_room[]" value = "<?= $room_id?>" /> <?= $room->get("name")?></td>
					<?if (!$houses_or_blocks){?><td><?= $room->get("floor");?></td><?}?>
					<td><?= $room->get("price")?></td>
					<td>
					<?if ($room->get("available")) echo("Available");
					elseif ($room->get("allocation") == NULL) echo("Allocated to a group but not a student");
					else{
						$user = new Table("user", $room->get("allocation"));
						echo($user->get("name"));
					}?>
					</td>
					</tr>
				<?}
			}
			elseif ($houses_or_blocks) echo("<td></td><td></td><td></td></tr>");
			else echo("<td></td><td></td><td></td><td></td></tr>");
		}?>
		</table>
		</form>
		</div>
	<?}
}