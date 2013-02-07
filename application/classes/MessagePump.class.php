<?php
require_once "JQueryPlugin.class.php";
class MessagePump
{
	public function __construct()
	{
		//parent::__construct('messagepump');
	}
	public static function Snippet()
	{
		$plugin = new JQueryPlugin('messagepump');
		$plugin->AddMember
		("post", <<<EODMessagePost

	function()
	{
		if(!jQuery.isFunction(options))
			return;
        var jqthis = $(this);
        var data = jqthis.data("post");
        if(!data)
			return;
		jqthis.queue("post",options);
	}
EODMessagePost
		);
		
		$plugin->AddMember
		("send", <<<EODMessageSend

	function()
	{
		if(!jQuery.isFunction(options))
			return;
        var jqthis = $(this);
        var data = jqthis.data("send");
        if(!data)
			return;
		jqthis.queue("send",options);
	}
EODMessageSend
		);
		
		$plugin->AddMember
		("init", <<<EODMessagePumpInit
				
	function()
    {
        var jqthis = $(this)
        var data = jqthis.data("messagepump");
        if(!data)
        {
        	jqthis.data("messagepump",
            {
				target : jqthis,
				interval : 0 /* milliseconds */
			});
        }
		jqthis.data("send",[]);
		jqthis.data("post",[]);
    }
EODMessagePumpInit
		); //->AddMember()

		$plugin->AddMember
		("setinterval", <<<EODMessagePumpSetInterval
      function()
      {
        var opts = options || { delay : 0 };
        var jqthis = $(this)
				
        var data = jqthis.data("messagepump");
        if(typeof data.intervalID !== "undefined" && data.intervalID)
        {
          window.clearInterval(data.intervalID);
        }
        if(opts.delay === 0)
        {
          if(typeof data.intervalID !== undefined)
          {
            delete data.intervalID;
          }
          return;
        }
        data.intervalID = window.setInterval(function() {
				if(jqthis.queue("send").length > 0)
				{
					jqthis.dequeue("send");
					return;
				}
				if(jqthis.queue("post").length > 0)
				{
					jqthis.dequeue("post");
					return;
				}
        }, opts.delay);
      }
EODMessagePumpSetInterval
		);//->AddMember
		return $plugin->Snippet();
	}
} // class MessagePump
// MessagePump::Snippet();

?>