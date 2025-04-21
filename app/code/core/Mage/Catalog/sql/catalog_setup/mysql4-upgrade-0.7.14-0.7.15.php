<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

$this->startSetup()->run("
delete link1.*
from {$this->getTable('catalog_product_link')} link1
inner join {$this->getTable('catalog_product_link')} link2 on link2.product_id=link1.product_id and link2.linked_product_id=link1.linked_product_id
and link2.link_id<>link1.link_id;
")->endSetup();
