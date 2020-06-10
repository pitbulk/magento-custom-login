<?php

/**
 * Custom_Login
 * @package Custom_Login
 */

namespace Custom\Login\Controller\Custom;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class Login extends Action
{

    private $customerSession;

    /**
     * @var AccountManagementInterface
     */
    private $customerAccountManagement;

    /**
     * @var AccountRedirect
     */
    protected $accountRedirect;

    /**
     * @var CustomerInterfaceFactory
     */
    private $customerFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    private $cookieMetadataManager;

    public function __construct(
        Context $context,
        Session $session,
        FormKey $formKey,
        AccountManagementInterface $customerAccountManagement,
        CustomerInterfaceFactory $customerFactory,
        CustomerRepositoryInterface $customerRepository,
        AccountRedirect $accountRedirect
    ) {
        $this->customerAccountManagement = $customerAccountManagement;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->customerSession = $session;
        $this->formKey = $formKey;
        $this->accountRedirect = $accountRedirect;

        parent::__construct($context);

        $this->_whitelistEndpoint();
    }

    public function execute()
    {
        $email = "customlogintest@example.com";
        $firstname = "custom login";
        $lastname = "test";

        try {
            $customer = $this->customerRepository->get($email);

            if (!isset($customer)) {
                throw new NoSuchEntityException();
            }
        } catch (NoSuchEntityException $e) {
            $customerEntity = $this->customerFactory->create();
            $customerEntity->setEmail($email);
            $customerEntity->setFirstname($firstname);
            $customerEntity->setLastname($lastname);

            $customer = $this->customerAccountManagement->createAccount($customerEntity);
        }

        $this->registerCustomerSession($customer);

        return $this->accountRedirect->getRedirect();
    }

    /**
     * Register customer session
     *
     */
    private function registerCustomerSession($customer)
    {
        $this->customerSession->setCustomerDataAsLoggedIn($customer);
        
        if ($this->getCookieManager()->getCookie('mage-cache-sessid')) {
            $metadata = $this->getCookieMetadataFactory()->createCookieMetadata();
            $metadata->setPath('/');
            $this->getCookieManager()->deleteCookie('mage-cache-sessid', $metadata);
        }
    }

    /**
     * Retrieve cookie manager
     *
     * @deprecated 100.1.0
     * @return \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    private function getCookieManager()
    {
        if (!$this->cookieMetadataManager) {
            $this->cookieMetadataManager = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\Stdlib\Cookie\PhpCookieManager::class
            );
        }
        return $this->cookieMetadataManager;
    }

    /**
     * Retrieve cookie metadata factory
     *
     * @deprecated 100.1.0
     * @return \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    private function getCookieMetadataFactory()
    {
        if (!$this->cookieMetadataFactory) {
            $this->cookieMetadataFactory = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory::class
            );
        }
        return $this->cookieMetadataFactory;
    }

    public function _whitelistEndpoint()
    {
        // CSRF Magento2.3 compatibility
        if (interface_exists("\Magento\Framework\App\CsrfAwareActionInterface")) {
            $request = $this->getRequest();
            if ($request instanceof HttpRequest && $request->isPost() && empty($request->getParam('form_key'))) {
                $request->setParam('form_key', $this->formKey->getFormKey());
            }
        }
    }
}
