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
use \RTL\Products\info\cProductInfo;
use \UI\Apps\APPContent;

// Create Application Content
$appContent = new APPContent($appID);
$actionFactory = $appContent->getActionFactory();

// Build the application view content
$appContent->build("", "productDetailsContainer", TRUE);

// Get product id to show detail for
$productID = engine::getVar("pid");
$product = new cProduct($productID);
$productInfo = $product->info();

// Set name
$name = HTML::select(".productDetails .sidebar .title")->item(0);
HTML::innerHTML($name, $productInfo['title']);

// Contact info section
$detailsContainer = HTML::select(".productDetails .detailsContainer")->item(0);
$section = DOM::create("div", "", "", "section product_info");
DOM::append($detailsContainer, $section);


// Load view
$attr = array();
$attr['pid'] = $productID;
$viewContainer = $appContent->getAppViewContainer($viewName = "details/productInfo", $attr, $startup = FALSE, $containerID = "productInfoViewContainer", $loading = FALSE, $preload = TRUE);
DOM::append($section, $viewContainer);


// Delete product button
$deleteButton = HTML::select(".productDetails .abutton.delete")->item(0);
$attr = array();
$attr['pid'] = $productID;
$actionFactory->setAction($deleteButton, $viewName = "details/deleteProduct", $holder = "", $attr, $loading = TRUE);

// Action to switch to details view
$appContent->addReportAction($name = "listviewer.switchto.details");

// Return output
return $appContent->getReport();
//#section_end#
?>