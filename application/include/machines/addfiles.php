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
		
		SendMessage(".class-id-loadfromconciergesheet",function(sm) {
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
			
		function DepopulateFiles()
		{
			var elem = $("table.class-id-filesystem");
			elem.hide();
			UnstashElement(elem,function() {
				var oTable = elem.dataTable({ bRetrieve: true });
				oTable.fnDestroy(true);
			});
		}
		$("button.class-id-loadfiles").button("disable");
		DepopulateFiles();
		SendMessage(".class-id-loadfromconciergesheet",function(sm) {
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

		$("table.class-id-patient tr").off("click");
EOD
	);			
	$machineFactories["addfiles"]->AddLeaveCallback("loadingpatients", <<<EOD

		function OnPatientRowClick()
		{ 
			// TODO: cleaup
			SendMessage(".class-id-loadfromconciergesheet",function(sm) {
				if($("table.class-id-patient tr.DTTT_selected").length > 0)
				{
					sm.selectpatient();
				}
				else
				{
					sm.unselectpatient();
				}
			});
		}
		$("table.class-id-patient tr").on("click",OnPatientRowClick);
	
EOD
	);
	$machineFactories["addfiles"]->AddEnterCallback("starting", <<<EOD

		$(".class-id-loadfromconciergesheet button.class-id-mainmenu").button().off("click");
		$(".class-id-loadfromconciergesheet button.class-id-loadfiles").button().off("click");
		$(".class-id-loadfromconciergesheet").fadeOut('fast',function() {
			SendMessage(".class-id-main",function(sm) {
				sm.run();
			});
		});
EOD
	);

	$machineFactories["addfiles"]->AddLeaveCallback("starting", <<<EOD

		function CancelFileLoading()
		{
			SendMessage(".class-id-loadfromconciergesheet",function(sm) {
				sm.cancel();
			});
		}
		
		function DoFileLoading()
		{
			oData = {
		        activities : []
		 	};
			var elem = $("table.class-id-filesystem");
			var oTable = elem.dataTable({ bRetrieve: true });
			var oTT = TableTools.fnGetInstance(elem[0]);
			var aData = oTT.fnGetSelectedData();
			for(var i = 0;i < aData.length;i++)
			{
				var o = {
				    path : aData[i].FullName,
				    specialty : aData[i].Specialty,
				    subspecialty : aData[i].Subspecialty,
				    firstname : aData[i].FirstName,
				    lastname : aData[i].LastName
				}
				oData.activities.push(o);
			}
			debugger;
			CallServer({
				command:"AddActivities",
				parameters: oData,
				success: function(data)
				{
					debugger;
				},
				failure:function()
				{
					debugger;
				}
			});
			debugger;
			// TODO: load the files
		}
			
		$(".class-id-loadfromconciergesheet button.class-id-mainmenu").button().on("click",CancelFileLoading);
		$(".class-id-loadfromconciergesheet button.class-id-loadfiles").button().on("click",DoFileLoading);
		$(".class-id-loadfromconciergesheet").fadeIn('fast',function() {
			SendMessage(".class-id-loadfromconciergesheet",function(sm) {
				sm.transition(); // TODO: why is this being called here, when transition() doesn't look valid
			});
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
			SendMessage(".class-id-loadfromconciergesheet",function(sm) {
				sm.loaded();
			});
			return true;
		});
EOD
	);
