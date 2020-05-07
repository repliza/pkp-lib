<?php
/**
 * @file controllers/grid/settings/reviewForms/ReviewFormElementGridCellProvider.inc.php
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class ReviewFormElementGridCellProvider
 * @ingroup controllers_grid_settings_reviewForms
 *
 * @brief Subclass for review form element column's cell provider
 */
import('lib.pkp.controllers.grid.settings.customForms.CustomFormElementGridCellProvider');

class ReviewFormElementGridCellProvider extends CustomFormElementGridCellProvider {
	//
	// Overridden methods from CustomFormElementGridRow
	//
	protected function createCustomFormContext($request) {
		import('lib.pkp.classes.context.reviewForms.ReviewFormContext');
		return new ReviewFormContext($request);
	}
}

