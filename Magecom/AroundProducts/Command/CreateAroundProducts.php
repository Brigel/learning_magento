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
    protected $prodHistoryCollection;
    protected $productsSessionHistoryFactory;
    protected $resourceProdSessHistory;

    protected $aroundProdsCollection;
    protected $aroundProductsFactory;
    protected $resourceAroundProducts;

    public function __construct(
        \Magecom\AroundProducts\Model\ResourceModel\ProductsSessionHistory\Collection $prodHistoryCollection,
        \Magecom\AroundProducts\Model\ResourceModel\AroundProducts\Collection $aroundProdsCollection,
        \Magecom\AroundProducts\Model\ProductsSessionHistoryFactory $productsSessionHistoryFactory,
        \Magecom\AroundProducts\Model\AroundProductsFactory $aroundProductsFactory,
        \Magecom\AroundProducts\Model\ResourceModel\ProductsSessionHistory $resourceProdSessHistory,
        \Magecom\AroundProducts\Model\ResourceModel\AroundProducts $resourceAroundProducts,
        $name = null
    ) {
        $this->prodHistoryCollection = $prodHistoryCollection;
        $this->productsSessionHistoryFactory = $productsSessionHistoryFactory;
        $this->resourceProdSessHistory = $resourceProdSessHistory;

        $this->aroundProdsCollection = $aroundProdsCollection;
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
            $productHistoryIds = $this->prodHistoryCollection->getAllIds();
            $productHistoryItems = $this->prodHistoryCollection->addFieldToFilter('id', ['in' => $productHistoryIds]);
            $aroundProductsItems = [];

            foreach ($productHistoryItems as $item) {
                foreach ($productHistoryItems as $secondItem) {
                    if (
                        $item->getData('id') !== $secondItem->getData('id')
                        &&
                        $item->getData('session_id') == $secondItem->getData('session_id')
                    ) {

                        $count = $this->aroundProdsCollection
                            ->addFieldToFilter('product_id_main', ['eq' => $item->getData('product_id')])
                            ->addFieldToFilter('product_id', ['eq' => $secondItem->getData('product_id')])
                            ->count();

                        if ($count != 0) {
                            continue;
                        }

                        $aroundProduct = $this->aroundProductsFactory->create();
                        $aroundProduct->setData([
                            'product_id_main' => $item->getData('product_id'),
                            'product_id' => $secondItem->getData('product_id')
                        ]);

                        $this->resourceAroundProducts->save($aroundProduct);

                    }
                }
            }
            foreach ($productHistoryItems as $item) {
                $this->resourceProdSessHistory->delete($item);
            }


        } catch (\Exception $e) {
            $output->writeln("<error>" . $e->getMessage() . "</error>");
            return;
        }

        $output->writeln("<info>Successful </info>");
    }

}