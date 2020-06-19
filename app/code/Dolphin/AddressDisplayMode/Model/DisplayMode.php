<?php

namespace Dolphin\AddressDisplayMode\Model;

use Dolphin\AddressDisplayMode\Helper\Data as DisplayModeHelper;
use Magento\Checkout\Model\ConfigProviderInterface;

class DisplayMode implements ConfigProviderInterface
{
    /*
    * @var DisplayModeHelper
    */
    protected $displayModeHelper;

    public function __construct(
        DisplayModeHelper $displayModeHelper
    )
    {
        $this->displayModeHelper = $displayModeHelper;
    }

    public function getConfig()
    {
        $additionalVariables['addressDisplayMode'] = $this->displayModeHelper->getDisplayMode();
        return $additionalVariables;
    }
}