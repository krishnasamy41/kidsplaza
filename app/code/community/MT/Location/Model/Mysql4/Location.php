<?php
/**
 * @category    MT
 * @package     MT_Location
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Location_Model_Mysql4_Location extends Mage_Core_Model_Mysql4_Abstract{
    public function _construct(){
        $this->_init('mtlocation/location', 'id');
    }
}