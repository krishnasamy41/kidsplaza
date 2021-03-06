<?php
/**
 * @category    MT
 * @package     MT_KidsPlaza
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_KidsPlaza_Model_Catalog_Config extends Mage_Catalog_Model_Config{
    /**
     * Retrieve Attributes Used for Sort by as array
     * key = code, value = name
     *
     * @return array
     */
    public function getAttributeUsedForSortByArray()
    {
        $_module = Mage::app()->getRequest()->getModuleName();
        $options = array(
            'position'  => Mage::helper('catalog')->__('Position')
        );
        if ($_module == 'catalog' || $_module == 'collection'){
            $options['entity_id'] = Mage::helper('catalog')->__('Lastest');
            $options['bestsell']  = Mage::helper('catalog')->__('Bestsell');
        }
        foreach ($this->getAttributesUsedForSortBy() as $attribute) {
            /* @var $attribute Mage_Eav_Model_Entity_Attribute_Abstract */
            $options[$attribute->getAttributeCode()] = $attribute->getStoreLabel();
        }

        return $options;
    }
}