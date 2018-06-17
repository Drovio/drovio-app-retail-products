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
use \UI\Apps\APPContent;

// Create Application Content
$appContent = new APPContent();
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "productsListViewContainer", TRUE);

// Get all company products
$products = cProduct::getProducts();

// Set relation categories
$listContainer = HTML::select(".listContainer")->item(0);
foreach ($products as $productInfo)
{
	$productID = $productInfo['id'];
	
	// Create person list item
	$listItem = DOM::create("div", "", "", "listItem");
	DOM::append($listContainer, $listItem);
	
	// Set action
	$attr = array();
	$attr['pid'] = $productID;
	$actionFactory->setAction($listItem, "details/productDetails", ".productsListView .detailsContainer .wbox.details", $attr, $loading = TRUE);
	
	// Ico
	$ico = DOM::create("div", "", "", "ico");
	DOM::append($listItem, $ico);
	
	// Title
	$title = DOM::create("div", $productInfo['title'], "", "title");
	DOM::append($listItem, $title);
}

if (empty($products))
{
	// Clear list container
	HTML::innerHTML($listContainer, "");
	
	// Add header
	$title = $appContent->getLiteral("main.list", "hd_noProducts");
	$hd = DOM::create("h2", $title, "", "hd");
	DOM::append($listContainer, $hd);
}

// Return output
return $appContent->getReport();
//#section_end#
?>