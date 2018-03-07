<?php

namespace Magecom\AroundProducts\Controller\Adminhtml\Products;

/**
 * Class InlineEdit
 */
abstract class InlineEdit extends \Magento\Backend\App\Action
{
    /**
     * JSON Factory
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_jsonFactory;


    protected $_aroundProductsFactory;
    protected $_resourceAroundProducts;

    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magecom\AroundProducts\Model\AroundProductsFactory $aroundProductsFactory,
        \Magecom\AroundProducts\Model\ResourceModel\AroundProducts $resourceAroundProducts,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->_jsonFactory = $jsonFactory;
        $this->_aroundProductsFactory = $aroundProductsFactory;
        $this->_resourceAroundProducts = $resourceAroundProducts;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->_jsonFactory->create();
        $error = false;
        $messages = [];
        $aroundProductItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($aroundProductItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }
        foreach (array_keys($aroundProductItems) as $aroundProductId) {
            /** @var \Magecom\AroundProducts\Model\AroundProducts $aroundProduct */
            $aroundProduct = $this->_aroundProductsFactory->create();

            $this->_resourceAroundProducts->load(
                $aroundProduct,
                $aroundProductId,
                'id');

            try {
                $aroundProductData = $aroundProductItems[$aroundProductId];
                $aroundProduct->addData($aroundProductData);
                $this->_resourceAroundProducts->save($aroundProduct);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithAroundProductId($aroundProduct, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithAroundProductId($aroundProduct, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithAroundProductId(
                    $aroundProduct,
                    __('Something went wrong while saving the Item.')
                );
                $error = true;
            }
        }
        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    protected function getErrorWithAroundProductId(\Magecom\AroundProducts\Model\AroundProducts $aroundProduct, $errorText)
    {
        return '[Item ID: ' . $aroundProduct->getId() . '] ' . $errorText;
    }
}
