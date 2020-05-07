<?php
/**
 * @file controllers/listbuilder/settings/reviewForms/ReviewFormElementResponseItemListbuilderHandler.inc.php
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2000-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class ReviewFormElementResponseItemListbuilderHandler
 * @ingroup controllers_listbuilder_settings_reviewForms
 *
 * @brief Review form element response item listbuilder handler
 */

import('lib.pkp.controllers.listbuilder.settings.customForms.CustomFormElementResponseItemListbuilderHandler');

class ReviewFormElementResponseItemListbuilderHandler extends CustomFormElementResponseItemListbuilderHandler {

	//
	// Overridden methods from CustomFormElementResponseItemListbuilderHandler
	//
	protected function createCustomFormContext($request) {
		import('lib.pkp.classes.context.reviewForms.ReviewFormContext');
		return new ReviewFormContext($request);
	}

}


