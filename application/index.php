<?php

function BuildMachines()
{
	$machines = array();
	$machines["mainmenu"] = new StateMachine();
	$machines["mainmenu"]->AddTransition("run", "starting", "waiting");
	$machines["mainmenu"]->AddTransition("user_addpatient", "waiting", "fading_addingpatient");
	$machines["mainmenu"]->AddTransition("user_addfiles", "waiting", "fading_addingfiles");
	$machines["mainmenu"]->AddTransition("continue","fading_addingpatient","addingpatient");
	$machines["mainmenu"]->AddTransition("continue","fading_addingfiles","addingfiles");
	$machines["mainmenu"]->AddTransition("show", "waiting", "fading_addingfiles");
	$machines["mainmenu"]->AddTransition("resume", "addingpatient", "waiting");
	$machines["mainmenu"]->AddTransition("resume", "addingfiles", "waiting");
	
	
	$acc = "var StateMachinesFactories = new Array();\n";
	foreach($machines as $name => $machine)
	{
		$acc .= "StateMachinesFactories['" . $name . "'] =  function() { \n return " . $machine->Generate("starting")  . "\n}";  
	}
	Utilities::SaveLibraryItem("machines.js",$acc);
}

function LoadIncludes()
{
	require_once(__ROOT__."/include/common.include.php");
}

function DefineConstants()
{
	define('__ROOT__', (dirname(__FILE__)));
	define('__PUBLIC__', __ROOT__ . '/../public');
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

DefineConstants();
LoadIncludes();
Utilities::LoadTheme();
BuildMachines();
echo GeneratePage();
