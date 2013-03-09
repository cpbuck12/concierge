<?php

$machineFactories["mainmenu"] = new StateMachine();
$machineFactories["mainmenu"]->AddTransition("run", "starting", "waiting");
$machineFactories["mainmenu"]->AddTransition("addpatient", "waiting", "addingpatient");
$machineFactories["mainmenu"]->AddTransition("addfiles", "waiting", "addingfiles");
$machineFactories["mainmenu"]->AddTransition("adddoctor", "waiting", "addingdoctor");
$machineFactories["mainmenu"]->AddTransition("listdoctors", "waiting", "listdoctors");
$machineFactories["mainmenu"]->AddTransition("addspecialty", "waiting", "addingspecialty");
$machineFactories["mainmenu"]->AddTransition("createwebsite", "waiting", "creatingwebsite");
$machineFactories["mainmenu"]->AddTransition("run", "addingfiles", "waiting");
$machineFactories["mainmenu"]->AddTransition("run", "addingpatient", "waiting");
$machineFactories["mainmenu"]->AddTransition("run", "addingdoctor", "waiting");
$machineFactories["mainmenu"]->AddTransition("run", "listdoctors", "waiting");
$machineFactories["mainmenu"]->AddTransition("run", "addingspecialty", "waiting");
$machineFactories["mainmenu"]->AddTransition("run", "creatingwebsite", "waiting");


$machineFactories["mainmenu"]->AddEnterCallback("waiting", <<<EOD

	$(".class-id-main").fadeIn('fast');
    return true;
EOD
);
	
$machineFactories["mainmenu"]->AddLeaveCallback("starting", <<<EOD
		$(".class-id-main").fadeIn('fast',function() {
			//var sm = $(this).data("statemachine");
			var sm = GetStateMachine();
//			sm.transition();
		});
		return true;
EOD
);
$machineFactories["mainmenu"]->AddLeaveCallback("waiting", <<<EOD
		$(".class-id-main").fadeOut('fast',function() {
			var sm = $(this).data("statemachine");
//			sm.transition();
		});
		return true;
EOD
);
$machineFactories["mainmenu"]->AddEnterCallback("addingfiles", <<<EOD

		SendMessage(".class-id-loadfromconciergesheet",function(sm) {
			sm.run();
		});
		return true;
EOD
);

$machineFactories["mainmenu"]->AddEnterCallback("addingdoctor", <<<EOD

		SendMessage(".class-id-adddoctorsheet",function(sm) {
			sm.run();
		});
		return true;
EOD
);

$machineFactories["mainmenu"]->AddEnterCallback("listdoctors", <<<EOD

		SendMessage(".class-id-updatedoctorsheet",function(sm) {
			sm.run();
		});
		return true;
EOD
);


$machineFactories["mainmenu"]->AddEnterCallback("addingpatient", <<<EOD

		var smOther = SendMessage(".class-id-addpatientsheet",function(sm) {
			sm.run();
		});
		return true;
EOD
);

$machineFactories["mainmenu"]->AddEnterCallback("addingspecialty", <<<EOD

		var smOther = SendMessage(".class-id-addspecialtysheet",function(sm) {
			sm.run();
		});
		return true;
EOD
);

$machineFactories["mainmenu"]->AddEnterCallback("creatingwebsite", <<<EOD

		var q = GetMessageQueue();
		var saveThis = this;
		var smOther = SendMessage(".class-id-folderbrowsersheet",function(sm) {
			sm.run({
				machine : saveThis,
				type: "folder" // "file"
			});
		});
		return true;
EOD
);
