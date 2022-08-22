<?php
class ImageEditor
{

	public static function page()
	{
		if (isset($_POST['submit_update'])) {
			$image_id = NULL;
			if (isset($_POST['select_image'])) {
				if (isset($_FILES['my_file']) && $_FILES['my_file']['name'] != "")
					HTML::HTMLerror("You can select an image or upload one, not both");
				elseif (count($_POST['select_image']) > 1)
					HTML::HTMLerror("You need to select a single image to update it");
				else
					$image_id = $_POST['select_image'][0];
			} elseif (isset($_FILES['my_file']) && $_FILES['my_file']['name'] != "") {
				$path = pathinfo($_FILES['my_file']['name']);
				$ext = $path['extension'];
				$types = array('jpg','jpeg','gif','png','apng','svg','bmp','ico');
				$valid = 0;
				foreach ($types as $type){
					if (!$valid && $ext == $type) $valid = 1;
				}
				if ($valid) {
					$images = Table::get_all("image");
					$max = 0;
					foreach($images as $image){ 
						$image_id = $image->get("id");
						if($image_id > $max) $max = $image_id;
					}
					$number = $max + 1;
					$path_filename_ext = "include/Ballot_images/Image" . $number. "." . $ext;
					if (move_uploaded_file($_FILES['my_file']['tmp_name'], $path_filename_ext)){
						$image_id = Table::insert("image", $path_filename_ext);
						if ($image_id != NULL) HTML::HTMLsuccess("Succesffuly uploaded image!");
						else HTML::HTMLerror("Failed to upload image");
					}
					else throw new Exception("Failed to move file");
				}
				else {
					HTML::HTMLerror("Please upload a compatable image type: jpg, jpeg, gif, png, apng, svg, bmp or ico");
					return false;
				}
			}
			if ($image_id == NULL)
				HTML::HTMLerror("You need to select an image or upload one to update it");
			else {
				if (isset($_POST['location']) && $_POST['location'] != ""){
					$image = new Table("image", $image_id);
					if ($image->add("houses", $_POST['location'], ["house","images"])) HTML::HTMLsuccess("Successfully added house/block to image!");
					else HTML::HTMLerror("Failed to add house/block to image");
				}
				if (isset($_POST['description']) && $_POST['description'] != ""){
					$image = new Table("image", $image_id);
					if ($image->set("description", $_POST['description'])) HTML::HTMLsuccess("Successfully changed image description!");
					else HTML::HTMLerror("Failed to change image description!");
				}
			}
		}
		if (isset($_POST['submit_remove'])) {
			if (isset($_POST['select_image'])) {
				if (isset($_POST['select_house']))
					HTML::HTMLerror("You can only remove images or houses/blocks at a time to avoid overlaps");
				else{
					foreach ($_POST['select_image'] as $image_id){
						$image = new Table("image", $image_id);
						if ($image->delete(["houses", "house", "images"])){
							unlink($image->get("src"));
							HTML::HTMLsuccess("image deleted successfully!");
						}
						else HTML::HTMLerror("Failed to delete image");
					}
				}
			} elseif (isset($_POST['select_house']))
				foreach ($_POST['select_house'] as $house_id) {
					$a_id = explode(",", $house_id);
					$image = new Table("image", $a_id[0]);
					if ($image->remove("houses", $a_id[1], ["house","images"])) HTML::HTMLsuccess("Successfully removed house/block from image!");
					else HTML::HTMLerror("Failed to remove house/block from image");
				}
			else
				HTML::HTMLerror("You can select images or houses (but not both) to remove them");
		}
		
		$admin = new Table("admin");
		if ($admin->get("name") == NULL) {
			HTML::HTMLerror("You do not have admin permission");
			return;
		} else {
			$houses = Table::get_all("house");
			$images = Table::get_all("image");
			?>
<div class="container">
	<form method="POST" enctype="multipart/form-data">
		<p>
			Please upload an image or select one from the table below to update:
			<input type="file" name="my_file">
		</p>
		<p>
			Add a house/block which the image can be used for: <select
				name="location">
				<option value="">Please select</option>
				<!-- <option value="blockmap">Map of Blocks</option>
			<option value="housemap">Map of Houses</option> -->
			<?foreach ($houses as $house) echo('<option value = "'.$house->get("id").'">'.$house->get("name").'</option>');?>
			</select>
		</p>
		<!-- <p>Note, if 'Map of Blocks' or 'Map of Houses' is selected all other houses/blocks will be over written</p> -->
		<p>
			Add a short description of what it shows: <input type="text"
				name="description" maxlength="255">
		</p>
		<p>
			<input type="submit" name="submit_update" value="Upload/Update Image">
		</p>

		<p>
			To delete images or remove houses/blocks they match to, select them
			from the table below and click here: <input type="submit"
				name="submit_remove" value="Remove">
		</p>

		<table class="table table-condensed table-bordered table-hover">
			<thead>
				<tr>
					<td>Image</td>
					<td>Houses</td>
					<td>Description</td>
				</tr>
			</thead>
  			<?
			
foreach ($images as $image) {
				$src = $image->get("src");
				$image_id = $image->get("id");
				?>
 				<tr>
				<td><input type="checkbox" name="select_image[]"
					value="<?= $image_id ?>">
					<div class="col-md-10" id="smalls">
						<a href="<?= $src ?>"><img class="ballot-gallery"
							src="<?= $src ?>" width=100 /></a>
					</div></td>
				<td class="col-md-6">
  				<?
				
$house_names = array();
				foreach ($image->get("houses", 1) as $house_id) {
					$image_house = new Table("house", $house_id);
					$house_name = '<input type = "checkbox" name = "select_house[]" value = "' . $image_id . ',' . $house_id . '"> ' . $image_house->get("name");
					array_push($house_names, $house_name);
				}
				echo (implode("<br>", $house_names));
				?>
  				</td>
				<td class="col-md-4"><?= $image->get("description") ?></td>
			</tr>
			<?}?> 
  			</table>
	</form>
</div>

<?
		
}
	}
}