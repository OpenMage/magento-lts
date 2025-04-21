<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Oauth
 */

/**
 * OAuth authorization block
 *
 * @package    Mage_Oauth
 */
abstract class Mage_Oauth_Block_AuthorizeBaseAbstract extends Mage_Oauth_Block_Authorize_Abstract
{
    /**
     * Retrieve user authorize form posting url
     *
     * @return string
     */
    abstract public function getPostActionUrl();

    /**
     * Retrieve reject authorization url
     *
     * @return string
     */
    public function getRejectUrl()
    {
        return $this->getUrl(
            $this->getRejectUrlPath() . ($this->getIsSimple() ? 'Simple' : ''),
            ['_query' => ['oauth_token' => $this->getToken()]],
        );
    }

    /**
     * Retrieve reject URL path
     *
     * @return string
     */
    abstract public function getRejectUrlPath();

    /**
     * Get form identity label
     *
     * @return string
     */
    abstract public function getIdentityLabel();

    /**
     * Get form identity label
     *
     * @return string
     */
    abstract public function getFormTitle();
}
