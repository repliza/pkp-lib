<?php

/**
 * @file classes/manager/form/CustomFormElementForm.inc.php
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2003-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class CustomFormElementForm
 * @ingroup controllers_grid_settings_customForms_form
 * @see CustomFormElement
 *
 * @brief Form for creating and modifying custom form elements.
 *
 */

import('lib.pkp.classes.db.DBDataXMLParser');
import('lib.pkp.classes.form.Form');
import('lib.pkp.classes.context.customForms.CustomFormContextTrait');

class CustomFormElementForm extends Form {
	use CustomFormContextTrait;

	/** @var $customFormId int The ID of the custom form being edited */
	var $customFormId;

	/** @var $customFormElementId int The ID of the custom form element being edited */
	var $customFormElementId;

	/**
	 * Constructor.
	 * @param $customFormId int
	 * @param $customFormElementId int
	 */
	function __construct($customFormId, $customFormElementId = null, $template = 'manager/customForms/customFormElementForm.tpl') {
		parent::__construct($template);

		$this->customFormId = $customFormId;
		$this->customFormElementId = $customFormElementId;

		// Validation checks for this form
		$this->addCheck(new FormValidatorLocale($this, 'question', 'required', 'manager.customFormElements.form.questionRequired'));
		$this->addCheck(new FormValidator($this, 'elementType', 'required', 'manager.customFormElements.form.elementTypeRequired'));
		$this->addCheck(new FormValidatorPost($this));
		$this->addCheck(new FormValidatorCSRF($this));
	}

	/**
	 * Get the names of fields for which localized data is allowed.
	 * @return array
	 */
	function getLocaleFieldNames() {
		$request = Application::get()->getRequest();
		$customFormContext = $this->getCustomFormContext($request);

		$customFormElementDao = $customFormContext->getCustomFormElementDAO();
		return $customFormElementDao->getLocaleFieldNames();
	}

	/**
	 * Display the form.
	 * @param $request PKPRequest
	 */
	function fetch($request, $template = null, $display = false) {
		$customFormContext = $this->getCustomFormContext($request);

		$templateMgr = TemplateManager::getManager($request);
		$customFormContext->importCustomFormElement();
		$customFormElementClassName = $customFormContext->getCustomFormElementClassName();
		$templateMgr->assign(array(
			'assocType' => $customFormContext->getAssocType(),
			'assocId' => $customFormContext->getAssocId(),
			'customFormId' => $this->customFormId,
			'customFormElementId' => $this->customFormElementId,
			'multipleResponsesElementTypes' => $customFormElementClassName::getMultipleResponsesElementTypes(),
			'multipleResponsesElementTypesString' => ';'.implode(';', $customFormElementClassName::getMultipleResponsesElementTypes()).';',
			'customFormElementTypeOptions' => $customFormElementClassName::getCustomFormElementTypeOptions(),
			'requiredCheckboxLabel' => 'manager.' . $customFormContext->getPrefixLowercased() . 'FormElements.required',
			'customFormElementResponseItemListbuilderHandler' => 'listbuilder.settings.' . $customFormContext->getPrefixLowercased() . 'Forms.' . $customFormContext->getPrefix() . 'FormElementResponseItemListbuilderHandler'
		));
		return parent::fetch($request, $template, $display);
	}

	/**
	 * Initialize form data from current custom form.
	 */
	function initData() {
		if ($this->customFormElementId) {
			$request = Application::get()->getRequest();
			$customFormContext = $this->getCustomFormContext($request);

			$customFormElementDao = $customFormContext->getCustomFormElementDAO();
			$customFormElement = $customFormElementDao->getById($this->customFormElementId, $this->customFormId);
			$this->initDataByCustomFormElement($customFormElement);
		} else {
			$this->initDataWithDefaultValues();
		}
	}

	protected function initDataByCustomFormElement($customFormElement) {
		$this->_data = array(
			'question' => $customFormElement->getQuestion(null), // Localized
			'description' => $customFormElement->getDescription(null), // Localized
			'required' => $customFormElement->getRequired(),

			'elementType' => $customFormElement->getElementType(),
			'possibleResponses' => $customFormElement->getPossibleResponses(null) //Localized
		);
	}

	protected function initDataWithDefaultValues() {
	}

	protected function getUserVars() {
		return array('question', 'description', 'required', 'elementType', 'possibleResponses');
	}

	/**
	 * Assign form data to user-submitted data.
	 */
	function readInputData() {
		$this->readUserVars($this->getUserVars());
	}

	protected function updateCustomFormElementFromData($customFormElement) {
		$customFormElement->setQuestion($this->getData('question'), null); // Localized
		$customFormElement->setDescription($this->getData('description'), null); // Localized
		$customFormElement->setRequired($this->getData('required') ? 1 : 0);
		$customFormElement->setElementType($this->getData('elementType'));
	}

	/**
	 * @copydoc Form::execute()
	 * @return int Review form element ID
	 */
	function execute(...$functionArgs) {
		$request = Application::get()->getRequest();
		$customFormContext = $this->getCustomFormContext($request);

		$customFormElementDao = $customFormContext->getCustomFormElementDAO();

		if ($this->customFormElementId) {
			$customFormElement = $customFormElementDao->getById($this->customFormElementId);
			$customFormDao = $customFormContext->getCustomFormDAO();
			$customForm = $customFormDao->getById($customFormElement->getCustomFormId(), $customFormContext->getAssocType(), $customFormContext->getAssocId());
			if (!$customForm) fatalError('Invalid custom form element ID!');
		} else {
			$customFormElement = $customFormElementDao->newDataObject();
			$customFormElement->setCustomFormId($this->customFormId);
			$customFormElement->setSequence(REALLY_BIG_NUMBER);
		}

		$this->updateCustomFormElementFromData($customFormElement);

		$customFormElementClassName = $customFormContext->getCustomFormElementClassName();

		if (in_array($this->getData('elementType'), $customFormElementClassName::getMultipleResponsesElementTypes())) {
			$this->setData('possibleResponsesProcessed', $customFormElement->getPossibleResponses(null));
			ListbuilderHandler::unpack($request, $this->getData('possibleResponses'), array($this, 'deleteEntry'), array($this, 'insertEntry'), array($this, 'updateEntry'));
			$customFormElement->setPossibleResponses($this->getData('possibleResponsesProcessed'), null);
		} else {
			$customFormElement->setPossibleResponses(null, null);
		}
		if ($customFormElement->getId()) {
			$customFormElementDao->deleteSetting($customFormElement->getId(), 'possibleResponses');
			$customFormElementDao->updateObject($customFormElement);
		} else {
			$this->customFormElementId = $customFormElementDao->insertObject($customFormElement);
			$customFormElementDao->resequenceCustomFormElements($this->customFormId);
		}
		parent::execute(...$functionArgs);
		return $this->customFormElementId;
	}

	/**
	 * @copydoc ListbuilderHandler::insertEntry()
	 */
	function insertEntry($request, $newRowId) {
		$possibleResponsesProcessed = (array) $this->getData('possibleResponsesProcessed');
		foreach ($newRowId['possibleResponse'] as $key => $value) {
			$possibleResponsesProcessed[$key][] = $value;
		}
		$this->setData('possibleResponsesProcessed', $possibleResponsesProcessed);
		return true;
	}

	/**
	 * @copydoc ListbuilderHandler::deleteEntry()
	 */
	function deleteEntry($request, $rowId) {
		$possibleResponsesProcessed = (array) $this->getData('possibleResponsesProcessed');
		foreach (array_keys($possibleResponsesProcessed) as $locale) {
			// WARNING: Listbuilders don't like zero row IDs. They are offset
			// by 1 to avoid this case, so 1 is subtracted here to normalize.
			unset($possibleResponsesProcessed[$locale][$rowId-1]);
		}
		$this->setData('possibleResponsesProcessed', $possibleResponsesProcessed);
		return true;
	}

	/**
	 * @copydoc ListbuilderHandler::updateEntry
	 */
	function updateEntry($request, $rowId, $newRowId) {
		$possibleResponsesProcessed = (array) $this->getData('possibleResponsesProcessed');
		foreach ($newRowId['possibleResponse'] as $locale => $value) {
			// WARNING: Listbuilders don't like zero row IDs. They are offset
			// by 1 to avoid this case, so 1 is subtracted here to normalize.
			$possibleResponsesProcessed[$locale][$rowId-1] = $value;
		}
		$this->setData('possibleResponsesProcessed', $possibleResponsesProcessed);
		return true;
	}
}

?>
