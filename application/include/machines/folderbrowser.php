<?php

$machineFactories["folderbrowser"] = new StateMachine();
$machineFactories["folderbrowser"]->AddTransition("run",       "starting","initializing");
$machineFactories["folderbrowser"]->AddTransition("error",     "initializing","shutdown");
$machineFactories["folderbrowser"]->AddTransition("initialize","initializing","waiting");
$machineFactories["folderbrowser"]->AddTransition("choose","waiting","waiting");
$machineFactories["folderbrowser"]->AddTransition("done","waiting","shutdown");
$machineFactories["folderbrowser"]->AddTransition("run","shutdown","initializing");

$machineFactories["folderbrowser"]->AddEnterCallback("shutdown", <<<EOD

	$(".class-id-folderbrowsersheet button.class-id-cancel").button().off().button("destroy"); 
	$(".class-id-folderbrowsersheet button.class-id-open").button().off().button("destroy");
	$(".class-id-folderbrowsersheet button.class-id-ok").button().off().button("destroy");
	$(".class-id-folderbrowsersheet table").each(function() {
		var element = $(this);
		UnstashTable(element);
	});	

	SendMessage(".class-id-folderbrowsersheet",function(sm) {
		debugger;
		$(".class-id-folderbrowsersheet").fadeOut(function() {
			sm.init.machine.run();
		});
	});

EOD
	);

$machineFactories["folderbrowser"]->AddAfterCallback("choose", <<<EOD

	CallServer({
		command:"CreateWebsite",
		parameters: { id : 8, destination : "c:\\\\temp\\\\parkit" },
		success: function(data)
		{
		alert(data);
		},
		failure:function()
		{
		alert("33 1/3");
		}
	});
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
			debugger;
			var oTable = $(foldersElement).dataTable({bRetrieve : true});
			var oTableTools;
			$(foldersElement).each(function(){ oTableTools = TableTools.fnGetInstance(this); });
		    var aData = oTableTools.fnGetSelectedData();
			CallServer({
				command:"SetCurrentDirectory",
				parameters:{ path : aData[0].FullName },
				success: function(_data)
				{
					debugger;
					$(".class-id-folderbrowsersheet button.class-id-open").button("disable");
					BuildFileBrowser(_data,foldersElement,volumesElement,filesElement,false,initSave.type);
				},
				failure: function() {
					debugger;
					// TODO 
				}
			});
		}
		else if(msg == "volume")
		{
			var oTable = $(volumesElement).dataTable({bRetrieve : true});
			var oTableTools;
			$(volumesElement).each(function(){ oTableTools = TableTools.fnGetInstance(this); });
		    var aData = oTableTools.fnGetSelectedData();

			CallServer({
				command:"SetCurrentDirectory",
				parameters:{ path : aData[0].Name },
				success: function(data)
				{
					$(".class-id-folderbrowsersheet button.class-id-open").button("disable");
					BuildFileBrowser(data,foldersElement,volumesElement,filesElement,false);
				},
				failure: function()
				{
					debugger;
		// TODO
				}
			});
		}
	}
EOD
);

$machineFactories["folderbrowser"]->AddEnterCallback("initializing", <<<EOD

	function FileBrowserOk()
	{
	    var FullName = 	$(".class-id-folderbrowsersheet div.class-id-folderlabel").text();
	
		SendMessage(".class-id-folderbrowsersheet",function(sm) {
			sm.done({
				button : "ok",
				filename : FullName
			});
		}); 
	}
	
	function FileBrowserOpen()
	{
		var filesElement = $(".class-id-folderbrowsersheet table.class-id-files");
		var oTable = $(filesElement).dataTable({bRetrieve : true});
		var oTableTools;
		$(filesElement).each(function(){ oTableTools = TableTools.fnGetInstance(this); });
	    var aData = oTableTools.fnGetSelectedData();
	    var FullName = aData[0].FullName;  // This has NOT BEEN CHECKED
		SendMessage(".class-id-folderbrowsersheet",function(sm) {
			sm.done({
				button : "open",
				foldername : FullName
			});
		}); 
	}
	
	function FileBrowserCancel()
	{
		SendMessage(".class-id-folderbrowsersheet",function(sm) {
			sm.done({
				button : "cancel",
			});
		}); 
	}
		
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

	CallServer({
		command:"SetCurrentDirectory",
		parameters:{},
		success: function(data)
		{
		    BuildFileBrowser(data,foldersElement,volumesElement,filesElement,true,msg.type);
		},
		failure: function()
		{
			//TODO
		}
	});
	$(".class-id-folderbrowsersheet").fadeIn();
		
				
EOD
);
