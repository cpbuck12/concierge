<html>
<head>
<title><?php print $head_title; ?></title>
<?php print $styles; ?>
<?php print $scripts; ?>
</head>
<body <?php print $classes; ?> <?php print $attributes; ?> > 
<?php print ThemeEngine::Render($page); ?>
</body>
</html>

<?php
	foreach($scripts_array as $script)
	{
		if(!isset($script["name"]))
			continue;
		$from = __THEMEROOT__ . "/library/" . $script["name"];
		$to = __PUBLIC__ . "/library/" . $script["name"];
		copy($from,$to);
	}
	foreach($libraries_array as $library)
	{
		if(!isset($library["name"]))
			continue;
		$from = __THEMEROOT__ . "/library/" . $library["name"];
		$to = __PUBLIC__ . "/library/" . $library["name"];
		@exec("xcopy \"$from\" \"$to\" /E/I/Y");
	} 
?>