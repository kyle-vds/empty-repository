<?php
class RoomViewer{

	public static function page($room = true){
		if (isset($_POST['select_house'])){
			$name = str_replace(" ", "_", $_POST['select_house']);
			if (isset($_POST[$name])) {
  				$house = new Table("house", $_POST[$name]);
  				$images = $house->get("images", 1);
				$descriptions = [];
				$srcs = [];
				foreach($images as $image_id){
					$image = new Table("image", $image_id);
					array_push($descriptions, $image->get("description"));
					array_push($srcs, $image->get("src"));
				}?>
  				<div class='container'>
  				<h2><?= $house->get("name") ?></h2>
  				<div class="row">
  				<div class="col-md-4">
				<?if($house->count("images") > 0){?>
 					<div id="gallery">
  					<div id="large">
  					<div class="thumbnail">
  					<a href='<?= $srcs[0] ?>' id="gallery-link"><img id="gallery-large" src="<?= $srcs[0] ?>" style="width: 100%;"/></a>
  					<div id="gallery-caption" class="caption"><?= $descriptions[0]; ?></div>
  					</div>
  					</div>
  					<div id="smalls" class='ballot-smalls'>
					<?for($i = 0; $i < count($srcs); $i++){ ?>
  						<a href="<?= $srcs[$i] ?>"><img class="ballot-gallery" src="<?= $srcs[$i]; ?>" width=100 /></a>
					<?}?>
  					</div>
  					</div>
  					<script>
        				var galleryImg = document.getElementById("gallery-large");
        				var galleryLnk = document.getElementById("gallery-link");
        				var galleryDsc = document.getElementById("gallery-caption");
        				var smallImages = document.getElementById("smalls").getElementsByTagName("a");
        				var descs = [<?= '"'.join('", "', array_map(function($s){ return str_replace("\n", " ", addslashes($s)); }, $descriptions)).'"'; ?>];
        				for(i = 0; i < smallImages.length; i++){
          					smallImages[i].ord = i;
          					smallImages[i].onclick = function(e){
            					var img = this.getElementsByTagName("img")[0]
            					galleryImg.src = img.src;
            					galleryImg.attributes['title'] = img.attributes['title'];
            					galleryLnk.attributes["href"].value = img.src;
            					galleryDsc.innerHTML = descs[this.ord];
            					e.preventDefault();
            					return false;
          					}
        				}
  					</script>
  				<?}?>
  				</div>
				<div class="col-md-8">  
 				<?echo($house->get("description"));
 				$floors = ["Basement" => 0, "Ground Floor" => 1, "First Floor" => 2, "Second Floor" => 3, "Third Floor" => 4, "Attic" => 5];
  				foreach($floors as $name => $value){
  					$condition = "`id` IN (".$house->get("rooms").") AND `floor` = ".$value;
  					$rooms = Table::get_all("room", $condition);
  					if ($rooms != NULL){?>
  						<h3><?= $name?></h3>
						<table class="table table-condensed table-bordered table-hover">
						<thead>
						<tr>
						<td>Room</td>
						<td>Rent</td>
						<td>Availability</td>
						</tr>
						</thead>
  						<?foreach ($rooms as $room) {?>
 							<tr>
							<td><?= $room->get("name")?></td>
							<td><?= $room->get("price")?></td>
							<td><? if ($room->get("available")) echo("Available"); 
							else echo("Unavailable");?></td>
							</tr>
  						<?}?>
  						</table>
  					<?}
  				}?>
  				<form action="" method="POST">
				<input type="submit" name="return" value="Return to Previous Page">
				</form>
				</div>
				</div>
				</div>
			<?}
			else throw new Exception("Unable to retrieve room data");
		}
		else{
			if ($room) {
				$houses = Table::get_all("house", "`house` = 0");
				$title = "Block";
				$src = "include/Ballot_images/Block_map/Map_of_Blocks";
			}
			else{
				$houses = Table::get_all("house", "`house` = 1");
				$title = "House";
				$src = "include/Ballot_images/House_map/Map_of_Houses";
			}?>
			<div class='container'>
			<div class="row">
				<div class="col-md-5">
				<img src='<?= $src ?>' width="406" height="576" usemap="#map" />
				</div>
				<div class="col-md-7">
				<form action="" method="POST">
				<table class="table table-condensed table-bordered table-hover">
				<thead><tr>
				<td><?= $title ?></td>
				<td>Size</td>
				<td>Rooms Available</td>
				<td>Maximum Rent (pounds/week)</td>
				<td>Minimum Rent (pounds/week)</td>
				</tr></thead>
    			<? foreach($houses as $house){?>
    				<tr>
					<td><input type="submit" name="select_house" value="<?= $house->get("name")?>"> <input type="hidden" name="<?= str_replace(" ", "_", $house->get("name")) ?>" value="<?= $house->get("id") ?>"></td>
					<td><?= $house->get("size")?></td>
					<td><?= $house->get("available") ?></td>
					<td><?= $house->get_minmax("rooms", "price", 0)?></td>
					<td><?= $house->get_minmax("rooms", "price", 1) ?></td>
					</tr> 
    			<?}?>
  				</table>
				</form>
				</div>
			</div>
			</div>
		<?}
	}
}