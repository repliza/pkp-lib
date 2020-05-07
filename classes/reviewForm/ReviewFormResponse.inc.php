<?php

/**
 * @file classes/reviewForm/ReviewFormResponse.inc.php
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2000-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class ReviewFormResponse
 * @ingroup reviewForm
 * @see ReviewFormResponseDAO
 *
 * @brief Basic class describing a review form response.
 *
 */

import ('lib.pkp.classes.customForm.CustomFormResponse');

class ReviewFormResponse extends CustomFormResponse {

	public function __construct() {
		parent::__construct();

		$this->setAssocType(ASSOC_TYPE_REVIEW_ASSIGNMENT);
	}

	//
	// Get/set methods
	//

	/**
	 * Get the review ID.
	 * @return int
	 */
	function getReviewId() {
		return $this->getAssocId();
	}

	/**
	 * Set the review ID.
	 * @param $reviewId int
	 */
	function setReviewId($reviewId) {
		$this->setAssocType(ASSOC_TYPE_REVIEW_ASSIGNMENT);
		$this->setAssocId($reviewId);
	}

	/**
	 * Get ID of review form element.
	 * @return int
	 */
	function getReviewFormElementId() {
		return parent::getCustomFormElementId();
	}

	/**
	 * Set ID of review form element.
	 * @param $reviewFormElementId int
	 */
	function setReviewFormElementId($reviewFormElementId) {
		parent::setCustomFormElementId($reviewFormElementId);
	}

	/**
	 * Set all data variables at once.
	 * @param $data array
	 */
	function setAllData(&$data) {
		$this->_data =& $data;

		$this->setAssocType(ASSOC_TYPE_REVIEW_ASSIGNMENT);
	}
}


