
<!-- date.template.php -->

<?php
	if(isset($date['#title']))
	{
?><span><?php
		print $date['#title'];
?></span><?php
    }
?><input type='text' <?php print $date['#classes']; ?>></input>
