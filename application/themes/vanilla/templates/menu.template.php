<!-- menu.template.php -->
<ul <?php echo $menu['#classes']; ?> >
<?php
	if(isset($menu["#content"]) && is_array($menu["#content"]))
		foreach($menu['#content'] as $content)
		{
			echo ThemeEngine::Render($content) . "\n";	
		} 
?>
</ul>

