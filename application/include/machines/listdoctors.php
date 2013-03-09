<?php

$machineFactories["listdoctors"] = new StateMachine();
$machineFactories["listdoctors"]->AddTransition("run","starting","waiting");
$machineFactories["listdoctors"]->AddTransition("badloading","waiting","badload");
$machineFactories["listdoctors"]->AddTransition("continue","badload","shutdown");
$machineFactories["listdoctors"]->AddTransition("cancel","waiting","shutdown");
$machineFactories["listdoctors"]->AddTransition("select","waiting","selected");
$machineFactories["listdoctors"]->AddTransition("cancel","selected","shutdown");
$machineFactories["listdoctors"]->AddTransition("update","selected","shutdown");
$machineFactories["listdoctors"]->AddTransition("badupdate","selected","displayingupdateerror");
$machineFactories["listdoctors"]->AddTransition("continue","displayingupdateerror","selected");
$machineFactories["listdoctors"]->AddTransition("run","shutdown","waiting");

$machineFactories["listdoctors"]->AddEnterCallback("selected", <<<EOD

	var sheet = $(".class-id-updatedoctorsheet");
	$("button.class-id-update",sheet).button("enable");
		
EOD
);

$machineFactories["listdoctors"]->AddEnterCallback("shutdown", <<<EOD

	$("button.class-id-cancel",sheet).off("click");
	$("button.class-id-update",sheet).off("click");
	var sheet = $(".class-id-updatedoctorsheet");
	var fields = $("table.class-id-doctorfields",sheet);
	var doctorList = $("table.class-id-doctorlist",sheet);
	UnstashTable(fields);
	UnstashTable(doctorList);
	sheet.fadeOut(function() {
		SendMessage(".class-id-main",function(sm) {
			sm.run();
		});
	});		
EOD
	);

$machineFactories["listdoctors"]->AddEnterCallback("badload", <<<EOD
	
	MessageBox("Update Doctor",msg,function() {
		SendMessage(".class-id-updatedoctorsheet",function(sm) {
			sm.continue();
		});
	});

EOD
	);

$machineFactories["listdoctors"]->AddEnterCallback("displayingupdateerror", <<<EOD

	var sheet = $(".class-id-updatedoctorsheet");
	MessageBox("Update Doctor",msg,function() {
		SendMessage(".class-id-updatedoctorsheet",function(sm) {
			sm.continue();
		});
	});

EOD
);

$machineFactories["listdoctors"]->AddBeforeCallback("select", <<<EOD

	var sheet = $(".class-id-updatedoctorsheet");
	var doctorList = $("table.class-id-doctorlist",sheet);
	var oTableDoctorList = doctorList.dataTable({bRetrieve:true});
	var oTableToolsDoctorList;
	doctorList.each(function() { oTableToolsDoctorList = TableTools.fnGetInstance(this); });
	var aData = oTableToolsDoctorList.fnGetSelectedData();
	var fieldList = $("table.class-id-doctorfields",sheet);
	var firstname = aData[0].firstname;
	var lastname = aData[0].lastname;
	var address1 = aData[0].address1;
	var address2 = aData[0].address2;
	var address3 = aData[0].address3;
	var city = aData[0].city
	var contact_person = aData[0].contact_person;
	var country = aData[0].country;
	var email = aData[0].email;
	var fax = aData[0].fax;
	var id = aData[0].id;
	var locality1 = aData[0].locality1;	
	var locality2 = aData[0].locality2;
	var postal_code = aData[0].postal_code;
	var shortname = aData[0].shortname;
	var telephone = aData[0].telephone;
	$("input.class-id-firstname",fieldList).val(firstname);
	$("input.class-id-lastname",fieldList).val(lastname);
	$("input.class-id-address1",fieldList).val(address1);
	$("input.class-id-address2",fieldList).val(address2);
	$("input.class-id-address3",fieldList).val(address3);
	$("input.class-id-city",fieldList).val(city);
	$("input.class-id-contactperson",fieldList).val(contact_person);
	$("input.class-id-country",fieldList).val(country);
	$("input.class-id-email",fieldList).val(email);
	$("input.class-id-fax",fieldList).val(fax);
	$("input.class-id-id",fieldList).val(id);
	$("input.class-id-locality1",fieldList).val(locality1);	
	$("input.class-id-locality2",fieldList).val(locality2);	
	$("input.class-id-postalcode",fieldList).val(postal_code);	
	$("input.class-id-shortname",fieldList).val(shortname);	
	$("input.class-id-telephone",fieldList).val(telephone);	
		
EOD
		);


$machineFactories["listdoctors"]->AddEnterCallback("waiting", <<<EOD

		
	function PopulateDoctors(doctorsJSON,sheet)
	{
		tbl = $("table.class-id-doctorlist",sheet);
		StashElement(tbl);
		tbl.dataTable({
			bJQueryUI : true,
			"sDom": 'T<"clear">lfrtip',
			"oTableTools": {
				"sRowSelect": "single",
				"aButtons" : [],
				"fnRowSelected" : function(nodes) {
					SendMessage(".class-id-updatedoctorsheet",function(sm)
					{
						sm.select();
					}); 
				}
			},
			"aoColumnDefs":
			[
				{ "sTitle": "First Name", "aTargets": [ 0 ], "mData": "firstname" },
				{ "sTitle": "Last Name", "aTargets": [ 1 ], "mData": "lastname" },
				{ "sTitle": "Address1", "aTargets": [ 2 ], "mData": "address1", bVisible:false },
				{ "sTitle": "Address2", "aTargets": [ 3 ], "mData": "address2", bVisible:false },
				{ "sTitle": "Address3", "aTargets": [ 4 ], "mData": "address3", bVisible:false },
				{ "sTitle": "City", "aTargets": [ 5 ], "mData": "city", bVisible:false },
				{ "sTitle": "Contact Person", "aTargets": [ 6 ], "mData": "contact_person", bVisible:false },
				{ "sTitle": "Country", "aTargets": [ 7 ], "mData": "country", bVisible:false },
				{ "sTitle": "Email", "aTargets": [ 8 ], "mData": "email", bVisible:false },
				{ "sTitle": "Fax", "aTargets": [ 9 ], "mData": "fax", bVisible:false },
				{ "sTitle": "Id", "aTargets": [ 10 ], "mData": "id", bVisible:false },
				{ "sTitle": "Locality 1", "aTargets": [ 11 ], "mData": "locality1", bVisible:false },
				{ "sTitle": "Locality 2", "aTargets": [ 12 ], "mData": "locality2", bVisible:false },
				{ "sTitle": "Postal Code", "aTargets": [ 13 ], "mData": "postal_code", bVisible:false },
				{ "sTitle": "Short Name", "aTargets": [ 14 ], "mData": "shortname", bVisible:false },
				{ "sTitle": "Telephone", "aTargets": [ 15 ], "mData": "telephone", bVisible:false }
			],
			"aaData" : doctorsJSON.doctors
		});
	} // PopulateDoctors
		
	function SetupFieldsTable(sheet)
	{
		var tbl = $("table.class-id-doctorfields",sheet);
		$("input.class-id-firstname",tbl).attr("readonly","true");
		$("input.class-id-lastname",tbl).attr("readonly","true");
		$("input.class-id-shortname",tbl).attr("readonly","true");
		$("input.class-id-id",tbl).attr("readonly","true");

		StashElement(tbl);
		tbl.dataTable({ "sDom":"t","bJQueryUI": true,"bPageinate":false, iDisplayLength:20,bSort: false});
	}
		
	function HandleLoadFailure()
	{
		var message;
		if(arguments.length == 0)
		{
			message = "Error communicating with web server";
		}
		else
		{
			var result = arguments[0];
			message = result.reason;
		}
		SendMessage(".class-id-updatedoctorsheet",function(sm) {
			sm.badloading(message);
		});
	}
		
	$("button.class-id-cancel",sheet).button().on("click",function() {
		SendMessage(".class-id-updatedoctorsheet",function(sm) {
			sm.cancel();
		});
	});
		
	var sheet = $(".class-id-updatedoctorsheet");
	$("button.class-id-update",sheet).button().button("disable").on("click",function() {
		var sheet = $(".class-id-updatedoctorsheet");
		var fieldList = $("table.class-id-doctorfields",sheet);
		var o =
		{
			firstname : $("input.class-id-firstname",fieldList).val(),
			lastname : $("input.class-id-lastname",fieldList).val(),
			address1 : $("input.class-id-address1",fieldList).val(),
			address2 : $("input.class-id-address2",fieldList).val(),
			address3 : $("input.class-id-address3",fieldList).val(),
			city : $("input.class-id-city",fieldList).val(),
			contactperson : $("input.class-id-contactperson",fieldList).val(),
			country : $("input.class-id-country",fieldList).val(),
			email : $("input.class-id-email",fieldList).val(),
			fax : $("input.class-id-fax",fieldList).val(),
			id : $("input.class-id-id",fieldList).val(),
			locality1 : $("input.class-id-locality1",fieldList).val(),	
			locality2 : $("input.class-id-locality2",fieldList).val(),	
			postalcode : $("input.class-id-postalcode",fieldList).val(),	
			shortname : $("input.class-id-shortname",fieldList).val(),	
			telephone : $("input.class-id-telephone",fieldList).val()	
		};
		CallServer({
			command:"UpdateDoctor",
			parameters:o,
			success: function(data)
			{
				var result = data;
				if(result.status == "ok")
				{
					SendMessage(".class-id-updatedoctorsheet",function(sm) {
						sm.update();
					});
				}
				else
				{
					SendMessage(".class-id-updatedoctorsheet",function(sm) {
						sm.badupdate(result.reason);
					});
				}
			},
			failure: function()
			{
				SendMessage(".class-id-updatedoctorsheet",function(sm) {
					sm.badupdate("Error contacting webserver");
				});
			}
		});
	});
		
	var sheet = $(".class-id-updatedoctorsheet");
	sheet.fadeIn();
	CallServer({
		command:"GetDoctors",
		parameters:{},
		success : function(data) {
			var doctorsJSON = data;
			if(doctorsJSON.status == "ok")
			{
		    	PopulateDoctors(doctorsJSON,sheet);
				SetupFieldsTable(sheet);
			}
			else
			{
				HandleLoadFailure(doctorsJSON);
			}
		},
		failure: function() {
			HandleLoadFailure();
		}
	});
	
		
EOD
		);
