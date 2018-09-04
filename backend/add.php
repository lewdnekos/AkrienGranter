<?php
	/* =========================================================
							Constants
	   =========================================================
	*/

	$clientId = "Not for you c:";
	$configPath = $clientId . "/" . $clientId . "-config.json";

	/* =========================================================
							Includes
	   =========================================================
	*/
	include("Client.php");
	include("Embed.php");
	include("Browser.php");
	include("OS.php");

	use \DiscordWebhooks\Client;
	use \DiscordWebhooks\Embed;
	
	// Set data type
	header('Content-Type: application/json');

	// Reading config file
	$content = file_get_contents($configPath);

	if($content === FALSE) {
		die(json_encode(array("code" => "error", "message" => "Error reading configuration file")));
	}

	$array = json_decode($content, true);

	// Setting up vars
	$lockdown = $array["lockdown"];
	$userToken = $array["userToken"];
	$discordLink = $array["discordLink"];
	$authKey = $array["authKey"];

	// Lockdown check
	if($lockdown){
		die(json_encode(array("code" => "error", "message" => "For security reasons Akrien Granter is currently locked down")));
	}

	// Direct access check
	if(empty($_POST) || empty($_POST['username']) || $_POST['username'] == "username"){
		die(json_encode(array("code" => "error", "message" => "Username cannot be blank")));
	}

	if(empty($_POST["key"]) || $_POST["key"] != $authKey){
		die(json_encode(array("code" => "error", "message" => "User authenticaton failed")));
	}
	// AAL API parameters
	$username = $_POST['username'];
	$uri = 'http://alphaantileak.net/api/v3/apps/abc646ea-07b6-443f-b7c7-ae319aa4e693/users/' . $username;
	$jsonTime = "{}"; // Unlimited time

	// cURL Initialization
	$ch = curl_init($uri);
	curl_setopt_array($ch, array(
	    CURLOPT_HTTPHEADER  => array('Authorization: ' . $userToken),
	    CURLOPT_RETURNTRANSFER  =>true,
	    CURLOPT_VERBOSE     => 1,
	    CURLOPT_CUSTOMREQUEST => "POST",
	    CURLOPT_POSTFIELDS => $jsonTime
	));

	// Get response
	$out = curl_exec($ch);
	curl_close($ch);

	// Discord webhook logging
	$webhook = new Client($discordLink);
	$embed = new Embed();

	// Error check
	$isError = $out != "{}";

	if($isError){
		$embed->title("Akrien Granter - Error");
	}else{
		$embed->title("Akrien Granter - New user");
	}

	// User info
	$embed->field("Username", $username, true);
	$embed->field("Date", date("Y-m-d H:i:s"), true);
	$embed->field("IP", $_SERVER['REMOTE_ADDR'], true);

	$error = "";
	$browser = new Browser();

	// Browser info
	$browserName = $browser->getBrowser();
	$browserVersion = $browser->getVersion();
	$browserData = $browserName . " " . $browserVersion;

	// Addiditional checks
	if($isError){
		$json = (array) json_decode($out, true);
		$error = $json['error'];
	}

	if($isError){
		$embed->field("Error code", $error, true);
		$embed->color(16711680); // Red
	}else{
		$embed->color(65280); // Green
	}

	$screenSize = null;
	session_start();

	// Screen information to get user's fingerprint
	if(isset($_SESSION['screen_width']) && isset($_SESSION['screen_height'])){
	    $screenSize = $_SESSION['screen_width'] . "x" . $_SESSION['screen_height'];
	}

	$osInfo = getOS($_SERVER['HTTP_USER_AGENT']);
	$fingerprint = $browserData . "-" . $osInfo;

	if($screenSize != null){
		$fingerprint .= "-" . $screenSize;
	}

	// Hash the fingerprint
	$fingerprint = strtolower(md5($fingerprint));

	// Field the Discord response
	$embed->field("OS", $osInfo, true);
	$embed->field("Browser", $browserData, true);
	$embed->field("Fingerprint", $fingerprint, true);

	// Send log
	$webhook->embed($embed)->send();

	// Final response to the user
	if(!$isError){
		die(json_encode(array("code" => "success", "message" => "Success! Access granted")));
	}

	echo(json_encode(array("code" => "error", "message" => $error)));
?>
