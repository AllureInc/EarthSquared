<?php

namespace Dolphin\QuickOrder\Plugin;

use Magento\Catalog\Model\ResourceModel\Product\Collection as Subject;
use Magento\Framework\DB\Select;

class GroupbyCollection
{
    public function aroundGetSelectCountSql(
        /* @noinspection PhpUnusedParameterInspection */
        Subject $subject,
        callable $proceed
    ) {
        /** @var Select $result */
        $result = $proceed();

        if (count($result->getPart(Select::GROUP))) {
            $group = $result->getPart(Select::GROUP);

            $result->reset(Select::GROUP);
            $result->reset(Select::COLUMNS);

            $result->columns(new \Zend_Db_Expr(("COUNT(DISTINCT " . implode(", ", $group) . ")")));
        }

        return $result;
    }
}
