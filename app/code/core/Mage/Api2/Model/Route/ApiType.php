<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Api2
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Webservice Api2 Route to find out API type from request
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Route_ApiType extends Mage_Api2_Model_Route_Abstract implements Mage_Api2_Model_Route_Interface
{
    /**
     * API url template with API type variable
     * @deprecated
     */
    const API_ROUTE = 'api/:api_type';

    /**
     * Prepares the route for mapping by splitting (exploding) it
     * to a corresponding atomic parts. These parts are assigned
     * a position which is later used for matching and preparing values.
     *
     * @param string $route Map used to match with later submitted URL path
     * @param array $defaults Defaults for map variables with keys as variable names
     * @param array $reqs Regular expression requirements for variables (keys as variable names)
     * @param Zend_Translate $translator Translator to use for this instance
     * @param mixed $locale
     */
    public function __construct(
        $route,
        $defaults = [],
        $reqs = [],
        Zend_Translate $translator = null,
        $locale = null
    ) {
        parent::__construct([Mage_Api2_Model_Route_Abstract::PARAM_ROUTE => str_replace('.php', '', basename(getenv('SCRIPT_FILENAME'))) . '/:api_type']);
    }
}
