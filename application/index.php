<?php


function BuildMachines()
{
	$machineFactories = array();
	
	$machineFactories["mainmenu"] = new StateMachine();
	$machineFactories["mainmenu"]->AddTransition("run", "starting", "waiting");
	$machineFactories["mainmenu"]->AddTransition("addpatient", "waiting", "addingpatient");
	$machineFactories["mainmenu"]->AddTransition("addfiles", "waiting", "addingfiles");
	$machineFactories["mainmenu"]->AddTransition("adddoctor", "waiting", "addingdoctor");
	$machineFactories["mainmenu"]->AddTransition("addspecialty", "waiting", "addingspecialty");
	$machineFactories["mainmenu"]->AddTransition("run", "addingfiles", "waiting");
	$machineFactories["mainmenu"]->AddTransition("run", "addingpatient", "waiting");
	$machineFactories["mainmenu"]->AddTransition("run", "addingdoctor", "waiting");
	$machineFactories["mainmenu"]->AddTransition("run", "addingspecialty", "waiting");
	
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

	$machineFactories["mainmenu"]->AddEnterCallback("addingpatient", <<<EOD
	
		var q = GetMessageQueue();
		var smOther = GetStateMachine(".class-id-addpatientsheet");
		q.messagepump("send",function() {
			smOther.run();
		});
		return true;
EOD
	);

	$machineFactories["mainmenu"]->AddEnterCallback("addingspecialty", <<<EOD
	
		var q = GetMessageQueue();
		var smOther = GetStateMachine(".class-id-addspecialtysheet");
		q.messagepump("send",function() {
			smOther.run();
		});
		return true;
EOD
	);
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$machineFactories["addspecialty"] = new StateMachine();
	$machineFactories["addspecialty"]->AddTransition("run","starting","waiting");
	$machineFactories["addspecialty"]->AddTransition("error","waiting","displayingerror");
	$machineFactories["addspecialty"]->AddTransition("continue","displayingerror","waiting");
	$machineFactories["addspecialty"]->AddTransition("cancel","waiting","starting");
	$machineFactories["addspecialty"]->AddTransition("success","waiting","displayingsuccess");
	$machineFactories["addspecialty"]->AddTransition("continue","displayingsuccess","starting");
	
	$machineFactories["addspecialty"]->AddEnterCallback("displayingsuccess", <<<EOD
		
		// TODO: add messagebox type acknowledgement
		var q = GetMessageQueue();
		var smAddSpecialty = GetStateMachine(".class-id-addspecialtysheet");
		q.messagepump("send",function() {
			smAddSpecialty.continue()
		});
EOD
	);
	$machineFactories["addspecialty"]->AddEnterCallback("starting", <<<EOD
		$(".class-id-addspecialtysheet button.class-id-mainmenu").button().off("click",CancelAddSpecialty);
		$(".class-id-addspecialtysheet button.class-id-addspecialty").button().off("click",DoAddSpecialty);
		var tbl = $("table.class-id-specialtyaddfields");
		$(".class-id-addspecialtysheet").fadeOut('fast',function()
		{
			UnstashElement(tbl,function() {
				var oTable = tbl.dataTable({ bRetrieve: true });
				oTable.fnDestroy();
			});
			var smMain = GetStateMachine(".class-id-main");
			var q = GetMessageQueue();
			q.messagepump('send',function()
			{
				smMain.run();
			});
		});
EOD
	);
	
	$machineFactories["addspecialty"]->AddLeaveCallback("starting", <<<EOD
	
		$(".class-id-addspecialtysheet button.class-id-mainmenu").button().on("click",CancelAddSpecialty);
		$(".class-id-addspecialtysheet button.class-id-addspecialty").button().on("click",DoAddSpecialty);
		var tbl = $("table.class-id-specialtyaddfields");
		StashElement(tbl);
		tbl.dataTable({ "sDom":"t","bJQueryUI": true,"bPageinate":false,
				iDisplayLength : 15, bSort : false});
		$(".class-id-addspecialtysheet").fadeIn('fast',function() {
			var sm = $(this).data("statemachine");
			sm.transition();
		});
		return StateMachine.ASYNC;
	
EOD
	);
	
	
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
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
		var tbl = $("table.class-id-doctoraddfields");
		$(".class-id-adddoctorsheet").fadeOut('fast',function()
		{
			UnstashElement(tbl,function() {
				var oTable = tbl.dataTable({ bRetrieve: true });
				oTable.fnDestroy();
			});
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
		var tbl = $("table.class-id-doctoraddfields");
		StashElement(tbl);
		tbl.dataTable({ "sDom":"t","bJQueryUI": true,"bPageinate":false,
				iDisplayLength : 15, bSort : false});
		$("input.class-id-country",tbl).val("USA");
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
					"sRowSelect": "single",
					"aButtons" : []
			
	        	},
				"aoColumnDefs":
				[
					{ "sTitle": "First Name", "aTargets": [ 0 ], "mData": "FirstName" },
					{ "sTitle": "Last Name", "aTargets": [ 1 ], "mData": "LastName" },
				],
				"aaData" : patientsOnDiskJSON.people
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

	
	$machineFactories["addpatient"] = new StateMachine();
	$machineFactories["addpatient"]->AddTransition("run","starting","running");
	$machineFactories["addpatient"]->AddTransition("loaddb","running","dbloaded");
	$machineFactories["addpatient"]->AddTransition("error","running","dberror");
	$machineFactories["addpatient"]->AddTransition("continue","dberror","starting");
	
	$machineFactories["addpatient"]->AddTransition("loadfiles","dbloaded","waiting");
	$machineFactories["addpatient"]->AddTransition("error","dbloaded","fileerror");
	$machineFactories["addpatient"]->AddTransition("continue","fileerror","starting");
	
	$machineFactories["addpatient"]->AddTransition("error","waiting","adderror");
	$machineFactories["addpatient"]->AddTransition("continue","adderror","starting");
	$machineFactories["addpatient"]->AddTransition("addpatient","waiting","addsuccess");
	$machineFactories["addpatient"]->AddTransition("continue","addsuccess","waiting");
	$machineFactories["addpatient"]->AddTransition("cancel","waiting","starting");

	$machineFactories["addpatient"]->AddEnterCallback("addsuccess", <<<EOD
	
		var q = GetMessageQueue();
		var sm = GetStateMachine(".class-id-addpatientsheet");
		q.messagepump("send",function() {
			sm.cancel();
		});
EOD
	);
	$machineFactories["addpatient"]->AddEnterCallback("dbloaded", <<<EOD

		$.ajax({
			type:"POST",
			url:"http://localhost:50505/ajax/GetPeopleOnDisk",
			data:"{}",
			dataType:"text",
			success: function(data)
			{
				var patientsOnDiskJSON = JSON.parse(data);
				$(".class-id-addpatientsheet table.class-id-patientsondisk").dataTable({
					bJQueryUI : true,
					"sDom": 'T<"clear">lfrtip',
					"oTableTools": {
						"sRowSelect": "single",
						"aButtons" : [],
						fnRowSelected : OnPatientAddRowClick
		        	},
					"aoColumnDefs":
					[
						{ "sTitle": "First Name", "aTargets": [ 0 ], "mData": "FirstName" },
						{ "sTitle": "Last Name", "aTargets": [ 1 ], "mData": "LastName" },
					],
					"aaData" : patientsOnDiskJSON.people
				});
				var q = GetMessageQueue();
				var sm = GetStateMachine(".class-id-addpatientsheet");
				q.messagepump("send",function() {
					sm.loadfiles();
				});
},
			error: function()
			{
				var q = GetMessageQueue();
				var sm = GetStateMachine(".class-id-addpatientsheet");
				q.messagepump("send",function() {
					sm.error();
				});
			}
		});
EOD
	);
	$machineFactories["addpatient"]->AddEnterCallback("starting", <<<EOD
		$(".class-id-addpatientsheet button.class-id-mainmenu").button().off("click",CancelAddPatient);
		$(".class-id-addpatientsheet button.class-id-addpatient").button().off("click",DoAddPatient).button("disable");
		$(".class-id-newpatientfields input").off("change keyup input",UpdateAddPatientButton);
		$(".class-id-newpatientfields select").off("change keyup input",UpdateAddPatientButton);
		$(".class-id-addpatientsheet input.class-id-dob").datepicker("destroy");
		//$("table.class-id-patientsondisk").off("click",OnPatientAddRowClick);
			
		$(".class-id-addpatientsheet").fadeOut('fast',function() {
			var q = GetMessageQueue();
			var smMain = GetStateMachine(".class-id-main");
			q.messagepump("send",function()
			{
				smMain.run();
			});
		});
EOD
	);
	$machineFactories["addpatient"]->AddLeaveCallback("starting", <<<EOD

		$(".class-id-addpatientsheet button.class-id-mainmenu").button().on("click",CancelAddPatient);
		$(".class-id-addpatientsheet button.class-id-addpatient").button().on("click",DoAddPatient);
		$(".class-id-newpatientfields input").on("change keyup input",UpdateAddPatientButton);
		$(".class-id-newpatientfields select").on("change keyup input",UpdateAddPatientButton);
			$(".class-id-newpatientfields select").on("change",UpdateAddPatientButton);
			$(".class-id-addpatientsheet input.class-id-dob").datepicker({
			changeYear:true,
			changeMonth: true,
			maxDate : "-1d",
			minDate : "-100y",
			yearRange : "-99:-15"
		});
		$(".class-id-addpatientsheet").fadeIn('fast');
			
EOD
	);
	$machineFactories["addpatient"]->AddEnterCallback("running", <<<EOD
		$.ajax({
			type:"POST",
			url:"http://localhost:50505/ajax/GetPeopleInDb",
			data:"{}",
			dataType:"text",
			success: function(data)
			{
				var patientsInDbJSON = JSON.parse(data);
				$(".class-id-addpatientsheet table.class-id-patientsindb").dataTable({
					bJQueryUI : true,
					"sDom": 'T<"clear">lfrtip',
					"oTableTools": {
						"sRowSelect": "single",
						"aButtons" : []
		        	},
					"aoColumnDefs":
					[
						{ "sTitle": "First Name", "aTargets": [ 0 ], "mData": "first" },
						{ "sTitle": "Last Name", "aTargets": [ 1 ], "mData": "last" },
					],
					"aaData" : patientsInDbJSON.patients
				});
				var q = GetMessageQueue();
				var sm = GetStateMachine(".class-id-addpatientsheet");
				q.messagepump("send",function() {
					sm.loaddb();
				});
			},
			error: function(o,msg,msg2)
			{
				debugger;
				var q = GetMessageQueue();
				var sm = GetStateMachine(".class-id-addpatientsheet");
				q.messagepump("send",function() {
					sm.error();
				});
			}
		});

EOD
	);
	$machineFactories["addpatient"]->AddEnterCallback("dberror", <<<EOD
		
		
		$.getJSON("http://localhost:50505/ajax/GetPeopleOnDisk", function (patientsOnDiskJSON) {
			$(".class-id-addpatientsheet table.class-id-patientsondisk").dataTable({
				bJQueryUI : true,
				"sDom": 'T<"clear">lfrtip',
				"oTableTools": {
					"sRowSelect": "single",
					"aButtons" : [],
					"fnRowSelected" : function() { alert("OnPatientAddRowClick"); }
	        	},
				"aoColumnDefs":
				[
					{ "sTitle": "First Name", "aTargets": [ 0 ], "mData": "FirstName" },
					{ "sTitle": "Last Name", "aTargets": [ 1 ], "mData": "LastName" },
				],
				"aaData" : patientsOnDiskJSON
			});
			$(".class-id-addpatientsheet table.class-id-fields").dataTable({
				bJQueryUI : true,
				"bSort": false,
				"sDom": 't',
				iDisplayLength : 15
			});
		});
		$.getJSON("http://localhost:50505/ajax/GetPeopleInDb", function (patientsInDbJSON) {
			$(".class-id-addpatientsheet table.class-id-patientsindb").dataTable({
				bJQueryUI : true,
				"sDom": '<"clear">lfrtip',
				"oTableTools": {
					"aButtons" : []
	        	},
			"aoColumnDefs":
				[
					{ "sTitle": "First Name", "aTargets": [ 0 ], "mData": "FirstName" },
					{ "sTitle": "Last Name", "aTargets": [ 1 ], "mData": "LastName" },
				],
				"aaData" : patientsInDbJSON
			}); 
		});   
		$(".class-id-patientsondisk td").on("click",OnPatientAddRowClick);
		$(".class-id-addpatientsheet button.class-id-mainmenu").button().on("click",CancelAddPatient);
		$(".class-id-addpatientsheet button.class-id-addpatient").button().on("click",DoAddPatient);
		$(".class-id-addpatientsheet input.class-id-dob").datepicker({
			changeYear:true,
			changeMonth: true,
			maxDate : "-1d",
			minDate : "-100y",
			yearRange : "-99:-15",
			numberOfMonths : [1,3],
			stepMonths : 3
		});
		$(".class-id-addpatientsheet").fadeIn('fast',function() {
			var sm = $(this).data("statemachine");
			sm.transition();
		});
		return StateMachine.ASYNC;
	
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
