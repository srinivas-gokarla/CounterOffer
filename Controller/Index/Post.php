<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Hyugan\CounterOffer\Controller\Index;

use Magento\Contact\Model\MailInterface;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Area;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Message\MessageInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;
use Magento\Framework\App\ActionInterface;
use Hyugan\CounterOffer\Model\Email;
use Magento\Framework\Controller\ResultFactory;


class Post implements ActionInterface
{
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var MailInterface
     */
    private $mail;

    /**
     * @var LoggerInterface
     */
    private $logger;
    private Email $email;
    private RedirectFactory $resultRedirectFactory;
    private RequestInterface $request;
    private ManagerInterface $messageManager;
    private ResultFactory $resultFactory;
    private RedirectInterface $redirect;

    /**
     * @param Email $email
     */
    public function __construct(
        RedirectFactory   $resultRedirectFactory,
        RequestInterface  $request,
        ResultFactory     $resultFactory,
        RedirectInterface $redirect,
        Email             $email,
        LoggerInterface   $logger,
        ManagerInterface $messageManager,
    )
    {
        $this->email = $email;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->request = $request;
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;
        $this->redirect = $redirect;
        $this->logger = $logger;
    }

    /**
     * Post user question
     *
     * @return Redirect
     */
    public function execute()
    {
        if (!$this->request->isPost()) {
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->redirect->getRefererUrl());
            return $resultRedirect;
        }
        try {
            if ($this->validatedParams()) {
                $this->email->sendEmail(
                    $this->request->getParam('price'),
                    $this->request->getParam('email'),
                    $this->request->getParam('name'),
                    $this->request->getParam('productname'),
                    $this->request->getParam('productsku'),
                    $this->request->getParam('comment'),
                    $this->request->getParam('telephone')
                );
                $this->messageManager->addSuccessMessage(
                    __('Thanks for contacting us with your counter price and details. We\'ll respond to you very soon.')
                );
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {

            $this->messageManager->addErrorMessage(
                __('An error occurred while processing your form. Please try again later.')
            );
            $this->logger->debug($e->getTraceAsString());
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->redirect->getRefererUrl());
        return $resultRedirect;
    }

    /**
     * Method to validated params.
     *
     * @return array
     * @throws \Exception
     */
    private function validatedParams()
    {
        $request = $this->request;
        if (trim($request->getParam('price', '')) === '') {
            throw new LocalizedException(__('Enter the Price.'));
        }
        if (trim($request->getParam('name', '')) === '') {
            throw new LocalizedException(__('Enter the Name and try again.'));
        }
        if (trim($request->getParam('comment', '')) === '') {
            throw new LocalizedException(__('Enter the comment and try again.'));
        }
        if (!str_contains($request->getParam('email', ''), '@')) {
            throw new LocalizedException(__('The email address is invalid. Verify the email address and try again.'));
        }
        if (trim($request->getParam('', '')) !== '') {
            // phpcs:ignore Magento2.Exceptions.DirectThrow
            throw new \Exception();
        }
        return $request->getParams();
    }
}
