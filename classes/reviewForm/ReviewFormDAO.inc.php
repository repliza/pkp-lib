<?php

/**
 * @file classes/reviewForm/ReviewFormDAO.inc.php
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2000-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class ReviewFormDAO
 * @ingroup reviewForm
 * @see ReviewerForm
 *
 * @brief Operations for retrieving and modifying ReviewForm objects.
 *
 */

import('lib.pkp.classes.customForm.CustomFormDAO');
import('lib.pkp.classes.reviewForm.ReviewForm');

class ReviewFormDAO extends CustomFormDAO {

	//
	// Overridden methods from CustomFormDAO
	//
	protected function createCustomFormDAOContext() {
		import('lib.pkp.classes.context.reviewForms.ReviewFormDAOContext');
		return new ReviewFormDAOContext();
		}

	protected function _getFormsQueryColumns($formTableNameAlias) {
		return parent::_getFormsQueryColumns($formTableNameAlias) . ',
				SUM(CASE WHEN ra.date_completed IS NOT NULL THEN 1 ELSE 0 END) AS complete_count,
			SUM(CASE WHEN ra.review_id IS NOT NULL AND ra.date_completed IS NULL THEN 1 ELSE 0 END) AS incomplete_count';
		}

	protected function _getFormsQueryJoins($formTableNameAlias) {
		$customFormDAOContext = $this->getCustomFormDAOContext();

		return 'LEFT JOIN review_assignments ra ON (ra.' . $customFormDAOContext->getFormIdTableColumnName() . ' = ' . $formTableNameAlias . '.' . $customFormDAOContext->getFormIdTableColumnName() . ' AND ra.declined<>1)';
	}



	/**
	 * Construct a new data object corresponding to this DAO.
	 * @return ReviewForm
	 */
	function newDataObject() {
		return new ReviewForm();
	}

	protected function initDataObjectFromRow($customForm, $row) {
		parent::initDataObjectFromRow($customForm, $row);

		$customForm->setCompleteCount($row['complete_count']);
		$customForm->setIncompleteCount($row['incomplete_count']);
	}

	/**
	 * Check if a review form exists with the specified ID.
	 * @param $reviewFormId int
	 * @param $assocType int
	 * @param $assocId int
	 * @return boolean
	 */
	function reviewFormExists($reviewFormId, $assocType, $assocId) {
		return parent::customFormExists($reviewFormId, $assocType, $assocId);
	}

	/**
	 * Check if a review form exists with the specified ID.
	 * @param $reviewFormId int
	 * @param $assocType int optional
	 * @param $assocId int optional
	 * @return boolean
	 */
	function unusedReviewFormExists($reviewFormId, $assocType = null, $assocId = null) {
		$reviewForm = $this->getById($reviewFormId, $assocType, $assocId);
		if (!$reviewForm) return false;
		if ($reviewForm->getCompleteCount()!=0 || $reviewForm->getIncompleteCount()!=0) return false;
		return true;
	}

	/**
	 * Sequentially renumber review form in their sequence order.
	 * @param $assocType int
	 * @param $assocId int
	 */
	function resequenceReviewForms($assocType, $assocId) {
		parent::resequenceCustomForms($assocType, $assocId);
		}
}

