<?php

	
	$machineFactories["addpatient"] = new StateMachine();
	$machineFactories["addpatient"]->AddTransition("run","starting","running");
	$machineFactories["addpatient"]->AddTransition("run","shutdown","running");
	$machineFactories["addpatient"]->AddTransition("loaddb","running","dbloaded");
	$machineFactories["addpatient"]->AddTransition("loaddberror","running","dberror");
	$machineFactories["addpatient"]->AddTransition("continue","dberror","shutdown");
	
	$machineFactories["addpatient"]->AddTransition("loadfiles","dbloaded","waiting");
	$machineFactories["addpatient"]->AddTransition("loadfileserror","dbloaded","fileerror");
	$machineFactories["addpatient"]->AddTransition("continue","fileerror","shutdown");
	
	$machineFactories["addpatient"]->AddTransition("addingerror","waiting","adderror");
	$machineFactories["addpatient"]->AddTransition("continue","adderror","shutdown");
	$machineFactories["addpatient"]->AddTransition("addpatient","waiting","addsuccess");
	$machineFactories["addpatient"]->AddTransition("continue","addsuccess","shutdown");
	$machineFactories["addpatient"]->AddTransition("cancel","waiting","shutdown");

	$machineFactories["addpatient"]->AddEnterCallback("addsuccess", <<<EOD
	
		alert("addsuccess");
		SendMessage(".class-id-addpatientsheet",function(sm) {
			sm.continue();
		});
EOD
	);

	$machineFactories["addpatient"]->AddLeaveCallback("waiting", <<<EOD

		debugger;
		var patientsindbelement = $(".class-id-addpatientsheet table.class-id-patientsindb");
		UnstashTable(patientsindbelement);
		var patientsondiskelement = $(".class-id-addpatientsheet table.class-id-patientsondisk");
		UnstashTable(patientsondiskelement);
		return true;

EOD
		);
	
	$machineFactories["addpatient"]->AddEnterCallback("fileerror", <<<EOD

		MessageBox("Error",msg,function() {
			SendMessage(".class-id-addpatientsheet",function(sm) {
				sm.continue();
			});
		});
		var patientsindbelement = $(".class-id-addpatientsheet table.class-id-patientsindb");
		var oTable = patientsindbelement.dataTable({ bRetrieve : true });
		oTable.fnDestroy();
		UnstashTable(patientsindbelement);
		
EOD
		);
	
	$machineFactories["addpatient"]->AddEnterCallback("dbloaded", <<<EOD

		function Failure(msg)
		{
			SendMessage(".class-id-addpatientsheet",function(sm) {
				sm.loadfileserror(msg);
			});
		}
			
		CallServer({
			command: "GetPeopleOnDisk",
			parameters: {},
			success: function(data)
			{
				if(data.status == "ok")
				{
					debugger;
					var patientsOnDiskJSON = data;
					var element = $(".class-id-addpatientsheet table.class-id-patientsondisk"); 
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
							{ "sTitle": "First Name", "aTargets": [ 0 ], "mData": "FirstName" },
							{ "sTitle": "Last Name", "aTargets": [ 1 ], "mData": "LastName" },
						],
						"aaData" : patientsOnDiskJSON.people
					});
					SendMessage(".class-id-addpatientsheet",function(sm) {
						sm.loadfiles();
					});
				}
				else
				{
					Failure("Could not get previously registered patients from the database.  Reason:"+data.reason);
				}
			},
			failure: function()
			{
				Failure("Error calling the server while trying to get previously registered patients from the database.");
			}
		});

EOD
	);
	$machineFactories["addpatient"]->AddEnterCallback("shutdown", <<<EOD

		$(".class-id-newpatientfields input").off("change keyup input");
		$(".class-id-newpatientfields select").off("change keyup input");

		/* BEGIN clean up the field set */
		$(".class-id-addpatientsheet input.class-id-dob").datepicker("destroy");
		var newpatientselement = $(".class-id-addpatientsheet table.class-id-newpatientfields");
		UnstashTable(newpatientselement);
		/* END clean up the field set */
		$(".class-id-addpatientsheet button.class-id-mainmenu").button().off("click").button("destroy");
		$(".class-id-addpatientsheet button.class-id-addpatient").button().off("click").button("destroy");

						
		$(".class-id-addpatientsheet").fadeOut('fast',function() {
			
			SendMessage(".class-id-main",function(sm) {
				sm.run();
			});
		});
EOD
	);
	$machineFactories["addpatient"]->AddEnterCallback("running", <<<EOD


		function DoAddPatient()
		{
			function Failure(msg)
			{
				SendMessage(".class-id-addpatientsheet",function(sm){
					sm.addingerror();
				});
			}
			
			o = {
				firstName : $(".class-id-addpatientsheet input.class-id-firstname").val(),
				lastName : $(".class-id-addpatientsheet input.class-id-lastname").val(),
				dateOfBirth : $(".class-id-addpatientsheet input.class-id-dob").val(),
				gender : $(".class-id-addpatientsheet select.class-id-gender").val(),
				emergencyContact : $(".class-id-addpatientsheet input.class-id-emergencycontact").val()
			};
			CallServer({
				command:"AddPatient",
				parameters:o,
				success: function(data)
				{
			debugger;
					if(data.status == "ok")
					{
						SendMessage(".class-id-addpatientsheet",function(sm){
							sm.addpatient();
						});
					}
					else
					{
						Failure("Adding patient failed, reason:"+data.reason);
					}
				},
				failure: function()
				{
			debugger;
					Failure("Error calling server while adding patient");
				}
			});
		}
			
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
		$(".class-id-addpatientsheet button.class-id-addpatient").button().on("click",DoAddPatient).button("disable");
		$(".class-id-newpatientfields input").on("change keyup input",UpdateAddPatientButton);
		$(".class-id-newpatientfields select").on("change keyup input",UpdateAddPatientButton);
		$(".class-id-newpatientfields select").on("change",UpdateAddPatientButton);
			
		var newpatientselement = $(".class-id-addpatientsheet table.class-id-newpatientfields");
		StashElement(newpatientselement);

		$(".class-id-addpatientsheet input.class-id-dob").val("01/01/2012").datepicker({
			changeYear:true,
			changeMonth: true,
			maxDate : new Date(),
			minDate : new Date("January 1,1900"),
			yearRange : "-99:-15"
		});
	
		newpatientselement.dataTable({
			bJQueryUI : true,
			"bSort": false,
			"sDom": 't',
			iDisplayLength : 15
		});
		$(".class-id-addpatientsheet").fadeIn('fast');

		function Failure(msg)
		{
			SendMessage(".class-id-addpatientsheet",function(sm) {
				sm.loaddberror(msg);
			});
		}

		CallServer({
			command:"GetPeopleInDb",
			paramters:{},
			success: function(data)
			{
				if(data.status == "ok")
				{
					var patientsInDbJSON = data;
					var patientsindbelement = $(".class-id-addpatientsheet table.class-id-patientsindb");
					StashElement(patientsindbelement);
					patientsindbelement.dataTable({
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
				}
				else
				{
					Failure(data.reason);
				}
			},
			failure: function()
			{
				Failure("Error calling server");
			}
		});
			
EOD
	);

		$machineFactories["addpatient"]->AddEnterCallback("dberror", <<<EOD
		
			
		MessageBox("Error",msg,function() {
			SendMessage(".class-id-addpatientsheet",function(sm) {
				sm.continue();
			});
		});
		return true;

EOD
	);

