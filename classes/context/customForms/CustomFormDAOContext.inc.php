<?php

import('lib.pkp.classes.context.customForms.BaseCustomFormContext');

class CustomFormDAOContext extends BaseCustomFormContext {

	public function getFormIdTableColumnName() {
		return $this->getPrefixLowercased() . "_form_id";
	}

	public function getFormsTableName() {
		return $this->getPrefixLowercased() . '_forms';
	}

	public function getFormSettingsTableName() {
		return $this->getPrefixLowercased() . '_form_settings';
	}

	public function getFormElementIdTableColumnName() {
		return $this->getPrefixLowercased() . "_form_element_id";
	}	

	public function getFormElementsTableName() {
		return $this->getPrefixLowercased() . '_form_elements';
	}

	public function getFormElementSettingsTableName() {
		return $this->getPrefixLowercased() . '_form_element_settings';
	}

	public function getFormResponsesTableName() {
		return $this->getPrefixLowercased() . '_form_responses';
	}

	public function getFormResponseDAOClassName() {
		return 'CustomFormResponseDAO';
	}

}

?>