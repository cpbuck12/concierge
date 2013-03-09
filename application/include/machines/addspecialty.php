<?php

	$machineFactories["addspecialty"] = new StateMachine();
	$machineFactories["addspecialty"]->AddTransition("run","starting","waiting");
	$machineFactories["addspecialty"]->AddTransition("error","waiting","displayingerror");
	$machineFactories["addspecialty"]->AddTransition("continue","displayingerror","waiting");
	$machineFactories["addspecialty"]->AddTransition("cancel","waiting","zombie");
	$machineFactories["addspecialty"]->AddTransition("success","waiting","displayingsuccess");
	$machineFactories["addspecialty"]->AddTransition("continue","displayingsuccess","zombie");
	$machineFactories["addspecialty"]->AddTransition("continue","zombie","starting");

	$machineFactories["addspecialty"]->AddLeaveCallback("starting", <<<EOD

		alert("leaving starting");
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
	$machineFactories["addspecialty"]->AddLeaveCallback("zombie", <<<EOD
		
		$(".class-id-addspecialtysheet button.class-id-mainmenu").button().off("click");
		$(".class-id-addspecialtysheet button.class-id-addspecialty").button().off("click");
		var tbl = $("table.class-id-specialtyaddfields");
		$(".class-id-addspecialtysheet").fadeOut('fast',function()
		{
			UnstashElement(tbl,function() {
				var oTable = tbl.dataTable({ bRetrieve: true });
				oTable.fnDestroy();
			});
			SendMessage(".class-id-main",function(sm) {
				sm.run();
			});
		});
EOD
	);
	
	$machineFactories["addspecialty"]->AddLeaveCallback("starting", <<<EOD
	
	
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
			CallServer({
				command:"AddSpecialty",
				parameters:o,
				success:function(data) {
					SendMessage(".class-id-addspecialtysheet",function(sm) {
						sm.success();
					});
				},
				failure:function() {
					// TODO
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
			var sm = $(this).data("statemachine");
			//sm.transition();
		});
		CallServer({
			command:"GetSpecialties",
			parameters:{},
			success: function(data)
			{
				var specialtiesJSON = data;
				$(".class-id-addspecialtysheet table.class-id-specialtiesindb").dataTable({
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
			},
			failure: function()
			{
				SendMessage(".class-id-addpatientsheet",function(sm) {
					sm.error();
				});
			}			
		});
		return true;
	
EOD
	);
	
