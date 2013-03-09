<?php

$machineFactories["adddoctor"] = new StateMachine();
$machineFactories["adddoctor"]->AddTransition("run","starting","waiting");
$machineFactories["adddoctor"]->AddTransition("error_","waiting","displayingerror");
$machineFactories["adddoctor"]->AddTransition("continue","displayingerror","waiting");
$machineFactories["adddoctor"]->AddTransition("cancel","waiting","shutdown");
$machineFactories["adddoctor"]->AddTransition("success","waiting","displayingsuccess");
$machineFactories["adddoctor"]->AddTransition("continue","displayingsuccess","shutdown");
$machineFactories["adddoctor"]->AddTransition("run","shutdown","waiting");

$machineFactories["adddoctor"]->AddEnterCallback("displayingsuccess", <<<EOD
		
		MessageBox("Add Doctor","Doctor successfully added",function() {
			SendMessage(".class-id-adddoctorsheet",function(sm) {
				sm.continue();
			});
		});
EOD
);


$machineFactories["adddoctor"]->AddEnterCallback("displayingerror", <<<EOD

		MessageBox("Add Doctor",msg,function() {
			SendMessage(".class-id-adddoctorsheet",function(sm) {
				sm.continue();
			});
		});

EOD
		);

$machineFactories["adddoctor"]->AddEnterCallback("shutdown", <<<EOD

		$(".class-id-adddoctorsheet button.class-id-mainmenu").button().off("click");
		$(".class-id-adddoctorsheet button.class-id-adddoctor").button().off("click");
		var tbl = $("table.class-id-doctoraddfields");
		$(".class-id-adddoctorsheet").fadeOut('fast',function()
		{
			UnstashTable(tbl);
			var smMain = GetStateMachine(".class-id-main");
			var q = GetMessageQueue();
			q.messagepump('send',function()
			{
				smMain.run();
			});
		});

EOD
);

$machineFactories["adddoctor"]->AddLeaveCallback("shutdown", <<<EOD

		$(".class-id-adddoctorsheet button.class-id-mainmenu").button().on("click",CancelAddDoctor);
		$(".class-id-adddoctorsheet button.class-id-adddoctor").button().on("click",DoAddDoctor);
		var tbl = $("table.class-id-doctoraddfields");
		StashElement(tbl);
		tbl.dataTable({ "sDom":"t","bJQueryUI": true,"bPageinate":false,
				iDisplayLength : 15, bSort : false});
		$(".class-id-adddoctorsheet").fadeIn();
		return true;

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
		$("input.class-id-city",tbl).val("New York");
		$("input.class-id-locality1",tbl).val("NY");
		$(".class-id-adddoctorsheet").fadeIn();		
		return true;

EOD
);

