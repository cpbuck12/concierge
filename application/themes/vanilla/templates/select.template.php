
<!-- select.template.php -->
<tr>
<td>
<?php
	if(isset($select['#title']))
	{
		print $select['#title'];
    }
?>
</td><td><select <?php print $select['#classes']; ?> >
<?php
	if(isset($select['#content']))
	{
		foreach($select['#content'] as $item)
		{
			$value = $item["#value"];
			$title = $item["#title"];
			print "<option value='$value'>$title</option>\n";
		}
	} 
?>
</select></td></tr>