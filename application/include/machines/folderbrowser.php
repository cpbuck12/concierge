<?php

$machineFactories["folderbrowser"] = new StateMachine();
$machineFactories["folderbrowser"]->AddTransition("run",       "starting","initializing");
$machineFactories["folderbrowser"]->AddTransition("error",     "initializing","shutdown");
$machineFactories["folderbrowser"]->AddTransition("initialize","initializing","waiting");
$machineFactories["folderbrowser"]->AddTransition("ok","waiting","ok");
$machineFactories["folderbrowser"]->AddTransition("choose","waiting","waiting");
$machineFactories["folderbrowser"]->AddTransition("cancel","waiting","cancel");
$machineFactories["folderbrowser"]->AddTransition("shutdown","ok","shutdown");
$machineFactories["folderbrowser"]->AddTransition("shutdown","cancel","shutdown");
$machineFactories["folderbrowser"]->AddTransition("run",       "shtutdown","initializing");

$machineFactories["folderbrowser"]->AddAfterCallback("choose", <<<EOD


		
EOD
);

$machineFactories["folderbrowser"]->AddEnterCallback("initializing", <<<EOD

	$(".class-id-folderbrowsersheet").fadeIn();
	$(".class-id-folderbrowsersheet button.class-id-open").button().on("click",DoChangeFolder); 
	debugger;
	this.owner = msg;
	$.ajax({
		type:"POST",
		url:"http://localhost:50505/ajax/SetCurrentDirectory",
		data:"{}",
		dataType:"text",
		success: function(data)
		{
			debugger;
			var result = JSON.parse(data);
			if(result.status == "ok")
			{
				var fileInfo = result.fileInfo;
				var currentPath = fileInfo.currentPath;
				var files = fileInfo.files;
				var folders = fileInfo.folders;
				var volumes = fileInfo.volumes;
		
				$(".class-id-folderbrowsersheet table.class-id-folders").dataTable({
					bJQueryUI : true,
					"sDom": 'T<"clear">lfrtip',
					"oTableTools": {
						"sRowSelect": "single",
						"aButtons" : [],
						"fnRowSelected" : function(nodes) {
							var fullName = $(nodes[1]).val();
							SendMessage(".class-id-folderbrowsersheet",function(sm) { sm.choose(); }); 
						}
					},
					"aoColumnDefs":
					[
						{ "sTitle": "Name", "aTargets": [ 0 ], "mData": "Name" },
						{ "sTitle": "FullName", "aTargets": [ 1 ], "mData": "FullName", bVisible : false },
					],
					"aaData" : folders
				});
		
				$(".class-id-folderbrowsersheet table.class-id-volumes").dataTable({
					bJQueryUI : true,
					"sDom": 'T<"clear">lfrtip',
					"oTableTools":
					{
						"sRowSelect": "single",
						"aButtons" : [],
						"fnRowSelected" : function(nodes)
						{
							var name = $(nodes[0]).val();
//							SendMessage(".class-id-folderbrowsersheet",function(sm) { sm.choose(); }); 
						}
					},
					"aoColumnDefs": [{"sTitle" : "Name", "aTargets" : [0], "mData" : "Name" }],
					"aaData" : volumes
				});
		
				$(".class-id-folderbrowsersheet table.class-id-files").dataTable({
					bJQueryUI : true,
					"sDom": 'T<"clear">lfrtip',
					"oTableTools": {
						"sRowSelect": "single",
						"aButtons" : []
					},
					"aoColumnDefs":
					[
						{ "sTitle": "Name", "aTargets": [ 0 ], "mData": "Name" },
						{ "sTitle": "Size", "aTargets": [ 1 ], "mData": "Length" },
						{ "sTitle": "Modified", "aTargets": [ 2 ], "mData": "LastWriteTime" },
						{ "sTitle": "FullName", "aTargets": [ 3 ], "mData": "FullName", bVisible : false },
					],
					"aaData" : files
				});
			}
		}
		
	});
				
EOD
);
