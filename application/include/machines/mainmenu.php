<?php

$machineFactories["mainmenu"] = new StateMachine();
$machineFactories["mainmenu"]->AddTransition("run", "starting", "waiting");
$machineFactories["mainmenu"]->AddTransition("addfile", "waiting", "addingfile");
$machineFactories["mainmenu"]->AddTransition("addpatient", "waiting", "addingpatient");
$machineFactories["mainmenu"]->AddTransition("addfiles", "waiting", "addingfiles");
$machineFactories["mainmenu"]->AddTransition("adddoctor", "waiting", "addingdoctor");
$machineFactories["mainmenu"]->AddTransition("listdoctors", "waiting", "listdoctors");
$machineFactories["mainmenu"]->AddTransition("addspecialty", "waiting", "addingspecialty");
$machineFactories["mainmenu"]->AddTransition("createwebsite", "waiting", "creatingwebsite");
$machineFactories["mainmenu"]->AddTransition("browsedocuments", "waiting", "browsingdocuments");
$machineFactories["mainmenu"]->AddTransition("run", "addingfile", "waiting");
$machineFactories["mainmenu"]->AddTransition("run", "addingfiles", "waiting");
$machineFactories["mainmenu"]->AddTransition("run", "addingpatient", "waiting");
$machineFactories["mainmenu"]->AddTransition("run", "addingdoctor", "waiting");
$machineFactories["mainmenu"]->AddTransition("run", "listdoctors", "waiting");
$machineFactories["mainmenu"]->AddTransition("run", "addingspecialty", "waiting");
$machineFactories["mainmenu"]->AddTransition("run", "creatingwebsite", "waiting");
$machineFactories["mainmenu"]->AddTransition("run", "browsingdocuments", "waiting");


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

$machineFactories["mainmenu"]->AddEnterCallback("addingfile", <<<EOD

		SendMessage(".class-id-loadfilesheet",function(sm) {
			sm.run();
		});
		return true;
EOD
);

$machineFactories["mainmenu"]->AddEnterCallback("addingfile", <<<EOD

		SendMessage(".class-id-loadfilesheet",function(sm) {
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

		var thisMachine = GetStateMachine(".class-id-main");
		var smOther = SendMessage(".class-id-folderbrowsersheet",function(sm) {
			sm.run({
				machine : thisMachine,
				type: "folder" // "file"
			});
		});
		return true;
EOD
);

$machineFactories["mainmenu"]->AddEnterCallback("browsingdocuments", <<<EOD

		var thisMachine = GetStateMachine(".class-id-main");
		var smOther = SendMessage(".class-id-documentbrowsersheet",function(sm) {
			sm.run({
				machine : thisMachine
			});
		});
		return true;
EOD
);
