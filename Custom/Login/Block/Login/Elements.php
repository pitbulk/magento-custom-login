<?php

/**
 * Custom_Login
 * @package Custom_Login
 */

namespace Custom\Login\Block\Login;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;

class Elements extends AbstractBlock
{
    private $storeManager;

    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {

        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    public function _toHtml()
    {
        $html = '';

        $loginUrl = $this->storeManager
                    ->getStore()
                    ->getUrl("mysso/custom/login");
        
            $html .= '
    <div class="block-title">
       <strong role="heading">Custom Login</strong>
    </div>
    <div class="block-content">
       <a class="action login primary"
          href="'.$loginUrl.'">Custom login</a>
    </div>';
        return $html;
    }
}
