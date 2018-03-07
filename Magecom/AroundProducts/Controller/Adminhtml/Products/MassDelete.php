<?php
namespace Magecom\AroundProducts\Controller\Adminhtml\Products;

/**
 * Class MassDelete
 */
class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * Mass Action Filter
     *
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $_filter;

    protected $_collectionFactory;
    protected $_resourceAroundProducts;

    public function __construct(
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Magecom\AroundProducts\Model\ResourceModel\AroundProducts\CollectionFactory $collectionFactory,
        \Magecom\AroundProducts\Model\ResourceModel\AroundProducts $resourceAroundProducts,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        $this->_resourceAroundProducts = $resourceAroundProducts;
        parent::__construct($context);
    }


    /**
     * execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());

        $delete = 0;
        foreach ($collection as $aroundProduct) {
            $this->_resourceAroundProducts->delete($aroundProduct);
            $delete++;
        }
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $delete));
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
