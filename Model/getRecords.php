<?php

namespace Hyugan\CounterOffer\Model;

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerFactory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryFactory;
use Magento\Review\Model\Review;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrdersFactory;
use Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory as InvoicesFactory;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory as ShipmentFactory;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory as ReviewFactory;
use Magento\Review\Model\ResourceModel\Review\Collection as ReviewCollection;
use Magento\Store\Model\StoreManagerInterface;
use Exception;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Catalog\Api\AttributeSetRepositoryInterface;
use Hyugan\CounterOffer\Helper\Config;

class getRecords
{

    protected CollectionFactory $productCollectionFactory;
    private CustomerFactory $CustomerInfoFactory;
    private CategoryFactory $CategoryCollectionFactory;
    private OrdersFactory $OrderCollectionFactory;
    private InvoicesFactory $invoicesCollectionFactory;
    private ShipmentFactory $ShipmentCollectionFactory;
    private ReviewFactory $reviewCollectionFactory;
    private StoreManagerInterface $storeManager;
    private AttributeSetRepositoryInterface $attributeSetRepository;
    private SearchCriteriaBuilder $searchCriteriaBuilder;
    private Config $config;

    /**
     * @param Context $context
     * @param CollectionFactory $productCollectionFactory
     * @param CustomerFactory $CustomerInfoFactory
     * @param CategoryFactory $CategoryCollectionFactory
     * @param OrdersFactory $OrderCollectionFactory
     * @param InvoicesFactory $invoicesCollectionFactory
     * @param ShipmentFactory $ShipmentCollectionFactory
     * @param ReviewFactory $reviewCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param AttributeSetRepositoryInterface $attributeSetRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        CollectionFactory               $productCollectionFactory,
        CustomerFactory                 $CustomerInfoFactory,
        CategoryFactory                 $CategoryCollectionFactory,
        OrdersFactory                   $OrderCollectionFactory,
        InvoicesFactory                 $invoicesCollectionFactory,
        ShipmentFactory                 $ShipmentCollectionFactory,
        ReviewFactory                   $reviewCollectionFactory,
        StoreManagerInterface           $storeManager,
        AttributeSetRepositoryInterface $attributeSetRepository,
        SearchCriteriaBuilder           $searchCriteriaBuilder,
        Config                          $config
    )
    {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->CustomerInfoFactory = $CustomerInfoFactory;
        $this->CategoryCollectionFactory = $CategoryCollectionFactory;
        $this->OrderCollectionFactory = $OrderCollectionFactory;
        $this->invoicesCollectionFactory = $invoicesCollectionFactory;
        $this->ShipmentCollectionFactory = $ShipmentCollectionFactory;
        $this->reviewCollectionFactory = $reviewCollectionFactory;
        $this->storeManager = $storeManager;
        $this->attributeSetRepository = $attributeSetRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->config = $config;
    }

    /**
     * @return Phrase
     */
    public function getContentForDisplay()
    {
        return __("Successful! This is a sample module in Magento 2 by hyugan.");
    }

    /**
     * @return Collection
     */
    public function getProductCollection($size = 10)
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToFilter('visibility', Visibility::VISIBILITY_BOTH);
        $collection->addAttributeToFilter('status', Status::STATUS_ENABLED);
        $collection->setPageSize($size);
        $collection->setOrder('entity_id');
        $collection->addAttributeToSelect('sku');
        return $collection;
    }

    public function getCustomerInfo()
    {
        $customerinfo = $this->CustomerInfoFactory->create();
        $customerinfo->addFieldToSelect("*");
        return $customerinfo;

    }

    public function getCategoryCollection()
    {
        $categorycollection = $this->CategoryCollectionFactory->create();
        $categorycollection->addFieldToSelect("*");
        $categorycollection->setPageSize(10);
        return $categorycollection;
    }

    public function getOrderCollection()
    {
        $ordercollection = $this->OrderCollectionFactory->create();
        $ordercollection->addFieldToSelect("*");
        $ordercollection->setPageSize(10);
        return $ordercollection;
    }

    public function getInvoiceCollection()
    {
        /** @var InvoicesCollection $invoicecollection */
        $invoicecollection = $this->invoicesCollectionFactory->create();
        $invoicecollection->addFieldToSelect("*");
        $invoicecollection->setPageSize(10);
        return $invoicecollection->getItems();
    }

    public function getShipmentCollection()
    {
        $shipmentcollection = $this->ShipmentCollectionFactory->create();
        $shipmentcollection->addFieldToSelect("*");
        $shipmentcollection->setPageSize(10);
        return $shipmentcollection->getItems();
    }

    public function getReviewCollection()
    {
        /** @var ReviewCollection $reviewcollection */
        $reviewcollection = $this->reviewCollectionFactory->create();
        $reviewcollection->addFieldToSelect('*')
            ->addStoreFilter($this->storeManager->getStore()->getId())
            ->addStatusFilter(Review::STATUS_APPROVED)
            ->setPageSize(10)
            ->setDateOrder()
            ->addRateVotes();
        return $reviewcollection;
    }

    public function listAttributeSet()
    {
        $attributeSetList = null;
        try {
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $attributeSet = $this->attributeSetRepository->getList($searchCriteria);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }

        if ($attributeSet->getTotalCount()) {
            $attributeSetList = $attributeSet;
        }

        return $attributeSetList;
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getNoticeMessage()
    {
        return $this->config->getMessageOne();
    }
}
