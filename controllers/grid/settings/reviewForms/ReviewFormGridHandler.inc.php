<?php

/**
 * @file controllers/grid/settings/reviewForms/ReviewFormGridHandler.inc.php
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2000-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class ReviewFormGridHandler
 * @ingroup controllers_grid_settings_reviewForms
 *
 * @brief Handle review form grid requests.
 */

import('lib.pkp.controllers.grid.settings.customForms.CustomFormGridHandler');

import('lib.pkp.controllers.grid.settings.reviewForms.ReviewFormGridRow');

class ReviewFormGridHandler extends CustomFormGridHandler {

	protected function createCustomFormContext($request) {
		import('lib.pkp.classes.context.reviewForms.ReviewFormContext');
		return new ReviewFormContext($request);
	}

	protected function beforeDeleteCustomForm($customFormId) {
		parent::beforeDeleteCustomForm($customFormId);

		$reviewAssignmentDao = DAORegistry::getDAO('ReviewAssignmentDAO');
		$reviewAssignments = $reviewAssignmentDao->getByReviewFormId($customFormId);

		foreach ($reviewAssignments as $reviewAssignment) {
			$reviewAssignment->setReviewFormId(null);
			$reviewAssignmentDao->updateObject($reviewAssignment);
		}
	}

	/**
	 * @copydoc CustomFormGridHandler::initialize()
	 */
	function initialize($request, $args = null) {
		parent::initialize($request, $args);

		$customFormContext = $this->getCustomFormContext($request);

		//
		// Grid columns.
		//
		$gridCellProvider = $customFormContext->getGridCellProvider();

		// Review Form 'in review'
		$this->addColumn(
			new GridColumn(
				'inReview',
				'manager.reviewForms.inReview',
				null,
				null,
				$gridCellProvider
			)
		);

		// Review Form 'completed'.
		$this->addColumn(
			new GridColumn(
				'completed',
				'manager.reviewForms.completed',
				null,
				null,
				$gridCellProvider
			)
		);
	}

	/**
	 * @copydoc CustomFormGridHandler::getRowInstance()
	 */
	protected function getRowInstance() {
		return new ReviewFormGridRow();
	}
}


