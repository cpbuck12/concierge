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
								'#name' => 'addfile',
								'#title' => 'Add file',
								'#theme' => 'menuitem',
								'#type' => 'menuitem'
							),
							array
							(
								'#name' => 'loadfromconciergemenuitem',
								'#title' => 'Load file from Concierge Directories',
								'#theme' => 'menuitem',
								'#type' => 'menuitem'
							),
							array
							(
								'#name' => 'addspecialtymenuitem',
								'#title' => 'Add a new specialty',
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
							),
							array
							(
								'#name' => 'managereleases',
								'#title' => 'Manage Releases',
								'#theme' => 'menuitem',
								'#type' => 'menuitem',
								'#submenu' => array
								(
									'#name' => 'managereleasesmenu',
									'#theme' => 'menu',
									'#type' => 'menu',
									'#content' => array
									(
										array
										(
											'#name' => 'listreleases',
											'#title' => 'List Releases',
											'#theme' => 'menuitem',
											'#type' => 'menuitem'
										),
										array
										(
											'#name' => 'addreleaserequest',
											'#title' => 'Add Release Request',
											'#theme' => 'menuitem',
											'#type' => 'menuitem'
										),
										array
										(
											'#name' => 'addreleaseresponse',
											'#title' => 'Add Release Response',
											'#theme' => 'menuitem',
											'#type' => 'menuitem'
										)
									)
								)
							),
							array
							(
								'#name' => 'managedoctors',
								'#title' => 'Manage Doctors',
								'#theme' => 'menuitem',
								'#type' => 'menuitem',
								'#submenu' => array
								(
									'#name' => 'managedoctorsmenu',
									'#theme' => 'menu',
									'#type' => 'menu',
									'#content' => array
									(
										array
										(
											'#name' => 'addnewdoctor',
											'#title' => 'Add New Doctor',
											'#theme' => 'menuitem',
											'#type' => 'menuitem'
										),
										array
										(
											'#name' => 'listdoctors',
											'#title' => 'List Doctors',
											'#theme' => 'menuitem',
											'#type' => 'menuitem'
										)
									)
								)
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
				array
				(
					"#name" => "mainmenu",
					"#theme" => "button",
					"#type" => "button",
					"#content" => "Main Menu"
				),
				array
				(
					"#name" => "loadfiles",
					"#theme" => "button",
					"#type" => "button",
					"#content" => "Load files"
				)
			)
		);
		$addPatientSheetdatabaseErrorDialogLayerContent = array
		(
			
		);
		$addPatientSheetmainLayerContent = array
		(
			"first" => array
			(
				"#name" => "patientsondisk",
				"#theme" => "table",
				"#type" => "table"
			),
			"second" => array
			(
				"#name" => "patientsindb",
				"#theme" => "table",
				"#type" => "table"
			),
			"third" => array
			(
				'#name' => 'newpatientfields',
				'#theme' => 'fieldset',
				'#type' => 'fieldset',
				'#legend' => 'New Patient',
				'#content' => array
				(
					array
					(
						'#name' => 'firstname',
						'#theme' => 'textfield',
						'#type' => 'textfield',
						'#title' => 'First Name'
					),
					array
					(
						'#name' => 'lastname',
						'#theme' => 'textfield',
						'#type' => 'textfield',
						'#title' => 'Last Name'
					),
					array
					(
						'#name' => 'dob',
						'#theme' => 'date',
						'#type' => 'date',
						'#title' => 'Date of Birth'
					),
					array
					(
						'#name' => 'gender',
						'#theme' => 'select',
						'#type' => 'select',
						'#title' => "Gender",
						"#content" => array
						(
							array
							(
								'#title' => 'Female',
								'#value' => 'F'
							),
							array
							(
								'#title' => 'Male',
								'#value' => 'M'
							)
						)
					),
					array
					(
							'#name' => 'emergencycontact',
							'#theme' => 'textfield',
							'#type' => 'textfield',
							'#title' => 'Emergency Contact'
					)
				)
			),
			"fourth" => array
			(
				'#name' => 'addpatient',
				"#theme" => "button",
				"#type" => "button",
				"#content" => "Add Patient"
			),
			"last" => array
			(
				"#name" => "mainmenu",
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
		
		$loadfileSheetmainLayerContent = array
		(
			'first' => array
			(
				'#name' => 'loadfilefields',
				'#theme' => 'fieldset',
				'#type' => 'fieldset',
				'#legend' => 'File Information',
				'#content' => array
				(
					array
					(
						'#name' => 'path',
						'#theme' => 'textfield',
						'#type' => 'textfield',
						'#title' => 'Path'
					),
					array
					(
						'#name' => 'name',
						'#theme' => 'textfield',
						'#type' => 'textfield',
						'#title' => 'Name'
					)
				)
			),
			'second' => array
			(
				array
				(
					'#name' => 'addfile',
					'#theme' => 'button',
					'#type' => 'button',
					'#content' => 'Add'
				)
			)
		);
		$loadfileSheetmainLayer = array
		(
			'#name' => 'loadfilemainlayer',
			'#theme' => 'layer',
			'#type' => 'layer',
			'#content' => array($loadfileSheetmainLayerContent)
		);
		$loadfileSheetLayers= array();
		$loadfileSheetLayers[] = $loadfileSheetmainLayer;
		$loadfileSheet = array('#name' => 'loadfilesheet', '#title' => 'Load File','#theme' => 'sheet', '#type' => 'sheet', '#layers' => $loadfileSheetLayers);
		
		$addPatientSheetmainLayer = array
		(
			"#name" => "mainlayer",
			"#theme" => "layer",
			"#type" => "layer",
			"#content" => array($addPatientSheetmainLayerContent)
	    ); 
		$addPatientSheetLayers = array();
		$addPatientSheetLayers[] = $addPatientSheetmainLayer;
		$addPatientSheet = array("#name" => "addpatientsheet", '#title' => "Add New Patient", "#theme" => "sheet", "#type" => "sheet", "#layers" => $addPatientSheetLayers);

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
		
		$addDoctorSheetmainLayerContent = array 
		(
			"first" => array
			(
				'#name' => 'doctorlist',
				'#theme' => 'table',
				'#type' => 'table'
			),
			"second" => array
			(
				'#name' => 'doctoraddfields',
				'#theme' => 'fieldset',
				'#type' => 'fieldset',
				'#legend' => 'Doctor Information',
				'#content' => array
				(
					array
					(
						'#name' => 'firstname',
						'#theme' => 'textfield',
						'#type' => 'textfield',
						'#title' => 'First Name'
					),
					array
					(
						'#name' => 'lastname',
						'#theme' => 'textfield',
						'#type' => 'textfield',
						'#title' => 'Last Name'
					),
					array
					(
						'#name' => 'shortname',
						'#theme' => 'textfield',
						'#type' => 'textfield',
						'#title' => 'Short Name'
					),
					array
					(
						'#name' => 'address1',
						'#theme' => 'textfield',
						'#type' => 'textfield',
						'#title' => 'Address Line 1'
					),
					array
					(
						'#name' => 'address2',
						'#theme' => 'textfield',
						'#type' => 'textfield',
						'#title' => 'Address Line 2'
					),
					array
					(
						'#name' => 'address3',
						'#theme' => 'textfield',
						'#type' => 'textfield',
						'#title' => 'Address Line 3'
					),
					array
					(
						'#name' => 'city',
						'#theme' => 'textfield',
						'#type' => 'textfield',
						'#title' => 'City'
					),
					array
					(
						'#name' => 'locality1',
						'#theme' => 'textfield',
						'#type' => 'textfield',
						'#title' => 'Locality Line 1'
					),
					array
					(
						'#name' => 'locality2',
						'#theme' => 'textfield',
						'#type' => 'textfield',
						'#title' => 'Locality Line 2'
					),
					array
					(
						'#name' => 'postalcode',
						'#theme' => 'textfield',
						'#type' => 'textfield',
						'#title' => 'Postal Code'
					),
					array
					(
						'#name' => 'country',
						'#theme' => 'textfield',
						'#type' => 'textfield',
						'#title' => 'Country'
					),
					array
					(
						'#name' => 'voice',
						'#theme' => 'textfield',
						'#type' => 'textfield',
						'#title' => 'Voice'
					),
					array
					(
						'#name' => 'fax',
						'#theme' => 'textfield',
						'#type' => 'textfield',
						'#title' => 'Fax'
					),
					array
					(
						'#name' => 'email',
						'#theme' => 'textfield',
						'#type' => 'textfield',
						'#title' => 'E-mail'
					),
					array
					(
						'#name' => 'contact',
						'#theme' => 'textfield',
						'#type' => 'textfield',
						'#title' => 'Contact Person'
					)
				)
			),
			"third" => array
			(
				array
				(
					'#name' => 'mainmenu',
					'#theme' => 'button',
					'#type' => 'button',
					'#content' => 'Main Menu'					
				),
				array
				(
					'#name' => 'adddoctor',
					'#theme' => 'button',
					'#type' => 'button',
					'#content' => 'Add Doctor'
				)
			)
		);
		
		$addSpecialtySheetmainLayerContent = array 
		(
			"first" => array
			(
					"#name" => "specialtiesindb",
					"#theme" => "table",
					"#type" => "table"
			),
			"second" => array
			(
				'#name' => 'specialtyaddfields',
				'#theme' => 'fieldset',
				'#type' => 'fieldset',
				'#legend' => 'Specialty Information',
				'#content' => array
				(
					array
					(
						'#name' => 'specialty',
						'#theme' => 'textfield',
						'#type' => 'textfield',
						'#title' => 'Specialty'
					),
					array
					(
						'#name' => 'subspecialty',
						'#theme' => 'textfield',
						'#type' => 'textfield',
						'#title' => 'Subspecialty'
					)
				)
			),
			"third" => array
			(
				array
				(
					'#name' => 'mainmenu',
					'#theme' => 'button',
					'#type' => 'button',
					'#content' => 'Main Menu'					
				),
				array
				(
					'#name' => 'addspecialty',
					'#theme' => 'button',
					'#type' => 'button',
					'#content' => 'Add Specialty'
				)
			)
				
		);
		$addDoctorSheetmainLayer  = array
		(
			'#name' => "adddoctormainlayer",
			"#theme" => "layer",
			"#type" => "layer",
			"#content" => array($addDoctorSheetmainLayerContent)				
		);
		$addDoctorSheetLayers = array();
		$addDoctorSheetLayers[] = $addDoctorSheetmainLayer;
		$addDoctorSheet = array ("#name" => "adddoctorsheet", "#title" => "Add New Doctor", "#theme" => "sheet", "#type" => "sheet", '#layers' => $addDoctorSheetLayers);

		
		
		
		$updateDoctorSheetmainLayerContent = array
		(
			"block0" => array
			(
				"#name" => "doctorlistblock",
				"#theme" => "block",
				"#type" => "block",
				"#content" => array
				(
					"first" => array
					(
							'#name' => 'doctorlist',
							'#theme' => 'table',
							'#type' => 'table'
					)
				)
			),
			"block1" => array
			(
				"#name" => "doctorfieldsblock",
				"#theme" => "block",
				"#type" => "block",
				"#content" => array
				(
					"second" => array
					(
						'#name' => 'doctorfields',
						'#theme' => 'fieldset',
						'#type' => 'fieldset',
						'#legend' => 'Edit',
						'#content' => array
						(
							array
							(
								'#name' => 'firstname',
								'#theme' => 'textfield',
								'#type' => 'textfield',
								'#title' => 'First Name'
							),
							array
							(
								'#name' => 'lastname',
								'#theme' => 'textfield',
								'#type' => 'textfield',
								'#title' => 'Last Name'
							),
							array
							(
								'#name' => 'address1',
								'#theme' => 'textfield',
								'#type' => 'textfield',
								'#title' => 'Address1'
							),
							array
							(
								'#name' => 'address2',
								'#theme' => 'textfield',
								'#type' => 'textfield',
								'#title' => 'Address2'
							),
							array
							(
								'#name' => 'address3',
								'#theme' => 'textfield',
								'#type' => 'textfield',
								'#title' => 'Address3'
							),
							array
							(
								'#name' => 'city',
								'#theme' => 'textfield',
								'#type' => 'textfield',
								'#title' => 'City'
							),
							array
							(
								'#name' => 'contactperson',
								'#theme' => 'textfield',
								'#type' => 'textfield',
								'#title' => 'Contact Person'
							),
							array
							(
								'#name' => 'country',
								'#theme' => 'textfield',
								'#type' => 'textfield',
								'#title' => 'Country'
							),
							array
							(
								'#name' => 'email',
								'#theme' => 'textfield',
								'#type' => 'textfield',
								'#title' => 'E-mail'
							),
							array
							(
								'#name' => 'fax',
								'#theme' => 'textfield',
								'#type' => 'textfield',
								'#title' => 'Fax'
							),
							array
							(
								'#name' => 'id',
								'#theme' => 'textfield',
								'#type' => 'textfield',
								'#title' => 'Id'
							),
							array
							(
								'#name' => 'locality1',
								'#theme' => 'textfield',
								'#type' => 'textfield',
								'#title' => 'Locality 1'
							),
							array
							(
								'#name' => 'locality2',
								'#theme' => 'textfield',
								'#type' => 'textfield',
								'#title' => 'Locality 2'
							),
							array
							(
								'#name' => 'postalcode',
								'#theme' => 'textfield',
								'#type' => 'textfield',
								'#title' => 'Postal Code'
							),
							array
							(
								'#name' => 'shortname',
								'#theme' => 'textfield',
								'#type' => 'textfield',
								'#title' => 'Short Name'
							),
							array
							(
								'#name' => 'telephone',
								'#theme' => 'textfield',
								'#type' => 'textfield',
								'#title' => 'Telephone'
							)
						)
					)
				)
			),
			"last" => array
			(
				array
				(
					"#name" => "update",
					"#theme" => "button",
					"#type" => "button",
					"#content" => "Update"
				),
				array
				(
					"#name" => "cancel",
					"#theme" => "button",
					"#type" => "button",
					"#content" => "Cancel"
				)
			)
		);
		
		$updateDoctorSheetmainLayer  = array
		(
				'#name' => "updatedoctormainlayer",
				"#theme" => "layer",
				"#type" => "layer",
				"#content" => array($updateDoctorSheetmainLayerContent)
		);
		$updateDoctorSheetLayers = array();
		$updateDoctorSheetLayers[] = $updateDoctorSheetmainLayer;
		$updateDoctorSheet = array ("#name" => "updatedoctorsheet", "#title" => "Update Doctor", "#theme" => "sheet", "#type" => "sheet", '#layers' => $updateDoctorSheetLayers);
		
		
		$addSpecialtySheetmainLayer = array
		(
			"#name" => "addspecialtymainlayer",
			"#theme" => "layer",
			"#content" => array($addSpecialtySheetmainLayerContent)
		);
		$addSpecialtySheetLayers = array();
		$addSpecialtySheetLayers[] = $addSpecialtySheetmainLayer;
		$addSpecialtySheet = array("#name" => "addspecialtysheet", "#title" => "Add New Specialty", "#theme" => "sheet", "#type" => "sheet", '#layers' => $addSpecialtySheetLayers);
		
		$folderBrowserSheetmainLayerContent = array
		(
			"block0" => array
			(
				"#name" => "folderlabel",
				"#theme" => "label",
				"#type" => "label",
				"#text" => "FOLDER"
			),
			"block1" => array
			(
				"#name" => "volumesblock",
				"#theme" => "block",
				"#type" => "block",
				"#content" => array
				(
					"volumes" => array
					(
						"#name" => "volumes",
						"#theme" => "table",
						"#type" => "table"
					)
				)
			),
			"block2" => array
			(
				"#name" => "foldersblock",
				"#theme" => "block",
				"#type" => "block",
				"#content" => array
				(
					"folders" => array
					(
						"#name" => "folders",
						"#theme" => "table",
						"#type" => "table"
					)
				)
					
			),
			"files" => array
			(
					"#name" => "files",
					"#theme" => "table",
					"#type" => "table"
			),
			"buttons" => array
			(
				"open" => array
				(
					'#name' => 'open',
					'#theme' => 'button',
					'#type' => 'button',
					'#content' => 'Open'
				),
				"ok" => array
				(
					'#name' => 'ok',
					'#theme' => 'button',
					'#type' => 'button',
					'#content' => 'Ok'
				),
				"cancel" => array
				(
					'#name' => 'cancel',
					'#theme' => 'button',
					'#type' => 'button',
					'#content' => 'Cancel'
				)
			)
		);
		$folderBrowserSheetmainLayer = array
		(
			"#name" => "folderbrowsermainlayer",
			"#theme" => "layer",
			"#content" => array($folderBrowserSheetmainLayerContent)
		);
		$folderBrowserSheetLayers = array();
		$folderBrowserSheetLayers[] = $folderBrowserSheetmainLayer;
		$folderBrowserSheet = array("#name" => "folderbrowsersheet", "#title" => "Folder Browser", "#theme" => "sheet", "#type" => "sheet", '#layers' => $folderBrowserSheetLayers);

		$documentBrowserSheetmainLayerContent = array
		(
			"docs" => array
			(
				"#name" => "documents",
				"#theme" => "table",
				"#type" => "table"
			),
			"view" => array
			(
				'#name' => 'view',
				'#theme' => 'button',
				'#type' => 'button',
				'#content' => 'View'
			),
			"delete" => array
			(
				'#name' => 'delete',
				'#theme' => 'button',
				'#type' => 'button',
				'#content' => 'Delete'
			),
			"mainmenu" => array
			(
				'#name' => 'mainmenu',
				'#theme' => 'button',
				'#type' => 'button',
				'#content' => 'Main Menu'
			)
		);
		$documentBrowserSheetmainLayer = array
		(
				"#name" => "documentbrowsermainlayer",
				"#theme" => "layer",
				"#content" => array($documentBrowserSheetmainLayerContent)
		);		
		$documentBrowserSheetLayers = array();
		$documentBrowserSheetLayers[] = $documentBrowserSheetmainLayer;		
		$documentBrowserSheet = array("#name" => "documentbrowsersheet", "#title" => "Document Browser", "#theme" => "sheet", "#type" => "sheet", '#layers' => $documentBrowserSheetLayers);
		
		$dialogsSheetmainLayerContent = array
		(
			"messagebox" => array
			(
				"#name" => "messagebox",
				"#theme" => "block",
				"#type" => "block",
				"#content" => array
				(
					"message" => array
					(
						"#name" => "message",
						"#theme" => "label",
						"#type" => "label",
						"#text" => "message"
					)
				)
			)
		);
		$dialogsSheetmainLayer = array
		(
			"#name" => "dialogsmainlayer",
			"#theme" => "layer",
			"#content" => array($dialogsSheetmainLayerContent)
		);
		$dialogsSheetLayers = array();
		$dialogsSheetLayers[] = $dialogsSheetmainLayer;
		$dialogsSheet = array("#name" => "dialogssheet","#title" => "This should never been seen","#theme" => "sheet", "#type" => "sheet", "#layers" => $dialogsSheetLayers);
		$sheets[] = $mainSheet;
		$sheets[] = $loadfromconciergeSheet;
		$sheets[] = $addPatientSheet;
		$sheets[] = $browseDocumentsSheet;
		$sheets[] = $createreportSheet;
		$sheets[] = $addDoctorSheet;
		$sheets[] = $updateDoctorSheet;
		$sheets[] = $addSpecialtySheet;
		$sheets[] = $folderBrowserSheet;
		$sheets[] = $documentBrowserSheet;
		$sheets[] = $dialogsSheet;
		$sheets[] = $loadfileSheet;
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
				array("name" => "TableTools.js", "weight" => 53),
				array("name" => "ZeroClipboard.js", "weight" => 56),
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
