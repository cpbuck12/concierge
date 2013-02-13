
<!-- textfield.template.php -->
<?php
	if(isset($textfield['#title']))
	{
?><span><?php
		print $textfield['#title'];
?></span><?php
    }
?><input type='text' <?php print $textfield['#classes']; ?>></input>
