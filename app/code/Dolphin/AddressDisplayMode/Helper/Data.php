<?php
/**
 * @category   Dolphin
 * @package    Dolphin_AddressDisplayMode
 */

namespace Dolphin\AddressDisplayMode\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{
    CONST ADDRESS_DISPLAY_MODE_LIST = 'list';
    CONST ADDRESS_DISPLAY_MODE_DROPDOWN = 'dropdown';

    /*
    * @var StoreManager
    */
    protected $storeManager;

    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    public function getDisplayMode()
    {
        return $this->storeManager->getWebsite()->getCode() == 'retail' ?
            self::ADDRESS_DISPLAY_MODE_LIST : self::ADDRESS_DISPLAY_MODE_DROPDOWN;
    }
}
