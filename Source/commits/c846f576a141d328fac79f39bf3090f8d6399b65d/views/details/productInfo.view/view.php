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
importer::import("RTL", "Finances");
importer::import("RTL", "Products");
importer::import("UI", "Apps");

// Import APP Packages
//#section_end#
//#section#[view]
use \UI\Apps\APPContent;
use \RTL\Finances\taxes;
use \RTL\Products\cProduct;
use \RTL\Products\cProductCodeManager;
use \RTL\Products\cProductPrice;
use \RTL\Products\cProductStock;
use \RTL\Products\info\cProductInfo;

// Create Application Content
$appContent = new APPContent($appID);
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "productInfoContainer", TRUE);
$viewContainer = HTML::select(".productInfo .viewContainer")->item(0);

// Get product id to show detail for
$productID = engine::getVar("pid");
$product = new cProduct($productID);
$productInfo = $product->info();

if (!empty($productInfo['tax_rate_id']))
{
	// Get all tax rates
	$taxRates = taxes::getTaxRates();
	$rate = $taxRates[$productInfo['tax_rate_id']];
	$productRate = $rate['rate'];
	$rateTitle = ($productRate * 100)."% (".$rate['title'].")";
	$infoRow = getInfoRow("rate", $rateTitle);
	DOM::append($viewContainer, $infoRow);
}

if (!empty($productInfo['tax_rate_id']))
{
	// Get all tax rates
	$mUnits = cProductStock::getMeasurementUnits();
	$infoRow = getInfoRow("mm", $mUnits[$productInfo['m_unit_id']]);
	DOM::append($viewContainer, $infoRow);
}

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
	$finalPrice = $priceInfo['price'] * (1 + $productRate);
	$finalPrice = number_format($finalPrice, 2);
	$infoRow = getInfoRow("price", $finalPrice." € (".number_format($priceInfo['price'], 2)." € + ".($productRate * 100)."%)");
	DOM::append($viewContainer, $infoRow);
}

// Add action to edit button
$editButton = HTML::select(".productInfo .edit")->item(0);
$attr = array();
$attr['pid'] = $productID;
$actionFactory->setAction($editButton, $viewName = "details/editProductInfo", $holder = ".productInfoContainer .editFormContainer", $attr, $loading = TRUE);

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