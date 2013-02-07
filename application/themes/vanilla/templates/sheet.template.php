<!-- sheet.teplate.php -->
<div <?php echo $sheet["#classes"]; ?> >
<h1 class='class-title'><?php echo $sheet["#title"]; ?></h1>
<?php
	if(isset($sheet['#layers']) && is_array($sheet['#layers']))
	{
		foreach($sheet['#layers'] as $layer)
		{
			echo ThemeEngine::Render($layer) . "\n";
		}
	}
?>
</div>

