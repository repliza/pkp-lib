<?php

/**
 * @file controllers/grid/settings/customChecklist/CustomChecklistGridHandler.inc.php
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2000-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class CustomChecklistGridHandler
 * @ingroup controllers_grid_settings_customChecklist
 *
 * @brief Handle customChecklist grid requests.
 */

import('lib.pkp.controllers.grid.settings.customList.CustomListGridHandler');
import('lib.pkp.controllers.grid.settings.customChecklist.CustomChecklistGridRow');

class CustomChecklistGridHandler extends CustomListGridHandler {

	protected function createCustomListContext($request) {
		import('lib.pkp.classes.context.customChecklist.CustomChecklistContext');
		return new CustomChecklistContext($request);
	}

	protected function getIdStem() {
		return 'Checklist';
	}

	//
	// Overridden template methods
	//
	/**
	 * @copydoc SetupGridHandler::initialize()
	 */
	function initialize($request, $args = null) {
		parent::initialize($request, $args);

		// Columns
		$this->addColumn(
			new GridColumn(
				'content',
				'grid.customChecklist.column.checklistItem',
				null,
				null,
				null,
				array('html' => true, 'maxLength' => 220)
			)
		);
	}

	/**
	 * @copydoc GridHandler::getRowInstance()
	 */
	protected function getRowInstance() {
		return new CustomChecklistGridRow();
	}
}

?>
