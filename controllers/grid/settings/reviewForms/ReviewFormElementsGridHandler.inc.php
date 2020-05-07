<?php

/**
 * @file controllers/grid/settings/reviewForms/ReviewFormElementsGridHandler.inc.php
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2000-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class ReviewFormElementsGridHandler
 * @ingroup controllers_grid_settings_reviewForms
 *
 * @brief Handle review form element grid requests.
 */

import('lib.pkp.controllers.grid.settings.customForms.CustomFormElementsGridHandler');
import('lib.pkp.controllers.grid.settings.reviewForms.ReviewFormElementGridRow');

class ReviewFormElementsGridHandler extends CustomFormElementsGridHandler {

		//
	// Overridden methods from CustomFormElementsGridHandler
	//
	protected function createCustomFormContext($request) {
		import('lib.pkp.classes.context.reviewForms.ReviewFormContext');
		return new ReviewFormContext($request);
	}

	/**
	 * @copydoc CustomFormElementsGridHandler::getRowInstance()
	 */
	protected function getRowInstance() {
		return new ReviewFormElementGridRow();
	}

	protected function beforeUpdateCustomFormElement($args, $request, $customFormElementId) {
		parent::beforeUpdateCustomFormElement($args, $request, $customFormElementId);

		$customFormContext = $this->getCustomFormContext($request);

		$customFormDao = $customFormContext->getCustomFormDAO();

		if (!$customFormDao->unusedReviewFormExists($this->customFormId, $customFormContext->getAssocType(), $customFormContext->getAssocId())) {
			fatalError('Invalid review form information!');
		}
	}

	protected function canDeleteCustomFormElement($args, $request) {
		$customFormContext = $this->getCustomFormContext($request);

		$reviewFormDao = $customFormContext->getCustomFormDAO();

		return parent::canDeleteCustomFormElement($args, $request) &&
			$reviewFormDao->unusedReviewFormExists($this->customFormId, $customFormContext->getAssocType(), $customFormContext->getAssocId());
	}
}


