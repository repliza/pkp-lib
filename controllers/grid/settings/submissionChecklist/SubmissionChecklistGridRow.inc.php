<?php

/**
 * @file controllers/grid/settings/submissionChecklist/SubmissionChecklistGridRow.inc.php
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2000-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class SubmissionChecklistGridRow
 * @ingroup controllers_grid_settings_submissionChecklist
 *
 * @brief Handle submissionChecklist grid row requests.
 */

import('lib.pkp.controllers.grid.settings.customChecklist.CustomChecklistGridRow');

class SubmissionChecklistGridRow extends CustomChecklistGridRow {
	//
	// Overridden methods from CustomChecklistGridRow
	//
	protected function createCustomListContext($request) {
		import('lib.pkp.classes.context.submissionChecklist.SubmissionChecklistContext');
		return new SubmissionChecklistContext($request);
	}
}


