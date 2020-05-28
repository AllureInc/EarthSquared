<?php

namespace IWD\OrderManager\Model\Inventory;

if (!interface_exists(\Magento\InventorySalesApi\Api\GetProductSalableQtyInterface::class)) {
    class Reservation{}
}else{
    class Reservation
    {
        /**
         * @var GetSkusByProductIdsInterface
         */
        private $getSkusByProductIds;

        /**
         * @var SalesEventInterfaceFactory
         */
        private $salesEventFactory;

        /**
         * @var SalesChannelInterfaceFactory
         */
        private $salesChannelFactory;

        /**
         * @var ItemToSellInterfaceFactory
         */
        private $itemsToSellFactory;

        /**
         * @var WebsiteRepositoryInterface
         */
        private $websiteRepository;

        /**
         * @var PlaceReservationsForSalesEventInterface
         */
        private $placeReservationsForSalesEvent;

        /**
         * @var IsSourceItemManagementAllowedForProductTypeInterface
         */
        private $isSourceItemManagementAllowedForProductType;

        /**
         * @var GetProductTypesBySkusInterface
         */
        private $getProductTypesBySkus;

        /**
         * @var StoreRepositoryInterface
         */
        private $storeRepository;

        /**
         * Reservation constructor.
         * @param GetSkusByProductIdsInterface $getSkusByProductIds
         * @param SalesEventInterfaceFactory $salesEventFactory
         * @param SalesChannelInterfaceFactory $salesChannelFactory
         * @param ItemToSellInterfaceFactory $itemsToSellFactory
         * @param WebsiteRepositoryInterface $websiteRepository
         * @param PlaceReservationsForSalesEventInterface $placeReservationsForSalesEvent
         * @param IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType
         * @param GetProductTypesBySkusInterface $getProductTypesBySkus
         * @param StoreRepositoryInterface $storeRepository
         */
        public function __construct(
            \Magento\InventoryCatalogApi\Model\GetSkusByProductIdsInterface $getSkusByProductIds,
            \Magento\InventorySalesApi\Api\Data\SalesEventInterfaceFactory $salesEventFactory,
            \Magento\InventorySalesApi\Api\Data\SalesChannelInterfaceFactory $salesChannelFactory,
            \Magento\InventorySalesApi\Api\Data\ItemToSellInterfaceFactory $itemsToSellFactory,
            \Magento\Store\Api\WebsiteRepositoryInterface $websiteRepository,
            \Magento\InventorySalesApi\Api\PlaceReservationsForSalesEventInterface $placeReservationsForSalesEvent,
            \Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType,
            \Magento\InventoryCatalogApi\Model\GetProductTypesBySkusInterface $getProductTypesBySkus,
            \Magento\Store\Api\StoreRepositoryInterface $storeRepository
        ) {
            $this->getSkusByProductIds = $getSkusByProductIds;
            $this->isSourceItemManagementAllowedForProductType = $isSourceItemManagementAllowedForProductType;
            $this->getProductTypesBySkus = $getProductTypesBySkus;
            $this->salesEventFactory = $salesEventFactory;
            $this->salesChannelFactory = $salesChannelFactory;
            $this->itemsToSellFactory = $itemsToSellFactory;
            $this->websiteRepository = $websiteRepository;
            $this->placeReservationsForSalesEvent = $placeReservationsForSalesEvent;
            $this->storeRepository = $storeRepository;
        }

        public function execute($items, $storeId = null)
        {
            if (empty($items)) {
                return true;
            }

            if (null === $storeId) {
                throw new \Magento\Framework\Exception\LocalizedException(__('$storeId parameter is required'));
            }
            $websiteId = $this->storeRepository->getById($storeId)->getWebsiteId();
            $websiteCode = $this->websiteRepository->getById((int)$websiteId)->getCode();
            $salesChannel = $this->salesChannelFactory->create([
                'data' => [
                    'type' => \Magento\InventorySalesApi\Api\Data\SalesChannelInterface::TYPE_WEBSITE,
                    'code' => $websiteCode
                ]
            ]);

            $salesEvent = $this->salesEventFactory->create([
                'type' => 'revert_products_sale',
                'objectType' => 'legacy_stock_management_api',
                'objectId' => 'none'
            ]);

            $productSkus = $this->getSkusByProductIds->execute(array_keys($items));
            $productTypes = $this->getProductTypesBySkus->execute(array_values($productSkus));

            $itemsToSell = [];
            foreach ($productSkus as $productId => $sku) {
                if (true === $this->isSourceItemManagementAllowedForProductType->execute($productTypes[$sku])) {
                    $itemsToSell[] = $this->itemsToSellFactory->create([
                        'sku' => $sku,
                        'qty' => (float)$items[$productId]
                    ]);
                }
            }

            $this->placeReservationsForSalesEvent->execute($itemsToSell, $salesChannel, $salesEvent);

            return true;
        }
    }
}