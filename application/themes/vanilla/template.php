<?php

class ThemeHook_hidden extends ThemeHook
{
	public function __construct()
	{
		parent::__construct();
		$this->info["type"] = "messagepump";
		$this->info["function"] = true;
		$this->info["variables"] = array("id" => "");
	}
	public function GenerateFunction($variables)
	{
		return "<div class='hidden' " . (isset($variables["id"]) ? ( "id='" . $variables["id"] . "'></div>" ) : ( "></div>" ));
	}
}

class ThemeHook_widget extends ThemeHook
{
	public function __construct()
	{
		parent::__construct();
	}
}

class ThemeHook_table extends ThemeHook_widget
{
	public function __construct()
	{
		parent::__construct();
		$this->info["type"] = $this->info["render element"] = "table";
	}
	public function Preprocess(&$variables)
	{
		$variables['table']['#classes_array'][] = 'class-table';
		$variables['table']['#classes_array'][] = 'class-id-' . $variables['table']['#name'];
		$variables['table']["#classes"] = "class='" . implode(" ",$variables['table']["#classes_array"]) . "'";
	}
}

class ThemeHook_button extends ThemeHook_widget
{
	public function __construct()
	{
		parent::__construct();
		$this->info["type"] = $this->info["render element"] = "button";
	}
	public function Preprocess(&$variables)
	{
		$variables['button']['#classes_array'][] = 'class-button';
		$variables['button']['#classes_array'][] = 'class-id-' . $variables['button']['#name'];
		$variables['button']["#classes"] = "class='" . implode(" ",$variables['button']["#classes_array"]) . "'";
	}
}


class ThemeHook_menu extends ThemeHook_widget
{
	public function __construct()
	{
		parent::__construct();
		$this->info["type"] = $this->info["render element"] = "menu";
	}
	public function Preprocess(&$variables)
	{
		$variables['menu']['#classes_array'][] = 'class-menu';
		$variables['menu']['#classes_array'][] = 'class-id-' . $variables['menu']['#name'];
		$variables['menu']["#classes"] = "class='" . implode(" ",$variables['menu']["#classes_array"]) . "'";
	}
}


class ThemeHook_menuitem extends ThemeHook_widget
{
	public function __construct()
	{
		parent::__construct();
		$this->info["type"] = $this->info["render element"] = "menuitem";
	}
	public function Preprocess(&$variables)
	{
		$variables['menuitem']['#classes_array'][] = 'class-menu-item';
		$variables['menuitem']['#classes_array'][] = 'class-id-' . $variables['menuitem']['#name'];
		$variables['menuitem']["#classes"] = "class='" . implode(" ",$variables['menuitem']["#classes_array"]) . "'";
	}
}

class ThemeHook_layer extends ThemeHook
{
	public function __construct()
	{
		parent::__construct();
		$this->info["type"] = $this->info["render element"] = "layer";
	}
	public function Preprocess(&$variables)
	{
		$variables['layer']['#classes_array'][] = 'class-initally-hidden';
		$variables['layer']['#classes_array'][] = 'class-layer';
		$variables['layer']['#classes_array'][] = 'class-id-' . $variables['layer']['#name'];
		$variables['layer']["#classes"] = "class='" . implode(" ",$variables['layer']["#classes_array"]) . "'";
	}
}

class ThemeHook_sheet extends ThemeHook
{
	public function __construct()
	{
		parent::__construct();
		$this->info["type"] = $this->info["render element"] = "sheet";
	}
	public function Preprocess(&$variables)
	{
		$variables['sheet']['#classes_array'][] = 'class-initially-hidden';
		$variables['sheet']['#classes_array'][] = 'class-sheet';
		$variables['sheet']['#classes_array'][] = 'class-id-' . $variables['sheet']['#name'];
		$variables['sheet']["#classes"] = "class='" . implode(" ",$variables['sheet']["#classes_array"]) . "'";
	}
}

class ThemeHook_page extends ThemeHook
{
	public function __construct()
	{
		parent::__construct();
		$this->info["type"] = "page";
//		if(!isset($this->info["variables"]))
//		{
//			$this->info["variables"] = array();
//		}
		//$this->info["function"] = true;
		//$this->info["variables"]["content"] = array();
		$this->info["render element"] = "page";
	}/*
	public function Process(&$variables)
	{
		foreach($variables['page']['sheets'] as $name => $sheet)
		{
			if(!isset($sheet['#name']))
				$variables['page']['sheets'][$name]['#name'] = $name;
		}
	}*/
}

class ThemeHook_html extends ThemeHook
{
	public function GenerateFunction($variables)
	{
		return "<html></html>";
	}
	public function Process(&$variables)
	{
		foreach($variables["styles_array"] as $style)
		{
			$from = __THEMEROOT__ . "/library/" . $style["name"];
			$to = __PUBLIC__ . "/library/" . $style["name"];
			copy($from,$to);
		}
		foreach($variables["libraryfolders_array"] as $folder)
		{
			$from = __THEMEROOT__ . "/library/" . $folder["name"];
			$to = __PUBLIC__ . "/library/" . $folder["name"];
			$command = "xcopy $from $to";
			$command = str_replace("/","\\",$command);
			$command .= " /e/i/y/q";
			exec($command);
		}
	}
	public function Preprocess(&$variables)
	{/*
		$variables["head"] = "\n<" . "!-- head -->\n";
		$variables["head_title"] = "TITLE!!!";
		$variables["styles"] = "\n<" . "!-- styles -->\n";
		$variables["scripts"] = "\n<" . "!-- scripts -->\n";
		$variables["page"] = "\n<" . "!-- page -->\n";
		$variables["classes"] = "\n<" . "!-- classes -->\n";
		$variables["attributes"] = "\n<" . "!-- attributes -->\n";*/
		$variables["head_title"] = "Concierge Administration";
		if(isset($variables["scripts_array"]) && !isset($variables["scripts"]))
		{
			$acc = "\r\n<!-- scripts start here -->\r\n";
			foreach($variables["scripts_array"] as $entry)
			{
				if(isset($entry["name"]))
				{
					$acc .= "<script type='text/javascript' src='library/" . $entry["name"] . "'></script>\r\n";
				}
				else if(isset($entry["code"]))
				{
					$acc .= "<script type='text/javascript'>\n" . $entry["code"] . "\n</script>\r\n";
				}
			}
			$acc .= "<!-- scripts stop here -->\r\n";
			$variables["scripts"] = $acc;
		}
		else
			$variables["scripts"] = "";
		if(isset($variables["styles_array"]) && !isset($variables["styles"]))
		{
			$acc = "<!-- styles start here -->\r\n";
			$acc .= "<style type='text/css'>\r\n";
			foreach($variables["styles_array"] as $entry)
			{
				$acc .= "@import 'library/" . $entry["name"] . "';\r\n";
			}
			$acc .= "</style>\r\n";
			$acc .= "<!-- styles stop here -->\r\n";
			$variables["styles"] = $acc;
		}
		else
			$variables["styles"] = "\r\n<!-- no styles today -->\r\n";
		if(isset($variables["attributes_array"]) && !isset($variables["attributes"]))
		{
			$acc = "";
			foreach($variables["attributes_array"] as $key => $val)
			{
				$acc .= " $key='$val'";
			}
			$variables["attributes"] = $acc;
		}
		else 
			$variables["attributes"] = "";
		if(isset($variables["classes_array"]) && !isset($variables["classes"]))
		{
			$acc = "";
			foreach($variables["attributes_array"] as $className)
			{
				$acc .= " $className";
			}
			$variables["classes"] = "class='" . $acc . "'";
		}
		else 
			$variables["classes"] = "";
		global $themeEngine;
		$variables["page"] = $variables["content"]; // $themeEngine->Theme("page", $variables["content"]);
		$variables["themeroot"] = __THEMEROOT__;
	}
	public function __construct()
	{
		parent::__construct();
		$this->info["type"] = "html";
		if(!isset($this->info["variables"]))
			$this->info["variables"] = array();
		//$this->info["function"] = true;
		$this->info["variables"]["scripts_array"] = array();
		$this->info["variables"]["styles_array"] = array();
		$this->info["variables"]["attributes_array"] = array();
		$this->info["variables"]["libraryfolders_array"] = array();
		$this->info["variables"]["content"] = array();
		$this->info["variables"]["page"] = array();
	}
}
