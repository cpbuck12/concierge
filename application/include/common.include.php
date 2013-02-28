<?php
require_once(__ROOT__. "/classes/Utilities.class.php");
require_once(__ROOT__. "/classes/MessagePump.class.php");
require_once(__ROOT__. "/classes/StateMachine.class.php");
require_once(__ROOT__. "/classes/JQueryPlugin.class.php");
require_once(__ROOT__. "/classes/ThemeEngine.class.php");

foreach(glob(__ROOT__. "/include/machines/*.php") as $filename)
{
	require_once $filename;
}