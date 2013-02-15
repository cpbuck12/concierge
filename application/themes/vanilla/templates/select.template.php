
<!-- select.template.php -->

<?php
	if(isset($select['#title']))
	{
?><span><?php
		print $select['#title'];
?></span><?php
    }
?><select <?php print $select['#classes']; ?> >
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
</select>