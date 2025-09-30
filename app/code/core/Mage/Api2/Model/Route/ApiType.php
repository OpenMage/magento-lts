<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api2
 */

/**
 * Webservice Api2 Route to find out API type from request
 *
 * @package    Mage_Api2
 */
class Mage_Api2_Model_Route_ApiType extends Mage_Api2_Model_Route_Abstract implements Mage_Api2_Model_Route_Interface
{
    /**
     * API url template with API type variable
     * @deprecated
     */
    public const API_ROUTE = 'api/:api_type';

    /**
     * Prepares the route for mapping by splitting (exploding) it
     * to a corresponding atomic parts. These parts are assigned
     * a position which is later used for matching and preparing values.
     *
     * @param string $route Map used to match with later submitted URL path
     * @param array $defaults Defaults for map variables with keys as variable names
     * @param array $reqs Regular expression requirements for variables (keys as variable names)
     * @param Zend_Translate|null $translator Translator to use for this instance
     * @param mixed $locale
     */
    public function __construct(
        $route,
        $defaults = [],
        $reqs = [],
        ?Zend_Translate $translator = null,
        $locale = null
    ) {
        // phpcs:ignore Ecg.Security.ForbiddenFunction.Found
        parent::__construct([Mage_Api2_Model_Route_Abstract::PARAM_ROUTE => str_replace('.php', '', basename(getenv('SCRIPT_FILENAME'))) . '/:api_type']);
    }
}
