<!-- label.template.php -->
<div <?php echo $label['#classes']; ?> ><?php 
	if(isset($label["#text"]) && !is_array($label["#text"]))
	{
		echo $label["#text"];
	}
?></div>
