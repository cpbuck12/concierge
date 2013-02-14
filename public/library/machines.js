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
      { name:                    'adddoctor', from:                      'waiting', to:                 'addingdoctor' },
      { name:                          'run', from:                  'addingfiles', to:                      'waiting' },
      { name:                          'run', from:                'addingpatient', to:                      'waiting' },
      { name:                          'run', from:                 'addingdoctor', to:                      'waiting' },
      { name:                   'addpatient', from:                     'starting', to:                     'starting' } /* filled */,
      { name:                     'addfiles', from:                     'starting', to:                     'starting' } /* filled */,
      { name:                    'adddoctor', from:                     'starting', to:                     'starting' } /* filled */,
      { name:                          'run', from:                      'waiting', to:                      'waiting' } /* filled */,
      { name:                   'addpatient', from:                  'addingfiles', to:                  'addingfiles' } /* filled */,
      { name:                     'addfiles', from:                  'addingfiles', to:                  'addingfiles' } /* filled */,
      { name:                    'adddoctor', from:                  'addingfiles', to:                  'addingfiles' } /* filled */,
      { name:                   'addpatient', from:                'addingpatient', to:                'addingpatient' } /* filled */,
      { name:                     'addfiles', from:                'addingpatient', to:                'addingpatient' } /* filled */,
      { name:                    'adddoctor', from:                'addingpatient', to:                'addingpatient' } /* filled */,
      { name:                   'addpatient', from:                 'addingdoctor', to:                 'addingdoctor' } /* filled */,
      { name:                     'addfiles', from:                 'addingdoctor', to:                 'addingdoctor' } /* filled */,
      { name:                    'adddoctor', from:                 'addingdoctor', to:                 'addingdoctor' } /* filled */
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
    onenterwaiting: function(event,from,to,msg)
    {

	$(".class-id-main").fadeIn('fast');
    return true;
    },
    onenteraddingfiles: function(event,from,to,msg)
    {

		var q = GetMessageQueue(); // jQuery("#hiddenmessagequeueelement");
		var smOther = GetStateMachine(".class-id-loadfromconciergesheet");
		q.messagepump("send",function() {
			smOther.run();
		});
		return true;
    },
    onenteraddingdoctor: function(event,from,to,msg)
    {
	
		var q = GetMessageQueue();
		var smOther = GetStateMachine(".class-id-adddoctorsheet");
		q.messagepump("send",function() {
			smOther.run();
		});
		return true;
    },
    onenteraddingpatient: function(event,from,to,msg)
    {
	
		var q = GetMessageQueue();
		var smOther = GetStateMachine(".class-id-addpatientsheet");
		q.messagepump("send",function() {
			smOther.run();
		});
		return true;
    }
  }
});
};
StateMachineFactories['adddoctor'] =  function() { 
 return StateMachine.create({
  initial : 'starting',
  events :
    [
      { name:                          'run', from:                     'starting', to:                      'waiting' },
      { name:                        'error', from:                      'waiting', to:              'displayingerror' },
      { name:                     'continue', from:              'displayingerror', to:                      'waiting' },
      { name:                       'cancel', from:                      'waiting', to:                     'starting' },
      { name:                      'success', from:                      'waiting', to:            'displayingsuccess' },
      { name:                     'continue', from:            'displayingsuccess', to:                     'starting' },
      { name:                        'error', from:                     'starting', to:                     'starting' } /* filled */,
      { name:                     'continue', from:                     'starting', to:                     'starting' } /* filled */,
      { name:                       'cancel', from:                     'starting', to:                     'starting' } /* filled */,
      { name:                      'success', from:                     'starting', to:                     'starting' } /* filled */,
      { name:                          'run', from:                      'waiting', to:                      'waiting' } /* filled */,
      { name:                     'continue', from:                      'waiting', to:                      'waiting' } /* filled */,
      { name:                          'run', from:              'displayingerror', to:              'displayingerror' } /* filled */,
      { name:                        'error', from:              'displayingerror', to:              'displayingerror' } /* filled */,
      { name:                       'cancel', from:              'displayingerror', to:              'displayingerror' } /* filled */,
      { name:                      'success', from:              'displayingerror', to:              'displayingerror' } /* filled */,
      { name:                          'run', from:            'displayingsuccess', to:            'displayingsuccess' } /* filled */,
      { name:                        'error', from:            'displayingsuccess', to:            'displayingsuccess' } /* filled */,
      { name:                       'cancel', from:            'displayingsuccess', to:            'displayingsuccess' } /* filled */,
      { name:                      'success', from:            'displayingsuccess', to:            'displayingsuccess' } /* filled */
    ],
  callbacks: {
    onleavestarting: function(event,from,to,msg)
    {

		$(".class-id-adddoctorsheet button.class-id-mainmenu").button().on("click",CancelAddDoctor);
		$(".class-id-adddoctorsheet button.class-id-adddoctor").button().on("click",DoAddDoctor);
			$(".class-id-adddoctorsheet").fadeIn('fast',function() {
			var sm = $(this).data("statemachine");
			sm.transition();
		});
		return StateMachine.ASYNC;

    },
    onenterdisplayingsuccess: function(event,from,to,msg)
    {
			
		// TODO: add messagebox type acknowledgement
		var q = GetMessageQueue();
		var smAddDoctor = GetStateMachine(".class-id-adddoctorsheet");
		q.messagepump("send",function() {
			smAddDoctor.continue()
		});
    },
    onenterstarting: function(event,from,to,msg)
    {
		$(".class-id-adddoctorsheet button.class-id-mainmenu").button().off("click",CancelAddDoctor);
		$(".class-id-adddoctorsheet button.class-id-adddoctor").button().off("click",DoAddDoctor);
		$(".class-id-adddoctorsheet").fadeOut('fast',function()
		{
			var smMain = GetStateMachine(".class-id-main");
			var q = GetMessageQueue();
			q.messagepump('send',function()
			{
				smMain.run();
			});
		});
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
      { name:                'selectpatient', from:             'waitingonpatient', to:              'patientselected' },
      { name:                'selectpatient', from:              'patientselected', to:            'patientreselected' },
      { name:                   'docontinue', from:            'patientreselected', to:              'patientselected' },
      { name:              'unselectpatient', from:              'patientselected', to:               'waitingpatient' },
      { name:                   'filechange', from:              'patientselected', to:              'patientselected' },
      { name:                         'load', from:              'patientselected', to:                      'loading' },
      { name:                       'cancel', from:              'patientselected', to:                   'cancelling' },
      { name:                       'cancel', from:             'waitingonpatient', to:                   'cancelling' },
      { name:                      'restart', from:                   'cancelling', to:                     'starting' },
      { name:                       'loaded', from:                     'starting', to:                     'starting' } /* filled */,
      { name:                'selectpatient', from:                     'starting', to:                     'starting' } /* filled */,
      { name:                   'docontinue', from:                     'starting', to:                     'starting' } /* filled */,
      { name:              'unselectpatient', from:                     'starting', to:                     'starting' } /* filled */,
      { name:                   'filechange', from:                     'starting', to:                     'starting' } /* filled */,
      { name:                         'load', from:                     'starting', to:                     'starting' } /* filled */,
      { name:                       'cancel', from:                     'starting', to:                     'starting' } /* filled */,
      { name:                      'restart', from:                     'starting', to:                     'starting' } /* filled */,
      { name:                          'run', from:              'loadingpatients', to:              'loadingpatients' } /* filled */,
      { name:                'selectpatient', from:              'loadingpatients', to:              'loadingpatients' } /* filled */,
      { name:                   'docontinue', from:              'loadingpatients', to:              'loadingpatients' } /* filled */,
      { name:              'unselectpatient', from:              'loadingpatients', to:              'loadingpatients' } /* filled */,
      { name:                   'filechange', from:              'loadingpatients', to:              'loadingpatients' } /* filled */,
      { name:                         'load', from:              'loadingpatients', to:              'loadingpatients' } /* filled */,
      { name:                       'cancel', from:              'loadingpatients', to:              'loadingpatients' } /* filled */,
      { name:                      'restart', from:              'loadingpatients', to:              'loadingpatients' } /* filled */,
      { name:                          'run', from:             'waitingonpatient', to:             'waitingonpatient' } /* filled */,
      { name:                       'loaded', from:             'waitingonpatient', to:             'waitingonpatient' } /* filled */,
      { name:                   'docontinue', from:             'waitingonpatient', to:             'waitingonpatient' } /* filled */,
      { name:              'unselectpatient', from:             'waitingonpatient', to:             'waitingonpatient' } /* filled */,
      { name:                   'filechange', from:             'waitingonpatient', to:             'waitingonpatient' } /* filled */,
      { name:                         'load', from:             'waitingonpatient', to:             'waitingonpatient' } /* filled */,
      { name:                      'restart', from:             'waitingonpatient', to:             'waitingonpatient' } /* filled */,
      { name:                          'run', from:              'patientselected', to:              'patientselected' } /* filled */,
      { name:                       'loaded', from:              'patientselected', to:              'patientselected' } /* filled */,
      { name:                   'docontinue', from:              'patientselected', to:              'patientselected' } /* filled */,
      { name:                      'restart', from:              'patientselected', to:              'patientselected' } /* filled */,
      { name:                          'run', from:            'patientreselected', to:            'patientreselected' } /* filled */,
      { name:                       'loaded', from:            'patientreselected', to:            'patientreselected' } /* filled */,
      { name:                'selectpatient', from:            'patientreselected', to:            'patientreselected' } /* filled */,
      { name:              'unselectpatient', from:            'patientreselected', to:            'patientreselected' } /* filled */,
      { name:                   'filechange', from:            'patientreselected', to:            'patientreselected' } /* filled */,
      { name:                         'load', from:            'patientreselected', to:            'patientreselected' } /* filled */,
      { name:                       'cancel', from:            'patientreselected', to:            'patientreselected' } /* filled */,
      { name:                      'restart', from:            'patientreselected', to:            'patientreselected' } /* filled */,
      { name:                          'run', from:                   'cancelling', to:                   'cancelling' } /* filled */,
      { name:                       'loaded', from:                   'cancelling', to:                   'cancelling' } /* filled */,
      { name:                'selectpatient', from:                   'cancelling', to:                   'cancelling' } /* filled */,
      { name:                   'docontinue', from:                   'cancelling', to:                   'cancelling' } /* filled */,
      { name:              'unselectpatient', from:                   'cancelling', to:                   'cancelling' } /* filled */,
      { name:                   'filechange', from:                   'cancelling', to:                   'cancelling' } /* filled */,
      { name:                         'load', from:                   'cancelling', to:                   'cancelling' } /* filled */,
      { name:                       'cancel', from:                   'cancelling', to:                   'cancelling' } /* filled */,
      { name:                          'run', from:               'waitingpatient', to:               'waitingpatient' } /* filled */,
      { name:                       'loaded', from:               'waitingpatient', to:               'waitingpatient' } /* filled */,
      { name:                'selectpatient', from:               'waitingpatient', to:               'waitingpatient' } /* filled */,
      { name:                   'docontinue', from:               'waitingpatient', to:               'waitingpatient' } /* filled */,
      { name:              'unselectpatient', from:               'waitingpatient', to:               'waitingpatient' } /* filled */,
      { name:                   'filechange', from:               'waitingpatient', to:               'waitingpatient' } /* filled */,
      { name:                         'load', from:               'waitingpatient', to:               'waitingpatient' } /* filled */,
      { name:                       'cancel', from:               'waitingpatient', to:               'waitingpatient' } /* filled */,
      { name:                      'restart', from:               'waitingpatient', to:               'waitingpatient' } /* filled */,
      { name:                          'run', from:                      'loading', to:                      'loading' } /* filled */,
      { name:                       'loaded', from:                      'loading', to:                      'loading' } /* filled */,
      { name:                'selectpatient', from:                      'loading', to:                      'loading' } /* filled */,
      { name:                   'docontinue', from:                      'loading', to:                      'loading' } /* filled */,
      { name:              'unselectpatient', from:                      'loading', to:                      'loading' } /* filled */,
      { name:                   'filechange', from:                      'loading', to:                      'loading' } /* filled */,
      { name:                         'load', from:                      'loading', to:                      'loading' } /* filled */,
      { name:                       'cancel', from:                      'loading', to:                      'loading' } /* filled */,
      { name:                      'restart', from:                      'loading', to:                      'loading' } /* filled */
    ],
  callbacks: {
    onleaveloadingpatients: function(event,from,to,msg)
    {

		$("table.class-id-patient tr").on("click",OnPatientRowClick);
	
    },
    onleavestarting: function(event,from,to,msg)
    {

		alert("leaving addfiles starging");
		$(".class-id-loadfromconciergesheet button.class-id-mainmenu").button().on("click",CancelFileLoading);
		$(".class-id-loadfromconciergesheet button.class-id-loadfiles").button().on("click",DoFileLoading);
			$(".class-id-loadfromconciergesheet").fadeIn('fast',function() {
			var sm = $(this).data("statemachine");
			sm.transition();
		});
		return StateMachine.ASYNC;

    },
    onentercancelling: function(event,from,to,msg)
    {

			
		var elem = $("table.class-id-patient");
		var oTable = elem.dataTable({ bRetrieve: true });
		oTable.fnDestroy();
			
		var q = GetMessageQueue();
		var sm = GetStateMachine(".class-id-loadfromconciergesheet");
		q.messagepump("send",function() {
			sm.restart();
		});
			
    },
    onenterwaitingpatient: function(event,from,to,msg)
    {

		$("button.class-id-loadfiles").hide();
    },
    onenterpatientreselected: function(event,from,to,msg)
    {
		$("button.class-id-loadfiles").button("disable");
		DepopulateFiles();
		var q = GetMessageQueue();
		var sm = GetStateMachine(".class-id-loadfromconciergesheet");
		q.messagepump("send",function()
		{
			//alert("sending docontinue");
			sm.docontinue();
		});
		return true;
    },
    onenterpatientselected: function(event,from,to,msg)
    {
		PopulateFiles(/*function() {
			var q = GetMessageQueue();
			var sm = GetStateMachine(".class-id-loadfromconciergesheet");
			q.messagepump("send",function() {
				sm.run();
		}*/);
		$("button.class-id-loadfiles").button("disable");
		return true;
    },
    onenterstarting: function(event,from,to,msg)
    {

		$(".class-id-loadfromconciergesheet button.class-id-mainmenu").button().off("click",CancelFileLoading);
		$(".class-id-loadfromconciergesheet button.class-id-loadfiles").button().off("click",DoFileLoading);
		$(".class-id-loadfromconciergesheet").fadeOut('fast',function() {
			var q = GetMessageQueue();
			var smMain = GetStateMachine(".class-id-main");
			q.messagepump("send",function()
			{
				smMain.run();
			});
		});
    },
    onenterloadingpatients: function(event,from,to,msg)
    {

			$.getJSON("http://localhost:50505/ajax/GetPeopleOnDisk", function (patientsOnDiskJSON) {
			var oTable = $("table.class-id-patient").dataTable({
				"bJQueryUI": true,
				"sDom": 'T<"clear">lfrtip',
				"oTableTools": {
					"sRowSelect": "single"
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
			q.messagepump("send",function() {
				sm.loaded();
			});
			return true;
		});
    },
    onbeforefilechange: function(event,from,to,msg)
    {

		var oTable = TableTools.fnGetInstance($("table.class-id-filesystem")[0]);
		var result = oTable.fnGetSelectedData();
		if(result.length > 0)
			$("button.class-id-loadfiles").button("enable");
		else
			$("button.class-id-loadfiles").button("disable");
    },
    onbeforerestart: function(event,from,to,msg)
    {

		$("table.class-id-patient tr").off("click",OnPatientRowClick);
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
    onleavewaitingpatient: function(event,from,to,msg)
    {
      return false; /* filled */
    },
    onleaveloading: function(event,from,to,msg)
    {
      return false; /* filled */
    }
  }
});
};
StateMachineFactories['addpatient'] =  function() { 
 return StateMachine.create({
  initial : 'starting',
  events :
    [
      { name:                          'run', from:                     'starting', to:                      'waiting' },
      { name:                        'error', from:                      'waiting', to:              'displayingerror' },
      { name:                     'continue', from:              'displayingerror', to:                      'waiting' },
      { name:                       'cancel', from:                      'waiting', to:                     'starting' },
      { name:                      'success', from:                      'waiting', to:            'displayingsuccess' },
      { name:                     'continue', from:            'displayingsuccess', to:                     'starting' },
      { name:                        'error', from:                     'starting', to:                     'starting' } /* filled */,
      { name:                     'continue', from:                     'starting', to:                     'starting' } /* filled */,
      { name:                       'cancel', from:                     'starting', to:                     'starting' } /* filled */,
      { name:                      'success', from:                     'starting', to:                     'starting' } /* filled */,
      { name:                          'run', from:                      'waiting', to:                      'waiting' } /* filled */,
      { name:                     'continue', from:                      'waiting', to:                      'waiting' } /* filled */,
      { name:                          'run', from:              'displayingerror', to:              'displayingerror' } /* filled */,
      { name:                        'error', from:              'displayingerror', to:              'displayingerror' } /* filled */,
      { name:                       'cancel', from:              'displayingerror', to:              'displayingerror' } /* filled */,
      { name:                      'success', from:              'displayingerror', to:              'displayingerror' } /* filled */,
      { name:                          'run', from:            'displayingsuccess', to:            'displayingsuccess' } /* filled */,
      { name:                        'error', from:            'displayingsuccess', to:            'displayingsuccess' } /* filled */,
      { name:                       'cancel', from:            'displayingsuccess', to:            'displayingsuccess' } /* filled */,
      { name:                      'success', from:            'displayingsuccess', to:            'displayingsuccess' } /* filled */
    ],
  callbacks: {
    onleavestarting: function(event,from,to,msg)
    {
		$.getJSON("http://localhost:50505/ajax/GetPeopleOnDisk", function (patientsOnDiskJSON) {
			$(".class-id-addpatientsheet table.class-id-patientsondisk").dataTable({
				bJQueryUI : true,
				"sDom": 'T<"clear">lfrtip',
				"oTableTools": {
					"sRowSelect": "single"
	        	},
				"aoColumnDefs":
				[
					{ "sTitle": "First Name", "aTargets": [ 0 ], "mData": "FirstName" },
					{ "sTitle": "Last Name", "aTargets": [ 1 ], "mData": "LastName" },
				],
				"aaData" : patientsOnDiskJSON
			}); 
		});
		$.getJSON("http://localhost:50505/ajax/GetPeopleInDb", function (patientsInDbJSON) {
			$(".class-id-addpatientsheet table.class-id-patientsindb").dataTable({
				bJQueryUI : true,
				"sDom": 'T<"clear">lfrtip',
				"oTableTools": {
					"sRowSelect": "single"
	        	},
				"aoColumnDefs":
				[
					{ "sTitle": "First Name", "aTargets": [ 0 ], "mData": "FirstName" },
					{ "sTitle": "Last Name", "aTargets": [ 1 ], "mData": "LastName" },
				],
				"aaData" : patientsInDbJSON
			}); 
		});
		$(".class-id-addpatientsheet button.class-id-mainmenu").button().on("click",CancelAddPatient);
		$(".class-id-addpatientsheet button.class-id-addpatient").button().on("click",DoAddPatient);
		$(".class-id-addpatientsheet input.class-id-dob").datepicker();
		$(".class-id-addpatientsheet").fadeIn('fast',function() {
			var sm = $(this).data("statemachine");
			sm.transition();
		});
		return StateMachine.ASYNC;
	
    },
    onenterstarting: function(event,from,to,msg)
    {

		$(".class-id-addpatientsheet button.class-id-mainmenu").button().off("click",CancelAddPatient);

		$(".class-id-addpatientsheet").fadeOut('fast',function() {
			var q = GetMessageQueue();
			var smMain = GetStateMachine(".class-id-main");
			q.messagepump("send",function()
			{
				smMain.run();
			});
		});

    }
  }
});
};
