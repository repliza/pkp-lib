<?php

/**
 * @file classes/reviewForm/ReviewFormResponseDAO.inc.php
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2000-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class ReviewFormResponseDAO
 * @ingroup reviewForm
 * @see ReviewFormResponse
 *
 * @brief Operations for retrieving and modifying ReviewFormResponse objects.
 *
 */

import ('lib.pkp.classes.customForm.CustomFormResponseDAO');
import ('lib.pkp.classes.reviewForm.ReviewFormResponse');

class ReviewFormResponseDAO extends CustomFormResponseDAO {
	//
	// Overridden methods from CustomFormElementDAO
	//
	protected function createCustomFormDAOContext() {
		import('lib.pkp.classes.context.reviewForms.ReviewFormDAOContext');
		return new ReviewFormDAOContext();
	}

	/**
	 * Retrieve a review form response.
	 * @param $reviewId int
	 * @param $reviewFormElementId int
	 * @return ReviewFormResponse
	 */
	function &getReviewFormResponse($reviewId, $reviewFormElementId) {
		return parent::getCustomFormResponse(ASSOC_TYPE_REVIEW_ASSIGNMENT, $reviewId, $reviewFormElementId);
	}

	/**
	 * Construct a new data object corresponding to this DAO.
	 * @return ReviewFormResponse
	 */
	function newDataObject() {
		return new ReviewFormResponse();
	}

	/**
	 * Delete review form responses by review ID
	 * @param $reviewId int
	 */
	function deleteByReviewId($reviewId) {
		return parent::deleteByAssoc(ASSOC_TYPE_REVIEW_ASSIGNMENT, $reviewId);
	}

	/**
	 * Delete group membership by user ID
	 * @param $reviewFormElementId int
	 */
	function deleteByReviewFormElementId($reviewFormElementId) {
		return parent::deleteByCustomFormElementId($reviewFormElementId);
	}

	/**
	 * Retrieve all review form responses for a review in an associative array.
	 * @param $reviewId int
	 * @return array review_form_element_id => array(review form response for this element)
	 */
	function &getReviewReviewFormResponseValues($reviewId) {
		return parent::getCustomFormResponseValues(ASSOC_TYPE_REVIEW_ASSIGNMENT, $reviewId);
	}

	/**
	 * Check if a review form response for the review.
	 * @param $reviewId int
	 * @param $reviewFormElementId int optional
	 * @return boolean
	 */
	function reviewFormResponseExists($reviewId, $reviewFormElementId = null) {
		return parent::customFormResponseExists(ASSOC_TYPE_REVIEW_ASSIGNMENT, $reviewId, $reviewFormElementId = null);
	}
}

