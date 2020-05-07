<?php

/**
 * @file controllers/grid/settings/reviewForms/ReviewFormElementGridRow.inc.php 
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2000-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class ReviewFormElementGridRow
 * @ingroup controllers_grid_settings_reviewForms
 *
 * @brief ReviewFormElements grid row definition
 */
import('lib.pkp.controllers.grid.settings.customForms.CustomFormElementGridRow');

class ReviewFormElementGridRow extends CustomFormElementGridRow {
	//
	// Overridden methods from CustomFormElementGridRow
	//
	protected function createCustomFormContext($request) {
		import('lib.pkp.classes.context.reviewForms.ReviewFormContext');
		return new ReviewFormContext($request);
	}
}

