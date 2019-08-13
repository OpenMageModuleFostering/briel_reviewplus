<?php

class Briel_ReviewPlus_Block_Adminhtml_Clientlog_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	
	public function __construct() {
        parent::__construct();
        $this->setId('clientlog');
        $this->setDefaultSort('id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
	}
	
	public function getRowUrl($row) {
		// return $this->getUrl('reviewplus/edit/index', array('id' => $row->getId()));
	}
	
	protected function _prepareCollection() {
        $collection = Mage::getModel('reviewplus/clientlog')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
	
	protected function _prepareColumns() {
		
        $this->addColumn('id', array(
            'header'    => Mage::helper('reviewplus')->__('ID'),
            'align'     =>'right',
            'width'     =>'50px',
            'index'     =>'id',
        ));
		
		$this->addColumn('order_id', array(
            'header'    => Mage::helper('reviewplus')->__('Order Increment ID'),
            'align'     =>'right',
            'width'     =>'100px',
            'index'     =>'order_id',
            'renderer'	=>'Briel_ReviewPlus_Block_Adminhtml_Clientlog_Renderer_Orderid',
        ));
		
		 $this->addColumn('customer_name', array(
            'header'    => Mage::helper('reviewplus')->__('Customer Name'),
            'align'     =>'left',
            'index'     =>'customer_name',
            'width'		=>'250px',
        ));
		
		$this->addColumn('customer_email', array(
            'header'    => Mage::helper('reviewplus')->__('Customer Email'),
            'align'     =>'left',
            'index'     =>'customer_email',
            'width'		=>'250px',
        ));
		
		$this->addColumn('ordered_products', array(
            'header'    => Mage::helper('reviewplus')->__('Ordered Product(s)'),
            'align'     =>'left',
            'index'     =>'ordered_products',
        ));
		
		$this->addColumn('send_time', array(
            'header'    => Mage::helper('reviewplus')->__('Send Time'),
            'align'     =>'left',
            'index'     =>'send_time',
            'type' 		=>'datetime',
            'width'		=>'150px',
            'renderer'  =>'Briel_ReviewPlus_Block_Adminhtml_Clientlog_Renderer_Sendtime',
            'filter_condition_callback' => array($this, '_datetimeFilter'),
        ));
		
		$this->addColumn('status', array(
            'header'    => Mage::helper('reviewplus')->__('Status'),
            'align'     =>'left',
            'index'     =>'status',
            'width'     =>'70px',
            'renderer'  =>'Briel_ReviewPlus_Block_Adminhtml_Clientlog_Renderer_Status',
            'type'  	=>'options',
            'options' 	=> array('0' => 'Not sent', '1' => 'Sent'),
        ));
		
		return parent::_prepareColumns();
	}

	protected function _prepareMassaction() {
		$this->setMassactionIdField('id');
		$this->getMassactionBlock()->setFormFieldName('clientlog_id');
		$this->getMassactionBlock()->addItem('delete', array(
			'label'=> Mage::helper('reviewplus')->__('Delete'),
			'url'  => $this->getUrl('*/*/massdelete', array('' => '')),
			'confirm' => Mage::helper('reviewplus')->__('Are you sure?')
		));
		$this->getMassactionBlock()->addItem('send', array(
			'label'=> Mage::helper('reviewplus')->__('Send followup email'),
			'url'  => $this->getUrl('*/*/masssend', array('' => '')),
			'confirm' => Mage::helper('reviewplus')->__('Send followup email?')
		));
		return $this;
	}
	
	protected function _datetimeFilter($collection, $column) {
		if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
		if (empty($value['from']) && isset($value['to'])) {
			$to = $value['to']->getTimestamp();
			$from = mktime(0, 0, 0);
			$this->getCollection()->addFieldToFilter('send_time', array('lteq' => $to));
			return $this;
		} else if (empty($value['to']) && isset($value['from'])) {
			$from = $value['from']->getTimestamp();
			$this->getCollection()->addFieldToFilter('send_time', array('gteq' => $from));
			return $this;
		} else {
			$from = $value['from']->getTimestamp();
			$to = $value['to']->getTimestamp();
			$this->getCollection()->addFieldToFilter('send_time', array('from' => $from, 'to' => $to));
			return $this;
		}
	}
}
?>