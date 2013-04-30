var StateMachineFactories = new Array();

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
} // var Base64

function GetMessageQueue()
{
	return jQuery("#hiddenmessagequeueelement");
}

function GetStateMachine()
{
	var e;
	if(arguments.length == 0)
	{
		e = jQuery(this);
	}
	else
	{
		arg = arguments[0];
		if(typeof arg == "string")
		{
			e = jQuery(arguments[0]);
		}
		else
		{
			e = arg;
		}
	}
	return e.data("statemachine");
}

function FunnelMessage(sm,cat,func)
{
	var q = GetMessageQueue();
	q.messagepump(cat,function() {
		func(sm);
	});
}

function SendMessage()
{
	var sm;
	var func;
	if(arguments.length == 1)
	{
		sm = GetStateMachine();
		func = arguments[0];
	}
	else
	{
		sm = GetStateMachine(arguments[0]);
		func = arguments[1];
	}
	FunnelMessage(sm,"send",func);
}

function PostMessage()
{
	var sm;
	var func;
	if(arguments.length == 1)
	{
		sm = GetStateMachine();
		func = arguments[0];
	}
	else
	{
		sm = GetStateMachine(arguments[0]);
		func = arguments[1];
	}
	FunnelMessage(sm,"post",func);
}

function SetStateMachine(selector,machine)
{
	jQuery(selector).data("statemachine",machine);
}


function OnFileSelectionChanged()
{
	SendMessage(".class-id-loadfromconciergesheet",function(sm) {
		sm.filechange();
	});
}

function OutterHtml(elem)
{
	return $(elem).clone().wrap("<p>").parent().html();
}

function UnstashTable(element)
{
	var oTable;
	var parent = $(element).parent();
	var prev_elem = $(parent).prev();
	oTable = $(element).dataTable({bRetrieve : true});
	oTable.fnDestroy();
	$(element).remove();
	var divText = Base64.decode(prev_elem.text());
	$(parent).remove();
	prev_elem.after(divText);
	var result = prev_elem.next();
	prev_elem.remove();
	return result;
}


function BuildFileBrowser(data,foldersElement,volumesElement,filesElement,firstTime,type)
{
	if(!firstTime)
	{
		foldersElement = UnstashTable(foldersElement);
		volumesElement = UnstashTable(volumesElement);
		filesElement = UnstashTable(filesElement);
	}
	StashElement(foldersElement);
	StashElement(volumesElement);
	StashElement(filesElement);
	var result = data;
	if(result.status == "ok")
	{
		var fileInfo = result.fileInfo;
		var currentPath = fileInfo.currentPath;
		var files = fileInfo.files;
		var folders = fileInfo.folders;
		var volumes = fileInfo.volumes;
		$(".class-id-folderbrowsersheet div.class-id-folderlabel").text(currentPath);
		$(foldersElement).dataTable({
			bJQueryUI : true,
			bSort : false,
			"sDom": 'T<"clear">tp',
			"oTableTools": {
				"sRowSelect": "single",
				"aButtons" : [],
				"fnRowSelected" : function(nodes) {
					var fullName = $(nodes[1]).val();
					SendMessage(".class-id-folderbrowsersheet",function(sm) { sm.choose("folder"); }); 
				}
			},
			"aoColumnDefs":
			[
				{ "sTitle": "Name", "aTargets": [ 0 ], "mData": "Name" },
				{ "sTitle": "FullName", "aTargets": [ 1 ], "mData": "FullName", bVisible : false },
			],
			"aaData" : folders
		});
		$(volumesElement).dataTable({
			bSort : false,
			bJQueryUI : true,
			"sDom": 'T<"clear">tp',
			"oTableTools":
			{
				"sRowSelect": "single",
				"aButtons" : [],
				"fnRowSelected" : function(nodes)
				{
					var name = $(nodes[0]).val();
					SendMessage(".class-id-folderbrowsersheet",function(sm) { sm.choose("volume"); }); 
				}
			},
			"aoColumnDefs": [{"sTitle" : "Volume", "aTargets" : [0], "mData" : "Name" }],
			"aaData" : volumes
		});
		$(filesElement).dataTable({
			bJQueryUI : true,
			bSort : false,
			"sDom": 'T<"clear">lfrtip',
			"oTableTools": {
				"sRowSelect": ((type == "file") ? "single" : ""),
				"aButtons" : [],
				"fnRowSelected" : function(nodes)
				{
					var name = $(nodes[0]).val();
					SendMessage(".class-id-folderbrowsersheet",function(sm) { sm.choose("file"); }); 
				}
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
		SendMessage(".class-id-folderbrowsersheet",function(sm) { sm.initialize(); }); 
	}
}


function downloadURL(url) {
    var hiddenIFrameID = 'hiddenDownloader',
        iframe = document.getElementById(hiddenIFrameID);
    if (iframe === null) {
        iframe = document.createElement('iframe');
        iframe.id = hiddenIFrameID;
        iframe.style.display = 'none';
        document.body.appendChild(iframe);
    }
    iframe.src = url;
};

function downloadFile(id) {
	debugger;
	window.open("http://localhost:50505/DownloadFile/"+id+".PDF","_blank")
	//downloadURL("http://localhost:50505/DownloadFile/"+id);
}


function StashElement(elem)
{
	$(elem).each(function() {
		var origHtml = OutterHtml(this);
		origHtml = Base64.encode(origHtml);
		$(this).before("<div class='class-stash'>"+origHtml+"</div>");
	});
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

	CallServer({
		command: "GetFilesOnDisk",
		parameters:{
			"firstname" : firstName,
			"lastname" : lastName
		},
		success: function(data)
		{
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
				aaData : data.files,
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
	
	var acc = JSON.stringify({
		"firstname" : firstName,
		"lastname" : lastName
	});
}

function OnPatientAddRowClick()
{
	var row = $(".class-id-addpatientsheet table.class-id-patientsondisk tr.DTTT_selected td");
	var firstName = $(row[0]).text();
	var lastName = $(row[1]).text();
	$(".class-id-addpatientsheet input.class-id-firstname").val(firstName).trigger("change");
	$(".class-id-addpatientsheet input.class-id-lastname").val(lastName).trigger("change");
}

function CallServer(options)
{
	$.ajax({
		type:"POST",
		url:("http://localhost:50505/ajax/"+options.command),
		data:JSON.stringify(options.parameters),
		dataType:"text",
		success:function(data) {
			options.success(JSON.parse(data));
		},
		error:function() {
			options.failure();
		}
	});
}

function DoChangeFolder()
{
	var elem = $(".class-id-folderbrowsersheet table.class-id-folders");
	var oTable = elem.dataTable({ bRetrieve : true });
	var oTableTools;
	elem.each(function() { oTableTools =  TableTools.fnGetInstance(this); } );
    var aData = oTableTools.fnGetSelectedData();
    var newFolder = aData[0].FullName;
    
    var elemFiles = $(".class-id-folderbrowsersheet table.class-id-files");
	var oTableFiles = elemFiles.dataTable({ bRetrieve : true });
	var oTableToolsFiles;
	elemFiles.each(function() { oTableToolsFiles =  TableTools.fnGetInstance(this); } );

	CallServer({
		command:"SetCurrentDirectory",
		parameters:{ path : newFolder},
		success: function(data) {
			var result = JSON.parse(data);
			if(result["status"] == "ok")
			{
				var fileInfo = result.fileInfo;
				var currentPath = fileInfo.currentPath;
				var files = fileInfo.files;
				var folders = fileInfo.folders;
				var volumes = fileInfo.volumes;
				oTable.fnClearTable();
				var i;
				for(i = 0;i < folders.length;i++)
				{
					
					var a = [];
					o = { Name : folders[i].Name, FullName : folders[i].FullName };
					a[0] = folders[i].Name;
					a[1] = folders[i].FullName;
					oTable.fnAddData(o);
				}
				oTableFiles.fnClearTable();
				for(i = 0;i < files.length;i++)
				{
					o = {
						Name : files[i].Name,
						Length : files[i].Length,
						LastWriteTime : files[i].LastWriteTime,
						FullName : files[i].FullName
					}
					oTableFiles.fnAddData(o);
				}
			}
		},
		failure:function() {
			//TODO
		}
	});
}


function CancelAddPatient()
{
	SendMessage(".class-id-addpatientsheet",function(sm) {
		sm.cancel();
	})
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
	SetStateMachine(".class-id-updatedoctorsheet",StateMachineFactories["listdoctors"]());
	SetStateMachine(".class-id-addpatientsheet",StateMachineFactories["addpatient"]());
	SetStateMachine(".class-id-addspecialtysheet",StateMachineFactories["addspecialty"]());
	SetStateMachine(".class-id-folderbrowsersheet",StateMachineFactories["folderbrowser"]());
	SetStateMachine(".class-id-documentbrowsersheet",StateMachineFactories["documentbrowser"]());
	SetStateMachine(".class-id-loadfilesheet",StateMachineFactories["addfile"]())
	/* end: state machines setup */

	$(".class-initially-hidden").hide().removeClass("class-initially-hidden");
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
						break;
					case "List Doctors":
						smMain.listdoctors();
						break;
					case "Create Website":
						smMain.createwebsite();
						break;
					case "Add file":
						smMain.addfile();
						break;
					case "Browse Documents":
						smMain.browsedocuments();
						break;
				}
			}
		}
	);
	smMain.run();
});


function YesNoBox(title,message,after)
{
	var dialogSheet =  $(".class-id-dialogssheet");
	var messagebox = $("div.class-id-messagebox",dialogSheet);
	var messageDiv = $("div.class-id-message",messagebox);
 	messageDiv.attr("title",title);
	messageDiv.text(message);
	var choice = "close";
	messageDiv.dialog({
		model: true,
		close : function(even,ui) {
			$(this).dialog("destroy");
			after(choice);
		},
		buttons : {
			Yes : function() {
				choice = "yes";
				$(this).dialog("close");
			},
			No : function() {
				choice = "no";
				$(this).dialog("close");
			}
		}
	});
}

function MessageBox(title,message,after)
{
	var dialogSheet =  $(".class-id-dialogssheet");
	var messagebox = $("div.class-id-messagebox",dialogSheet);
	var messageDiv = $("div.class-id-message",messagebox);
 	messageDiv.attr("title",title);
	messageDiv.text(message);
	messageDiv.dialog({
		modal : true,
		close : function(event,ui) {
			$(this).dialog("destroy");
			after();
		},
		buttons : {
			Ok : function() {
				$(this).dialog("close");
			}
		}
	});
}
