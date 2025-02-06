<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Reports
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
UPDATE {$installer->getTable('cms_page')} SET `layout_update_xml` = CONCAT(IFNULL(layout_update_xml, ''), '<!--<reference name=\"content\">
<block type=\"catalog/product_new\" name=\"home.catalog.product.new\" alias=\"product_new\" template=\"catalog/product/new.phtml\" after=\"cms_page\"/>
<block type=\"reports/product_viewed\" name=\"home.reports.product.viewed\" alias=\"product_viewed\" template=\"reports/home_product_viewed.phtml\" after=\"product_new\"/>
<block type=\"reports/product_compared\" name=\"home.reports.product.compared\" template=\"reports/home_product_compared.phtml\" after=\"product_viewed\" />
</reference><reference name=\"right\">
<action method=\"unsetChild\"><alias>right.reports.product.viewed</alias></action>
<action method=\"unsetChild\"><alias>right.reports.product.compared</alias></action>
</reference>-->') WHERE `identifier`='home';
");

$installer->endSetup();
