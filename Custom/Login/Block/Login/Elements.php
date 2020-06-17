<?php

/**
 * Custom_Login
 * @package Custom_Login
 */

namespace Custom\Login\Block\Login;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Data\Form\FormKey;
use Magento\Store\Model\StoreManagerInterface;

class Elements extends AbstractBlock
{
    private $storeManager;
    private $formkey;

    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        array $data = [],
        FormKey $formkey
    ) {

        $this->storeManager = $storeManager;
        $this->formkey = $formkey;
        parent::__construct($context, $data);
    }

    public function _toHtml()
    {
        $html = '';

        $formkey = $this->formkey->getFormKey();
        $loginUrl = $this->storeManager
                    ->getStore()
                    ->getUrl("mysso/custom/login");
        
            $html .= '
    <div class="block-title">
       <strong role="heading">Custom Login</strong>
    </div>
    <div class="block-content">
       <form id="custom_login" action="'.$loginUrl.'" method="post" novalidate="novalidate">
       <input name="form_key" type="hidden" value="'.$formkey.'">
       <button type="submit" class="action login primary" name="Custom Login" id="customsend"><span>Custom Login</span></button>
       </form>
    </div>';
        return $html;
    }
}
