#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

// Login Function takes the username and password from the form/appache/front end which is stored in variables and then passed to the function where the variable userlookip is called/run on the sql database which selects id, username, and password colums from the users table where the username matches the passed data from the form in the username variable, if it matches it returns true if it doesnt it returns false.
function doLogin($username,$password)
{
	// lookup username in databas
	$userlookup = "SELECT id,username, password FROM users WHERE username = '$username' ";
	// check password
	if ($password == 'password') {	
    return true; }
    //return false if not valid
}else {
	return false;
}

// Registration Function
function doRegister($username,$password) {
	// when called the function runs/passes a sql statement that inserts into the users table in the username and password columns the username and password data from the $username and $password variables which will come from the form and frontend/appache.
$registerUser = "INSERT INTO users (username, password) VALUES ('$username, $password)";


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

