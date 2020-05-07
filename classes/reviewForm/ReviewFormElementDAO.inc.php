<?php

/**
 * @file classes/reviewForm/ReviewFormElementDAO.inc.php
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2000-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class ReviewFormElementDAO
 * @ingroup reviewForm
 * @see ReviewFormElement
 *
 * @brief Operations for retrieving and modifying ReviewFormElement objects.
 *
 */

import ('lib.pkp.classes.customForm.CustomFormElementDAO');
import ('lib.pkp.classes.reviewForm.ReviewFormElement');

class ReviewFormElementDAO extends CustomFormElementDAO {

	//
	// Overridden methods from CustomFormElementDAO
	//
	protected function createCustomFormDAOContext() {
		import('lib.pkp.classes.context.reviewForms.ReviewFormDAOContext');
		return new ReviewFormDAOContext();
	}

	/**
	 * Returns the map with key value pairs of {db column name} => {db value}
	 * @param $customFormElement customFormElement
	 * @return array
	 */
	protected function _getDbValueMap($customFormElement) {
		return array_merge(
			parent::_getDbValueMap($customFormElement),
			array(
				'included' => (int) $customFormElement->getIncluded()
			)
		);
	}

	/**
	 * @copydoc CustomFormElementDAO::__construct()
	 */
	function __construct($dataSource = null, $callHooks = true) {
		parent::__construct($dataSource, $callHooks);

		HookRegistry::register('ReviewFormElementDAO::_fromRow', array($this, 'reviewFormElementDAOFromRow'));
	}

	function reviewFormElementDAOFromRow($hookName, $args) {
		$customFormElement =& $args[0];
		$row =& $args[1];

		$customFormElement->setIncluded($row['included']);

		return false;
	}

	/**
	 * Construct a new data object corresponding to this DAO.
	 * @return ReviewFormElement
	 */
	function newDataObject() {
		return new ReviewFormElement();
	}

	/**
	 * Retrieve all elements for a review form.
	 * @param $reviewFormId int
	 * @param $rangeInfo object RangeInfo object (optional)
	 * @param $included boolean True for only included comments; false for non-included; null for both
	 * @return DAOResultFactory containing ReviewFormElements ordered by sequence
	 */
	function getByReviewFormId($reviewFormId, $rangeInfo = null, $included = null) {
		return parent::getByCustomFormId($reviewFormId, $rangeInfo, $included);
	}

	/**
	 * Retrieve ids of all required elements for a review form.
	 * @param $reviewFormId int
	 * return array
	 */
	function getRequiredReviewFormElementIds($reviewFormId) {
		return parent::getRequiredCustomFormElementIds($reviewFormId);
	}

	/**
	 * Check if a review form element exists with the specified ID.
	 * @param $reviewFormElementId int
	 * @param $reviewFormId int optional
	 * @return boolean
	 */
	function reviewFormElementExists($reviewFormElementId, $reviewFormId = null) {
		return parent::customFormElementExists($reviewFormElementId, $reviewFormId);
	}

	/**
	 * Sequentially renumber a review form elements in their sequence order.
	 * @param $reviewFormId int
	 */
	function resequenceReviewFormElements($reviewFormId) {
		parent::resequenceCustomFormElements($reviewFormId);
	}
}

