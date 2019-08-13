<?php

class Briel_ReviewPlus_IndexController extends Mage_Core_Controller_Front_Action {

	public function indexAction() {
		$this->loadLayout();
		$this->renderLayout();
	}

	public function rateAction() {
		$post = $this->getRequest()->getPost();
		$skus = $post['sku'];
		foreach($skus as $sku) {
			// write details to REVIEWPLUS_REVIEWS table
			$reviews_db = Mage::getModel('reviewplus/reviews')->load($post['rating-id'][$sku]);
			$reviews_db->setData('product_review', $post['detail'][$sku])->save();
		} // end sku FOREACH
		$this->_redirect('/');
	} // end of method
	
	
	/*public function oldrateAction() {
		$post = $this->getRequest()->getPost();
		$skus = $post['sku'];
		foreach($skus as $sku) {
			// write data to REVIEWPLUS_REVIEWS table
			$reviews_db = Mage::getModel('reviewplus/reviews')->load($post['rating-id'][$sku]);
			$reviews_db->setData('product_review', $post['detail'][$sku])->save();
			// shape $rating into acceptable form
			$rating = array("1" => $post['rating'][$sku], "2" => $post['rating'][$sku], "3" => $post['rating'][$sku]);
			// write data to MAGENTO REVIEWS table; will appear on product reviews grid for approval
			$review_data = array('ratings' => array("1" => $post['rating'][$sku], "2" => $post['rating'][$sku], "3" => $post['rating'][$sku]), 'validate_rating' => '', 'nickname' => $post['nickname'][$sku], 'title' => $post['title'][$sku], 'detail' => $post['detail'][$sku]);
			if (!empty($review_data)) {
				$review_db = Mage::getModel('review/review')->setData($review_data);
				$validate = $review_db->validate();
				if ($validate === true) {
					try {
						$review_db->setEntityId($review_db->getEntityIdByCode(Mage_Review_Model_Review::ENTITY_PRODUCT_CODE))
								  ->setEntityPkValue($post['product-id'][$sku])
								  ->setStatusId(Mage_Review_Model_Review::STATUS_PENDING)
								  ->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
								  ->setStoreId(Mage::app()->getStore()->getId())
								  ->setStores(array(Mage::app()->getStore()->getId()))
								  ->save();
						foreach ($rating as $rating_id => $option_id) {
							Mage::getModel('rating/rating')->setRatingId($rating_id)
														   ->setReviewId($review_db->getId())
														   ->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
														   ->addOptionVote($option_id, $post['product-id'][$sku]);
						}		
						$review_db->aggregate();
					} catch (Exception $e) {
						$session = Mage::getSingleton('core/session');
						$session->addError($this->__('Unable to post the review.'));
					}
				}
			}
		}
		$this->_redirect('');
	}*/
}
?>