<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 
 */
if ($do == 'oauth' || $action == 'credit' || $action == 'passport' || $action == 'uc') {
	define('FRAME', 'setting');
} else {
	define('FRAME', 'members');
}

$frames = buildframes(array(FRAME));
$frames = $frames[FRAME];
