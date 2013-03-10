<?php

$machineFactories["adddoctor"] = new StateMachine();
$machineFactories["adddoctor"]->AddTransition("run","starting","waiting");
$machineFactories["adddoctor"]->AddTransition("error_","waiting","displayingerror");
$machineFactories["adddoctor"]->AddTransition("continue","displayingerror","waiting");
$machineFactories["adddoctor"]->AddTransition("cancel","waiting","shutdown");
$machineFactories["adddoctor"]->AddTransition("success","waiting","displayingsuccess");
$machineFactories["adddoctor"]->AddTransition("continue","displayingsuccess","shutdown");
$machineFactories["adddoctor"]->AddTransition("run","shutdown","waiting");

$machineFactories["adddoctor"]->AddEnterCallback("displayingsuccess", <<<EOD
		
		MessageBox("Add Doctor","Doctor successfully added",function() {
			SendMessage(".class-id-adddoctorsheet",function(sm) {
				sm.continue();
			});
		});
EOD
);


$machineFactories["adddoctor"]->AddEnterCallback("displayingerror", <<<EOD

		MessageBox("Add Doctor",msg,function() {
			SendMessage(".class-id-adddoctorsheet",function(sm) {
				sm.continue();
			});
		});

EOD
		);

$machineFactories["adddoctor"]->AddEnterCallback("shutdown", <<<EOD

		$(".class-id-adddoctorsheet button.class-id-mainmenu").button().off("click");
		$(".class-id-adddoctorsheet button.class-id-adddoctor").button().off("click");
		var tbl = $("table.class-id-doctoraddfields");
		$(".class-id-adddoctorsheet").fadeOut('fast',function()
		{
			UnstashTable(tbl);
			var smMain = GetStateMachine(".class-id-main");
			var q = GetMessageQueue();
			q.messagepump('send',function()
			{
				smMain.run();
			});
		});

EOD
);

$machineFactories["adddoctor"]->AddLeaveCallback("shutdown", <<<EOD

		this.init();/*
		$(".class-id-adddoctorsheet button.class-id-mainmenu").button().on("click",CancelAddDoctor);
		$(".class-id-adddoctorsheet button.class-id-adddoctor").button().on("click",DoAddDoctor);
		var tbl = $("table.class-id-doctoraddfields");
		StashElement(tbl);
		tbl.dataTable({ "sDom":"t","bJQueryUI": true,"bPageinate":false,
				iDisplayLength : 15, bSort : false});*/
		$(".class-id-adddoctorsheet").fadeIn();
		return true;

EOD
);


$machineFactories["adddoctor"]->AddLeaveCallback("starting", <<<EOD


		function DoAddDoctor()
		{
			var vars = [];
			var o = {};
			var sheet = $(".class-id-adddoctorsheet");
			var acc = "";
			$(".class-fieldset input",sheet).each(function()
			{
				var _this = $(this);
				var classes = _this.attr("class").split(' ');
				for(var i = 0;i < classes.length;i++)
				{
					if(classes[i].substr(0,9) == "class-id-")
					{
						var id = classes[i].substr(9);
						o[id] = _this.val();
		//				acc = acc + id + "\\n";
		//				acc = acc + _this.val() + "\\n";
						break;
					}
				}
			});
			CallServer({
				command:"AddDoctor",
				parameters:o,
				success: function(data)
				{
					if(data.status == "ok")
					{	
						SendMessage(".class-id-adddoctorsheet",function(sm) {
							sm.success();
						})
					}
					else // status == "error"
					{
						SendMessage(".class-id-adddoctorsheet",function(sm) {
							sm.error_("Error adding doctor.  Reason:"+data.reason);
						});
					}
				},
				failure: function()
				{
					SendMessage(".class-id-adddoctorsheet",function(sm) {
						sm.error_("Error adding doctor");
					});
				}
				
			});
		}

		
		
		this.init = function() {
			$(".class-id-adddoctorsheet button.class-id-mainmenu").button().on("click",function() {
				SendMessage(".class-id-adddoctorsheet",function(sm) {
					sm.cancel();
				});
			});
			$(".class-id-adddoctorsheet button.class-id-adddoctor").button().on("click",DoAddDoctor);
			var tbl = $("table.class-id-doctoraddfields");
			StashElement(tbl);
			tbl.dataTable({ "sDom":"t","bJQueryUI": true,"bPageinate":false,
					iDisplayLength : 15, bSort : false});
		}
		this.init();
		var tbl = $("table.class-id-doctoraddfields");
		$("input.class-id-country",tbl).val("USA");
		$("input.class-id-city",tbl).val("New York");
		$("input.class-id-locality1",tbl).val("NY");
		$(".class-id-adddoctorsheet").fadeIn();		
		return true;

EOD
);

