<?php
class BallotViewer{

	public static function page($room = true){
		if ($room) {
			$ballot = "room_ballot";
			$priorities = ["Second Years and Third Years Abroad" => 1, "Third Years with confirmed fourth" => 2, "First Years" => 3 ];
		} else {
			$ballot = "housing_ballot";
			$priorities = ["Groups of 9" => 1, "Groups of 8" => 2, "Groups of 7" => 3, "Groups of 6" => 4, "Groups of 5" => 5, "Groups of 4" => 6, "Groups of less than 3" => 7];
		}
		?>
		<div class="container">
		<table class="table table-condensed table-bordered table-hover">
		<thead><tr>
		<td>Position</td>
		<td>Group Members</td>
		</tr></thead>
  		<?foreach ($priorities as $priority_name => $priority_value) {
  			$condition = "`priority` = ".$priority_value." ORDER BY `order`";
  			$groups = Table::get_all($ballot, $condition);
  			if ($groups != NULL){?>
  				<tr><td colspan="2"><h3><?= $priority_name ?></h3></td></tr>
  				<?foreach($groups as $group){?>
          			<tr>
					<td class="col-md-1" style="text-align: right;"><?= $group->get("order")?></td>
				 	<td class="col-md-8"><?
					$names = array();
					foreach ($group->get("crsids", 1) as $crsid) {
						$user = new Table("user", $crsid);
						array_push($names, $user->get("name"));
					}
					echo (implode("<br>", $names));
					?></td>
					</tr>
				<?}
			}
		}?>
   		</table>
		</div>
	<?}
}