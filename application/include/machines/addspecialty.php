<?php

	$machineFactories["addspecialty"] = new StateMachine();
	$machineFactories["addspecialty"]->AddTransition("run","starting","gettingspecialties");
	$machineFactories["addspecialty"]->AddTransition("errorgetting","gettingspecialties","displayingerrorandleave");
	$machineFactories["addspecialty"]->AddTransition("continue","gettingspecialties","waiting");
	$machineFactories["addspecialty"]->AddTransition("added","waiting","displayingsuccess");
	$machineFactories["addspecialty"]->AddTransition("cancel","waiting","shutdown");
	
	$machineFactories["addspecialty"]->AddTransition("continue","displayingsuccess","shutdown");
	$machineFactories["addspecialty"]->AddTransition("continue","displayingerrorandleave","shutdown");
	$machineFactories["addspecialty"]->AddTransition("run","shutdown","gettingspecialties");
	
	$machineFactories["addspecialty"]->AddTransition("erroradding","waiting","displayingerrorandleave");
	
	$machineFactories["addspecialty"]->AddTransition("cancel","waiting","shutdown");
	
	
	$machineFactories["addspecialty"]->AddLeaveCallback("waiting", <<<EOD
	
		var element = $(".class-id-addspecialtysheet table.class-id-specialtiesindb");
		UnstashTable(element);	
		
EOD
		);
	$machineFactories["addspecialty"]->AddEnterCallback("displayingsuccess", <<<EOD
		
		MessageBox("Add Specialty","Added",function() {
			SendMessage(".class-id-addspecialtysheet",function(sm) {
				sm.continue();
			});
		});

EOD
	);
	$machineFactories["addspecialty"]->AddEnterCallback("shutdown", <<<EOD
		
		$(".class-id-addspecialtysheet button.class-id-mainmenu").button().off("click").button("destroy");
		$(".class-id-addspecialtysheet button.class-id-addspecialty").button().off("click").button("destroy");
		var tbl = $("table.class-id-specialtyaddfields");
		UnstashTable(tbl);
		$(".class-id-addspecialtysheet").fadeOut('fast',function()
		{
			SendMessage(".class-id-main",function(sm) {
				sm.run();
			});
		});
EOD
	);

	$machineFactories["addspecialty"]->AddEnterCallback("displayingerrorandleave", <<<EOD

		MessageBox("Error",msg,function() {
			SendMessage(".class-id-addspecialtysheet",function(sm) {
				sm.continue();
			});
		});
	
EOD
	);
	
	$machineFactories["addspecialty"]->AddEnterCallback("gettingspecialties", <<<EOD

		function Failure(msg)
		{
			SendMessage(".class-id-addpatientsheet",function(sm) {
				sm.errorgetting(msg);
			});
		}

		function CancelAddSpecialty()
		{
			SendMessage(".class-id-addspecialtysheet",function(sm) {
				sm.cancel();
			});
		}
		function DoAddSpecialty()
		{
			var vars = [];
			var o = {};
			var sheet = $(".class-id-addspecialtysheet");
			var acc = "";
			$(".class-fieldset input",sheet).each(function()
			{
				_this = $(this);
				var classes = _this.attr("class").split(' ');
				for(var i = 0;i < classes.length;i++)
				{
					if(classes[i].substr(0,9) == "class-id-")
					{
						var id = classes[i].substr(9);
						o[id] = _this.val();
						break;
					}
				}
			});
			function Failure(msg)
			{
				SendMessage(".class-id-addspecialtysheet",function(sm) {
					debugger;
					sm.erroradding(msg);
				});
			}
			CallServer({
				command:"AddSpecialty",
				parameters:o,
				success:function(data) {
					if(data.status == "ok")
					{
						SendMessage(".class-id-addspecialtysheet",function(sm) {
							sm.added();
						});
					}
					else
					{
						Failure("Could not add specialty.  Reason:"+data.reason);
					}
				},
				failure:function() {
					Failure("Error calling server while trying to add specialty");
				}
			});
		}
			
		$(".class-id-addspecialtysheet button.class-id-mainmenu").button().on("click",CancelAddSpecialty);
		$(".class-id-addspecialtysheet button.class-id-addspecialty").button().on("click",DoAddSpecialty);

		var tbl = $("table.class-id-specialtyaddfields");
		StashElement(tbl);
		tbl.dataTable({ "sDom":"t","bJQueryUI": true,"bPageinate":false,
				iDisplayLength : 15, bSort : false});
		$(".class-id-addspecialtysheet").fadeIn('fast',function() {
	
			CallServer({
				command:"GetSpecialties",
				parameters:{},
				success: function(data)
				{
					if(data.status == "ok")
					{
						var specialtiesJSON = data;
						var element = $(".class-id-addspecialtysheet table.class-id-specialtiesindb");
						StashElement(element);
						element.dataTable({
							bJQueryUI : true,
							"sDom": 'T<"clear">lfrtip',
							"oTableTools": {
								"sRowSelect": "single",
								"aButtons" : [],
								fnRowSelected : OnPatientAddRowClick
				        	},
							"aoColumnDefs":
							[
								{ "sTitle": "Specialty", "aTargets": [ 0 ], "mData": "specialty" },
								{ "sTitle": "Sub-specialty", "aTargets": [ 1 ], "mData": "subspecialty" }
							],
							"aaData" : specialtiesJSON.specialties
						});
						SendMessage(".class-id-addspecialtysheet",function(sm) {
							sm.continue();
						});
					}
					else
					{
						Failure("Error retrieving specialties from the database, reason:"+data.reason);
					}
				},
				failure: function()
				{
					Failure("Error calling server while retrieving specialties");
				}			
			});
		});			

EOD
		);
