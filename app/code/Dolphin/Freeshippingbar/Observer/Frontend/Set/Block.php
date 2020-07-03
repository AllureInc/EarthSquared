<?php

declare (strict_types = 1);

namespace Dolphin\Freeshippingbar\Observer\Frontend\Set;

class Block implements \Magento\Framework\Event\ObserverInterface
{
    const BLOCK_NAME = 'dolphin_free_shipping_bar';

    protected $HelperData;
    private $processed = false;

    public function __construct(
        \Dolphin\Freeshippingbar\Helper\Data $HelperData,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->HelperData = $HelperData;
        $this->_logger = $logger;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        if (!$this->processed && $this->HelperData->isEnabled()) {
            $barposition = $this->HelperData->getBarPosition();

            $position = 'after.body.start';

            if ($barposition == 'Page Top') {
                $position = 'after.body.start';
                $sub_position = 'before="-"';
            }

            if ($barposition == 'Content Top') {
                $position = 'page.top';
                $sub_position = 'before="-"';
            }

            if ($barposition == 'Content Bottom') {
                $position = 'main';
                $sub_position = 'after="-"';
            }

            $layout = $observer->getEvent()->getLayout();

            if ($layout->hasElement($position)) {

                $layout->addBlock(
                    \Dolphin\Freeshippingbar\Block\Shippingbar::class,
                    self::BLOCK_NAME,
                    $position
                );

            }

            $this->processed = true;
        }
    }
}
