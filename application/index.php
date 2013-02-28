<?php

$machineFactories = array();

function BuildMachines()
{
	global $machineFactories;
	$acc = "/* Generated from ". __FILE__. " */\n";
	foreach($machineFactories as $name => $machineFactory)
	{
		$acc .= "StateMachineFactories['" . $name . "'] =  function() { \n return " . $machineFactory->Generate("starting")  . "\n};\n";  
	}
	$acc .= <<<EOD

EOD
	;
	Utilities::SaveLibraryItem("machines.js",$acc);
}

function GeneratePage()
{
	$page = ThemeEngine::BuildPage();
	$themeHook = new ThemeHook();
	global $themeEngine;
	$themeEngine = new ThemeEngine();
	$result = ThemeEngine::Render($page);
	$f = fopen(__ROOT__ . "/../public/index.html","w");
	fwrite($f,$result);
	fclose($f);
	return $result;
}

define('__ROOT__', (dirname(__FILE__)));
define('__PUBLIC__', __ROOT__ . '/../public');
require_once(__ROOT__."/include/common.include.php");
Utilities::LoadTheme();
BuildMachines();
echo GeneratePage();
