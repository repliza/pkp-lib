<?php

/**
 * @file controllers/grid/settings/submissionChecklist/form/SubmissionChecklistForm.inc.php
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class SubmissionChecklistForm
 * @ingroup controllers_grid_settings_submissionChecklist_form
 *
 * @brief Form for adding/edditing a submissionChecklist
 * stores/retrieves from an associative array
 */

import('lib.pkp.controllers.grid.settings.customChecklist.form.CustomChecklistForm');

class SubmissionChecklistForm extends CustomChecklistForm {
	//
	// Overridden methods from CustomChecklistForm
	//
	protected function createCustomListContext($request) {
		import('lib.pkp.classes.context.submissionChecklist.SubmissionChecklistContext');
		return new SubmissionChecklistContext($request);
	}

	/**
	 * Constructor.
	 */
	function __construct($submissionChecklistId = null) {
		parent::__construct($submissionChecklistId, 'controllers/grid/settings/submissionChecklist/form/submissionChecklistForm.tpl');
	}
}

