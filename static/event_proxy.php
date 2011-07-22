<?php

$events_url   = "http://events.ucf.edu";
$query_string = http_build_query($_GET);
$events_data  = file_get_contents($events_url.'?'.$query_string);

if ($events_data){
	print $events_data;
}

?>