<?php

class ThemeHook
{
	// load includes
	// message the variables
	// either render template or call rendering function
	// ["template"].tpl.php
	public $info = array();
	public function __construct()
	{
		$this->info["includes"] = array();
//		$this->info["variables"] = array();
		$this->info["render element"] = "";
	}
	public function Preprocess(&$variables)
	{
		return; // optionally handled in derviced classes
	}
	public function Process(&$variables)
	{
		return; // optionally handled in derviced classes
	}
	protected function GenerateFunction($variables)
	{
		return ""; // optionally handled in derviced classes
	}
	public function Generate($variables)
	{// 1 function 2 theme implementation of hook (xxx.template.php) 
		if(isset($this->info["function"]) && $this->info["function"])
		{
			return $this->GenerateFunction($variables);
		}
		$this->Preprocess($variables);
		$this->Process($variables);
		// xxx.template.php
		$theme = Utilities::Theme();
		$result = Utilities::RenderTheme($theme, $this->info["type"], $variables);
		return $result;
	}
	static public function GetThemeHook($name)
	{
		$className = "ThemeHook_" . $name;
		if(class_exists($className))
			return new $className;
		$className = "ThemeHook_default_" . $name;
		if(class_exists($className))
			return new $className;
		return null;
	}
}
// non default classes are in __ROOT__/themes/*/template.php
// e.g. ThemeHook_html

class ThemeHook_default_html extends ThemeHook
{
	public function GenerateFunction($variables)
	{
		return "<html></html>";
	}
	public function __construct()
	{
		parent::__construct();
		$this->info["type"] = "html";
		$this->info["function"] = true;
		if(!isset($this->info["variables"]))
			$this->info["variables"] = array();
	}
}

class ThemeEngine
{
	static public function BuildPage()
	{
		$mainSheetmainLayerContent = array
		(
			'#name' => 'mainmenu',
			'#theme' => 'menu',
			'#type' => 'menu',
			'#content' => array
			(
				array
				(
					"#title" => "Database Maintenance",
					"#name" => "databasemaintenance",
					"#theme" => "menuitem",
					'#type' => "menuitem",
					"#submenu" => array
					(
						'#name' => 'databasemaintenancemenu',
						'#theme' => 'menu',
						'#type' => 'menu',
						'#content' => array
						(
							array
							(
								'#name' => 'loadfromconciergemenuitem',
								'#title' => 'Load file from Concierge Directories',
								'#theme' => 'menuitem',
								'#type' => 'menuitem'
							),
							array
							(
								'#name' => 'registernewpatient',
								'#title' => 'Register a new Concierge Patient',
								'#theme' => 'menuitem',
								'#type' => 'menuitem'
							),
							array
							(
								'#name' => 'browsedocuments',
								'#title' => 'Browse Documents',
								'#theme' => 'menuitem',
								'#type' => 'menuitem'
							)
						)
					),
					"#content" => array
					(
						array
						(
							"#name" => "submenuname",
							"#theme" => "menu",
							"#type" => "menu",
							"#title" => "Sub Menu Item Title"
						)
					)
				),
				array
				(
					"#name" => "createreport",
					"#theme" => "menuitem",
					"#type" => "menuitem",
					"#title" => "Create Report"
				),
				array
				(
					"#name" => "createwebsite",
					"#theme" => "menuitem",
					"#type" => "menuitem",
					"#title" => "Create Website"
				)
			)
		);
		$mainSheetmainLayer = array
		(
			"#name" => "mainlayer",
			"#theme" => "layer",
			"#type" => "layer",
			"#content" => array ( $mainSheetmainLayerContent )
		);
		$mainSheetLayers = array();
		$mainSheetLayers[] = $mainSheetmainLayer;
		$sheets = array();
		$mainSheet = array
		(
			"#name" => "main",
			"#title" => "Main Menu",
			"#theme" => "sheet",
			"#type" => "sheet",
			"#layers" => $mainSheetLayers
		);
		$loadfromconciergeSheetmainLayerContent = array
		(
			"utility" => array
			(
				"#id" => "hiddenmessagequeueelement",
				"#theme" => "hidden",
				"#type" => "hidden"
			),
			"first" => array
			(
				"#name" => "patient",
				"#theme" => "table",
				"#type" => "table"
			),
			"second" => array
			(
				"#name" => "filesystem",
				"#theme" => "table",
				"#type" => "table"
			),
			"last" => array
			(
				"#name" => "mainmenu",
				"#theme" => "button",
				"#type" => "button",
				"#content" => "Main Menu"
			)
		);
		$registerPatientSheetmainLayerContent = array
		(
			"first" => array
			(
				"#name" => "patient2",
				"#theme" => "table",
				"#type" => "table"
			),
			"last" => array
			(
				"#name" => "mainmenu2",
				"#theme" => "button",
				"#type" => "button",
				"#content" => "Main Menu"
			)
		);
		$browseDocumentsSheetmainLayerContent = array
		(
			"first" => array
			(
				"#name" => "patient3",
				"#theme" => "table",
				"#type" => "table"
			),
			"last" => array
			(
				"#name" => "mainmenu3",
				"#theme" => "button",
				"#type" => "button",
				"#content" => "Main Menu"
			)
		);
		$loadfromconciergeSheetmainLayer = array
		(
			"#name" => "mainlayer2",
			"#theme" => "layer",
			"#type" => "layer",
			"#content" => array($loadfromconciergeSheetmainLayerContent)
	    );
		$loadfromconciergeSheetLayers = array();
		$loadfromconciergeSheetLayers[] = $loadfromconciergeSheetmainLayer;
		$loadfromconciergeSheet = array ("#name" => "loadfromconciergesheet", "#title" => "Concierge Loader", "#theme" => "sheet" , "#type" => "sheet", "#layers" => $loadfromconciergeSheetLayers);
		
		$registerPatientSheetmainLayer = array
		(
			"#name" => "mainlayer",
			"#theme" => "layer",
			"#type" => "layer",
			"#content" => array($registerPatientSheetmainLayerContent)
	    ); 
		$registerPatientSheetLayers = array();
		$registerPatientSheetLayers[] = $registerPatientSheetmainLayer;
		$registerPatientSheet = array("#name" => "registerpatientsheet", '#title' => "Register New Patient", "#theme" => "sheet", "#type" => "sheet", "#layers" => $registerPatientSheetLayers);

		$browseDocumentsSheetmainLayer = array
		(
			"#name" => "mainlayer",
			"#theme" => "layer",
			"#type" => "layer",
			"#content" => array($browseDocumentsSheetmainLayerContent)
		);
		$browseDocumentsSheetLayers = array();
		$browseDocumentsSheetLayers[] = $browseDocumentsSheetmainLayer;
		$browseDocumentsSheet = array("#name" => "browsedocmentssheet", "#title" => "Browse Documents", "#theme" => "sheet", "#type" => "sheet", "#layers" => $browseDocumentsSheetLayers);
		
		$createreportSheet = array ("#name" => "createreport", "#title" => "Create Report", "#theme" => "sheet" , "#type" => "sheet");
		$sheets[] = $mainSheet;
		$sheets[] = $loadfromconciergeSheet;
		$sheets[] = $registerPatientSheet;
		$sheets[] = $browseDocumentsSheet;
		$sheets[] = $createreportSheet;
		$messagePumpPlugin = new MessagePump();
		$inlineCode = $messagePumpPlugin->Snippet(); 
		$result = array
		(
			"#theme" => "html",
			"#scripts_array" => array
			(
				array("name" => "jquery.js","weight" => 10),
				array("name" => "jquery-ui.js", "weight" => 20),
				array("name" => "jquery.dataTables.js", "weight" => 30),
				array("name" => "state-machine.js", "weight" => 40),
				array("name" => "ColReorderWithResize.js", "weight" => 50),
				array("name" => "code.js", "weight" => 60),
				array("name" => "machines.js", "weight" => 70),
				array("code" => $inlineCode, "weight" => 80)
			),
			"#styles_array" => array
			(
				array("name" => "jquery-ui.css", "weight" => 10),
				array("name" => "jquery-ui-1.9.2.custom.css", "weight" => 20),
				array("name" => "jquery.dataTables.css", "weight" => 30),
				array("name" => "main.css", "weight" => 40)
			),
			"#libraryfolders_array" => array
			(
				array("name" => "images")
			),
			"#libraries_array" => array
			(
					array("name" => "images")
			),
			"#classes_array" => array
			(
				"body-class"
			),
			"#content" => array
			(
				"#theme" => "page",
				"sheets" => $sheets
			)
			// "#page" => $sheets
		);
		return $result;
	}
	//http://api.drupal.org/api/drupal/includes%21common.inc/function/element_children/7
	static private function Children(&$elements)
	{
		$children = array();
		if(!is_array($elements) || !isset($elements))
		{
			$i = 5;
		}
		foreach($elements as $key => $value)
		{
			if ($key === '' || $key[0] !== '#')
			{
				$children[$key] = $value;
			}
		}
		return array_keys($children);
	}
	//http://api.drupal.org/api/drupal/includes%21common.inc/function/drupal_render/7
	static public function Render(&$elements)
	{
		if(empty($elements))
			return;
		if(!empty($elements["#printed"]))
			return;
		if (isset($elements['#markup']) && !isset($elements['#type']))
		{
    			$elements['#type'] = 'markup';
		}
		$children = self::Children($elements);
		
		if(!isset($elements['#children']))
		{
			$elements['#children'] = '';
		}
		if(isset($elements['#theme']))
		{
			global $themeEngine;
			$elements['#children'] = $themeEngine->Theme($elements['#theme'], $elements);
		}
		if($elements['#children'] == '')
		{
			foreach ($children as $key)
			{
				$elements['#children'] .= self::Render($elements[$key]);
			}
		}
		$output = $elements["#children"];
		$elements["#printed"] = true;
		return $output;
	}
	//http://api.drupal.org/api/drupal/includes%21theme.inc/function/theme/7
	// $theme is a string which names an item, not really the name of a theme
	public function Theme($theme,$variables)
	{
		$theme = ThemeHook::GetThemeHook($theme);
		if(!isset($theme))
			return ""; // don't know what else to do here
		if(!empty($theme->info["includes"]))
		{
			foreach($theme->info["includes"] as $include_file)
			{
				include_once __ROOT__ . "/" . $include_file;
			}
		}
		if(isset($variables["#theme"]))
		{
			//Utilities::LoadTheme($variables["#theme"]);
			$element = $variables;
			$variables = array();
			if(isset($theme->info["variables"]))
			{
				foreach(array_keys($theme->info["variables"]) as $name)
				{
					if(isset($element["#$name"]))
					{
						$variables[$name] = $element["#$name"];
					}
				}
			}
			else
			{
				$variables[$theme->info["render element"]] = $element;
			}
		}
		if(!empty($theme->info["variables"]))
		{
			$variables += $theme->info["variables"];
		}
		elseif(!empty($theme->info["render element"]))
		{
			$variables += array($theme->info["render element"] => array());
		}
		// TODO : preproc/proc stuff
		return $theme->Generate($variables);
	}
};
