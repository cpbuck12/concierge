<?php

function BuildMachines()
{
	$machines = array();
	$machines["mainmenu"] = new StateMachine();
	$machines["mainmenu"]->AddTransition("addpatient", "waiting", "addingpatient");
	$machines["mainmenu"]->AddTransition("addfiles", "waiting", "addingfiles");
	$machines["mainmenu"]->AddTransition("resume", "addingpatient", "waiting");
	$machines["mainmenu"]->AddTransition("resume", "addingfiles", "waiting");
	$machines["mainmenu"]->AddTransition("run", "starting", "waiting");
//	$machines["mainmenu"]->AddLeaveCallback("starting", $code);
	
//	public function AddLeaveCallback($stateName,$code)
	
	$acc = "var StateMachines = new Array();\n";
	foreach($machines as $name => $machine)
	{
		$acc .= "StateMachines['" . $name . "'] = " . $machine->Generate("starting");  
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
