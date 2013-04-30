<?php

$machineFactories["addfile"] = new StateMachine();
$machineFactories["addfile"]->AddTransition("run","starting","waiting");
$machineFactories["addfile"]->AddTransition("startload","waiting","loading");
$machineFactories["addfile"]->AddTransition("goodload","loading","starting");
$machineFactories["addfile"]->AddTransition("badload","loading","errormsg");
$machineFactories["addfile"]->AddTransition("continue","errormsg","run");


$machineFactories["addfile"]->AddLeaveCallback("starting", <<<EOD

	$(".class-id-loadfilesheet").fadeIn('fast',function() {});
	$('.class-id-loadfilesheet button.class-id-addfile').button().on('click',function() {
		var path = $('.class-id-loadfilesheet input.class-id-path').val();
		var name = $('.class-id-loadfilesheet input.class-id-name').val();
		path = $.trim(path);
		name = $.trim(name);
		if(path.length == 0)
		{
			alert("No path");
			return;
		}
		CallServer({
			command: "AddFile",
			parameters: {
			    "path": path,
				"name": name
			},
			success: function(data) {
				if(data.status == "ok")
				{
					alert("OK");
				}
				else
				{
					alert("error");
				}
			},
			failure: function() {
				alert("failure");
			}
		});
});
		
EOD
);
