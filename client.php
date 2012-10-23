<?

date_default_timezone_set('Asia/Taipei');
require_once("class.websocket_client.php");

$payload = json_encode(array(
	'action' => 'echo',
	'data' => 'dos'
));

$clients = new WebsocketClient;

$clients->connect('127.0.0.1', 8000, '/', false, 1000000);
echo "response: ".$clients->sendData($payload)."\n";

?>
