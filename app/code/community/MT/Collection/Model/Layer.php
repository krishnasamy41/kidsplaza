<?php
/**
 * @category    MT
 * @package     MT_Collection
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Collection_Model_Layer extends Mage_Catalog_Model_Layer{
    public function getProductCollection(){
        if (!isset($this->_productCollections[0])){
            switch (Mage::registry('current_collection')){
                case 'brand':
                    $collection = $this->_getBrandCollection();
                    break;
                case 'daily':
                    $collection = $this->_getDailyCollection();
                    break;
                case 'promotion':
                    $collection = $this->_getPromotionCollection();
                    break;
                case 'bestseller':
                    $collection = $this->_getBestsellerCollection();
                    break;
                case 'mostviewed':
                    $collection = $this->_getMostViewedCollection();
                    break;
                case 'lastest':
                default:
                    $collection = $this->_getLastestCollection();
                    break;
            }
            $this->_productCollections[0] = $collection;
        }
        return $this->_productCollections[0];
    }

    protected function _getBrandCollection(){
        $brandId = Mage::registry('current_brand');
        $collection = Mage::getResourceModel('catalog/product_collection');
        $collection->addAttributeToSelect('brand');
        $collection->addAttributeToFilter('brand', array('eq' => $brandId));
        $this->prepareProductCollection($collection);
        return $collection;
    }

    protected function _getDailyCollection(){
        /* @var $date Mage_Core_Model_Date */
        $date = Mage::getModel('core/date');
        $today = $date->date('Y-m-d');
        $collection = Mage::getResourceModel('catalog/product_collection');
        $collection->addAttributeToSelect('groupon_enable');
        $collection->addAttributeToFilter('groupon_enable', array('eq' => 1));
        $collection->addAttributeToSelect('groupon_to');
        $collection->addAttributeToFilter('groupon_to', array(
            'from'  => sprintf('%s 00:00:00', $today),
            'to'    => sprintf('%s 23:59:59', $today)
        ));
        $this->prepareProductCollection($collection);
        return $collection;
    }

    protected function _getPromotionCollection(){
        $categoryId = Mage::app()->getRequest()->getParam('parent');
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $websiteId = (int)Mage::app()->getWebsite()->getId();
        /* @var $session Mage_Customer_Model_Session */
        $session = Mage::getSingleton('customer/session');
        $customerGroupId = $session->isLoggedIn() ? $session->getCustomer()->getGroupId() : 0;

        $select = $connection->select()
            ->from('catalog_product_index_price', array('entity_id'))
            ->where('price > final_price')
            ->where('website_id = ?', $websiteId)
            ->where('customer_group_id = ?', $customerGroupId);

        if ($categoryId){
            $select
                ->join(
                    array('c' => 'catalog_category_product_index'),
                    join(' AND ', array('c.product_id = entity_id')),
                    array()
                )
                ->where('c.store_id = ?', Mage::app()->getStore()->getId())
                ->where('c.category_id = ?', $categoryId);
        }

        $collection = Mage::getResourceModel('catalog/product_collection');
        $collection->getSelect()->join(
            array('e2' => $select),
            join(' AND ', array('e2.entity_id = e.entity_id')),
            array()
        );
        $this->prepareProductCollection($collection);
        unset($connection, $select);
        return $collection;
    }

    protected function _getMostViewedCollection(){
        $categoryId = Mage::app()->getRequest()->getParam('parent');
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $storeId = (int)Mage::app()->getStore()->getId();

        $select = $connection->select()
            ->from('report_event', array('object_id', 'views' => 'COUNT(report_event.event_id)'))
            ->join('report_event_types', 'report_event.event_type_id = report_event_types.event_type_id', array())
            ->where('report_event.store_id = ?', $storeId)
            ->where('report_event_types.event_name = ?', 'catalog_product_view')
            ->group('report_event.object_id')
            ->order('views DESC')
            ->having('views > ?', 0);

        if ($categoryId){
            $select
                ->join(
                    array('c' => 'catalog_category_product_index'),
                    join(' AND ', array('c.product_id = object_id')),
                    array()
                )
                ->where('c.store_id = ?', Mage::app()->getStore()->getId())
                ->where('c.category_id = ?', $categoryId);
        }

        $collection = Mage::getResourceModel('catalog/product_collection');
        $collection->getSelect()->join(
            array('e2' => $select),
            join(' AND ', array('e2.object_id = e.entity_id')),
            array()
        );
        $this->prepareProductCollection($collection);
        unset($connection, $select);
        return $collection;
    }

    protected function _getBestsellerCollection(){
        $categoryId = Mage::app()->getRequest()->getParam('parent');
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');

        $select = $connection->select()
            ->from('sales_flat_order_item', array('product_id', 'count' => 'SUM(sales_flat_order_item.qty_ordered)'))
            ->join(
                'sales_flat_order',
                'sales_flat_order_item.order_id = sales_flat_order.entity_id',
                array())
            ->where('sales_flat_order.status = ?', 'complete')
            ->group('sales_flat_order_item.product_id')
            ->order('count DESC');

        if ($categoryId){
            $select
                ->join(
                    array('c' => 'catalog_category_product_index'),
                    join(' AND ', array('c.product_id = sales_flat_order_item.product_id')),
                    array()
                )
                ->where('c.store_id = ?', Mage::app()->getStore()->getId())
                ->where('c.category_id = ?', $categoryId);
        }

        $collection = Mage::getResourceModel('catalog/product_collection');
        $collection->getSelect()->join(
            array('e2' => $select),
            join(' AND ', array('e2.product_id = e.entity_id')),
            array()
        );
        $this->prepareProductCollection($collection);
        unset($connection, $select);
        return $collection;
    }

    protected function _getLastestCollection(){
        $categoryId = Mage::app()->getRequest()->getParam('parent');
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');

        $select = $connection->select()
            ->from(array('p' => 'catalog_product_entity'), array('entity_id', 'updated_at'))
            ->order('p.entity_id DESC')
            ->limit(100);

        if ($categoryId){
            $select
                ->join(
                    array('c' => 'catalog_category_product_index'),
                    join(' AND ', array('c.product_id = p.entity_id')),
                    array()
                )
                ->where('c.store_id = ?', Mage::app()->getStore()->getId())
                ->where('c.category_id = ?', $categoryId);
        }

        $collection = Mage::getResourceModel('catalog/product_collection');
        $collection->getSelect()->join(
            array('e2' => $select),
            join(' AND ', array('e2.entity_id = e.entity_id')),
            array()
        );

        $this->prepareProductCollection($collection);
        unset($connection, $select);
        return $collection;
    }
}