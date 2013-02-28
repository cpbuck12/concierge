<?php

	$machineFactories["addspecialty"] = new StateMachine();
	$machineFactories["addspecialty"]->AddTransition("run","starting","waiting");
	$machineFactories["addspecialty"]->AddTransition("error","waiting","displayingerror");
	$machineFactories["addspecialty"]->AddTransition("continue","displayingerror","waiting");
	$machineFactories["addspecialty"]->AddTransition("cancel","waiting","zombie");
	$machineFactories["addspecialty"]->AddTransition("success","waiting","displayingsuccess");
	$machineFactories["addspecialty"]->AddTransition("continue","displayingsuccess","zombie");
	$machineFactories["addspecialty"]->AddTransition("continue","zombie","starting");

	$machineFactories["addspecialty"]->AddLeaveCallback("starting", <<<EOD

		alert("leaving starting");
EOD
	);
	
	$machineFactories["addspecialty"]->AddEnterCallback("displayingsuccess", <<<EOD
		
		// TODO: add messagebox type acknowledgement
		var q = GetMessageQueue();
		var smAddSpecialty = GetStateMachine(".class-id-addspecialtysheet");
		q.messagepump("send",function() {
			smAddSpecialty.continue()
		});
EOD
	);
	$machineFactories["addspecialty"]->AddLeaveCallback("zombie", <<<EOD
		
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
			//sm.transition();
		});
		$.ajax({
			type:"POST",
			url:"http://localhost:50505/ajax/GetSpecialties",
			data:"{}",
			dataType:"text",
			success: function(data)
			{
				debugger;
				var specialtiesJSON = JSON.parse(data);
				$(".class-id-addspecialtysheet table.class-id-specialtiesindb").dataTable({
					bJQueryUI : true,
					"sDom": 'T<"clear">lfrtip',
					"oTableTools": {
						"sRowSelect": "single",
						"aButtons" : [],
						fnRowSelected : OnPatientAddRowClick
		        	},
					"aoColumnDefs":
					[
						{ "sTitle": "Specialty", "aTargets": [ 0 ], "mData": "specialty" },
						{ "sTitle": "Sub-specialty", "aTargets": [ 1 ], "mData": "subspecialty" }
					],
					"aaData" : specialtiesJSON.specialties
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
		return true;
	
EOD
	);
	
