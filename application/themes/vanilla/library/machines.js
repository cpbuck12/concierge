var StateMachinesFactories = new Array();
StateMachinesFactories['mainmenu'] =  function() { 
 return StateMachine.create({
  initial : 'starting',
  events :
    [
      { name:                          'run', from:                     'starting', to:                      'waiting' },
      { name:              'user_addpatient', from:                      'waiting', to:         'fading_addingpatient' },
      { name:                'user_addfiles', from:                      'waiting', to:           'fading_addingfiles' },
      { name:                     'continue', from:         'fading_addingpatient', to:                'addingpatient' },
      { name:                     'continue', from:           'fading_addingfiles', to:                  'addingfiles' },
      { name:                         'show', from:                      'waiting', to:           'fading_addingfiles' },
      { name:                       'resume', from:                'addingpatient', to:                      'waiting' },
      { name:                       'resume', from:                  'addingfiles', to:                      'waiting' },
      { name:              'user_addpatient', from:                     'starting', to:                     'starting' } /* filled */,
      { name:                'user_addfiles', from:                     'starting', to:                     'starting' } /* filled */,
      { name:                     'continue', from:                     'starting', to:                     'starting' } /* filled */,
      { name:                         'show', from:                     'starting', to:                     'starting' } /* filled */,
      { name:                       'resume', from:                     'starting', to:                     'starting' } /* filled */,
      { name:                          'run', from:                      'waiting', to:                      'waiting' } /* filled */,
      { name:                     'continue', from:                      'waiting', to:                      'waiting' } /* filled */,
      { name:                       'resume', from:                      'waiting', to:                      'waiting' } /* filled */,
      { name:                          'run', from:         'fading_addingpatient', to:         'fading_addingpatient' } /* filled */,
      { name:              'user_addpatient', from:         'fading_addingpatient', to:         'fading_addingpatient' } /* filled */,
      { name:                'user_addfiles', from:         'fading_addingpatient', to:         'fading_addingpatient' } /* filled */,
      { name:                         'show', from:         'fading_addingpatient', to:         'fading_addingpatient' } /* filled */,
      { name:                       'resume', from:         'fading_addingpatient', to:         'fading_addingpatient' } /* filled */,
      { name:                          'run', from:           'fading_addingfiles', to:           'fading_addingfiles' } /* filled */,
      { name:              'user_addpatient', from:           'fading_addingfiles', to:           'fading_addingfiles' } /* filled */,
      { name:                'user_addfiles', from:           'fading_addingfiles', to:           'fading_addingfiles' } /* filled */,
      { name:                         'show', from:           'fading_addingfiles', to:           'fading_addingfiles' } /* filled */,
      { name:                       'resume', from:           'fading_addingfiles', to:           'fading_addingfiles' } /* filled */,
      { name:                          'run', from:                'addingpatient', to:                'addingpatient' } /* filled */,
      { name:              'user_addpatient', from:                'addingpatient', to:                'addingpatient' } /* filled */,
      { name:                'user_addfiles', from:                'addingpatient', to:                'addingpatient' } /* filled */,
      { name:                     'continue', from:                'addingpatient', to:                'addingpatient' } /* filled */,
      { name:                         'show', from:                'addingpatient', to:                'addingpatient' } /* filled */,
      { name:                          'run', from:                  'addingfiles', to:                  'addingfiles' } /* filled */,
      { name:              'user_addpatient', from:                  'addingfiles', to:                  'addingfiles' } /* filled */,
      { name:                'user_addfiles', from:                  'addingfiles', to:                  'addingfiles' } /* filled */,
      { name:                     'continue', from:                  'addingfiles', to:                  'addingfiles' } /* filled */,
      { name:                         'show', from:                  'addingfiles', to:                  'addingfiles' } /* filled */
    ],
  callbacks: {
    
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
    onleavestarting: function(event,from,to,msg)
    {
      return false; /* filled */
    },
    onleavewaiting: function(event,from,to,msg)
    {
      return false; /* filled */
    },
    onleavefading_addingpatient: function(event,from,to,msg)
    {
      return false; /* filled */
    },
    onleavefading_addingfiles: function(event,from,to,msg)
    {
      return false; /* filled */
    },
    onleaveaddingpatient: function(event,from,to,msg)
    {
      return false; /* filled */
    },
    onleaveaddingfiles: function(event,from,to,msg)
    {
      return false; /* filled */
    }
  }
});
}