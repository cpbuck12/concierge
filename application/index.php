<?php

function BuildMachines()
{
	$machineFactories = array();
	
	$machineFactories["mainmenu"] = new StateMachine();
	$machineFactories["mainmenu"]->AddTransition("run", "starting", "waiting");
	$machineFactories["mainmenu"]->AddTransition("addpatient", "waiting", "addingpatient");
	$machineFactories["mainmenu"]->AddTransition("addfiles", "waiting", "addingfiles");
	$machineFactories["mainmenu"]->AddTransition("adddoctor", "waiting", "addingdoctor");
	$machineFactories["mainmenu"]->AddTransition("run", "addingfiles", "waiting");
	$machineFactories["mainmenu"]->AddTransition("run", "addingpatient", "waiting");
	$machineFactories["mainmenu"]->AddTransition("run", "addingdoctor", "waiting");
	
	$machineFactories["mainmenu"]->AddEnterCallback("waiting", <<<EOD

	$(".class-id-main").fadeIn('fast');
    return true;
EOD
	);
			
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

	$machineFactories["mainmenu"]->AddEnterCallback("addingdoctor", <<<EOD
	
		var q = GetMessageQueue();
		var smOther = GetStateMachine(".class-id-adddoctorsheet");
		q.messagepump("send",function() {
			smOther.run();
		});
		return true;
EOD
	);
	
	$machineFactories["adddoctor"] = new StateMachine();
	$machineFactories["adddoctor"]->AddTransition("run","starting","waiting");
	$machineFactories["adddoctor"]->AddTransition("error","waiting","displayingerror");
	$machineFactories["adddoctor"]->AddTransition("continue","displayingerror","waiting");
	$machineFactories["adddoctor"]->AddTransition("cancel","waiting","starting");
	$machineFactories["adddoctor"]->AddTransition("success","waiting","displayingsuccess");
	$machineFactories["adddoctor"]->AddTransition("continue","displayingsuccess","starting");
	
	$machineFactories["adddoctor"]->AddEnterCallback("displayingsuccess", <<<EOD
			
		// TODO: add messagebox type acknowledgement
		var q = GetMessageQueue();
		var smAddDoctor = GetStateMachine(".class-id-adddoctorsheet");
		q.messagepump("send",function() {
			smAddDoctor.continue()
		});
EOD
	);
	$machineFactories["adddoctor"]->AddEnterCallback("starting", <<<EOD
		$(".class-id-adddoctorsheet button.class-id-mainmenu").button().off("click",CancelAddDoctor);
		$(".class-id-adddoctorsheet button.class-id-adddoctor").button().off("click",DoAddDoctor);
		$(".class-id-adddoctorsheet").fadeOut('fast',function()
		{
			var smMain = GetStateMachine(".class-id-main");
			var q = GetMessageQueue();
			q.messagepump('send',function()
			{
				smMain.run();
			});
		});
EOD
	);

	$machineFactories["adddoctor"]->AddLeaveCallback("starting", <<<EOD

		$(".class-id-adddoctorsheet button.class-id-mainmenu").button().on("click",CancelAddDoctor);
		$(".class-id-adddoctorsheet button.class-id-adddoctor").button().on("click",DoAddDoctor);
			$(".class-id-adddoctorsheet").fadeIn('fast',function() {
			var sm = $(this).data("statemachine");
			sm.transition();
		});
		return StateMachine.ASYNC;

EOD
	);
		
	$machineFactories["addfiles"] = new StateMachine();
	$machineFactories["addfiles"]->AddTransition("run","starting","loadingpatients");
	$machineFactories["addfiles"]->AddTransition("loaded","loadingpatients","waitingonpatient");
//	$machineFactories["addfiles"]->AddTransition("notloaded","loadingpatients","cancelling"); // TODO: not handled, timeouts not handled in firefox with $.getJSON
	$machineFactories["addfiles"]->AddTransition("selectpatient","waitingonpatient","patientselected");
	$machineFactories["addfiles"]->AddTransition("selectpatient","patientselected","patientreselected");
	$machineFactories["addfiles"]->AddTransition("docontinue","patientreselected","patientselected");
	$machineFactories["addfiles"]->AddTransition("unselectpatient","patientselected","waitingpatient");
	
	$machineFactories["addfiles"]->AddTransition("filechange","patientselected","patientselected");
	$machineFactories["addfiles"]->AddTransition("load","patientselected","loading");
	$machineFactories["addfiles"]->AddTransition("cancel","patientselected","cancelling");
	$machineFactories["addfiles"]->AddTransition("cancel","waitingonpatient","cancelling");
	$machineFactories["addfiles"]->AddTransition("restart","cancelling","starting");

	$machineFactories["addfiles"]->AddEnterCallback("cancelling", <<<EOD

			
		var elem = $("table.class-id-patient");
		var oTable = elem.dataTable({ bRetrieve: true });
		oTable.fnDestroy();
			
		var q = GetMessageQueue();
		var sm = GetStateMachine(".class-id-loadfromconciergesheet");
		q.messagepump("send",function() {
			sm.restart();
		});
			
EOD
	);
	$machineFactories["addfiles"]->AddAfterCallback("filechange",<<<EOD

		var oTable = TableTools.fnGetInstance($("table.class-id-filesystem")[0]);
		var result = oTable.fnGetSelectedData();
		if(result.length > 0)
			$("button.class-id-loadfiles").button("enable");
		else
			$("button.class-id-loadfiles").button("disable");
EOD
	);
	$machineFactories["addfiles"]->AddEnterCallback("waitingpatient",<<<EOD

		$("button.class-id-loadfiles").hide();
EOD
	);
	$machineFactories["addfiles"]->AddEnterCallback("patientreselected",<<<EOD
		$("button.class-id-loadfiles").button("disable");
		DepopulateFiles();
		var q = GetMessageQueue();
		var sm = GetStateMachine(".class-id-loadfromconciergesheet");
		q.messagepump("send",function()
		{
			//alert("sending docontinue");
			sm.docontinue();
		});
		return true;
EOD
	);
	$machineFactories["addfiles"]->AddEnterCallback("patientselected", <<<EOD
		PopulateFiles(/*function() {
			var q = GetMessageQueue();
			var sm = GetStateMachine(".class-id-loadfromconciergesheet");
			q.messagepump("send",function() {
				sm.run();
		}*/);
		$("button.class-id-loadfiles").button("disable");
		return true;
EOD
	);
	$machineFactories["addfiles"]->AddAfterCallback("restart", <<<EOD

		$("table.class-id-patient tr").off("click",OnPatientRowClick);
EOD
	);			
	$machineFactories["addfiles"]->AddLeaveCallback("loadingpatients", <<<EOD

		$("table.class-id-patient tr").on("click",OnPatientRowClick);
	
EOD
	);
	$machineFactories["addfiles"]->AddEnterCallback("starting", <<<EOD

		$(".class-id-loadfromconciergesheet button.class-id-mainmenu").button().off("click",CancelFileLoading);
		$(".class-id-loadfromconciergesheet button.class-id-loadfiles").button().off("click",DoFileLoading);
		$(".class-id-loadfromconciergesheet").fadeOut('fast',function() {
			var q = GetMessageQueue();
			var smMain = GetStateMachine(".class-id-main");
			q.messagepump("send",function()
			{
				smMain.run();
			});
		});
EOD
	);

	$machineFactories["addfiles"]->AddLeaveCallback("starting", <<<EOD

		alert("leaving addfiles starging");
		$(".class-id-loadfromconciergesheet button.class-id-mainmenu").button().on("click",CancelFileLoading);
		$(".class-id-loadfromconciergesheet button.class-id-loadfiles").button().on("click",DoFileLoading);
			$(".class-id-loadfromconciergesheet").fadeIn('fast',function() {
			var sm = $(this).data("statemachine");
			sm.transition();
		});
		return StateMachine.ASYNC;

EOD
	);
	$machineFactories["addfiles"]->AddEnterCallback("loadingpatients", <<<EOD
			$.getJSON("http://localhost:50505/ajax/GetPeopleOnDisk", function (patientsOnDiskJSON) {
			var oTable = $("table.class-id-patient").dataTable({
				"bJQueryUI": true,
				"sDom": 'T<"clear">lfrtip',
				"oTableTools": {
					"sRowSelect": "single"
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
