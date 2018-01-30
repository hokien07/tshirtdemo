turn off wordpress debug
 
-- You can add this code on wp-config.php

-----------Thanks----------------------------------------------------
ini_set('display_errors','Off');

ini_set('error_reporting', E_ALL );

define('WP_DEBUG', false);

define('WP_DEBUG_DISPLAY', false);