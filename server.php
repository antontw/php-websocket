<?

set_time_limit(0);
require 'class.PHPWebSocket.php';

function on_message($clientID, $message, $messageLength, $binary)
{
	global $Server;
	$ip = long2ip($Server->wsClients[$clientID][6]);

	// check if message length is 0
	if ($messageLength == 0) {
		$Server->wsClose($clientID);
		return;
	}

	//The speaker is the only person in the room. Don't let them feel lonely.
	if (sizeof($Server->wsClients) == 1)
		$Server->wsSend($clientID, "There isn't anyone else in the room, but I'll still listen to you. --Your Trusty Server");
	else
		//Send the message to everyone but the person who said it
		foreach ($Server->wsClients as $id => $client)
			if ($id != $clientID)
				$Server->wsSend($id, "Visitor $clientID ($ip) said \"$message\"");
}

function on_open($clientID)
{
	global $Server;
	$ip = long2ip($Server->wsClients[$clientID][6]);

	$Server->log("$ip ($clientID) has connected.");

	//Send a join notice to everyone but the person who joined
	foreach ($Server->wsClients as $id => $client)
		if ($id != $clientID)
			$Server->wsSend($id, "Visitor $clientID ($ip) has joined the room.");
}

function on_close($clientID, $status)
{
	global $Server;
	$ip = long2ip($Server->wsClients[$clientID][6]);

	$Server->log("$ip ($clientID) has disconnected.");

	//Send a user left notice to everyone in the room
	foreach ($Server->wsClients as $id => $client)
		$Server->wsSend($id, "Visitor $clientID ($ip) has left the room.");
}

// start the server
$Server = new PHPWebSocket();
$Server->bind('message', 'on_message');
$Server->bind('open', 'on_open');
$Server->bind('close', 'on_close');
$Server->wsStartServer('0.0.0.0', 8000);

?>
