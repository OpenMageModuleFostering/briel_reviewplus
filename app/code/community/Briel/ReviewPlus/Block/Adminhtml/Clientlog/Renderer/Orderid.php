<?php

class Briel_ReviewPlus_Block_Adminhtml_Clientlog_Renderer_Orderid extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	
	public function render(Varien_Object $row) {
		$value =  $row->getData($this->getColumn()->getIndex());
		$order_db = Mage::getModel('sales/order')->load($value);
		$increment_id = $order_db->getIncrementId();
		return $increment_id;
	}
}
?>