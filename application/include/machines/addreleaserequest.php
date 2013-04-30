<?php

$machineFactories["addreleaserequest"] = new StateMachine();
$machineFactories["addreleaserequest"]->AddTransition("run","starting","loadingpatients");
$machineFactories["addreleaserequest"]->AddTransition("loaded","loadingpatients","waitingonpatient");
$machineFactories["addreleaserequest"]->AddTransition("notloaded","loadingpatients","cancelling");
$machineFactories["addreleaserequest"]->AddTransition("selectpatient","waitingonpatient","patientselected");
$machineFactories["addreleaserequest"]->AddTransition("selectpatient","patientselected","patientreselected");
$machineFactories["addreleaserequest"]->AddTransition("docontinue","patientreselected","patientselected");

$machineFactories["addreleaserequest"]->AddTransition("filechange","patientselected","patientselected");
$machineFactories["addreleaserequest"]->AddTransition("selectdoctor","patientselected","doctorselected");
$machineFactories["addreleaserequest"]->AddTransition("selectdoctor","doctorselected","doctorselected");
$machineFactories["addreleaserequest"]->AddTransition("selectpatient","doctorselected","patientselected");
	
$machineFactories["addreleaserequest"]->AddEnterCallback("loadingpatients", <<< EOD
		
		; // NOOP
EOD
);