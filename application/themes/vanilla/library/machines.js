var StateMachinesFactories = new Array();
StateMachinesFactories['mainmenu'] =  function() { 
 return StateMachine.create({
  initial : 'starting',
  events :
    [
      { name:         'addpatient', from:            'waiting', to:      'addingpatient' },
      { name:           'addfiles', from:            'waiting', to:        'addingfiles' },
      { name:             'resume', from:      'addingpatient', to:            'waiting' },
      { name:             'resume', from:        'addingfiles', to:            'waiting' },
      { name:                'run', from:           'starting', to:            'waiting' },
      { name:             'resume', from:            'waiting', to:            'waiting' } /* filled */,
      { name:                'run', from:            'waiting', to:            'waiting' } /* filled */,
      { name:         'addpatient', from:      'addingpatient', to:      'addingpatient' } /* filled */,
      { name:           'addfiles', from:      'addingpatient', to:      'addingpatient' } /* filled */,
      { name:                'run', from:      'addingpatient', to:      'addingpatient' } /* filled */,
      { name:         'addpatient', from:        'addingfiles', to:        'addingfiles' } /* filled */,
      { name:           'addfiles', from:        'addingfiles', to:        'addingfiles' } /* filled */,
      { name:                'run', from:        'addingfiles', to:        'addingfiles' } /* filled */,
      { name:         'addpatient', from:           'starting', to:           'starting' } /* filled */,
      { name:           'addfiles', from:           'starting', to:           'starting' } /* filled */,
      { name:             'resume', from:           'starting', to:           'starting' } /* filled */
    ],
  callbacks: {
    onleaveresume: function(event,from,to,msg)
    {
return true;
    },
    onleavewaiting: function(event,from,to,msg)
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
    },
    onleavestarting: function(event,from,to,msg)
    {
      return false; /* filled */
    }
  }
});
}