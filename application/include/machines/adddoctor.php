<?php

$machineFactories["adddoctor"] = new StateMachine();
$machineFactories["adddoctor"]->AddTransition("run","starting","waiting");
$machineFactories["adddoctor"]->AddTransition("error","waiting","displayingerror");
$machineFactories["adddoctor"]->AddTransition("continue","displayingerror","waiting");
$machineFactories["adddoctor"]->AddTransition("cancel","waiting","starting");
$machineFactories["adddoctor"]->AddTransition("success","waiting","displayingsuccess");
$machineFactories["adddoctor"]->AddTransition("continue","displayingsuccess","shutdown");
$machineFactories["adddoctor"]->AddTransition("run","shutdown","waiting");

$machineFactories["adddoctor"]->AddEnterCallback("displayingsuccess", <<<EOD
		
		// TODO: add messagebox type acknowledgement
		var q = GetMessageQueue();
		var smAddDoctor = GetStateMachine(".class-id-adddoctorsheet");
		q.messagepump("send",function() {
			smAddDoctor.continue()
		});
EOD
);
$machineFactories["adddoctor"]->AddEnterCallback("shutdown", <<<EOD
		$(".class-id-adddoctorsheet button.class-id-mainmenu").button().off("click",CancelAddDoctor);
		$(".class-id-adddoctorsheet button.class-id-adddoctor").button().off("click",DoAddDoctor);
		var tbl = $("table.class-id-doctoraddfields");
		$(".class-id-adddoctorsheet").fadeOut('fast',function()
		{
		debugger;
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

$machineFactories["adddoctor"]->AddEnterCallback("waiting", <<<EOD

		$(".class-id-adddoctorsheet button.class-id-mainmenu").button().on("click",CancelAddDoctor);
		$(".class-id-adddoctorsheet button.class-id-adddoctor").button().on("click",DoAddDoctor);
		var tbl = $("table.class-id-doctoraddfields");
		StashElement(tbl);
		tbl.dataTable({ "sDom":"t","bJQueryUI": true,"bPageinate":false,
				iDisplayLength : 15, bSort : false});
		$("input.class-id-country",tbl).val("USA");
		$(".class-id-adddoctorsheet").fadeIn('fast',function() {
//			var sm = $(this).data("statemachine");
//			sm.transition();
		});
		return true;

EOD
);

