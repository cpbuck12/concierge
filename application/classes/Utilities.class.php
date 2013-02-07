<?php
class Utilities
{
	public static function BuildPage($variables)
	{
		return array
		(
		);		
	}
	public static function Themes()
	{
		$result = array();
		$dh = opendir(__ROOT__ . "/themes");
		while(($file = readdir($dh)) !== false)
		{
			if(!strcmp($file,".") || !strcmp($file,".."))
				continue;
			$fullFileName = __ROOT__ . "/themes/" . $file;
			if(filetype($fullFileName) === "dir")
			{
				$result[] = array("name" => $file, "path" => $fullFileName);
			}
		}
		return $result;
	}
	public static function DefaultTheme()
	{
		$themes = self::Themes();
		return $themes[0];
	}
	public static function Theme($name = null)
	{
		if(!isset($name))
			return self::DefaultTheme();
		$themes = self::Themes();
		foreach($themes as $theme)
			if($theme["name"] == $name)
				return $theme;
		return null;
	}
	public static function LoadTheme($themeName = null)
	{
		if(!isset($themeName))
			$theme = self::DefaultTheme();
		else
		{
			$themes = self::Themes();
			foreach($themes as $theme2)
			{
				if($theme["name"] == $themeName)
				{
					$theme = $theme2;
					break;
				}
			}
		}
		if(isset($theme))
		{
			define('__THEMEROOT__', $theme["path"]);
			include_once $theme["path"] . "/template.php";
		}
	}
	public static function SaveLibraryItem($fileName,$content)
	{
		$path = __THEMEROOT__ . "/library/" . $fileName;
		$f = fopen($path,"w");
		fwrite($f,$content);
		fclose($f);
	}
	public static function RenderTheme($theme,$hook,$variables)
	{
		return self::Render($theme["path"] . "/templates/" . $hook . ".template.php",$variables);
	}
	public static function Render($fileName, $variables=array())
	{
		extract($variables,EXTR_SKIP);
		ob_start();
		include($fileName);
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}
}