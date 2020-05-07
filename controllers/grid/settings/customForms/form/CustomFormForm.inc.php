<?php

/**
 * @file controllers/grid/settings/customForms/form/CustomFormForm.inc.php
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2003-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class CustomFormForm
 * @ingroup controllers_grid_settings_customForms_form
 *
 * @brief Form for manager to edit a custom form.
 */

import('lib.pkp.classes.form.Form');
import('lib.pkp.classes.context.customForms.CustomFormContextTrait');

class CustomFormForm extends Form {
	use CustomFormContextTrait;

	/** The ID of the custom form being edited, if any */
	var $customFormId;

	/**
	 * Constructor.
	 * @param $customFormId omit for a new custom form
	 */
	function __construct($customFormId = null, $template = 'manager/customForms/customFormForm.tpl') {
		parent::__construct($template);
		$this->customFormId = $customFormId ? (int) $customFormId : null;

		$request = Application::get()->getRequest();
		$customFormContext = $this->getCustomFormContext($request);

		// Validation checks for this form
		$this->addCheck(new FormValidatorLocale($this, 'title', 'required', 'manager.' . $customFormContext->getPrefixLowercased() . 'Forms.form.titleRequired'));
		$this->addCheck(new FormValidatorPost($this));
		$this->addCheck(new FormValidatorCSRF($this));
	}

	/**
	 * Assign form data to user-submitted data.
	 */
	function readInputData() {
		$this->readUserVars(array('title', 'description'));
	}

	/**
	 * Initialize form data from current settings.
	 */
	function initData() {
		if ($this->customFormId) {
			$request = Application::get()->getRequest();
			$customFormContext = $this->getCustomFormContext($request);

			$customFormDao = $customFormContext->getCustomFormDAO();
			$customForm = $customFormDao->getById($this->customFormId, $customFormContext->getAssocType(), $customFormContext->getAssocId());

			$this->setData('title', $customForm->getTitle(null));
			$this->setData('description', $customForm->getDescription(null));
		}
	}

	/**
	 * Display the form.
	 */
	function fetch($request, $template = null, $display = false) {
		$json = new JSONMessage();

		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('customFormId', $this->customFormId);

		$customFormContext = $this->getCustomFormContext($request);
		$templateMgr->assign('assocType', $customFormContext->getAssocType());
		$templateMgr->assign('assocId', $customFormContext->getAssocId());

		return parent::fetch($request, $template, $display);
	}

	/**
	 * @copydoc Form::execute()
	 */
	function execute(...$functionArgs) {
		$request = Application::get()->getRequest();
		$customFormContext = $this->getCustomFormContext($request);

		$customFormDao = $customFormContext->getCustomFormDAO();

		if ($this->customFormId) {
			$customForm = $customFormDao->getById($this->customFormId, $customFormContext->getAssocType(), $customFormContext->getAssocId());
		} else {
			$customForm = $customFormDao->newDataObject();
			$customForm->setAssocType($customFormContext->getAssocType());
			$customForm->setAssocId($customFormContext->getAssocId());
			$customForm->setActive(0);
			$customForm->setSequence(REALLY_BIG_NUMBER);
		}

		$customForm->setTitle($this->getData('title'), null); // Localized
		$customForm->setDescription($this->getData('description'), null); // Localized

		if ($this->customFormId) {
			$customFormDao->updateObject($customForm);
			$this->customFormId = $customForm->getId();
		} else {
			$this->customFormId = $customFormDao->insertObject($customForm);
			$customFormDao->resequenceCustomForms($customFormContext->getAssocType(), $customFormContext->getAssocId());
		}
		parent::execute(...$functionArgs);
	}

	/**
	 * Get a list of field names for which localized settings are used
	 * @return array
	 */
	function getLocaleFieldNames() {
		$request = Application::get()->getRequest();
		$customFormContext = $this->getCustomFormContext($request);
		$customFormDao = $customFormContext->getCustomFormDAO();
		return $customFormDao->getLocaleFieldNames();
	}
}

?>
