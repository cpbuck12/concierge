<!-- block.template.php -->
<div <?php echo $block['#classes']; ?> >
<?php 
	if(isset($block["#content"]) && is_array($block["#content"]))
		foreach($block['#content'] as $content)
		{
			echo ThemeEngine::Render($content) . "\n";	
		}
?>		
</div>
