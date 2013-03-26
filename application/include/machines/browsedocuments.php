<?php

$machineFactories["documentbrowser"] = new StateMachine();
$machineFactories["documentbrowser"]->AddTransition("run","starting","initializing");
$machineFactories["documentbrowser"]->AddTransition("run","shutdown","initializing");
$machineFactories["documentbrowser"]->AddTransition("continue","initializing","running");
$machineFactories["documentbrowser"]->AddTransition("delete","running","deleting");
$machineFactories["documentbrowser"]->AddTransition("continue","deleting","running");
$machineFactories["documentbrowser"]->AddTransition("loaderror","initializing","badload");
$machineFactories["documentbrowser"]->AddTransition("continue","badload","shutdown");
$machineFactories["documentbrowser"]->AddTransition("done","running","shutdown");
$machineFactories["documentbrowser"]->AddTransition("view","running","running");

$machineFactories["documentbrowser"]->AddLeaveCallback("deleting", <<<EOD

	var sheet = $(".class-id-documentbrowsersheet");
	$("button.class-id-view",sheet).button("enable");
	$("button.class-id-delete",sheet).button("enable");

EOD
	);

$machineFactories["documentbrowser"]->AddEnterCallback("deleting", <<<EOD

		
		YesNoBox("!!!!!WARNING!!!!!","You are about to permanently remove a file.  This cannot be undone.  DO YOU REALLY WANT TO DO THIS?",function(choice) {
			if(choice == "close" || choice == "no")
			{
				SendMessage(".class-id-documentbrowsersheet",function(sm) {
					fm.canceldelete();
				});
			}
			else
			{
				var sheet = $(".class-id-documentbrowsersheet");
				var table = $("table.class-id-documents",sheet);
				var oTable = table.dataTable({ bRetrieve : true });
				var oTableTools;
				table.each(function() { oTableTools =  TableTools.fnGetInstance(this); } );
				var row = oTableTools.fnGetSelected();
		    	var aData = oTableTools.fnGetSelectedData();
				var id = aData[0].Id;
		
				function Failure(msg)
				{
					MessageBox("Error",msg,function() {
						SendMessage(".class-id-documentbrowsersheet",function(sm) {
							sm.continue();
						});
					});
				}
				var buttonMain = $("button.class-id-mainmenu",sheet);
				var buttonView = $("button.class-id-view",sheet);
				var buttonDelete = $("button.class-id-delete",sheet);
		
				buttonMain.button("disable");
				buttonView.button("disable");
				buttonDelete.button("disable");
				CallServer({
					command: "DeleteDocument",
					parameters: { "id" : id },
					success: function(data)
					{
						buttonMain.button("enable");
						if(data.status == "ok")
						{
							SendMessage(".class-id-documentbrowsersheet",function(sm) {
								oTable.fnDeleteRow(row);
								sm.continue();
							});
						}
						else
						{
							Failure("DeleteFile failed, reason:"+data.reason);
							return false;
						}
					},
					failure: function()
					{
						buttonMain.button("enable");
						buttonView.button("enable");
						buttonDelete.button("enable");
						Failure("Error calling server for DeleteFile");
						return false;
					}
				});
			}
		});

EOD

		);
		

$machineFactories["documentbrowser"]->AddBeforeCallback("view", <<<EOD

		debugger;
		var sheet = $(".class-id-documentbrowsersheet");
		var table = $("table.class-id-documents",sheet);
		var oTable = table.dataTable({ bRetrieve : true });
		var oTableTools;
		table.each(function() { oTableTools =  TableTools.fnGetInstance(this); } );
    	var aData = oTableTools.fnGetSelectedData();
		var id = aData[0].Id;
		downloadFile(id);
EOD
	);

$machineFactories["documentbrowser"]->AddLeaveCallback("running", <<<EOD

		return true;
EOD
	);

$machineFactories["documentbrowser"]->AddEnterCallback("shutdown", <<<EOD

		var sheet = $(".class-id-documentbrowsersheet");
		var table = $("table.class-id-documents",sheet);
		UnstashTable(table);
		
		var sheet = $(".class-id-documentbrowsersheet");
		var table = $("table.class-id-documents",sheet);
		var buttonMain = $("button.class-id-mainmenu",sheet);
		var buttonView = $("button.class-id-view",sheet);
		var buttonDelete = $("button.class-id-delete",sheet);
		buttonMain.off("click").button("destroy");
		buttonView.off("click").button("destroy");
		buttonView.off("click").button("destroy");
		
		SendMessage(".class-id-documentbrowsersheet",function(sm) {
			sheet.fadeOut(function() {
				sm.init.machine.run();
			});
		});
		
EOD
	);

$machineFactories["documentbrowser"]->AddEnterCallback("badload", <<<EOD

		MessageBox("Error",msg,function() {
			SendMessage(".class-id-documentbrowsersheet",function(sm) {
				sm.continue();
			});
		});

EOD
	);

$machineFactories["documentbrowser"]->AddEnterCallback("initializing", <<<EOD

		var sheet = $(".class-id-documentbrowsersheet");
		var table = $("table.class-id-documents",sheet);
		var buttonMain = $("button.class-id-mainmenu",sheet);
		var buttonView = $("button.class-id-view",sheet);
		var buttonDelete = $("button.class-id-delete",sheet);
		this.init = msg;
		sheet.fadeIn();
		debugger;
		buttonMain.button().on("click",function() {
			SendMessage(".class-id-documentbrowsersheet",function(sm) {
				sm.done();
			});
		});
		buttonView.button().on("click",function() {
			SendMessage(".class-id-documentbrowsersheet",function(sm) {
				sm.view();
			});
		}).button("disable");
		buttonDelete.button().on("click",function() {
			SendMessage(".class-id-documentbrowsersheet",function(sm) {
				sm.delete();
			});
		}).button("disable");
		function Failure(msg)
		{
			SendMessage(".class-id-documentbrowsersheet",function(sm) {
				sm.loaderror(msg);
			});
		}
		CallServer({
			command:"BrowseDocuments",
			parameters: {},
			success: function(data)
			{
				if(data.status == "ok")
				{
					StashElement(table);
					table.dataTable({
						bJQueryUI: true,
						sDom: 'T<"clear">lfrtip',
						oTableTools: {
							"sRowSelect": "single",
							"aButtons" : [],
							"fnRowSelected": function(nodes) {
								buttonView.button("enable");
								buttonDelete.button("enable");
							}
			        	},
						aoColumnDefs:
						[
							{ "sTitle": "Specialty", "aTargets": [ 0 ], "mData": "Specialty" },
							{ "sTitle": "Subspecialty", "aTargets": [ 1 ], "mData": "Subspecialty" },
							{ "sTitle": "Doctor", "aTargets": [ 2 ], "mData": "Doctor" },
							{ "sTitle": "Procedure", "aTargets": [ 3 ], "mData": "Procedure" },
							{ "sTitle": "Location", "aTargets": [ 4 ], "mData": "Location" },
							{ "sTitle": "Patient", "aTargets": [ 5 ], "mData": "Patient" },
							{ "sTitle": "Original Filename", "aTargets": [ 6 ], "mData": "Path" },		
							{ "sTitle": "Checksum", "aTargets": [ 7 ], "mData": "Checksum" },
							{ "sTitle": "Id", "aTargets": [8], "mData" : "Id", bVisible : false }
						],
						"aaData" : data.documents
					});
					SendMessage(".class-id-documentbrowsersheet",function(sm) {
						sm.continue();
					});
				}
				else
				{
					Failure("Could not get documents.  Reason:"+data.reason);
				}
			},
			failure: function()
			{
				Failure("Could not call server while retrieving documents");
			}
		});
		
EOD
	);