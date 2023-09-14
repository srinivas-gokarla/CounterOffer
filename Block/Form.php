<?php

namespace Hyugan\CounterOffer\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Helper\Data;

class Form extends Template
{
    private Template $template;
    private Data $catalogHelper;

    /**
     * @param Context $context
     * @param Template $template
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Template         $template,
        Data $catalogHelper,
        array            $data = []
    ) {
        parent::__construct($context, $data);
        $this->template = $template;
        $this->catalogHelper = $catalogHelper;
    }

    /**
     * @return string
     */
    public function getActionUrl()
    {
        return $this->_escaper->escapeHtml($this->getUrl('counteroffer/index/post', ['_secure' => true]));
    }
    public function getProduct(){
        return $this->catalogHelper->getProduct();
    }
}

