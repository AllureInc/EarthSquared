<?php

namespace Earthsquared\Customize\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    protected $_storeManager;
    protected $_registry;
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Block\Account\AuthorizationLink $customerSession,
        \Magento\Customer\Model\Session $session,
        \Magento\Wishlist\Model\Wishlist $wishlist
    ) {
        $this->_storeManager = $storeManager;
        $this->_registry = $registry;
        $this->customerSession = $customerSession;
        $this->session = $session;
        $this->wishlist = $wishlist;
    }
    /**
     * Get store identifier
     *
     * @return  int
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * Get website identifier
     *
     * @return string|int|null
     */
    public function getWebsiteId()
    {
        return $this->_storeManager->getStore()->getWebsiteId();
    }

    /**
     * Get Store code
     *
     * @return string
     */
    public function getStoreCode()
    {
        return $this->_storeManager->getStore()->getCode();
    }

    /**
     * Get Store name
     *
     * @return string
     */
    public function getStoreName()
    {
        return $this->_storeManager->getStore()->getName();
    }

    /**
     * Get current url for store
     *
     * @param bool|string $fromStore Include/Exclude from_store parameter from URL
     * @return string
     */
    public function getStoreUrl($fromStore = true)
    {
        return $this->_storeManager->getStore()->getCurrentUrl($fromStore);
    }

    /**
     * Check if store is active
     *
     * @return boolean
     */
    public function isStoreActive()
    {
        return $this->_storeManager->getStore()->isActive();
    }

    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }

    public function isCustomerLoggedIn()
    {
        return $this->customerSession->isLoggedIn();
    }

    public function getCustomerIdLoggedIn()
    {
        return $this->session->getCustomerId();
    }
    public function getWishlistItemcollection($cutsid)
    {
        $wishlist = $this->wishlist->loadByCustomerId($cutsid);
        return $wishlist;
    }
}
