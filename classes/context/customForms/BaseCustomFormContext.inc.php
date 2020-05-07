<?php

class BaseCustomFormContext {

	public function getPrefix() {
		return "Custom";
	}

	public function getPrefixLowercased() {
		return lcfirst($this->getPrefix());
	}

	public function getCustomFormElementDAO() {
		import('lib.pkp.controllers.grid.settings.customForms.form.CustomFormElements');
		return DAORegistry::getDAO('CustomFormElementDAO');
	}
}

?>