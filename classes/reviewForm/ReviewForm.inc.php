<?php

/**
 * @defgroup reviewForm Review Form
 * Implements review forms, which are forms that can be created and customized
 * by the manager and presented to the reviewer in order to assess submissions.
 */

/**
 * @file classes/reviewForm/ReviewForm.inc.php
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2000-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class ReviewForm
 * @ingroup reviewForm
 * @see ReviewerFormDAO
 *
 * @brief Basic class describing a review form.
 *
 */

import('lib.pkp.classes.customForm.CustomForm');

class ReviewForm extends CustomForm {

	//
	// Get/set methods
	//
	/**
	 * @copydoc CustomForm::isEditable()
	 */
	function isEditable() {
		$result = parent::isEditable();

		return $result && $this->getIncompleteCount() == 0 && $this->getCompleteCount() == 0;
	}

	/**
	 * @copydoc CustomForm::isDeletable()
	 */
	function isDeletable() {
		$result = parent::isDeletable();

		return $result && $this->getIncompleteCount() == 0 && $this->getCompleteCount() == 0;
	}

	/**
	 * Get the number of completed reviews for this review form.
	 * @return int
	 */
	function getCompleteCount() {
		return $this->getData('completeCount');
	}

	/**
	 * Set the number of complete reviews for this review form.
	 * @param $completeCount int
	 */
	function setCompleteCount($completeCount) {
		$this->setData('completeCount', $completeCount);
	}

	/**
	 * Get the number of incomplete reviews for this review form.
	 * @return int
	 */
	function getIncompleteCount() {
		return $this->getData('incompleteCount');
	}

	/**
	 * Set the number of incomplete reviews for this review form.
	 * @param $incompleteCount int
	 */
	function setIncompleteCount($incompleteCount) {
		$this->setData('incompleteCount', $incompleteCount);
	}
}

