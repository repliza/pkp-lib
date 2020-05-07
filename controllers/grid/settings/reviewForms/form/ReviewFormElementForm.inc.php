<?php

/**
 * @file classes/manager/form/ReviewFormElementForm.inc.php
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class ReviewFormElementForm
 * @ingroup controllers_grid_settings_reviewForms_form
 * @see ReviewFormElement
 *
 * @brief Form for creating and modifying review form elements.
 *
 */

import('lib.pkp.controllers.grid.settings.customForms.form.CustomFormElementForm');

class ReviewFormElementForm extends CustomFormElementForm {

	//
	// Overridden methods from CustomFormElementForm
	//
	protected function createCustomFormContext($request) {
		import('lib.pkp.classes.context.reviewForms.ReviewFormContext');
		return new ReviewFormContext($request);
	}

	/**
	 * @copydoc CustomFormElementForm::__construct()
	 */
	function __construct($reviewFormId, $reviewFormElementId = null) {
		parent::__construct($reviewFormId, $reviewFormElementId, 'manager/reviewForms/reviewFormElementForm.tpl');
	}

	function fetch($request, $template = null, $display = false) {
		$customFormContext = $this->getCustomFormContext($request);

		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('includedCheckboxLabel', 'manager.' . $customFormContext->getPrefixLowercased() . 'FormElements.included');

		return parent::fetch($request, $template, $display);
	}

	protected function initDataByCustomFormElement($customFormElement) {
		parent::initDataByCustomFormElement($customFormElement);

		$this->setData('included', $customFormElement->getIncluded());
	}

	protected function initDataWithDefaultValues() {
		parent::initDataWithDefaultValues();

		$this->setData('included', 1);
	}

	protected function getUserVars() {
		return array_merge(parent::getUserVars(), array('included'));
	}

	protected function updateCustomFormElementFromData($customFormElement) {
		parent::updateCustomFormElementFromData($customFormElement);

		$customFormElement->setIncluded($this->getData('included') ? 1 : 0);
	}
}

