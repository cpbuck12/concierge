<?php

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
