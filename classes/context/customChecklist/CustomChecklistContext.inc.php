<?php

import('lib.pkp.classes.context.customList.CustomListContext');

abstract class CustomChecklistContext extends CustomListContext {
	public function __construct() {
		parent::__construct();
	}

	public function getGridTitleLocaleKey() {
		return 'manager.setup.customChecklist';
	}

	public function getCustomListForm($customChecklistId) {
		import('lib.pkp.controllers.grid.settings.customChecklist.form.CustomChecklistForm');
		$customChecklistForm = new CustomChecklistForm($customChecklistId);

		return $customChecklistForm;
	}

	public function getGridHandlerComponent() {
		return 'grid.settings.customChecklist.CustomChecklistGridHandler';
	}
}

?>
