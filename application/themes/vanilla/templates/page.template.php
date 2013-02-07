<!-- page.template.php -->
<?php
	foreach($page["sheets"] as $sheetName => $sheet)
	{
		echo ThemeEngine::Render($sheet) . "\n";
	}
?>
