<!-- layer.template.php -->
<div <?php echo $layer['#classes']; ?> >
<?php if(isset($layer['#title'])) { ?>
<div class='class-title'><?php echo $layer['#title']; ?></div>
<?php }
	if(isset($layer['#content']) && is_array($layer['#content']))
	{
		foreach($layer['#content'] as $content)
		{
			echo ThemeEngine::Render($content);
		}
	}
?>
</div>
