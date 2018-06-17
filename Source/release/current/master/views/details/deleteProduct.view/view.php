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
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;
use \RTL\Products\cProduct;

$productID = engine::getVar('pid');
$product = new cProduct($productID);
if (engine::isPost())
{
	// Create form error Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Delete product
	$status = $product->remove();

	// If there is an error in creating the folder, show it
	if ($status !== TRUE)
	{
		$err_header = appLiteral::get("products.details", "hd_deleteProduct");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, DOM::create("span", "Error deleting product."));
		return $errFormNtf->getReport();
	}
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE);
	
	// Add action to reload list
	$succFormNtf->addReportAction($type = "products.list.reload", $value = "");
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}


// Build the frame
$frame = new dialogFrame();
$title = appLiteral::get("products.details", "hd_deleteProduct");
$frame->build($title, "", FALSE)->engageApp("details/deleteProduct");
$form = $frame->getFormFactory();

// Header
$productInfo = $product->info();
$attr = array();
$attr['pname'] = $productInfo['title'];
$title = appLiteral::get("products.details", "lbl_sureDeleteProduct", $attr);
$hd = DOM::create("h3", $title);
$frame->append($hd);

// Person id
$input = $form->getInput($type = "hidden", $name = "pid", $value = $productID, $class = "", $autofocus = FALSE, $required = FALSE);
$frame->append($input);

// Return the report
return $frame->getFrame();
//#section_end#
?>