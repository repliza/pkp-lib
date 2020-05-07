<?php
/**
 * @file controllers/grid/settings/customForms/form/PreviewCustomForm.inc.php
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2003-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class PreviewCustomForm
 * @ingroup controllers_grid_settings_customForms_form
 *
 * @brief Form for manager to preview custom form.
 */

import('lib.pkp.classes.db.DBDataXMLParser');
import('lib.pkp.classes.form.Form');
import('lib.pkp.classes.context.customForms.CustomFormContextTrait');

class PreviewCustomForm extends Form {
	use CustomFormContextTrait;

	/** The ID of the custom form being edited */
	var $customFormId;

	/**
	 * Constructor.
	 * @param $template string
	 * @param $customFormId omit for a new custom form
	 */
	function __construct($customFormId = null, $template = 'manager/customForms/previewCustomForm.tpl') {
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
		$json = new JSONMessage();

		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('customFormId', $this->customFormId);

		return parent::fetch($request, $template, $display);
	}

	/**
	 * Initialize form data from current settings.
	 */
	function initData() {
		if ($this->customFormId) {
			// Get custom form
			$request = Application::get()->getRequest();
			$customFormContext = $this->getCustomFormContext($request);

			$customFormDao = $customFormContext->getCustomFormDAO();
			$customForm = $customFormDao->getById($this->customFormId, $customFormContext->getAssocType(), $customFormContext->getAssocId());

			// Get custom form elements
			$customFormElementDao = $customFormContext->getCustomFormElementDAO();
			$customFormElements = $customFormElementDao->getByCustomFormId($this->customFormId);

			// Set data
			$this->setData('title', $customForm->getLocalizedTitle(null));
			$this->setData('description', $customForm->getLocalizedDescription(null));

			$customFormElementArrayDataKey = $this->customFormElementArrayDataKey();
			$this->setData($customFormElementArrayDataKey, $customFormElements);

			$formFieldNameStem = $this->getFormFieldNameStem();
			$this->setData('formFieldNameStem', $formFieldNameStem);
		}
	}

	protected function customFormElementArrayDataKey() {
		return 'customFormElements';
	}

	protected function getFormFieldNameStem() {
		return 'customFormResponses';
	}

	/**
	 * Assign form data to user-submitted data.
	 */
	function readInputData() {
		parent::readInputData();
	}
}
?>
