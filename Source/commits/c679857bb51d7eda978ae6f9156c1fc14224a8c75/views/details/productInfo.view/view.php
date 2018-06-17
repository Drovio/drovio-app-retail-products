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
importer::import("RTL", "Products");
importer::import("UI", "Apps");

// Import APP Packages
//#section_end#
//#section#[view]
use \RTL\Products\cProduct;
use \RTL\Products\cProductCodeManager;
use \RTL\Products\cProductPrice;
use \RTL\Products\info\cProductInfo;
use \UI\Apps\APPContent;

// Create Application Content
$appContent = new APPContent($appID);
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "productInfoContainer", TRUE);
$viewContainer = HTML::select(".productInfo .viewContainer")->item(0);

// Get product id to show detail for
$productID = engine::getVar("pid");

// List all product codes
$codeManager = new cProductCodeManager($productID);
$codes = $codeManager->getAllCodes();
foreach ($codes as $codeInfo)
{
	$infoRow = getInfoRow("code", $codeInfo['code']);
	DOM::append($viewContainer, $infoRow);
}

// List all product prices
$priceManager = new cProductPrice($productID);
$prices = $priceManager->getAllPrices();
foreach ($prices as $priceInfo)
{
	$infoRow = getInfoRow("price", $priceInfo['price']);
	DOM::append($viewContainer, $infoRow);
}

// List all product info
//$prdInfo = new cProductInfo($productID);
//$productInfo = $prdInfo->get();
foreach ($productInfo as $infoID => $infoValues)
{
	//$infoRow = getInfoRow("phone", $phoneInfo['phone']);
	//DOM::append($viewContainer, $infoRow);
}

// Add action to edit button
$editButton = HTML::select(".productInfo .edit")->item(0);
$attr = array();
$attr['pid'] = $productID;
$actionFactory->setAction($editButton, $viewName = "details/editProductInfo", $holder = ".productInfoContainer .editFormContainer", $attr, $loading = TRUE);

// Action to switch to details view
$appContent->addReportAction($name = "listviewer.switchto.details");

// Return output
return $appContent->getReport();

function getInfoRow($type, $value)
{
	$infoRow = DOM::create("div", "", "", "infoRow");
	HTML::addClass($infoRow, $type);
	
	// Create ico
	$ico = DOM::create("div", "", "", "ico");
	DOM::append($infoRow, $ico);
	
	$value = DOM::create("div", $value, "", "ivalue");
	DOM::append($infoRow, $value);
	
	return $infoRow;
}
//#section_end#
?>