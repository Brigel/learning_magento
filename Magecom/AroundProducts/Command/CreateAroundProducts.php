<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category  Magecom
 * @package   Magecom_Translation
 * @copyright Copyright (c) 2017 Magecom, Inc. (http://www.magecom.net)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */

declare(strict_types=1);

namespace Magecom\AroundProducts\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Translate
 */
class CreateAroundProducts extends Command
{
    protected $prodHistoryCollectionFactory;
    protected $productsSessionHistoryFactory;
    protected $resourceProdSessHistory;

    protected $aroundProdsCollectionFactory;
    protected $aroundProductsFactory;
    protected $resourceAroundProducts;

    public function __construct(
        \Magecom\AroundProducts\Model\ResourceModel\ProductsSessionHistory\CollectionFactory $prodHistoryCollectionFactory,
        \Magecom\AroundProducts\Model\ResourceModel\AroundProducts\CollectionFactory $aroundProdsCollectionFactory,
        \Magecom\AroundProducts\Model\ProductsSessionHistoryFactory $productsSessionHistoryFactory,
        \Magecom\AroundProducts\Model\AroundProductsFactory $aroundProductsFactory,
        \Magecom\AroundProducts\Model\ResourceModel\ProductsSessionHistory $resourceProdSessHistory,
        \Magecom\AroundProducts\Model\ResourceModel\AroundProducts $resourceAroundProducts,
        $name = null
    ) {
        $this->prodHistoryCollectionFactory = $prodHistoryCollectionFactory;
        $this->productsSessionHistoryFactory = $productsSessionHistoryFactory;
        $this->resourceProdSessHistory = $resourceProdSessHistory;

        $this->aroundProdsCollectionFactory = $aroundProdsCollectionFactory;
        $this->aroundProductsFactory = $aroundProductsFactory;
        $this->resourceAroundProducts = $resourceAroundProducts;

        parent::__construct($name);
    }

    protected
    function configure()
    {
        $this->setName('magecom:create-around-products');
        $this->setDescription('Create around products from temp table');
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected
    function execute(
        InputInterface $input,
        OutputInterface $output
    ) {

        $output->writeln("<info>Start creating...</info>");

        try {

            $productHistoryCollection = $this->prodHistoryCollectionFactory->create();

            $productHistoryCollection->addFieldToSelect('session_id')
                ->addFieldToFilter(
                    'processed',
                    ['eq' => 0]
                )
                ->getSelect()
                ->group('session_id');
            $productHistorySessionIds = $productHistoryCollection->getItems();
//            $productHistoryItems = $this->prodHistoryCollection->addFieldToFilter('id', ['in' => $productHistorySessionIds]);
            $aroundProductsItems = [];
            foreach ($productHistorySessionIds as $sessionId) {
                $sessionId = $sessionId->getData('session_id');
                $productHistoryCollection = $this->prodHistoryCollectionFactory->create();
                $productHistoryItems =
                    $productHistoryCollection
                        ->addFieldToFilter(
                            'session_id',
                            ['eq' => $sessionId]
                        )->addFieldToFilter(
                            'processed',
                            ['eq' => 0]
                        )->getItems();
                if ($productHistoryItems <= 1) {
                    continue;
                }
                foreach ($productHistoryItems as $item) {
                    foreach ($productHistoryItems as $secondItem) {
                        if (
                            $item->getData('id') !== $secondItem->getData('id')
                        ) {

                            $itemAroundProduct = $this->aroundProdsCollectionFactory->create()
                                ->addFieldToFilter('product_id_main', ['eq' => $item->getData('product_id')])
                                ->addFieldToFilter('product_id', ['eq' => $secondItem->getData('product_id')])
                                ->getFirstItem();

                            if (!$itemAroundProduct->isEmpty()) {
                                $itemAroundProduct->setData(
                                    'counter', ($itemAroundProduct->getData('counter') + 1)
                                );
                            } else {
                                $itemAroundProduct->setData([
                                    'product_id_main' => $item->getData('product_id'),
                                    'product_id' => $secondItem->getData('product_id'),
                                    'counter' => 1,
                                ]);
                            }
                            $this->resourceAroundProducts->save($itemAroundProduct);
                        }
                    }
                }
                foreach ($productHistoryItems as $item) {
                    $item->addData([
                        'processed' => 1
                    ]);
                    $this->resourceProdSessHistory->save($item);
                }
            }


        } catch (\Exception $e) {
            $output->writeln("<error>" . $e->getMessage() . "</error>");
            return;
        }

        $output->writeln("<info>Successful </info>");
    }

}