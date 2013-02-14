<!-- fieldset.template.php -->

<table <?php print $fieldset['#classes'] ?>>
<caption><?php print $fieldset['#legend']; ?></caption>
<thead>
	<tr><th>Item</th><th>Value</th></tr>
</thead>
<tbody>
<?php 
	foreach($fieldset['#content'] as $name => $item)
	{
		echo ThemeEngine::Render($item) . "\n";
	}
?>
</tbody>	
</table>
