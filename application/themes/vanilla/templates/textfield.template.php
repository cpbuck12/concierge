
<!-- textfield.template.php -->
<?php
	if(isset($textfield['#title']))
	{
?><tr><td><?php
		print $textfield['#title'];
?></td><td><input type='text' <?php
		print $textfield['#classes'];
?>></input></td></tr><?php 
	}
?>
