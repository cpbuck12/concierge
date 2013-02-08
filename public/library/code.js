
/*
jQuery(document).ready(function() {
	StateMachines["mainmenu"].run();
});
*/
jQuery(document).ready(function () {
	var abc = jQuery("#hiddenmessagequeueelement");
	abc.messagepump();
	abc.messagepump("setinterval",{ delay : 1000 });
	/*
	for(var i = 0;i < 10;i++)
	{
		var method = (i % 2) ? "post" : "send";
		var message = i.toString();
		abc.messagepump(method,function() { alert(message); });
	}
	abc.messagepump("post",function() { alert("Fred"); });
	abc.messagepump("send",function() { alert("Wilma"); });
	*/
	$(".class-initially-hidden").hide().removeClass("class-initially-hidden");
	$(".class-id-main").fadeIn();
	
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
	$(".class-id-mainmenu").menu
	(
		{
			select : function(event,ui)
			{
				//debugger;
				//alert(ui.item.html());
				switch($("div",ui.item).text())
				{
				case "Load file from Concierge Directories":
					$(".class-id-main").fadeOut(function() {
						$(".class-id-loadfromconciergesheet").fadeIn();
					});
					$.getJSON("http://localhost:50505/ajax/GetPeopleOnDisk", function (patientsOnDiskJSON) {
						var patientsOnDisk = $.parseJSON(patientsOnDiskJSON);
						var oTable = $(".class-id-loadfromconciergesheet table.class-id-patient").dataTable({
//							"bDestroy": true,
							"bJQueryUI": true,
							oTableTools :	{
                                "sDom": 'T<"clear">lfrtip',
                                sRowSelect: "single"
								
//								sDom : 'R<"H"lfr>t<"F"ip>',
//								"sRowSelect": "single"
							},
//							"sPaginationType": "full_numbers",
							//sDom : 'R',
							"aoColumnDefs": [
								{ "sTitle": "First Name", "aTargets": [ 0 ], "mData": "FirstName" },
								{ "sTitle": "Last Name", "aTargets": [ 1 ], "mData": "LastName" },
						    ],
						    "aaData" : patientsOnDiskJSON
						});
						$(".class-id-patient tr").click(function() {
//							$("table.class-id-patient").click(function() {
//							$(".class-id-loadfromconciergesheet table.class-id-patient").click(function() {
								//alert("LICK");
								$("tr td", $(this).parent()).removeClass("ui-state-highlight");
								var items = $("td", this).addClass("ui-state-highlight");
							});
					});
					$(".class-id-loadfromconciergesheet table.class-id-filesystem").dataTable({
						sDom : 'R',
						"bDestroy": true,
						"bJQueryUI": true,
						"aoColumnDefs": [
					      { "sTitle": "Specialty", "aTargets": [ 0 ] },
					      { "sTitle": "Subspecialty", "aTargets": [ 1 ] },
					      { "sTitle": "Filename", "aTargets": [ 2 ] },
					      { "sTitle": "Hash", "aTargets": [ 3 ] }					      
					    ]
					});
					break;
				case "Register a new Concierge Patient":
					$(".class-id-main").fadeOut(function() {
						$(".class-id-registerpatientsheet").fadeIn();
					});
					$(".class-id-registerpatientsheet table.class-id-patient2").dataTable({
						"bDestroy": true,
						"bJQueryUI": true,
						sDom : 'R<"H"lfr>t<"F"ip>',
						"sPaginationType": "full_numbers",
						//sDom : 'R',
						"aoColumnDefs": [
							{ "sTitle": "First Name", "aTargets": [ 0 ] },
							{ "sTitle": "Last Name", "aTargets": [ 1 ] },
					    ]
					});
					break;
				case "Browse Documents":
					$(".class-id-main").fadeOut(function() {
						$(".class-id-browsedocmentssheet").fadeIn();
					});
				}
			}
		}
	);
});