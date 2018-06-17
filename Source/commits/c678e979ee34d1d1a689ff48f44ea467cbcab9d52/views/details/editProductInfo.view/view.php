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
importer::import("UI", "Forms");

// Import APP Packages
//#section_end#
//#section#[view]
use \UI\Apps\APPContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \RTL\Finances\taxes;
use \RTL\Products\cProduct;
use \RTL\Products\cProductCodeManager;
use \RTL\Products\cProductPrice;
use \RTL\Products\cProductStock;
use \RTL\Products\info\cProductInfo;

// Create Application Content
$appContent = new APPContent($appID);
$actionFactory = $appContent->getActionFactory();

// Get product id to show detail for
$productID = engine::getVar("pid");
if (engine::isPost())
{
	// Update product information
	$product = new cProduct($productID);
	$product->update($_POST['title']);
	
	// Set units and tax rate
	$product->setMUnit($_POST['m_unit']);
	$product->setTaxRate($_POST['tax_rate']);
	
	// Update codes
	$codeManager = new cProductCodeManager($productID);
	$allCodes = $codeManager->getAllCodes();
	foreach ($allCodes as $codeInfo)
	{
		$codeTypeID = $codeInfo['type_id'];
		$codeValue = $_POST['code'][$codeTypeID];
		if (!isset($_POST['code_type'][$codeTypeID]) || empty($codeValue))
		{
			$codeManager->remove($codeTypeID);
			continue;
		}
		
		// Check if the type changed
		if ($_POST['code_type'][$codeTypeID] != $codeTypeID)
			$codeManager->remove($codeTypeID);
		
		// Get phone value
		$typeID = $_POST['code_type'][$codeTypeID];
		$codeManager->set($typeID, $codeValue, $expirationTime = "");
	}
	
	// Create new phones
	foreach ($_POST['new_code_type'] as $codeID => $codeTypeID)
	{
		$codeValue = $_POST['new_code'][$codeID];
		if (!empty($codeValue))
			$codeManager->set($codeTypeID, $codeValue, $expirationTime = "");
	}
	
	
	// Update prices
	$priceManager = new cProductPrice($productID);
	$allPrices = $priceManager->getAllPrices();
	foreach ($allPrices as $priceInfo)
	{
		$priceTypeID = $priceInfo['type_id'];
		$priceValue = $_POST['price'][$priceTypeID];
		if (!isset($_POST['price_type'][$priceTypeID]) || empty($priceValue))
		{
			$priceManager->remove($priceTypeID);
			continue;
		}
		
		// Check if the type changed
		if ($_POST['price_type'][$priceTypeID] != $priceTypeID)
			$priceManager->remove($priceTypeID);
		
		// Get phone value
		$typeID = $_POST['price_type'][$priceTypeID];
		$priceManager->set($typeID, $priceValue);
	}
	
	// Create new phones
	foreach ($_POST['new_price_type'] as $priceID => $priceTypeID)
	{
		$priceValue = $_POST['new_price'][$priceID];
		if (!empty($priceValue))
			$priceManager->set($priceTypeID, $priceValue);
	}
	
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE, $disposable = FALSE);
	
	// Add action to reload info
	$succFormNtf->addReportAction($type = "productinfo.reload", $value = "");
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

// Build the application view content
$appContent->build("", "editProductInfoContainer", TRUE);
$formContainer = HTML::select(".editProductInfo .formContainer")->item(0);

// Build form
$form = new simpleForm();
$editForm = $form->build()->engageApp("details/editProductInfo")->get();
DOM::append($formContainer, $editForm);

$input = $form->getInput($type = "hidden", $name = "pid", $value = $productID, $class = "", $autofocus = FALSE, $required = FALSE);
$form->append($input);


// Product basic info
$product = new cProduct($productID);
$productInfo = $product->info();

$title = $appContent->getLiteral("products.details.edit", "hd_basicInfo");
$group = getEditGroup($title, FALSE);
$form->append($group);

$title = $appContent->getLiteral("products.details.edit", "lbl_productTitle");
$ph = $appContent->getLiteral("products.details.edit", "lbl_productTitle", array(), FALSE);
$fRow = getSimpleFormRow($form, $title, $productInfo['title'], $ph, $name = "title");
DOM::append($group, $fRow);


// Product unit of measurement
$fRow = DOM::create("div", "", "", "frow");
DOM::append($group, $fRow);

$mUnitsResource = cProductStock::getMeasurementUnits();
$input = $form->getResourceSelect("m_unit", $multiple = "", $class = "finput", $resource = $mUnitsResource, $selectedValue = $productInfo['m_unit_id']);
$inputID = DOM::attr($input, "id");

$labelTitle = $appContent->getLiteral("products.details.edit", "lbl_productUnit");
$label = $form->getLabel($labelTitle, $for = $inputID, $class = "flabel");

// Append to frow
DOM::append($fRow, $label);
DOM::append($fRow, $input);


// Product tax rates
$fRow = DOM::create("div", "", "", "frow");
DOM::append($group, $fRow);

$taxRates = taxes::getTaxRates();
$taxRatesResource = array();
foreach ($taxRates as $rateID => $rateInfo)
	$taxRatesResource[$rateID] = ($rateInfo['rate'] * 100)."% (".$rateInfo['title'].")";
$input = $form->getResourceSelect("tax_rate", $multiple = "", $class = "finput", $resource = $taxRatesResource, $selectedValue = $productInfo['tax_rate_id']);
$inputID = DOM::attr($input, "id");

$labelTitle = $appContent->getLiteral("products.details.edit", "lbl_productTaxRate");
$label = $form->getLabel($labelTitle, $for = $inputID, $class = "flabel");

// Append to frow
DOM::append($fRow, $label);
DOM::append($fRow, $input);




// Code group
$title = $appContent->getLiteral("products.details.edit", "hd_codes");
$group = getEditGroup($title);
$form->append($group);

// Get all product codes
$codeManager = new cProductCodeManager($productID);
$codeTypes = $codeManager->getCodeTypes();
$allCodes = $codeManager->getAllCodes();
foreach ($allCodes as $codeInfo)
{
	$ph = $appContent->getLiteral("products.details.edit", "lbl_code_new_ph", array(), FALSE);
	$fRow = getSelectFormRow($form, $typeResource = $codeTypes, $typeValue = $codeInfo['type_id'], $valueValue = $codeInfo['code'], $class = "", $ph, $name = "code", $id = $codeInfo['type_id'], $removable = TRUE);
	DOM::append($group, $fRow);
}

// Create a new row
$ph = $appContent->getLiteral("products.details.edit", "lbl_code_new_ph", array(), FALSE);
$fRow = getSelectFormRow($form, $typeResource = $codeTypes, $typeValue = "", $valueValue = "", $class = "new", $ph, $name = "new_code", $id = "");
DOM::append($group, $fRow);


// Price group
$title = $appContent->getLiteral("products.details.edit", "hd_prices");
$group = getEditGroup($title);
$form->append($group);

// Get all product prices
$priceManager = new cProductPrice($productID);
$priceTypes = $priceManager->getPriceTypes();
$allPrices = $priceManager->getAllPrices();
foreach ($allPrices as $priceInfo)
{
	$ph = $appContent->getLiteral("products.details.edit", "lbl_price_new_ph", array(), FALSE);
	$fRow = getSelectFormRow($form, $typeResource = $priceTypes, $typeValue = $priceInfo['type_id'], $valueValue = $priceInfo['price'], $class = "", $ph, $name = "price", $id = $priceInfo['type_id'], $removable = TRUE);
	DOM::append($group, $fRow);
}

// Create a new row
$ph = $appContent->getLiteral("products.details.edit", "lbl_price_new_ph", array(), FALSE);
$fRow = getSelectFormRow($form, $typeResource = $priceTypes, $typeValue = "", $valueValue = "", $class = "new", $ph, $name = "new_price", $id = "");
DOM::append($group, $fRow);


// Set action to switch to edit info
$appContent->addReportAction($type = "productinfo.edit", $value = "");

// Return output
return $appContent->getReport();

function getEditGroup($title, $newButton = TRUE)
{
	$group = DOM::create("div", "", "", "editGroup");
	
	// Add new button
	if ($newButton)
	{
		$create_new = DOM::create("div", "", "", "ico create_new");
		DOM::append($group, $create_new);
	}
	
	// Header
	$hd = DOM::create("h3", $title, "", "ghd");
	DOM::append($group, $hd);
	
	return $group;
}

function getSelectFormRow($form, $typeResource, $typeValue, $valueValue, $class, $ph, $name, $id = "", $removable = FALSE)
{
	// Create a new row
	$fRow = DOM::create("div", "", "", "frow");
	HTML::addClass($fRow, $class);
	
	$select = $form->getResourceSelect($name."_type[$id]", $multiple = "", $class = "fselect", $resource = $typeResource, $selectedValue = $typeValue);
	DOM::append($fRow, $select);
	$input = $form->getInput($type = "text", $name."[$id]", $value = $valueValue, $class = "finput", $autofocus = FALSE, $required = FALSE);
	DOM::append($fRow, $input);
	DOM::attr($input, "placeholder", $ph);
	
	// Remove ico
	if ($removable)
	{
		$removeIco = DOM::create("div", "", "", "ico remove");
		DOM::append($fRow, $removeIco);
	}
	
	return $fRow;
}

function getSimpleFormRow($form, $labelTitle, $valueValue, $ph, $name)
{
	// Create a new row
	$fRow = DOM::create("div", "", "", "frow");
	
	$input = $form->getInput($type = "text", $name, $value = $valueValue, $class = "finput", $autofocus = FALSE, $required = FALSE);
	DOM::attr($input, "placeholder", $ph);
	$inputID = DOM::attr($input, "id");
	$label = $form->getLabel($labelTitle, $for = $inputID, $class = "flabel");
	
	// Append to frow
	DOM::append($fRow, $label);
	DOM::append($fRow, $input);
	
	return $fRow;
}
//#section_end#
?>