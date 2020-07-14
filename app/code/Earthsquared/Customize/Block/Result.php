<?php
namespace Earthsquared\Customize\Block;

use Magento\CatalogSearch\Helper\Data;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Framework\View\Element\Template\Context;
use Magento\Search\Model\QueryFactory;

class Result extends \Magento\CatalogSearch\Block\Result
{
    /**
     * Escaper
     *
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;
    public function __construct(
        Context $context,
        LayerResolver $layerResolver,
        Data $catalogSearchData,
        QueryFactory $queryFactory,
        \Magento\Framework\Escaper $_escaper,
        array $data = []
    ) {
        $this->_escaper = $_escaper;
        parent::__construct($context, $layerResolver, $catalogSearchData, $queryFactory, $data);
    }

    public function getSearchQueryText()
    {
        $count = $this->_getQuery()->getNumResults();
        return __('Showing %1 results for "%2"', $count, $this->catalogSearchData->getEscapedQueryText());
    }
}
