<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Api2
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * oAuth Authentication adapter
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Auth_Adapter_Oauth extends Mage_Api2_Model_Auth_Adapter_Abstract
{
    /**
     * Process request and figure out an API user type and its identifier
     *
     * Returns stdClass object with two properties: type and id
     *
     * @param Mage_Api2_Model_Request $request
     * @return stdClass
     */
    public function getUserParams(Mage_Api2_Model_Request $request)
    {
        /** @var Mage_Oauth_Model_Server $oauthServer */
        $oauthServer   = Mage::getModel('oauth/server', $request);
        $userParamsObj = (object) ['type' => null, 'id' => null];

        try {
            $token    = $oauthServer->checkAccessRequest();
            $userType = $token->getUserType();

            if (Mage_Oauth_Model_Token::USER_TYPE_ADMIN == $userType) {
                $userParamsObj->id = $token->getAdminId();
            } else {
                $userParamsObj->id = $token->getCustomerId();
            }
            $userParamsObj->type = $userType;
        } catch (Exception $e) {
            throw new Mage_Api2_Exception($oauthServer->reportProblem($e), Mage_Api2_Model_Server::HTTP_UNAUTHORIZED);
        }
        return $userParamsObj;
    }

    /**
     * Check if request contains authentication info for adapter
     *
     * @param Mage_Api2_Model_Request $request
     * @return bool
     */
    public function isApplicableToRequest(Mage_Api2_Model_Request $request)
    {
        $headerValue = $request->getHeader('Authorization');

        return $headerValue && strtolower(substr($headerValue, 0, 5)) === 'oauth';
    }
}
