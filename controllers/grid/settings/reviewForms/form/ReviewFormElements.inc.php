<?php
/**
 * @file controllers/grid/settings/reviewForms/form/ReviewFormElements.inc.php
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class ReviewFormElements
 * @ingroup controllers_grid_settings_reviewForms_form
 *
 * @brief Form for manager to edit review form elements.
 */

import('lib.pkp.controllers.grid.settings.customForms.form.CustomFormElements');

class ReviewFormElements extends CustomFormElements {

	//
	// Overridden methods from CustomFormElementForm
	//
	protected function createCustomFormContext($request) {
		import('lib.pkp.classes.context.reviewForms.ReviewFormContext');
		return new ReviewFormContext($request);
	}

	/**
	 * @copydoc CustomFormElements::__construct()
	 */
	function __construct($reviewFormId) {
		parent::__construct($reviewFormId, 'manager/reviewForms/reviewFormElements.tpl');
	}
}

