<!-- menuitem.template.php -->
<li <?php echo $menuitem['#classes']; ?> >
<a href='#'><div><?php
	if(isset($menuitem["#title"]))
	{
		echo $menuitem["#title"];
	}
?></div></a><?php
if(isset($menuitem["#submenu"]) && is_array($menuitem["#submenu"]))
{
	echo ThemeEngine::Render($menuitem['#submenu']);
}
?>
</li>

