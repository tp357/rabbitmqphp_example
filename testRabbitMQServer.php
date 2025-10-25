#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$dbConnect = new mysqli("tirth-VMware20-1","backrabbituser",'backpassword','testingdb');

// if connection error end file and post error
if ($dbConnect-> connect_error) {
	die ("Connection Failure" . $dbConnect-> connect_error);
}

// Login Function takes the username and password from the form/appache/front end which is stored in variables and then passed to the function where the variable userlookip is called/run on the sql database which selects id, username, and password colums from the users table where the username matches the passed data from the form in the username variable, if it matches it returns true if it doesnt it returns false.


// Registration Function
function doRegister($username,$password) {
	global $dbConnect;
	// when called the function runs/passes a sql statement that inserts into the users table in the username and password columns the username and password data from the $username and $password variables which will come from the form and frontend/appache.
// User Check
	$userCheck = $dbConnect->query("SELECT id FROM users WHERE username = '$username'");
	if ($userCheck->num_rows > 0) {
		return ['status' => 'error', 'message'=> 'Username Taken'];
}
//Password Hash
$hashpassword = password_hash($password,PASSWORD_DEFAULT);

// Insert User into Database
$userInsert = "INSERT INTO users (username, password) VALUES '$username', '$hashpassword')";
if ($dbConnect->query($userInsert)) {
	return ['status' => 'success' , 'message' => 'User Registered'];

}else {
return ['status' => 'error', 'message' => 'Not Registered'];

}

}

function doLogin($username,$password) {
	global $dbConnect;
// username database lookup
	$userLookup = $dbConnect->query("SELECT password FROM users WHERE username = '$username'");
	//check user existance
	if($userLookup->num_rows > 0) {
		$row = $userLookup->fetch_assoc();
		if (password_verify($password, $row['password'] {
			//create session key when logging in
			$sessionKey = md5($username. time());
			// Session Storage
			$dbConnect->query('INSERT INTO sessions (username, session_key) VALUES ('$username', '$sessionKey')');
			return ['status' => 'success', 'sessionKey', => $sessionKey];
		}else {
			return ['status' => 'error', 'message' => 'Username not Stored in db'];


		}
	
	}


// Session Validation Function
function doValidate($sessionKey) {
	global $dbConnect;
	$validateQuery = $dbConnect->query("SELECT username FROM sessions WHERE session_key = '$sessionKey'");
	if ($validateQuery->num_rows 0) {
		$row = $validateQuery->fetch_assoc();
		return ['status' => 'success', 'username' => $row['username']];
	}else {
		return ['status' => 'error', 'message' => 'No Session Found'];
	
	}
}

function requestProcessor($request)
{
  echo "received request".PHP_EOL;
  var_dump($request);
  if(!isset($request['type']))
  {
    return "ERROR: unsupported message type";
  }
  switch ($request['type'])
  {
	case "register":
		return doRegister($request['username'],$request['password']);
    case "login":
      return doLogin($request['username'],$request['password']);
    case "validate_session":
      return doValidate($request['sessionId']);
  }
  return array("returnCode" => '0', 'message'=>"Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

$server->process_requests('requestProcessor');
exit();
?>

