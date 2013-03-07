<?php

$machineFactories["folderbrowser"] = new StateMachine();
$machineFactories["folderbrowser"]->AddTransition("run",       "starting","initializing");
$machineFactories["folderbrowser"]->AddTransition("error",     "initializing","shutdown");
$machineFactories["folderbrowser"]->AddTransition("initialize","initializing","waiting");
$machineFactories["folderbrowser"]->AddTransition("choose","waiting","waiting");
$machineFactories["folderbrowser"]->AddTransition("done","waiting","shutdown");
$machineFactories["folderbrowser"]->AddTransition("run","shutdown","initializing");

$machineFactories["folderbrowser"]->AddEnterCallback("shutdown", <<<EOD

	$(".class-id-folderbrowsersheet button.class-id-cancel").button().off('click',FileBrowserCancel).button("destroy"); 
	$(".class-id-folderbrowsersheet button.class-id-open").button().off('click',FileBrowserOpen).button("destroy");
	$(".class-id-folderbrowsersheet button.class-id-ok").button().off('click',FileBrowserOk).button("destroy");
	$(".class-id-folderbrowsersheet table").each(function() {
		var element = $(this);
		UnstashTable(element);
	});	
		
	var machine = this.init.machine;
	var q = GetMessageQueue();
	$(".class-id-folderbrowsersheet").fadeOut("fast",function() {
		q.messagepump("send",function() { machine.run(msg); });
	});
EOD
	);

$machineFactories["folderbrowser"]->AddAfterCallback("choose", <<<EOD

//	var oTable = elem.dataTable({ bRetrieve : true });
//	var oTableTools;
//	elem.each(function() { oTableTools =  TableTools.fnGetInstance(this); } );
	
	var initSave = this.init;
	var foldersElement = $(".class-id-folderbrowsersheet table.class-id-folders");
    var volumesElement = $(".class-id-folderbrowsersheet table.class-id-volumes");
	var filesElement   = $(".class-id-folderbrowsersheet table.class-id-files");
	if(msg == "file")
	{
		$(".class-id-folderbrowsersheet button.class-id-open").button("enable");
	}
	else
	{
		if(msg == "folder")
		{
			var oTable = $(foldersElement).dataTable({bRetrieve : true});
			var oTableTools;
			$(foldersElement).each(function(){ oTableTools = TableTools.fnGetInstance(this); });
		    var aData = oTableTools.fnGetSelectedData();
			var payload = JSON.stringify({ path : aData[0].FullName });
			$.ajax({
				type:"POST",
				url:"http://localhost:50505/ajax/SetCurrentDirectory",
				data : payload,
				dataType : "text",
				success: function(_data)
				{
					$(".class-id-folderbrowsersheet button.class-id-open").button("disable");
					BuildFileBrowser(_data,foldersElement,volumesElement,filesElement,false,initSave.type);
				}
			});
		}
		else if(msg == "volume")
		{
			var oTable = $(volumesElement).dataTable({bRetrieve : true});
			var oTableTools;
			$(volumesElement).each(function(){ oTableTools = TableTools.fnGetInstance(this); });
		    var aData = oTableTools.fnGetSelectedData(); 
			var payload = JSON.stringify({ path : aData[0].Name });
			$.ajax({
				type:"POST",
				url:"http://localhost:50505/ajax/SetCurrentDirectory",
				data : payload,
				dataType : "text",
				success: function(data)
				{
					$(".class-id-folderbrowsersheet button.class-id-open").button("disable");
					BuildFileBrowser(data,foldersElement,volumesElement,filesElement,false);
				}
			});
		}
	}
EOD
);

$machineFactories["folderbrowser"]->AddEnterCallback("initializing", <<<EOD

    this.init = msg;
	var foldersElement = $(".class-id-folderbrowsersheet table.class-id-folders");
    var volumesElement = $(".class-id-folderbrowsersheet table.class-id-volumes");
	var filesElement   = $(".class-id-folderbrowsersheet table.class-id-files");
	$(".class-id-folderbrowsersheet button.class-id-open").button().button("disable");
	if(msg.type == "file")
	{
		$(".class-id-folderbrowsersheet button.class-id-ok").button().button("disable"); 
	} 
	else
	{
		$(".class-id-folderbrowsersheet button.class-id-ok").button(); 
	} 
	if(msg.type == "folder")
	{
		$(".class-id-folderbrowsersheet button.class-id-ok").button();
	}
	else
	{
		$(".class-id-folderbrowsersheet button.class-id-ok").button().button("disable");
	} 
	$(".class-id-folderbrowsersheet button.class-id-cancel").button().on('click',FileBrowserCancel); 
	$(".class-id-folderbrowsersheet button.class-id-open").button().on('click',FileBrowserOpen);
	$(".class-id-folderbrowsersheet button.class-id-ok").button().on('click',FileBrowserOk); 
		
		//StashElement(foldersElement);
	//StashElement(volumesElement);
	//StashElement(filesElement);
	$.ajax({
		type:"POST",
		url:"http://localhost:50505/ajax/SetCurrentDirectory",
		data:"{}",
		dataType:"text",
		success: function(data)
		{
		    BuildFileBrowser(data,foldersElement,volumesElement,filesElement,true,msg.type);
		}
    }); // ajax
	$(".class-id-folderbrowsersheet").fadeIn();
		/*
	$(".class-id-folderbrowsersheet").fadeIn();
	$(".class-id-folderbrowsersheet button.class-id-open").button().on("click",DoChangeFolder); 
	this.owner = msg;
	$.ajax({
		type:"POST",
		url:"http://localhost:50505/ajax/SetCurrentDirectory",
		data:"{}",
		dataType:"text",
		success: function(data)
		{
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
		
	}); // ajax
	*/
				
EOD
);
