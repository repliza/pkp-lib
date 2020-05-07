<?php
/**
 * @file controllers/grid/settings/customForms/form/CustomFormElements.inc.php
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2003-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class CustomFormElements
 * @ingroup controllers_grid_settings_customForms_form
 *
 * @brief Form for manager to edit custom form elements.
 */

import('lib.pkp.classes.db.DBDataXMLParser');
import('lib.pkp.classes.form.Form');
import('lib.pkp.classes.context.customForms.CustomFormContextTrait');

class CustomFormElements extends Form {
	use CustomFormContextTrait;

	/** The ID of the custom form being edited */
	var $customFormId;

	/**
	 * Constructor.
	 * @param $template string
	 * @param $customFormId
	 */
	function __construct($customFormId, $template = 'manager/customForms/customFormElements.tpl') {
		parent::__construct($template);

		$this->customFormId = (int) $customFormId;

		// Validation checks for this form
		$this->addCheck(new FormValidatorPost($this));
		$this->addCheck(new FormValidatorCSRF($this));
	}

	/**
	 * Display the form.
	 */
	function fetch($request, $template = null, $display = false) {
		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('customFormId', $this->customFormId);

		return parent::fetch($request, $template, $display);
	}

	/**
	 * Initialize form data from current settings.
	 * @param $customForm CustomForm optional
	 */
	function initData($customForm = null) {
		if (isset($this->customFormId)) {
			// Get custom form
			$request = Application::get()->getRequest();
			$customFormContext = $this->getCustomFormContext($request);

			$customFormDao = $this->getCustomFormDAO();
			$customForm = $customFormDao->getById($this->customFormId, $customFormContext->getAssocType(), $customFormContext->getAssocId());

			// Get custom form elements
			$customFormElementDao = $customFormContext->getCustomFormElementDAO();
			$customFormElements = $customFormElementDao->getByCustomFormId($customFormId, null);

			// Set data
			$this->setData('customFormId', $customFormId);
			$this->setData('customFormElements', $customFormElements);
		}
	}
}

?>
