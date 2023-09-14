<?php

namespace Hyugan\CounterOffer\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Config
{
    CONST XML_PATH = 'hyugan_settings/notice/messageone';
    private ScopeConfigInterface $ScopeConfigInterface;
    private StoreManagerInterface $StoreManagerInterface;

    public function __construct(
        ScopeConfigInterface  $ScopeConfigInterface,
        StoreManagerInterface $StoreManagerInterface,
    ) {
        $this->ScopeConfigInterface = $ScopeConfigInterface;
        $this->StoreManagerInterface = $StoreManagerInterface;
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getMessageOne()
    {
        return $this->ScopeConfigInterface->getValue(self::XML_PATH);
        ScopeInterface::SCOPE_STORE;
        $this->StoreManagerInterface->getStore()->getStoreId();
    }
}
