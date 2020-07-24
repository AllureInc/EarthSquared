<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace IWD\OrderGrid\Ui\Component\Control;

use Magento\Ui\Component\Control\Action;

/**
 * Class PdfAction
 */
class Delete extends \Magento\Ui\Component\MassAction
{
    /**
     * Prepare
     *
     * @return void
     */
    public function prepare()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $scopeConfig = $objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
        $request = $objectManager->get('Magento\Framework\App\Request\Http');

        $creditmemo = $scopeConfig->getValue('iwd_ordergrid/allow_delete/credit_memos');
        $invoice = $scopeConfig->getValue('iwd_ordergrid/allow_delete/invoices');
        $shipping = $scopeConfig->getValue('iwd_ordergrid/allow_delete/shipments');

        parent::prepare();
        $config = $this->getConfiguration();

        $allowedActions = [];

        foreach ($config['actions'] as $action) {
            if($request->getControllerName() == 'creditmemo' && $action['type'] == 'delete' && $creditmemo){
                $allowedActions[] = $action;
            }elseif($request->getControllerName() == 'invoice' && $action['type'] == 'delete' && $invoice){
                $allowedActions[] = $action;
            }elseif($request->getControllerName() == 'shipment' && $action['type'] == 'delete' && $shipping){
                $allowedActions[] = $action;
            }elseif($action['type'] != 'delete'){
                $allowedActions[] = $action;
            }
        }

        $config['actions'] = $allowedActions;

        $this->setData('config', (array)$config);
    }
}
