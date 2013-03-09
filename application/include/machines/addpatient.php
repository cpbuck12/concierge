<?php

	
	$machineFactories["addpatient"] = new StateMachine();
	$machineFactories["addpatient"]->AddTransition("run","starting","running");
	$machineFactories["addpatient"]->AddTransition("loaddb","running","dbloaded");
	$machineFactories["addpatient"]->AddTransition("error","running","dberror");
	$machineFactories["addpatient"]->AddTransition("continue","dberror","starting");
	
	$machineFactories["addpatient"]->AddTransition("loadfiles","dbloaded","waiting");
	$machineFactories["addpatient"]->AddTransition("error","dbloaded","fileerror");
	$machineFactories["addpatient"]->AddTransition("continue","fileerror","starting");
	
	$machineFactories["addpatient"]->AddTransition("error","waiting","adderror");
	$machineFactories["addpatient"]->AddTransition("continue","adderror","starting");
	$machineFactories["addpatient"]->AddTransition("addpatient","waiting","addsuccess");
	$machineFactories["addpatient"]->AddTransition("continue","addsuccess","waiting");
	$machineFactories["addpatient"]->AddTransition("cancel","waiting","starting");

	$machineFactories["addpatient"]->AddEnterCallback("addsuccess", <<<EOD
	
		SendMessage(".class-id-addpatientsheet",function(sm) {
			sm.cancel();
		});
EOD
	);
	$machineFactories["addpatient"]->AddEnterCallback("dbloaded", <<<EOD

		$.ajax({
			type:"POST",
			url:"http://localhost:50505/ajax/GetPeopleOnDisk",
			data:"{}",
			dataType:"text",
			success: function(data)
			{
				var patientsOnDiskJSON = JSON.parse(data);
				$(".class-id-addpatientsheet table.class-id-patientsondisk").dataTable({
					bJQueryUI : true,
					"sDom": 'T<"clear">lfrtip',
					"oTableTools": {
						"sRowSelect": "single",
						"aButtons" : [],
						fnRowSelected : OnPatientAddRowClick
		        	},
					"aoColumnDefs":
					[
						{ "sTitle": "First Name", "aTargets": [ 0 ], "mData": "FirstName" },
						{ "sTitle": "Last Name", "aTargets": [ 1 ], "mData": "LastName" },
					],
					"aaData" : patientsOnDiskJSON.people
				});
				SendMessage(".class-id-addpatientsheet",function(sm) {
					sm.loadfiles();
				});
			},
			error: function()
			{
				SendMessage(".class-id-addpatientsheet",function(sm) {
					sm.error();
				});
			}
		});
EOD
	);
	$machineFactories["addpatient"]->AddEnterCallback("starting", <<<EOD
			
		$(".class-id-addpatientsheet button.class-id-mainmenu").button().off("click");
		$(".class-id-addpatientsheet button.class-id-addpatient").button().off("click").button("disable");
		$(".class-id-newpatientfields input").off("change keyup input");
		$(".class-id-newpatientfields select").off("change keyup input");
		$(".class-id-addpatientsheet input.class-id-dob").datepicker("destroy");
		//$("table.class-id-patientsondisk").off("click",OnPatientAddRowClick);
			
		$(".class-id-addpatientsheet").fadeOut('fast',function() {
			
			SendMessage(".class-id-main",function(sm) {
				sm.run();
			});
		});
EOD
	);
	$machineFactories["addpatient"]->AddLeaveCallback("starting", <<<EOD

		function UpdateAddPatientButton()
		{
			function FieldEmpty(name)
			{
				var s = $(".class-id-newpatientfields " + name).val();
				s = $.trim(s);
				if(s == "")
					return true;
				else
					return false;
			}
			function SetButton(isOn)
			{
				$("button.class-id-addpatient").button(isOn ? "enable" : "disable");
			}
			if(FieldEmpty(".class-id-firstname"))
			{
				SetButton(false);
				return;
			}
			if(FieldEmpty(".class-id-lastname"))
			{
				SetButton(false);
				return;
			}
			if(FieldEmpty(".class-id-dob"))
			{
				SetButton(false);
				return;
			}
			if(FieldEmpty(".class-id-emergencycontact"))
			{
				SetButton(false);
				return;
			}
			SetButton(true);
		}						
		$(".class-id-addpatientsheet button.class-id-mainmenu").button().on("click",CancelAddPatient);
		$(".class-id-addpatientsheet button.class-id-addpatient").button().on("click",DoAddPatient);
		$(".class-id-newpatientfields input").on("change keyup input",UpdateAddPatientButton);
		$(".class-id-newpatientfields select").on("change keyup input",UpdateAddPatientButton);
			$(".class-id-newpatientfields select").on("change",UpdateAddPatientButton);
			$(".class-id-addpatientsheet input.class-id-dob").datepicker({
			changeYear:true,
			changeMonth: true,
			maxDate : "-1d",
			minDate : "-100y",
			yearRange : "-99:-15"
		});
		$(".class-id-addpatientsheet").fadeIn('fast');
			
EOD
	);
	$machineFactories["addpatient"]->AddEnterCallback("running", <<<EOD
		$.ajax({
			type:"POST",
			url:"http://localhost:50505/ajax/GetPeopleInDb",
			data:"{}",
			dataType:"text",
			success: function(data)
			{
				var patientsInDbJSON = JSON.parse(data);
				$(".class-id-addpatientsheet table.class-id-patientsindb").dataTable({
					bJQueryUI : true,
					"sDom": 'T<"clear">lfrtip',
					"oTableTools": {
						"sRowSelect": "single",
						"aButtons" : []
		        	},
					"aoColumnDefs":
					[
						{ "sTitle": "First Name", "aTargets": [ 0 ], "mData": "first" },
						{ "sTitle": "Last Name", "aTargets": [ 1 ], "mData": "last" },
					],
					"aaData" : patientsInDbJSON.patients
				});
				SendMessage(".class-id-addpatientsheet",function(sm) {
					sm.loaddb();
				});
			},
			error: function()
			{
				SendMessage(".class-id-addpatientsheet",function(sm) {
					sm.error();
				});
			}
		});

EOD
	);
	$machineFactories["addpatient"]->AddEnterCallback("dberror", <<<EOD
		
		
		$.getJSON("http://localhost:50505/ajax/GetPeopleOnDisk", function (patientsOnDiskJSON) {
			$(".class-id-addpatientsheet table.class-id-patientsondisk").dataTable({
				bJQueryUI : true,
				"sDom": 'T<"clear">lfrtip',
				"oTableTools": {
					"sRowSelect": "single",
					"aButtons" : [],
					"fnRowSelected" : function() { alert("OnPatientAddRowClick"); }
	        	},
				"aoColumnDefs":
				[
					{ "sTitle": "First Name", "aTargets": [ 0 ], "mData": "FirstName" },
					{ "sTitle": "Last Name", "aTargets": [ 1 ], "mData": "LastName" },
				],
				"aaData" : patientsOnDiskJSON
			});
			$(".class-id-addpatientsheet table.class-id-fields").dataTable({
				bJQueryUI : true,
				"bSort": false,
				"sDom": 't',
				iDisplayLength : 15
			});
		});
		$.getJSON("http://localhost:50505/ajax/GetPeopleInDb", function (patientsInDbJSON) {
			$(".class-id-addpatientsheet table.class-id-patientsindb").dataTable({
				bJQueryUI : true,
				"sDom": '<"clear">lfrtip',
				"oTableTools": {
					"aButtons" : []
	        	},
				"aoColumnDefs":
				[
					{ "sTitle": "First Name", "aTargets": [ 0 ], "mData": "FirstName" },
					{ "sTitle": "Last Name", "aTargets": [ 1 ], "mData": "LastName" },
				],
				"aaData" : patientsInDbJSON
			}); 
		});   
		$(".class-id-patientsondisk td").on("click",OnPatientAddRowClick);
		$(".class-id-addpatientsheet button.class-id-mainmenu").button().on("click",CancelAddPatient);
		$(".class-id-addpatientsheet button.class-id-addpatient").button().on("click",DoAddPatient);
		$(".class-id-addpatientsheet input.class-id-dob").datepicker({
			changeYear:true,
			changeMonth: true,
			maxDate : "-1d",
			minDate : "-100y",
			yearRange : "-99:-15",
			numberOfMonths : [1,3],
			stepMonths : 3
		});
		$(".class-id-addpatientsheet").fadeIn('fast',function() {
			SendMessage($(this),function(sm) {
				sm.transition();
			});
		});
		return true;
	
EOD
	);

