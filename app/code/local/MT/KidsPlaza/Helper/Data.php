<?php
/**
 * @category    MT
 * @package     MT_KidsPlaza
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_KidsPlaza_Helper_Data extends Mage_Core_Helper_Abstract{
    /**
     * Get JSON encoded configuration array which can be used for JS dynamic
     * price calculation depending on product options
     *
     * @param $view Mage_Catalog_Block_Product_View
     * @return string
     * @version 1.8.1.0
     */
    public function getJsonConfig($view) {
        $_request = Mage::getSingleton('tax/calculation')->getRateRequest(false, false, false);
        /* @var $product Mage_Catalog_Model_Product */
        $product = $view->getProduct();
        $_request->setProductClassId($product->getTaxClassId());
        $defaultTax = Mage::getSingleton('tax/calculation')->getRate($_request);

        $_request = Mage::getSingleton('tax/calculation')->getRateRequest();
        $_request->setProductClassId($product->getTaxClassId());
        $currentTax = Mage::getSingleton('tax/calculation')->getRate($_request);

        $_regularPrice = $product->getPrice();
        $_finalPrice = $product->getFinalPrice();
        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
            $_priceInclTax = Mage::helper('tax')->getPrice($product, $_finalPrice, true,
                null, null, null, null, null, false);
            $_priceExclTax = Mage::helper('tax')->getPrice($product, $_finalPrice, false,
                null, null, null, null, null, false);
        } else {
            $_priceInclTax = Mage::helper('tax')->getPrice($product, $_finalPrice, true);
            $_priceExclTax = Mage::helper('tax')->getPrice($product, $_finalPrice);
        }
        $_tierPrices = array();
        $_tierPricesInclTax = array();
        foreach ($product->getTierPrice() as $tierPrice) {
            $_tierPrices[] = Mage::helper('core')->currency($tierPrice['website_price'], false, false);
            $_tierPricesInclTax[] = Mage::helper('core')->currency(
                Mage::helper('tax')->getPrice($product, (int)$tierPrice['website_price'], true),
                false, false);
        }
        $config = array(
            'productId'           => $product->getId(),
            'priceFormat'         => Mage::app()->getLocale()->getJsPriceFormat(),
            'includeTax'          => Mage::helper('tax')->priceIncludesTax() ? 'true' : 'false',
            'showIncludeTax'      => Mage::helper('tax')->displayPriceIncludingTax(),
            'showBothPrices'      => Mage::helper('tax')->displayBothPrices(),
            'productPrice'        => Mage::helper('core')->currency($_finalPrice, false, false),
            'productOldPrice'     => Mage::helper('core')->currency($_regularPrice, false, false),
            'priceInclTax'        => Mage::helper('core')->currency($_priceInclTax, false, false),
            'priceExclTax'        => Mage::helper('core')->currency($_priceExclTax, false, false),
            /**
             * @var skipCalculate
             * @deprecated after 1.5.1.0
             */
            'skipCalculate'       => ($_priceExclTax != $_priceInclTax ? 0 : 1),
            'defaultTax'          => $defaultTax,
            'currentTax'          => $currentTax,
            'idSuffix'            => '_clone',
            'oldPlusDisposition'  => 0,
            'plusDisposition'     => 0,
            'plusDispositionTax'  => 0,
            'oldMinusDisposition' => 0,
            'minusDisposition'    => 0,
            'tierPrices'          => $_tierPrices,
            'tierPricesInclTax'   => $_tierPricesInclTax,
        );

        $responseObject = new Varien_Object();
        Mage::dispatchEvent('catalog_product_view_config', array('response_object' => $responseObject));
        if (is_array($responseObject->getAdditionalOptions())) {
            foreach ($responseObject->getAdditionalOptions() as $option => $value) {
                $config[$option] = $value;
            }
        }

        //Vietnamese format
        $config['priceFormat']['precision'] = 0;
        $config['priceFormat']['requiredPrecision'] = 0;

        return Mage::helper('core')->jsonEncode($config);
    }

    /**
     * Remove Vietnamese characters
     */
    public function normalize($input){
        if (!$input) return '';

        $unicode = array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'A'	=> 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ằ|Ẳ|Ẵ|Ặ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'd' => 'đ',
            'D'	=> 'Đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'E'	=> 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'I'	=> 'Í|Ì|Ỉ|Ĩ|Ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'O'	=> 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'U'	=> 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'Y'	=> 'Ý|Ỳ|Ỷ|Ỹ|Ỵ'
        );
        foreach($unicode as $nonUnicode => $uni){
            $input = preg_replace("/($uni)/i", $nonUnicode, $input);
        }

        return $input;
    }

    public function cleanCacheByObject($object){
        if ($object instanceof Mage_Catalog_Model_Product){
            /* @var $object Mage_Catalog_Model_Product */
            foreach ($object->getCategoryCollection() as $category){
                /* @var $category Mage_Catalog_Model_Category */
                $tags = array();
                foreach (explode('/', $category->getPath()) as $parentId){
                    $tags[] = "KIDSPLAZA_CATEGORY_{$parentId}";
                }
                if (count($tags)){
                    Mage::app()->cleanCache($tags);
                }
            }
        }elseif ($object instanceof Mage_Catalog_Model_Category){
            /* @var $object Mage_Catalog_Model_Category */
            Mage::app()->cleanCache(array("KIDSPLAZA_CATEGORY_{$object->getId()}"));
        }
    }

    public function resize($imageUrl, $width, $height=null, $dir=null){
        if (!$imageUrl) return '';
        if (!is_numeric($width)) return '';
        if (!$height) $height = $width;
        $base = Mage::getBaseDir('media').DS;
        if (!$dir){
            $dir = str_replace($base, '', dirname($imageUrl));
            $dir .= DS.$width.'x'.$height.DS;
        }
        if (!is_dir($base.$dir)){
            @mkdir($base.$dir, 0777, true);
        }
        if (!is_file($imageUrl)){
            // check if placeholder defined in config
            $baseDir = Mage::getSingleton('catalog/product_media_config')->getBaseMediaPath();
            $isConfigPlaceholder = Mage::getStoreConfig("catalog/placeholder/image_placeholder");
            $configPlaceholder = '/placeholder/' . $isConfigPlaceholder;
            if ($isConfigPlaceholder && $this->_fileExists($baseDir . $configPlaceholder)) {
                $file = $configPlaceholder;
            } else {
                // replace file with skin or default skin placeholder
                $skinBaseDir = Mage::getDesign()->getSkinBaseDir();
                $skinPlaceholder = "/images/catalog/product/placeholder/image.jpg";
                $file = $skinPlaceholder;
                if (file_exists($skinBaseDir . $file)) {
                    $baseDir = $skinBaseDir;
                } else {
                    $baseDir = Mage::getDesign()->getSkinBaseDir(array('_theme' => 'default'));
                    if (!file_exists($baseDir . $file)) {
                        $baseDir = Mage::getDesign()->getSkinBaseDir(array('_theme' => 'default', '_package' => 'base'));
                    }
                }
            }
            $imageUrl = $baseDir.$file;
        }
        $imageResized = $base.$dir.basename($imageUrl);
        if (!is_file($imageResized)){
            $imageObj = new Varien_Image($imageUrl);
            $imageObj->constrainOnly(true);
            $imageObj->keepAspectRatio(true);
            $imageObj->keepFrame(true);
            $imageObj->backgroundColor(array(255, 255, 255));
            $imageObj->resize($width, $height);
            $imageObj->save($imageResized);
        }
        return str_replace('\\', '/', Mage::getBaseUrl('media').$dir.basename($imageResized));
    }

    /**
     * First check this file on FS
     * If it doesn't exist - try to download it from DB
     *
     * @param string $filename
     * @return bool
     */
    protected function _fileExists($filename) {
        if (file_exists($filename)) {
            return true;
        } else {
            return Mage::helper('core/file_storage_database')->saveFileToFilesystem($filename);
        }
    }

    /**
     * Check if product already in wishlist
     */
    public function inWishlist($product){
        if (!$product || !($product instanceof Mage_Catalog_Model_Product)) return false;
        /* @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if (!$customer->getId()) return false;
        /* @var $wishlist Mage_Wishlist_Model_Wishlist */
        $wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($customer);
        if (!$wishlist->getId()) return false;
        foreach ($wishlist->getItemCollection() as $item){
            if ($item->getProductId()==$product->getId()) return true;
        }
        unset($customer, $wishlist);
        return false;
    }

    public function avatar($str=null){
        if (!$str) $str = microtime();
        return Mage::getBaseUrl().'identicon.php?size=60&hash='.md5($str);
    }

    public function getBrandUrl($brand, $label){
        if (!is_numeric($brand) || !is_string($label)) return '';
        $idPath = sprintf('collection/brand/%d', $brand);
        $urlModel = Mage::getModel('core/url_rewrite');
        /* @var $urlModel Mage_Core_Model_Url_Rewrite */
        $urlModel->setStoreId(Mage::app()->getStore()->getId());
        $urlModel->loadByIdPath($idPath);
        if ($urlModel->getId()) return Mage::getUrl().$urlModel->getRequestPath();
        else{
            $urlService = Mage::getModel('catalog/product_url');
            /* @var $urlService Mage_Catalog_Model_Product_Url */
            try{
                $urlModel->setIsSystem(0)
                    ->setIdPath($idPath)
                    ->setTargetPath('collection/view/brand/id/'.$brand)
                    ->setRequestPath('thuong-hieu/'.$urlService->formatUrlKey($label))
                    ->save();

                return Mage::getUrl().$urlModel->getRequestPath();
            }catch (Exception $e){
                return '';
            }
        }
    }

    public function getCustomRewriteUrl($url)
    {
        $urlModel = Mage::getModel('core/url_rewrite');
        /* @var $urlModel Mage_Core_Model_Url_Rewrite */
        $urlModel->setStoreId(Mage::app()->getStore()->getId());

        switch ($url){
            case 'customer/account/login':
            case 'customer/account/login/':
                $urlModel->setIdPath('customer/account/login/');
                $urlModel->setRequestPath('tai-khoan/dang-nhap');
                break;
            case 'customer/account/forgotpassword':
            case 'customer/account/forgotpassword/':
                $urlModel->setIdPath('customer/account/forgotpassword/');
                $urlModel->setRequestPath('tai-khoan/quen-mat-khau');
                break;
            case 'customer/account':
            case 'customer/account/':
            case 'customer/account/index':
            case 'customer/account/index/':
                $urlModel->setIdPath('customer/account/');
                $urlModel->setRequestPath('tai-khoan');
                break;
            case 'customer/account/edit':
            case 'customer/account/edit/':
                $urlModel->setIdPath('customer/account/edit/');
                $urlModel->setRequestPath('tai-khoan/thay-doi-thong-tin-tai-khoan');
                break;
            case 'sales/order/history':
            case 'sales/order/history/':
                $urlModel->setIdPath('sales/order/history/');
                $urlModel->setRequestPath('tai-khoan/don-hang-cua-toi');
                break;
            case 'customer/address':
            case 'customer/address/':
            case 'customer/address/index':
            case 'customer/address/index/':
                $urlModel->setIdPath('customer/address/index/');
                $urlModel->setRequestPath('tai-khoan/so-dia-chi');
                break;
            case 'customer/address/new':
            case 'customer/address/new/':
                $urlModel->setIdPath('customer/address/new/');
                $urlModel->setRequestPath('tai-khoan/so-dia-chi-moi');
                break;
            case 'customer/address/edit':
            case 'customer/address/edit/':
                $urlModel->setIdPath('customer/address/edit/');
                $urlModel->setRequestPath('tai-khoan/sua-dia-chi');
                break;
            case 'newsletter/manage':
            case 'newsletter/manage/':
            case 'newsletter/manage/index':
            case 'newsletter/manage/index/':
                $urlModel->setIdPath('newsletter/manage/index/');
                $urlModel->setRequestPath('tai-khoan/danh-sach-nhan-tin');
                break;
            case 'downloadable/customer/products':
            case 'downloadable/customer/products/':
                $urlModel->setIdPath('downloadable/customer/products/');
                $urlModel->setRequestPath('tai-khoan/san-pham-so');
                break;
            case 'wishlist':
            case 'wishlist/':
            case 'wishlist/index':
            case 'wishlist/index/':
            case 'wishlist/index/index':
            case 'wishlist/index/index/':
                $urlModel->setIdPath('wishlist/');
                $urlModel->setRequestPath('tai-khoan/danh-sach-yeu-thich');
                break;
            case 'mtpoint/point':
            case 'mtpoint/point/':
            case 'mtpoint/point/index':
            case 'mtpoint/point/index/':
                $urlModel->setIdPath('mtpoint/point');
                $urlModel->setRequestPath('tai-khoan/diem-tich-luy');
                break;
            case 'customer/account/logout':
            case 'customer/account/logout/':
                $urlModel->setIdPath('customer/account/logout/');
                $urlModel->setRequestPath('tai-khoan/dang-xuat');
                break;
        }

        $urlModel->loadByIdPath($urlModel->getIdPath());

        if ($urlModel->getId()){
            return Mage::getUrl().$urlModel->getRequestPath();
        }else{
            try{
                $urlModel->setIsSystem(0);
                $urlModel->setTargetPath($urlModel->getIdPath());
                $urlModel->save();
                return Mage::getUrl().$urlModel->getRequestPath();
            }catch (Exception $e){
                return Mage::getUrl($url);
            }
        }
    }

    public function getProductsCombo($productId)
    {
        $model = Mage::getModel('catalog/product')->load($productId);
        $productIds = explode(',', $model->getProductsCombo());
        $products = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', array('in' => $productIds));
        $products->load();
        return $products;
    }

    public function countdown($product){
        if ($product instanceof Mage_Catalog_Model_Product && $product->getGrouponEnable()) {
            return sprintf('<div class="groupon-timedown" data-cdate="%s"></div>', date('Y/m/d H:i', strtotime($product->getGrouponTo())));
        }
    }

    public function getDiscount($product){
        if (!$product) return;
        if ($product->getPrice() == 0) return;
        return floor(($product->getPrice()-$product->getFinalPrice())*100/$product->getPrice());
    }
}