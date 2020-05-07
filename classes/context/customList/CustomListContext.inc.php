<?php

abstract class CustomListContext {
	public function __construct() {
	}

	public function getPrefix() {
		return "Custom";
	}

	public function getPrefixLowercased() {
		return lcfirst($this->getPrefix());
	}

	public function getRequestArgs() {
		return array();
	}

	public abstract function getSetting($locale = null);
	public abstract function updateSetting($value, $type = null, $isLocalized = false);

	public function getGridTitleLocaleKey() {
		return 'manager.setup.customList';
	}

	public function getCustomListForm($customListItemId) {
		import('lib.pkp.controllers.grid.settings.customList.form.CustomListForm');
		$customListForm = new CustomListForm($customListItemId);

		return $customListForm;
	}

	public function getGridHandlerComponent() {
		return 'grid.settings.customList.CustomListGridHandler';
	}
}

?>
