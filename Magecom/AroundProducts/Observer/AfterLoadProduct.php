<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magecom\AroundProducts\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;

class AfterLoadProduct implements ObserverInterface
{
    protected $prodHistoryCollection;
    protected $sessionManager;
    protected $productsSessionHistoryFactory;
    protected $resourseProdSessHistory;

    public function __construct(
        \Magecom\AroundProducts\Model\ResourceModel\ProductsSessionHistory\Collection $prodHistoryCollection,
        \Magento\Framework\Session\SessionManagerInterface $sessionManager,
        \Magecom\AroundProducts\Model\ProductsSessionHistoryFactory $productsSessionHistoryFactory,
        \Magecom\AroundProducts\Model\ResourceModel\ProductsSessionHistory $resourseProdSessHistory
    ) {
        $this->prodHistoryCollection = $prodHistoryCollection;
        $this->sessionManager = $sessionManager;
        $this->productsSessionHistoryFactory = $productsSessionHistoryFactory;
        $this->resourseProdSessHistory = $resourseProdSessHistory;
    }

    public function execute(EventObserver $observer)
    {
        $product = $observer->getEvent();
        $id = $observer->getEvent()->getData('request')->getParam('id');
        if(empty($id)){
            return;
        }
        $sessId = $this->sessionManager->getSessionId();
        $count = $this->prodHistoryCollection
            ->addFieldToFilter('product_id', ['eq' => $id])
            ->addFieldToFilter('session_id', ['eq' => $sessId])->count();
        if ($count == 0) {
            /**
             * @var \Magecom\AroundProducts\Model\ProductsSessionHistory $newItem
             */
            $newItem = $this->productsSessionHistoryFactory->create();
            $newItem->setData([
                'product_id' => $id,
                'session_id' => $sessId
            ]);
            $this->resourseProdSessHistory->save($newItem);
        }
    }
}
