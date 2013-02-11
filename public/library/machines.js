/* Generated from __FILE__ */
var StateMachineFactories = new Array();
StateMachineFactories['mainmenu'] =  function() { 
 return StateMachine.create({
  initial : 'starting',
  events :
    [
      { name:                          'run', from:                     'starting', to:                      'waiting' },
      { name:                   'addpatient', from:                      'waiting', to:                'addingpatient' },
      { name:                     'addfiles', from:                      'waiting', to:                  'addingfiles' },
      { name:                          'run', from:                  'addingfiles', to:                      'waiting' },
      { name:                          'run', from:                'addingpatient', to:                      'waiting' },
      { name:                   'addpatient', from:                     'starting', to:                     'starting' } /* filled */,
      { name:                     'addfiles', from:                     'starting', to:                     'starting' } /* filled */,
      { name:                          'run', from:                      'waiting', to:                      'waiting' } /* filled */,
      { name:                   'addpatient', from:                  'addingfiles', to:                  'addingfiles' } /* filled */,
      { name:                     'addfiles', from:                  'addingfiles', to:                  'addingfiles' } /* filled */,
      { name:                   'addpatient', from:                'addingpatient', to:                'addingpatient' } /* filled */,
      { name:                     'addfiles', from:                'addingpatient', to:                'addingpatient' } /* filled */
    ],
  callbacks: {
    onleavestarting: function(event,from,to,msg)
    {
		$(".class-id-main").fadeIn('fast',function() {
			var sm = $(this).data("statemachine");
			sm.transition();
		});
		return StateMachine.ASYNC;
    },
    onleavewaiting: function(event,from,to,msg)
    {
		$(".class-id-main").fadeOut('fast',function() {
			var sm = $(this).data("statemachine");
			sm.transition();			
		});
		return StateMachine.ASYNC;
    },
    onenteraddingfiles: function(event,from,to,msg)
    {

		var q = GetMessageQueue(); // jQuery("#hiddenmessagequeueelement");
		var smOther = GetStateMachine(".class-id-loadfromconciergesheet");
		q.messagepump("send",function() {
			smOther.run();
		});
		return true;
    }
  }
});
};
StateMachineFactories['addfiles'] =  function() { 
 return StateMachine.create({
  initial : 'starting',
  events :
    [
      { name:                          'run', from:                     'starting', to:              'loadingpatients' },
      { name:                       'loaded', from:              'loadingpatients', to:             'waitingonpatient' },
      { name:                    'notloaded', from:              'loadingpatients', to:                   'cancelling' },
      { name:                'selectpatient', from:             'waitingonpatient', to:              'patientselected' },
      { name:                'selectpatient', from:              'patientselected', to:              'patientselected' },
      { name:                   'filechange', from:              'patientselected', to:              'patientselected' },
      { name:                         'load', from:              'patientselected', to:                      'loading' },
      { name:                       'cancel', from:              'patientselected', to:                   'cancelling' },
      { name:                       'cancel', from:             'waitingonpatient', to:                   'cancelling' },
      { name:                      'restart', from:                   'cancelling', to:                     'starting' },
      { name:                       'loaded', from:                     'starting', to:                     'starting' } /* filled */,
      { name:                    'notloaded', from:                     'starting', to:                     'starting' } /* filled */,
      { name:                'selectpatient', from:                     'starting', to:                     'starting' } /* filled */,
      { name:                   'filechange', from:                     'starting', to:                     'starting' } /* filled */,
      { name:                         'load', from:                     'starting', to:                     'starting' } /* filled */,
      { name:                       'cancel', from:                     'starting', to:                     'starting' } /* filled */,
      { name:                      'restart', from:                     'starting', to:                     'starting' } /* filled */,
      { name:                          'run', from:              'loadingpatients', to:              'loadingpatients' } /* filled */,
      { name:                'selectpatient', from:              'loadingpatients', to:              'loadingpatients' } /* filled */,
      { name:                   'filechange', from:              'loadingpatients', to:              'loadingpatients' } /* filled */,
      { name:                         'load', from:              'loadingpatients', to:              'loadingpatients' } /* filled */,
      { name:                       'cancel', from:              'loadingpatients', to:              'loadingpatients' } /* filled */,
      { name:                      'restart', from:              'loadingpatients', to:              'loadingpatients' } /* filled */,
      { name:                          'run', from:             'waitingonpatient', to:             'waitingonpatient' } /* filled */,
      { name:                       'loaded', from:             'waitingonpatient', to:             'waitingonpatient' } /* filled */,
      { name:                    'notloaded', from:             'waitingonpatient', to:             'waitingonpatient' } /* filled */,
      { name:                   'filechange', from:             'waitingonpatient', to:             'waitingonpatient' } /* filled */,
      { name:                         'load', from:             'waitingonpatient', to:             'waitingonpatient' } /* filled */,
      { name:                      'restart', from:             'waitingonpatient', to:             'waitingonpatient' } /* filled */,
      { name:                          'run', from:              'patientselected', to:              'patientselected' } /* filled */,
      { name:                       'loaded', from:              'patientselected', to:              'patientselected' } /* filled */,
      { name:                    'notloaded', from:              'patientselected', to:              'patientselected' } /* filled */,
      { name:                      'restart', from:              'patientselected', to:              'patientselected' } /* filled */,
      { name:                          'run', from:                   'cancelling', to:                   'cancelling' } /* filled */,
      { name:                       'loaded', from:                   'cancelling', to:                   'cancelling' } /* filled */,
      { name:                    'notloaded', from:                   'cancelling', to:                   'cancelling' } /* filled */,
      { name:                'selectpatient', from:                   'cancelling', to:                   'cancelling' } /* filled */,
      { name:                   'filechange', from:                   'cancelling', to:                   'cancelling' } /* filled */,
      { name:                         'load', from:                   'cancelling', to:                   'cancelling' } /* filled */,
      { name:                       'cancel', from:                   'cancelling', to:                   'cancelling' } /* filled */,
      { name:                          'run', from:                      'loading', to:                      'loading' } /* filled */,
      { name:                       'loaded', from:                      'loading', to:                      'loading' } /* filled */,
      { name:                    'notloaded', from:                      'loading', to:                      'loading' } /* filled */,
      { name:                'selectpatient', from:                      'loading', to:                      'loading' } /* filled */,
      { name:                   'filechange', from:                      'loading', to:                      'loading' } /* filled */,
      { name:                         'load', from:                      'loading', to:                      'loading' } /* filled */,
      { name:                       'cancel', from:                      'loading', to:                      'loading' } /* filled */,
      { name:                      'restart', from:                      'loading', to:                      'loading' } /* filled */
    ],
  callbacks: {
    onleavestarting: function(event,from,to,msg)
    {

		$("button.class-id-mainmenu").button().on("click",CancelFileLoading);
		$(".class-id-loadfromconciergesheet").fadeIn('fast',function() {
			var sm = $(this).data("statemachine");
			sm.transition();
		});
		return StateMachine.ASYNC;

    },
    onenterloaded: function(event,from,to,msg)
    {
alert('loaded');
    },
    onenterloadingpatients: function(event,from,to,msg)
    {
			$.getJSON("http://localhost:50505/ajax/GetPeopleOnDisk", function (patientsOnDiskJSON) {
			var patientsOnDisk = $.parseJSON(patientsOnDiskJSON);
			var oTable = $("table.class-id-patient").dataTable({
				"bJQueryUI": true,
				oTableTools :
				{
					"sDom": 'T<"clear">lfrtip',
					sRowSelect: "single"
				},
				"aoColumnDefs":
				[
					{ "sTitle": "First Name", "aTargets": [ 0 ], "mData": "FirstName" },
					{ "sTitle": "Last Name", "aTargets": [ 1 ], "mData": "LastName" },
				],
				"aaData" : patientsOnDiskJSON
			});
			var q = GetMessageQueue();
			var sm  = GetStateMachine(".class-id-loadfromconciergesheet");
			alert("loadpatients");
			q.messagepump("send",function() {
				sm.loaded();
			});
			return true;
		});
    },
    onbeforecancel: function(event,from,to,msg)
    {
alert('cancel2'+' '+from+' '+to);
    }
/* ****************************************************************************************
*******************************************************************************************
*******************************************************************************************
*******************************************************************************************					
*******************************************************************************************					
*******************************************************************************************
*******************************************************************************************
*******************************************************************************************					
*******************************************************************************************
****************                                                         ******************
**************** OOOOOOO  OOOOO  OOOOO   OOOOO   OOOOOOO OOOOOO   OOOOO  ******************
****************  O    O    O      O       O      O    O  O    O O     O ******************
****************  O         O      O       O      O       O    O O       ******************
****************  O  O      O      O       O      O  O    O    O O       ******************
****************  OOOO      O      O       O      OOOO    OOOOO   OOOOO  ******************
****************  O  O      O      O       O      O  O    O  O         O ******************
****************  O         O      O       O      O       O  O         O ******************
****************  O         O      O   O   O   O  O    O  O   O  O     O ******************
**************** OOOO     OOOOO  OOOOOOO OOOOOOO OOOOOOO OOO  OO  OOOOO  ******************
****************                                                         ******************					
*******************************************************************************************					
*******************************************************************************************
*******************************************************************************************
*******************************************************************************************					
*******************************************************************************************					
*******************************************************************************************
*******************************************************************************************					
*******************************************************************************************					
*******************************************************************************************
*******************************************************************************************
*******************************************************************************************					
******************************************************************************************/,
    onleaveloading: function(event,from,to,msg)
    {
      return false; /* filled */
    }
  }
});
};
