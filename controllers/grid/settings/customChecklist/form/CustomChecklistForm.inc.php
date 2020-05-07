<?php

/**
 * @file controllers/grid/settings/customChecklist/form/CustomChecklistForm.inc.php
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2003-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class CustomChecklistForm
 * @ingroup controllers_grid_settings_customChecklist_form
 *
 * @brief Form for adding/edditing a customChecklist
 * stores/retrieves from an associative array
 */

import('lib.pkp.controllers.grid.settings.customList.form.CustomListForm');

class CustomChecklistForm extends CustomListForm {

	protected function createCustomListContext($request) {
		import('lib.pkp.classes.context.customChecklist.CustomChecklistContext');
		return new CustomChecklistContext($request);
	}

	/**
	 * Constructor.
	 */
	function __construct($customListRowId = null, $template = 'controllers/grid/settings/customChecklist/form/customChecklistForm.tpl') {
		parent::__construct($customListRowId, $template);

		$request = Application::get()->getRequest();
		$customChecklistContext = $this->getCustomListContext($request);

		// Validation checks for this form
		$this->addCheck(new FormValidatorLocale($this, 'content', 'required', 'maganer.setup.' . $customChecklistContext->getPrefixLowercased() . 'ContentRequired'));
	}

	protected function getUserVars() {
		return array_merge(parent::getUserVars(), array('content'));
	}

	public function getLocaleFieldNames() {
		return array_merge(parent::getLocaleFieldNames(), array('content'));
	}
}

?>
