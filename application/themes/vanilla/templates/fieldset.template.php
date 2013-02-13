
<!-- fieldset.template.php -->
<fieldset <?php print $fieldset['#classes'];?> >
	<legend><?php print $fieldset['#legend']; ?></legend>
<?php
	foreach($fieldset['#content'] as $name => $item)
	{
		echo ThemeEngine::Render($item) . "\n";
	}
?>
</fieldset>
