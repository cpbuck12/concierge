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

function CancelFileLoading()
{
	var q = GetMessageQueue();
	var sm = GetStateMachine(".class-id-loadfromconciergesheet");
	q.messagepump("send",function() {
		sm.cancel();
	});
}

jQuery(document).ready(function () {
	
	/* begin: message pump setup */
	var abc = jQuery("#hiddenmessagequeueelement");
	abc.messagepump();
	abc.messagepump("setinterval",{ delay : 1000 });
	/* end: message pump setup */
	
	/* begin: state machines setup */
	SetStateMachine(".class-id-main",StateMachineFactories["mainmenu"]());
	SetStateMachine(".class-id-loadfromconciergesheet",StateMachineFactories["addfiles"]());
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
	$(".class-id-mainmenu").menu
	(
		{
			select : function(event,ui)
			{
				switch($("div",ui.item).text())
				{
					case "Load file from Concierge Directories":
						smMain.addfiles();
						break;
					case "Register a new Concierge Patient":
						smMain.addpatient();
						break;
				}
			}
		}
	);
	smMain.run();
});