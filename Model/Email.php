<?php

namespace Hyugan\CounterOffer\Model;

use Hyugan\CounterOffer\Model\Exception;
use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Validator\EmailAddress;
use Magento\Framework\Validator\ValidatorChain;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Email
{
    const EMAIL_TEMPLATE = 'counter_message';
    protected StateInterface $inlineTranslation;

    protected TransportBuilder $transportBuilder;
    private StoreManagerInterface $storeManager;
    private SenderResolverInterface $senderResolver;
    private LoggerInterface $logger;

    /**
     * @param Context $context
     * @param StateInterface $inlineTranslation
     * @param TransportBuilder $transportBuilder
     * @param StoreManagerInterface $storeManager
     * @param SenderResolverInterface $senderResolver
     */
    public function __construct(
        Context                 $context,
        StateInterface          $inlineTranslation,
        TransportBuilder        $transportBuilder,
        StoreManagerInterface   $storeManager,
        SenderResolverInterface $senderResolver
    )
    {
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $context->getLogger();
        $this->storeManager = $storeManager;
        $this->senderResolver = $senderResolver;
    }

    public function sendEmail($price, $customerEmail, $customerName,$productname, $productsku, $message=null, $telephone=null): void
    {
        try {
            $this->inlineTranslation->suspend();
            $sender ='general';
            $managerName = __('Store Manager');
            if ($resolvedContact = $this->senderResolver->resolve($sender)) {
                $managerName = $resolvedContact['name'];
            }
            $transport = $this->transportBuilder
                ->setTemplateIdentifier(self::EMAIL_TEMPLATE)
                ->setTemplateOptions(
                    [
                        'area' => Area::AREA_FRONTEND,
                        'store' => $this->storeManager->getStore()->getId()
                    ]
                )
                ->setTemplateVars([
                    'managerName' => $managerName,
                    'customer'  => $customerName .' ('. $customerEmail.')',
                    'price' => $price,
                    'telephone' => $telephone,
                    'comment' => $message,
                    'productsku' => $productsku,
                    'productname' => $productname
                ])
                ->setReplyTo($customerEmail, $customerName)
                ->setFromByScope($sender);
            $toEmail = $resolvedContact['email'];
            if ($toEmail && ValidatorChain::is($toEmail, EmailAddress::class)) {
                $transport->addTo($toEmail);
            }
            $transport->getTransport()
                ->sendMessage();
            $this->inlineTranslation->resume();
        } catch (Exception $e) {
            $this->logger->debug($e->getTraceAsString());
        }
    }
}
