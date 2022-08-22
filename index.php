<?php
require_once "app/Objects/ErrorHandler.php";
require_once "app/Objects/Table.php";
require_once "app/Objects/HTML.php";
require_once "app/Maintenance.php";
require_once "app/YourPage.php";
require_once "app/RoomViewer.php";
require_once "app/BallotViewer.php";
require_once "app/Home.php";
require_once "app/ControlPanel.php";
require_once "app/BallotEditor.php";
require_once "app/RoomEditor.php";
require_once "app/ImageEditor.php";

// buffer the output
ob_start();

// if no page is requested then serve up the home page
if (isset($_GET["q"]) && $_GET['q'] != "")
	$url = rtrim($_GET["q"], "/");
else
	$url = "home";

?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="author" content="Fitzwilliam College JCR">
<meta name="title" content="Fitz JCR Housing Ballot System">
<meta name="description" content="">
<title>Fitz JCR Balloting System</title>
<link rel="stylesheet"
	href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
	integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u"
	crossorigin="anonymous">
<link rel="stylesheet"
	href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css"
	integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp"
	crossorigin="anonymous">
<link rel="stylesheet" href="/include/css/sticky-footer-navbar.css">
<link rel="stylesheet" href="/include/css/groupballot.css">
<link rel="stylesheet" href="/include/css/roomview.css">
<script
	src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script
	src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
	integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
	crossorigin="anonymous"></script>
</head>

<body>
  <?$admin = new Table("admin");?>
    <!-- Fixed navbar -->
	<nav class="navbar navbar-default">
		<div class="container-fluid">
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed"
					data-toggle="collapse" data-target="#bs-example-navbar-collapse-1"
					aria-expanded="false">
					<span class="sr-only">Toggle navigation</span> <span
						class="icon-bar"></span> <span class="icon-bar"></span> <span
						class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="/home">Fitz JCR Housing Ballot System</a>
			</div>

			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse"
				id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav navbar-right">
					<li><a href="/yourpage?q=yourpage">Your Page</a></li>
					<li class="dropdown"><a href="#" class="dropdown-toggle"
						data-toggle="dropdown" role="button" aria-haspopup="true"
						aria-expanded="false">Room Ballot <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a href="/rooms?q=rooms">Rooms</a></li>
							<li><a href="/roomballot?q=roomballot">Ballot</a></li>
						</ul></li>
					<li class="dropdown"><a href="#" class="dropdown-toggle"
						data-toggle="dropdown" role="button" aria-haspopup="true"
						aria-expanded="false">Housing Ballot <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a href="/houses?q=houses">Houses</a></li>
							<li><a href="/housingballot?q=housingballot">Ballot</a></li>
						</ul></li>
	<?if($admin->get("name") != NULL){?>
          <li class="dropdown"><a href="#" class="dropdown-toggle"
						data-toggle="dropdown" role="button" aria-haspopup="true"
						aria-expanded="false">Admin <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a href="/controlpanel?q=controlpanel">Control Panel</a></li>
							<li><a href="/balloteditor?q=balloteditor">Ballot Editor</a></li>
							<li><a href="/roomeditor?q=roomeditor">Room Editor</a></li>
							<li><a href="/imageeditor?q=imageeditor">Image Editor</a></li>
						</ul></li>
    <?}?>
        </ul>
			</div>
			<!-- /.navbar-collapse -->
		</div>
		<!-- /.container-fluid -->
	</nav>
	<div class="container">
<?php
switch ($url) {
	case "home":
		echo ('<div class="page-header"><h1>Fitz JCR Balloting System</h1></div>');
		Home::page();
		break;
	case "yourpage":
		echo ('<div class="page-header"><h1>Your Page</h1></div>');
		YourPage::page();
		break;
	case "rooms":
		echo ('<div class="page-header"><h1>Rooms in the Ballot</h1></div>');
		RoomViewer::page(true);
		break;
	case "houses":
		echo ('<div class="page-header"><h1>Houses in the Ballot</h1></div>');
		RoomViewer::page(false);
		break;
	case "roomballot":
		echo ('<div class="page-header"><h1>Room Ballot</h1></div>');
		BallotViewer::page(true);
		break;
	case "housingballot":
		echo ('<div class="page-header"><h1>Housing Ballot</h1></div>');
		BallotViewer::page(false);
		break;
	case "controlpanel":
		echo ('<div class="page-header"><h1>Control Panel</h1></div>');
		ControlPanel::page();
		break;
	case "balloteditor":
		echo ('<div class="page-header"><h1>Ballot Editor</h1></div>');
		BallotEditor::page();
		break;
	case "roomeditor":
		echo ('<div class="page-header"><h1>Room Editor</h1></div>');
		RoomEditor::page();
		break;
	case "imageeditor":
		echo ('<div class="page-header"><h1>Image Editor</h1></div>');
		ImageEditor::page();
		break;
	default:
		throw new Exception("Failed to retrieve page name");
}
?>
    </div>
	<footer class="footer">
		<div class="container">
			<p class="text-muted">
				<a href="https://github.com/kyle-vds/fitz-roomballot-2022">Fitz JCR Housing Ballot System </a>
			</p>
		</div>
	</footer>
</body>
</html>
<?php

// return the buffered content all at once
ob_flush();

?>
