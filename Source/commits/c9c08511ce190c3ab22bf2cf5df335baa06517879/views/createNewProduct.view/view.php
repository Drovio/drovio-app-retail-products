<?php
//#section#[header]
// Use Important Headers
use \API\Platform\importer;
use \API\Platform\engine;
use \Exception;

// Check Platform Existance
if (!defined('_RB_PLATFORM_')) throw new Exception("Platform is not defined!");

// Import DOM, HTML
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");

use \UI\Html\DOM;
use \UI\Html\HTML;

// Import application for initialization
importer::import("AEL", "Platform", "application");
use \AEL\Platform\application;

// Increase application's view loading depth
application::incLoadingDepth();

// Set Application ID
$appID = 55;

// Init Application and Application literal
application::init(55);
// Secure Importer
importer::secure(TRUE);

// Import SDK Packages
importer::import("AEL", "Literals");
importer::import("RTL", "Products");
importer::import("UI", "Apps");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");

// Import APP Packages
//#section_end#
//#section#[view]
use \AEL\Literals\appLiteral;
use \UI\Apps\APPContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\popups\popup;

use \RTL\Products\cProduct;

$appContent = new APPContent($appID);
$actionFactory = $appContent->getActionFactory();

if (engine::isPost())
{
	// Check if something is empty
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Check contact name
	if (empty($_POST['title']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = appLiteral::get("main.create", "lbl_title_placeholder");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Create new product
	$product = new cProduct();
	$status = $product->create($_POST['title']);
	
	// If there is an error in creating the library, show it
	if (!$status)
	{
		$err_header = appLiteral::get("main.create", "lbl_create");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, DOM::create("span", "Error creating product..."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE, $disposable = FALSE);
	
	// Add action to reload list
	$succFormNtf->addReportAction($type = "products.list.reload", $value = "");
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

// Build the application view content
$appContent->build("", "createNewProductDialog", TRUE);

$formContainer = HTML::select(".createNewProductDialog .formContainer")->item(0);
// Build form
$form = new simpleForm("");
$imageForm = $form->build($action = "", $defaultButtons = FALSE)->engageApp("createNewProduct")->get();
DOM::append($formContainer, $imageForm);

// Contact name
$ph = appLiteral::get("main.create", "lbl_title_placeholder", array(), FALSE);
$input = $form->getInput($type = "text", $name = "title", $value = "", $class = "bginp", $autofocus = TRUE, $required = TRUE);
DOM::attr($input, "placeholder", $ph);
$form->append($input);

$title = appLiteral::get("main.create", "lbl_create");
$create_btn = $form->getSubmitButton($title, $id = "btn_create", $name = "");
$form->append($create_btn);

// Create popup
$pp = new popup();
$pp->type($type = popup::TP_PERSISTENT, $toggle = FALSE);
$pp->background(TRUE);
$pp->build($appContent->get());

return $pp->getReport();
//#section_end#
?>