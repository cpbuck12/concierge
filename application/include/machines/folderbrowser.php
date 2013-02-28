<?php

$machineFactories["folderbrowser"] = new StateMachine();
$machineFactories["folderbrowser"]->AddTransition("run",  "starting","initializing");
$machineFactories["folderbrowser"]->AddTransition("error","initializing","shutdown");
$machineFactories["folderbrowser"]->AddTransition("",     "initializing","waiting");
$machineFactories["folderbrowser"]->AddTransition("run","shtutdown","initializing");
