<?php

/**
 * @file controllers/grid/settings/submissionChecklist/SubmissionChecklistGridHandler.inc.php
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2000-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class SubmissionChecklistGridHandler
 * @ingroup controllers_grid_settings_submissionChecklist
 *
 * @brief Handle submissionChecklist grid requests.
 */

import('lib.pkp.controllers.grid.settings.customChecklist.CustomChecklistGridHandler');
import('lib.pkp.controllers.grid.settings.submissionChecklist.SubmissionChecklistGridRow');

class SubmissionChecklistGridHandler extends CustomChecklistGridHandler {
	//
	// Overridden methods from CustomChecklistGridHandler
	//
	protected function createCustomListContext($request) {
		import('lib.pkp.classes.context.submissionChecklist.SubmissionChecklistContext');
		return new SubmissionChecklistContext($request);
	}

	/**
	 * Constructor
	 */
	function __construct() {
		parent::__construct();
	}

	/**
	 * @copydoc GridHandler::getRowInstance()
	 */
	protected function getRowInstance() {
		return new SubmissionChecklistGridRow();
	}
}

