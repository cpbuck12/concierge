<?php

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
		return true;

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
