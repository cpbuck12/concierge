
<!-- date.template.php -->
<tr>
<td>
<?php
	if(isset($date['#title']))
	{
		print $date['#title'];
    }
?>
</td><td><input type='text' <?php print $date['#classes']; ?>></input></td>
</tr>