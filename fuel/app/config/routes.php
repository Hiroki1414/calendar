<?php
return array(
	'_root_'  => 'calendar/index',  // The default route
	'_404_'   => 'calendar/404',    // The main 404 route
	
	'hello(/:name)?' => array('welcome/hello', 'name' => 'hello'),
);
