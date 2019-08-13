<?php

class Briel_ReviewPlus_Adminhtml_ReviewsController extends Mage_Adminhtml_Controller_Action {
	
	public function indexAction() {
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function massdeleteAction() {
		$post = $this->getRequest()->getPost();
		$review_ids = $post['review_id'];
		if (!is_array($review_ids)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('reviewplus')->__('Please select at least one entry.'));
		} else {
			try {
				foreach($review_ids as $id) {
					Mage::getModel('reviewplus/reviews')->load($id)->delete();
				}
				$success_msg = $this->__('Entries deleted successfully.');
	            Mage::getSingleton('adminhtml/session')->addSuccess($success_msg);
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}

	public function massapproveAction() {
		$post = $this->getRequest()->getPost();
		$review_ids = $post['review_id'];
		if (!is_array($review_ids)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('reviewplus')->__('Please select at least one entry.'));
		} else {
			try {
				foreach($review_ids as $id) {
					// compile review data array
					$reviewplus_reviews_db = Mage::getModel('reviewplus/reviews')->load($id);
					$nickname = $reviewplus_reviews_db->getData('customer_name');
					$detail = $reviewplus_reviews_db->getData('product_review');
					$rtng = $reviewplus_reviews_db->getData('product_rating');
					$sku = $reviewplus_reviews_db->getData('product_sku');
					$store_id = $reviewplus_reviews_db->getData('store_id');
					$review_status = $reviewplus_reviews_db->getData('review_status');
					if ($review_status == 'approved') {
						$error_msg = $this->__('Review #'.$id.' already approved.');
	            		Mage::getSingleton('adminhtml/session')->addError($error_msg);
					} else {
						$product_id = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku)->getId();
						$reviewplus_reviews_db->setData('review_status', 'approved')->save();
						// get active RATING options
						$rating_collection = Mage::getModel('rating/rating')
							->getResourceCollection()
							->addEntityFilter('product')
							->setPositionOrder()
							->addRatingPerStoreName(Mage::app()->getStore()->getId())
							->setStoreFilter(Mage::app()->getStore()->getId())
							->load()
							->addOptionToItems();
						$rating = array();
						foreach($rating_collection as $rating_option) {
							$rating[$rating_option->getId()] = $rtng;
						}
						$review_data = array(
							'ratings' => $rating,
							'validate_rating' => '', 
							'nickname' => $nickname, 
							'title' => '', 
							'detail' => $detail
						);
						$review_db = Mage::getModel('review/review')->setData($review_data);
						$review_db->setEntityId($review_db->getEntityIdByCode(Mage_Review_Model_Review::ENTITY_PRODUCT_CODE))
								->setEntityPkValue($product_id)
								->setStatusId(Mage_Review_Model_Review::STATUS_APPROVED)
								->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
								->setStoreId($store_id)
								->setStores(array($store_id))
								->save();
						foreach ($rating as $rating_id => $option_id) {
						Mage::getModel('rating/rating')->setRatingId($rating_id)
								->setReviewId($review_db->getId())
								->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
								->addOptionVote($option_id, $product_id);
						}
	                    $review_db->aggregate();
						$success_msg = $this->__('Review #'.$id.' approved successfully.');
	            		Mage::getSingleton('adminhtml/session')->addSuccess($success_msg);
					}
				}
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	} // end of moethod
	
} // end of class
?>