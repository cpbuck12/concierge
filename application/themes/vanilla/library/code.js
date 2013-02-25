var Base64 = {
// private property
_keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

// public method for encoding
encode : function (input) {
    var output = "";
    var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
    var i = 0;

    input = Base64._utf8_encode(input);

    while (i < input.length) {

        chr1 = input.charCodeAt(i++);
        chr2 = input.charCodeAt(i++);
        chr3 = input.charCodeAt(i++);

        enc1 = chr1 >> 2;
        enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
        enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
        enc4 = chr3 & 63;

        if (isNaN(chr2)) {
            enc3 = enc4 = 64;
        } else if (isNaN(chr3)) {
            enc4 = 64;
        }

        output = output +
        Base64._keyStr.charAt(enc1) + Base64._keyStr.charAt(enc2) +
        Base64._keyStr.charAt(enc3) + Base64._keyStr.charAt(enc4);

    }

    return output;
},

// public method for decoding
decode : function (input) {
    var output = "";
    var chr1, chr2, chr3;
    var enc1, enc2, enc3, enc4;
    var i = 0;

    input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

    while (i < input.length) {

        enc1 = Base64._keyStr.indexOf(input.charAt(i++));
        enc2 = Base64._keyStr.indexOf(input.charAt(i++));
        enc3 = Base64._keyStr.indexOf(input.charAt(i++));
        enc4 = Base64._keyStr.indexOf(input.charAt(i++));

        chr1 = (enc1 << 2) | (enc2 >> 4);
        chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
        chr3 = ((enc3 & 3) << 6) | enc4;

        output = output + String.fromCharCode(chr1);

        if (enc3 != 64) {
            output = output + String.fromCharCode(chr2);
        }
        if (enc4 != 64) {
            output = output + String.fromCharCode(chr3);
        }

    }

    output = Base64._utf8_decode(output);

    return output;

},

// private method for UTF-8 encoding
_utf8_encode : function (string) {
    string = string.replace(/\r\n/g,"\n");
    var utftext = "";

    for (var n = 0; n < string.length; n++) {

        var c = string.charCodeAt(n);

        if (c < 128) {
            utftext += String.fromCharCode(c);
        }
        else if((c > 127) && (c < 2048)) {
            utftext += String.fromCharCode((c >> 6) | 192);
            utftext += String.fromCharCode((c & 63) | 128);
        }
        else {
            utftext += String.fromCharCode((c >> 12) | 224);
            utftext += String.fromCharCode(((c >> 6) & 63) | 128);
            utftext += String.fromCharCode((c & 63) | 128);
        }

    }

    return utftext;
},

// private method for UTF-8 decoding
_utf8_decode : function (utftext) {
    var string = "";
    var i = 0;
    var c = c1 = c2 = 0;

    while ( i < utftext.length ) {

        c = utftext.charCodeAt(i);

        if (c < 128) {
            string += String.fromCharCode(c);
            i++;
        }
        else if((c > 191) && (c < 224)) {
            c2 = utftext.charCodeAt(i+1);
            string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
            i += 2;
        }
        else {
            c2 = utftext.charCodeAt(i+1);
            c3 = utftext.charCodeAt(i+2);
            string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
            i += 3;
        }

    }
    return string;
}
}

function GetMessageQueue()
{
	return jQuery("#hiddenmessagequeueelement");
}

function GetStateMachine(selector)
{
	var e = jQuery(selector);
	return e.data("statemachine");
}

function SetStateMachine(selector,machine)
{
	jQuery(selector).data("statemachine",machine);
}

function DepopulateFiles()
{
	var elem = $("table.class-id-filesystem");
	elem.hide();
	UnstashElement(elem,function() {
		var oTable = elem.dataTable({ bRetrieve: true });
		oTable.fnDestroy(true);
	});
}

function OnFileSelectionChanged()
{
	var q = GetMessageQueue();
	var sm = GetStateMachine(".class-id-loadfromconciergesheet");
	q.messagepump("send",function() {
		sm.filechange();
	});
}

function OutterHtml(elem)
{
	return $(elem).clone().wrap("<p>").parent().html();
}

function StashElement(elem)
{
	var origHtml = OutterHtml(elem);
	origHtml = Base64.encode(origHtml);
	elem.before("<div class='class-stash'>"+origHtml+"</div>");
}

function UnstashElement(elem,remover)
{
	var prev_elem = $(elem).prev();
	var divText = Base64.decode(prev_elem.text());
	remover();
	prev_elem.after(divText);
	prev_elem.remove();
}

function PopulateFiles(after)
{
	StashElement($("table.class-id-filesystem"));
	var row = $("table.class-id-patient tr.DTTT_selected td");
	var firstName = $(row[0]).text();
	var lastName = $(row[1]).text();

	var acc = JSON.stringify({
		"firstname" : firstName,
		"lastname" : lastName
	});
	$.ajax({
		type:"POST",
		url:"http://localhost:50505/ajax/GetFilesOnDisk",
		data:acc,
		dataType:"text",
		success: function(data)
		{
			var filesOnDiskJSON = JSON.parse(data);
			var elem = $("table.class-id-filesystem");
			elem.show();
			var oTable = elem.dataTable({
				"aoColumnDefs":
					[
						{ "sTitle": "Hash", bVisible: false, "aTargets": [ 0 ], "mData": "Hash" },
						{ "sTitle": "First Name", bVisible:false, "aTargets": [ 1 ], "mData": "FirstName" },
						{ "sTitle": "Last Name", bVisible:false, "aTargets": [ 2 ], "mData": "LastName" },
						{ "sTitle": "Specialty", "aTargets": [ 3 ], "mData": "Specialty" },
						{ "sTitle": "Subspecialty", "aTargets": [ 4 ], "mData": "Subspecialty" },
						{ "sTitle": "Filename", "aTargets": [ 5 ], "mData": "FileName" },
						{ "sTitle": "Path",bVisible:false, "aTargets": [ 6 ], "mData": "FullName" }
					],
				aaData : filesOnDiskJSON.files,
				bJQueryUI: true,
				"sDom": 'T<"clear">lfrtip',
				oTableTools : 
				{
					sRowSelect : "multi",
					fnRowSelected : OnFileSelectionChanged,
					fnRowDeselected : OnFileSelectionChanged
				}
			});
			if(typeof after === "function")
				after();
		},
		error: function()
		{
			alert("ajax failure, danger will robinson!")
		}
	});
/*	
	$.getJSON("http://localhost:50505/ajax/GetFilesOnDisk?FirstName="+firstName+"&LastName="+lastName, function (filesOnDiskJSON) {
		var elem = $("table.class-id-filesystem");
		elem.show();
		var oTable = elem.dataTable({
			"aoColumnDefs":
				[
					{ "sTitle": "Hash", bVisible: false, "aTargets": [ 0 ], "mData": "Hash" },
					{ "sTitle": "First Name", bVisible:false, "aTargets": [ 1 ], "mData": "FirstName" },
					{ "sTitle": "Last Name", bVisible:false, "aTargets": [ 2 ], "mData": "LastName" },
					{ "sTitle": "Specialty", "aTargets": [ 3 ], "mData": "Specialty" },
					{ "sTitle": "Subspecialty", "aTargets": [ 4 ], "mData": "Subspecialty" },
					{ "sTitle": "Filename", "aTargets": [ 5 ], "mData": "FileName" },
					{ "sTitle": "Path",bVisible:false, "aTargets": [ 6 ], "mData": "FullName" }
				],
			aaData : filesOnDiskJSON,
			bJQueryUI: true,
			"sDom": 'T<"clear">lfrtip',
			oTableTools : 
			{
				sRowSelect : "multi",
				fnRowSelected : OnFileSelectionChanged,
				fnRowDeselected : OnFileSelectionChanged
			}
		});
		if(typeof after === "function")
			after();
	});
	*/
}

function OnPatientAddRowClick()
{
	var row = $(".class-id-addpatientsheet table.class-id-patientsondisk tr.DTTT_selected td");
	var firstName = $(row[0]).text();
	var lastName = $(row[1]).text();
	$(".class-id-addpatientsheet input.class-id-firstname").val(firstName).trigger("change");
	$(".class-id-addpatientsheet input.class-id-lastname").val(lastName).trigger("change");
}

function OnPatientRowClick()
{
	var q = GetMessageQueue();
	var sm = GetStateMachine(".class-id-loadfromconciergesheet");
	q.messagepump("send",function() {
		if($("table.class-id-patient tr.DTTT_selected").length > 0)
		{
			sm.selectpatient();
		}
		else
		{
			sm.unselectpatient();
		}
	});
}


function CancelAddDoctor()
{
	var q = GetMessageQueue();
	var sm = GetStateMachine(".class-id-adddoctorsheet");
	q.messagepump("send",function() {
		sm.cancel();
	});
}

function DoAddDoctor()
{
	var vars = [];
	var o = {};
	var sheet = $(".class-id-adddoctorsheet");
	var acc = "";
	$(".class-fieldset input",sheet).each(function()
	{
		$this = $(this);
		var classes = $this.attr("class").split(' ');
		for(var i = 0;i < classes.length;i++)
		{
			if(classes[i].substr(0,9) == "class-id-")
			{
				var id = classes[i].substr(9);
				o[id] = $this.val();
//				acc = acc + id + "\n";
//				acc = acc + $this.val() + "\n";
				break;
			}
		}
	});
	acc = JSON.stringify(o);
	$.ajax({
		type:"POST",
		url:"http://localhost:50505/ajax/AddDoctor",
		data:acc,
		dataType:"text",
		success: function(data)
		{
//			var o = JSON.parse(data);
			var q = GetMessageQueue();
			var sm = GetStateMachine(".class-id-adddoctorsheet");
			q.messagepump("send",function() {
				sm.success();
			});
		},
		error: function()
		{
			alert("ajax failure, danger will robinson!")
		}
	});
}

function DoAddPatient()
{
	o = {
		firstName : $(".class-id-addpatientsheet input.class-id-firstname").val(),
		lastName : $(".class-id-addpatientsheet input.class-id-lastname").val(),
		dateOfBirth : $(".class-id-addpatientsheet input.class-id-dob").val(),
		gender : $(".class-id-addpatientsheet select.class-id-gender").val(),
		emergencyContact : $(".class-id-addpatientsheet input.class-id-emergencycontact").val()
	};
	acc = JSON.stringify(o);
	//acc = firstName + "\n" + lastName + "\n";
	$.ajax({
		type:"POST",
		url:"http://localhost:50505/ajax/AddPatient",
		data:acc,
		dataType:"text",
		success: function(data)
		{
			var q = GetMessageQueue();
			var sm = GetStateMachine(".class-id-addpatientsheet");
			q.messagepump("send",function() {
				sm.addpatient();
			});
		},
		error: function()
		{
			var q = GetMessageQueue();
			var sm = GetStateMachine(".class-id-addpatientsheet");
			q.messagepump("send",function() {
				sm.error();
			});
		}
	});
}

function CancelAddPatient()
{
	var q = GetMessageQueue();
	var sm = GetStateMachine(".class-id-addpatientsheet");
	q.messagepump("send",function() {
		sm.cancel();
	});
}

function CancelFileLoading()
{
	var q = GetMessageQueue();
	var sm = GetStateMachine(".class-id-loadfromconciergesheet");
	q.messagepump("send",function() {
		sm.cancel();
	});
}

function DoFileLoading()
{/*
	{ "sTitle": "Hash", bVisible: false, "aTargets": [ 0 ], "mData": "Hash" },
	{ "sTitle": "First Name", bVisible:false, "aTargets": [ 1 ], "mData": "FirstName" },
	{ "sTitle": "Last Name", bVisible:false, "aTargets": [ 2 ], "mData": "LastName" },
	{ "sTitle": "Specialty", "aTargets": [ 3 ], "mData": "Specialty" },
	{ "sTitle": "Subspecialty", "aTargets": [ 4 ], "mData": "Subspecialty" },
	{ "sTitle": "Filename", "aTargets": [ 5 ], "mData": "FileName" },
	{ "sTitle": "Path",bVisible:false, "aTargets": [ 6 ], "mData": "FullName" }
*/
	oData = {
        activities : []
 	};
	var elem = $("table.class-id-filesystem");
	var oTable = elem.dataTable({ bRetrieve: true });
	var oTT = TableTools.fnGetInstance(elem[0]);
	var aData = oTT.fnGetSelectedData();
	for(var i = 0;i < aData.length;i++)
	{
		var o = {
		    path : aData[i].FullName,
		    specialty : aData[i].Specialty,
		    subspecialty : aData[i].Subspecialty,
		    firstname : aData[i].FirstName,
		    lastname : aData[i].LastName
		}
		oData.activities.push(o);
	}
	$.ajax({
		type:"POST",
		url:"http://localhost:50505/ajax/AddActivities",
		data:JSON.stringify(oData),
		dataType:"text",
		success: function(data)
		{
			debugger;
		},
		error: function(data)
		{
			debugger;
		}
	});
	debugger;
	/*
	var elem = $("table.class-id-filesystem");
	var oTable = elem.dataTable({ bRetrieve: true });
	var oTT = TableTools.fnGetInstance(elem[0]);
	var aData = oTT.fnGetSelectedData();
	var acc = "";
	for(var i = 0;i < aData.length;i++)
	{
		var o = aData[i];
		acc = acc + o.Specialty + "\n" + o.Subspecialty + "\n" + o.FullName + "\n";
	}
	$.post("http://localhost:50505/ajax/UploadFile",acc,function(data)
	{
		debugger;
	});
	*/
	// TODO: load the files
}

jQuery(document).ready(function () {
	
	/* begin: message pump setup */
	var abc = jQuery("#hiddenmessagequeueelement");
	abc.messagepump();
	abc.messagepump("setinterval",{ delay : 100 });
	/* end: message pump setup */
	
	/* begin: state machines setup */
	SetStateMachine(".class-id-main",StateMachineFactories["mainmenu"]());
	SetStateMachine(".class-id-loadfromconciergesheet",StateMachineFactories["addfiles"]());
	SetStateMachine(".class-id-adddoctorsheet",StateMachineFactories["adddoctor"]());
	SetStateMachine(".class-id-addpatientsheet",StateMachineFactories["addpatient"]());
	/* end: state machines setup */

	$(".class-initially-hidden").hide().removeClass("class-initially-hidden");
//	$(".class-id-main").fadeIn();
	
	/*
	$("button.class-id-mainmenu").button().click(function() {
		$(".class-id-loadfromconciergesheet").fadeOut(function() {
				$(".class-id-main").fadeIn();
				$(".class-id-mainmenu").menu();
			});		
	});
	$("button.class-id-mainmenu2").button().click(function() {
		$(".class-id-registerpatientsheet").fadeOut(function() {
				$(".class-id-main").fadeIn();
				$(".class-id-mainmenu").menu();
			});		
	});
	$("button.class-id-mainmenu3").button().click(function() {
		$(".class-id-browsedocmentssheet").fadeOut(function() {
				$(".class-id-main").fadeIn();
				$(".class-id-mainmenu").menu();
			});		
	});
	*/
	var smMain = GetStateMachine(".class-id-main");
	$("ul.class-id-mainmenu").menu
	(
		{
			select : function(event,ui)
			{
				switch($("div",ui.item).text())
				{
					case "Load file from Concierge Directories":
						smMain.addfiles();
						break;
					case "Add a new specialty":
						smMain.addspecialty();
						break;
					case "Register a new Concierge Patient":
						smMain.addpatient();
						break;
					case "Add New Doctor":
						smMain.adddoctor();
				}
			}
		}
	);
	smMain.run();
});

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

