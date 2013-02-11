<?php

function BuildMachines()
{
	$machineFactories = array();
	
	$machineFactories["mainmenu"] = new StateMachine();
	$machineFactories["mainmenu"]->AddTransition("run", "starting", "waiting");
	$machineFactories["mainmenu"]->AddTransition("addpatient", "waiting", "addingpatient");
	$machineFactories["mainmenu"]->AddTransition("addfiles", "waiting", "addingfiles");
	$machineFactories["mainmenu"]->AddTransition("run", "addingfiles", "waiting");
	$machineFactories["mainmenu"]->AddTransition("run", "addingpatient", "waiting");
	
	$machineFactories["mainmenu"]->AddLeaveCallback("starting", <<<EOD
		$(".class-id-main").fadeIn('fast',function() {
			var sm = $(this).data("statemachine");
			sm.transition();
		});
		return StateMachine.ASYNC;
EOD
	);
	$machineFactories["mainmenu"]->AddLeaveCallback("waiting", <<<EOD
		$(".class-id-main").fadeOut('fast',function() {
			var sm = $(this).data("statemachine");
			sm.transition();			
		});
		return StateMachine.ASYNC;
EOD
	);
	$machineFactories["mainmenu"]->AddEnterCallback("addingfiles", <<<EOD

		var q = GetMessageQueue(); // jQuery("#hiddenmessagequeueelement");
		var smOther = GetStateMachine(".class-id-loadfromconciergesheet");
		q.messagepump("send",function() {
			smOther.run();
		});
		return true;
EOD
	);
	
	$machineFactories["addfiles"] = new StateMachine();
	$machineFactories["addfiles"]->AddTransition("run","starting","loadingpatients");
	$machineFactories["addfiles"]->AddTransition("loaded","loadingpatients","waitingonpatient");
	$machineFactories["addfiles"]->AddTransition("notloaded","loadingpatients","cancelling"); // TODO: not handled, timeouts not handled in firefox with $.getJSON
	$machineFactories["addfiles"]->AddTransition("selectpatient","waitingonpatient","patientselected");
	$machineFactories["addfiles"]->AddTransition("selectpatient","patientselected","patientselected");
	$machineFactories["addfiles"]->AddTransition("filechange","patientselected","patientselected");
	$machineFactories["addfiles"]->AddTransition("load","patientselected","loading");
	$machineFactories["addfiles"]->AddTransition("cancel","patientselected","cancelling");
	$machineFactories["addfiles"]->AddTransition("cancel","waitingonpatient","cancelling");
	$machineFactories["addfiles"]->AddTransition("restart","cancelling","starting");
	
	$machineFactories["addfiles"]->AddLeaveCallback("starting", <<<EOD

		$("button.class-id-mainmenu").button().on("click",CancelFileLoading);
		$(".class-id-loadfromconciergesheet").fadeIn('fast',function() {
			var sm = $(this).data("statemachine");
			sm.transition();
		});
		return StateMachine.ASYNC;

EOD
	);
//	$machineFactories["addfiles"]->AddEnterCallback("cancelling","alert('cancel');");
	$machineFactories["addfiles"]->AddBeforeCallback("cancel","alert('cancel2'+' '+from+' '+to);");
	
	$machineFactories["addfiles"]->AddEnterCallback("loaded","alert('loaded');");
	$machineFactories["addfiles"]->AddEnterCallback("loadingpatients", <<<EOD
			$.getJSON("http://localhost:50505/ajax/GetPeopleOnDisk", function (patientsOnDiskJSON) {
			var patientsOnDisk = $.parseJSON(patientsOnDiskJSON);
			var oTable = $("table.class-id-patient").dataTable({
				"bJQueryUI": true,
				oTableTools :
				{
					"sDom": 'T<"clear">lfrtip',
					sRowSelect: "single"
				},
				"aoColumnDefs":
				[
					{ "sTitle": "First Name", "aTargets": [ 0 ], "mData": "FirstName" },
					{ "sTitle": "Last Name", "aTargets": [ 1 ], "mData": "LastName" },
				],
				"aaData" : patientsOnDiskJSON
			});
			var q = GetMessageQueue();
			var sm  = GetStateMachine(".class-id-loadfromconciergesheet");
			alert("loadpatients");
			q.messagepump("send",function() {
				sm.loaded();
			});
			return true;
		});
EOD
	);
	
	$acc = "/* Generated from __FILE__ */\n";
	$acc .= "var StateMachineFactories = new Array();\n";
	foreach($machineFactories as $name => $machineFactory)
	{
		$acc .= "StateMachineFactories['" . $name . "'] =  function() { \n return " . $machineFactory->Generate("starting")  . "\n};\n";  
	}
	$acc .= <<<EOD

EOD
	;
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
