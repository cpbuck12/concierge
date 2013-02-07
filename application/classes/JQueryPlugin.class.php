<?php
class JQueryPlugin
{
	public $Name;
	public $functions = array();
	public $setterFunctions = array();
	function __construct($Name)
	{
		$this->Name = $Name;
	}
	function AddMember($Name,$Source)
	{
		$this->functions[$Name] = $Source;
	}
	function AddGetter($Name,$Source)
	{
		$this->setterFunctions[$Name] = $Source;
	}
	function Snippet()
	{
		$funcs = array();
		foreach($this->functions as $key => $value)
		{
			$funcs[] = <<<EODJQuerySnippetMethods
    {$key} : function(options)
    {
      return this.each(
{$value});
    }
EODJQuerySnippetMethods
			;
		} // foreach($this->functions as $key => $value)
		$setterFuncs = array();
		foreach($this->setterFunctions as $key => $value)
		{
			$setterFuncs[] = <<<EODJQuerySnippetSetterMethods
    {$key} : function(options)
    {
      var func = ${value};
      return func(options);
    }
EODJQuerySnippetSetterMethods
			;
		}
		$allFuncs = implode(", // end of method and start of next method \n",$funcs + $setterFuncs);
		$fullText = <<<EODJQueryPluginSnippet
/* Begin snippet for JQueryPlugin({$this->Name}) */
(function($)  {
  var methods =
  {
    // start of methods
$allFuncs
    // end of methods
  };
  $.fn.{$this->Name} = function(method)
  {
    if ( methods[method] )
    {
      return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
    }
    else if ( typeof method === 'object' || ! method )
    {
      return methods.init.apply( this, arguments );
    }
    else
    {
      $.error( 'Method ' +  method + ' does not exist on jQueryPlugin' );
    }
  };
})(jQuery);
/* End snippet for JQueryPlugin({$this->Name}) */
EODJQueryPluginSnippet
;
		return $fullText;
	} // function Snippet
} // class JQueryPlugin

?>
