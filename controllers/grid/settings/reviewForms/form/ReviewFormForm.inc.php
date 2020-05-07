<?php

/**
 * @file controllers/grid/settings/reviewForms/form/ReviewFormForm.inc.php
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class ReviewFormForm
 * @ingroup controllers_grid_settings_reviewForms_form
 *
 * @brief Form for manager to edit a review form.
 */

import('lib.pkp.controllers.grid.settings.customForms.form.CustomFormForm');

class ReviewFormForm extends CustomFormForm {

	//
	// Overridden methods from CustomFormElementForm
	//
	protected function createCustomFormContext($request) {
		import('lib.pkp.classes.context.reviewForms.ReviewFormContext');
		return new ReviewFormContext($request);
	}

	/**
	 * @copydoc CustomFormForm::__construct()
	 */
	function __construct($reviewFormId = null) {
		parent::__construct($reviewFormId, 'manager/reviewForms/reviewFormForm.tpl');
	}
}

