<?php
class StateMachine
{
	protected $transitions = array();
	protected $leaves = array();
	protected $enters = array();
	protected $befores = array();
	protected $afters = array();
	public $defaultHandlerCode = "      return false; /* filled */";
	
	public function AddTransition($event,$from,$to)
	{
		$this->transitions[] = array("event" => $event,"from" => $from,"to" => $to);
	}
	public function AddLeaveCallback($stateName,$code)
	{
		$this->leaves[] = array($stateName,$code);
	}
	public function AddEnterCallback($stateName,$code)
	{
		$this->enters[] = array($stateName,$code);
	}
	public function AddBeforeCallback($eventName,$code)
	{
		$this->befores[] = array($eventName,$code);
	}
	public function AddAfterCallback($eventName,$code)
	{
		$this->befores[] = array($eventName,$code);
	}
	protected function AllFroms()
	{
		$acc = array();
		foreach($this->transitions as $transition)
		{
			$acc[] = $transition["from"];
		}
		$result = array_unique($acc);
		return $result;
	}
	protected function AllTos()
	{
		$acc = array();
		foreach($this->transitions as $transition)
		{
			$acc[] = $transition["to"];
		}
		$result = array_unique($acc);
		return $result;
	}
	protected function AllEventsForFromState($stateFrom)
	{ // we want to find all the froms which do not each contain all the events
		$eventsPresent = array();
		foreach($this->transitions as $transition)
		{
			if($transition["from"] == $stateFrom)
			{
				$eventsPresent[] = $transition["event"];
			}
		}
		return array_unique($eventsPresent);
	}
	protected function AllEventsMissingForFromState($stateFrom)
	{
		$allEvents = $this->AllEvents();
		$present = $this->AllEventsForFromState($stateFrom);
		$result =  array_diff($allEvents,$present);
		return $result;
	}
	protected function EventsMissingForFromState($stateFrom)
	{
		return count($this->AllEventsMissingForFromState($stateFrom)) > 0;
	}
	protected function AllStates()
	{ 
		$tos = $this->AllTos();
		$froms = $this->AllFroms();
		$result = array_merge($froms,$tos);
		$result = array_unique($result);
		return $result;
	}
	protected function AllEvents()
	{
		$acc = array();
		foreach($this->transitions as $transition)
		{
			$acc[] = $transition["event"];
		}
		return array_unique($acc);
	}
	protected function FormatTransition($event,$from,$to,$extra = "")
	{
		$result = sprintf("{ name: %30s, from: %30s, to: %30s }","'".$event."'","'".$from."'","'".$to."'") . $extra;
//		$result = "{ name: '" . $event . "', from: '" . $from . "', to: '" . $to . "' }" . $extra;
		return $result;
	}
	protected function FormatCallback($funcName,$code)
	{
		$result = $funcName . ": function(event,from,to,msg)\n    {\n" . $code . "\n    }";
		return $result;
	}
	protected function GenerateTransitions($initialState)
	{
		$eventBuffers = array();
		foreach($this->transitions as $transition)
		{
			$eventBuffers[] = $this->FormatTransition($transition["event"],$transition["from"],$transition["to"]); 
		}
		$eventBuffer = implode(",\n      ",$eventBuffers);
		$eventBuffers = array();
		foreach($this->AllStates() as $state)
		{
			foreach($this->AllEventsMissingForFromState($state) as $event)
			{
				$eventBuffers[] = $this->FormatTransition($event,$state,$state," /* filled */"); 
			}
		}
		if(count($eventBuffers)>0)
		{
			$eventBuffer = $eventBuffer . ",\n      " . implode(",\n      ",$eventBuffers);
		}
		$callbackBuffers = array();
		foreach($this->leaves as $leave)
		{
			$callbackBuffers[] = $this->FormatCallback("onleave" . $leave[0],$leave[1]);
		}
		foreach($this->enters as $enter)
		{
			$callbackBuffers[] = $this->FormatCallback("onenter" . $enter[0],$enter[1]);
		}
		foreach($this->befores as $before)
		{
			$callbackBuffers[] = $this->FormatCallback("onbefore" . $before[0],$before[1]);
		}
		foreach($this->afters as $after)
		{
			$callbackBuffers[] = $this->FormatCallback("onafter" . $after[0],$after[1]);
		}
		$callbackBuffer = implode(",\n    ",$callbackBuffers);
		$callbackBuffers = array();
		$heading = false;
		foreach($this->AllStates() as $state)
		{
			$missings = $this->AllEventsForFromState($state);
			if(count($missings) == 0)
			{
				if(!$heading)
				{
					$heading = true;
					$callbackBuffers = array();
					$callbackBuffers[] = <<<EODHeading

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
******************************************************************************************/
EODHeading
			;
				}
				$callbackBuffers[] = $this->FormatCallback("onleave" . $state,$this->defaultHandlerCode);
			}
		}
		$callbackBuffer .= implode(",\n    ",$callbackBuffers);
		$buffer = <<<EODStateMachineGenerate
StateMachine.create({
  initial : '{$initialState}',
  events :
    [
      {$eventBuffer}
    ],
  callbacks: {
    ${callbackBuffer}
  }
});
EODStateMachineGenerate
		;
		return $buffer;
	}
	public function Generate($initialEvent)
	{
		return $this->GenerateTransitions($initialEvent);
	}
}