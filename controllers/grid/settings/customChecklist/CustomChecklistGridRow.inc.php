<?php

/**
 * @file controllers/grid/settings/customChecklist/CustomChecklistGridRow.inc.php
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2000-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class CustomChecklistGridRow
 * @ingroup controllers_grid_settings_customChecklist
 *
 * @brief Handle customChecklist grid row requests.
 */

import('lib.pkp.controllers.grid.settings.customList.CustomListGridRow');

class CustomChecklistGridRow extends CustomListGridRow {

	protected function createCustomListContext($request) {
		import('lib.pkp.classes.context.customChecklist.CustomChecklistContext');
		return new CustomChecklistContext($request);
	}

	//
	// Overridden template methods
	//
	/**
	 * @copydoc GridRow::initialize()
	 */
	function initialize($request, $template = null) {
		parent::initialize($request, $template);
	}
}

?>
