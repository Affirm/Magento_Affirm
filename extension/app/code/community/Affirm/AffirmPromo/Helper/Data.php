<?php
class Affirm_AffirmPromo_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Get configuration settings for current page
     * @return Varien_Object $config
     */
    public function getSectionConfig()
    {
        $codeMap = array(
            Mage::getStoreConfig('affirmpromo/developer_settings/path_catalog_product')=>'catalog_product',
            Mage::getStoreConfig('affirmpromo/developer_settings/path_catalog_category')=>'catalog_category',
            Mage::getStoreConfig('affirmpromo/developer_settings/path_homepage')=>'homepage',
            Mage::getStoreConfig('affirmpromo/developer_settings/path_checkout_cart')=>'checkout_cart'
        );
        $config = new Varien_Object();
        $module = Mage::app()->getRequest()->getModuleName();
        $controller = Mage::app()->getRequest()->getControllerName();
        $action = Mage::app()->getRequest()->getActionName();

        if (isset($codeMap[$module.'.'.$controller.'.'.$action])){
            $pageCode = $codeMap[$module.'.'.$controller.'.'.$action];
            $size = Mage::getStoreConfig('affirmpromo/'.$pageCode.'/size');
            $position = Mage::getStoreConfig('affirmpromo/'.$pageCode.'/position');
            list($positionHorizontal, $positionVertical) = explode('-',$position);
            $display = Mage::getStoreConfig('affirmpromo/'.$pageCode.'/display');
            $config->setPageCode($pageCode)
                ->setDisplay($display)
                ->setSize($size)
                ->setPositionHorizontal($positionHorizontal)
                ->setPositionVertical($positionVertical);
        }
        return $config;
    }

    /**
     * Get html code for promo snippet
     *
     * @param string $pageCode
     * @return string $snippet
     */
    public function getSnippetCode($pageCode='')
    {
        if (!Mage::getStoreConfig('affirmpromo/settings/active') || !$this->getSectionConfig()->getDisplay()) {
            return '';
        }

        $id = Mage::getStoreConfig('affirmpromo/settings/id');
        $container = Mage::getStoreConfig('affirmpromo/developer_settings/container');
        $size = $this->getSectionConfig()->getSize();
        $apiKey = Mage::getStoreConfig('payment/affirm/api_key');
        $promoKey = Mage::getStoreConfig('affirmpromo/settings/promo_key');

        $snippet = '<div class="affirm-promo" data-promo-size="'.$size.'" data-promo-key="'.$promoKey.'"></div>
                    <script>
                    var _affirm_config = {
                        public_api_key:         "'.$apiKey.'",
                        script:                 "https://dux8ocbmxvxce.cloudfront.net/js/v2/affirm.js"
                    };
                    (function(l,g,m,e,a,f,b){var d,c=l[m]||{},
                        h=document.createElement(f),
                        n=document.getElementsByTagName(f)[0],
                        k=function(a,b,c){
                            return function(){a[b]._.push([c,arguments])}
                        };
                        c[e]=k(c,e,"set");
                        d=c[e];c[a]={};c[a]._=[];d._=[];c[a][b]=k(c,a,b);a=0;
                        for(b="set add save post open empty reset on off trigger ready setProduct".split(" ");
                            a<b.length;a++)d[b[a]]=k(c,e,b[a]);a=0;for(b=["get","token","url","items"];
                            a<b.length;a++)d[b[a]]=function(){};h.async=!0;
                        h.src=g[f];n.parentNode.insertBefore(h,n);
                        delete g[f];d(g);l[m]=c})(window,_affirm_config,
                        "affirm","checkout","ui","script","ready");
                    </script>';

        if (!empty($container)) {
            // TODO(brian): handle malformed container string
            $snippet = str_replace('{container}', $snippet, $container);
        }
        return $snippet;
    }

    /**
     * Get cart total price
     * @return string
     */
    public  function getCartPrice()
    {
        $price = (string)(Mage::helper('checkout/cart')->getQuote()->getGrandTotal()>0 ?
            Mage::app()->getStore()->formatPrice(
                Mage::helper('checkout/cart')->getQuote()->getGrandTotal(), false) : '');

         if ($price[0] == '$'){
             $price = substr($price, 1);
         }
         return $price;
    }

    /**
     * Get item price (in case customer on product view page)
     * @return string
     */
    public function getItemPrice()
    {
        $request = Mage::app()->getRequest();
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        if ($module.$controller.$action == 'catalogproductview'){
            $prodId = $request->getParam('id');
            $product = Mage::getModel('catalog/product')->load($prodId);
            if ($product && $product->getPrice()){
                $price = (string)Mage::app()->getStore()->formatPrice($product->getPrice(), false);
            }
            if ($price[0] == '$'){
                $price = substr($price, 1);
            }
            return $price;
        }
        return '';
    }

    /**
     * Get page name
     * @return string
     */
    public  function getPageName()
    {
        $path = Mage::app()->getRequest()->getOriginalPathInfo();
        if (empty($path) || $path == '/'){
            return 'home';
        } else if ($path[0] == '/' || $path[strlen($path)-1]=='/'){
            if ($path[0] == '/'){
                $path = substr($path, 1);
            }
            if ($path[strlen($path)-1]=='/'){
                $path = substr($path, 0, -1);
            }
        }
        return $path;
    }
}
