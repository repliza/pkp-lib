<?php

/**
 * @file classes/customForm/CustomFormElement.inc.php
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2000-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class CustomFormElement
 * @ingroup customForm
 * @see CustomFormElementDAO
 *
 * @brief Basic class describing a custom form element.
 *
 */

define('CUSTOM_FORM_ELEMENT_TYPE_SMALL_TEXT_FIELD',	0x000001);
define('CUSTOM_FORM_ELEMENT_TYPE_TEXT_FIELD',		0x000002);
define('CUSTOM_FORM_ELEMENT_TYPE_TEXTAREA',		0x000003);
define('CUSTOM_FORM_ELEMENT_TYPE_CHECKBOXES',		0x000004);
define('CUSTOM_FORM_ELEMENT_TYPE_RADIO_BUTTONS',	0x000005);
define('CUSTOM_FORM_ELEMENT_TYPE_DROP_DOWN_BOX',	0x000006);

class CustomFormElement extends DataObject {

	/**
	 * Get localized question.
	 * @return string
	 */
	function getLocalizedQuestion() {
		return $this->getLocalizedData('question');
	}

	/**
	 * Get localized description.
	 * @return string
	 */
	function getLocalizedDescription() {
		return $this->getLocalizedData('description');
	}

	/**
	 * Get localized list of possible responses.
	 * @return array
	 */
	function getLocalizedPossibleResponses() {
		return $this->getLocalizedData('possibleResponses');
	}

	//
	// Get/set methods
	//

	/**
	 * Get the custom form ID of the custom form element.
	 * @return int
	 */
	function getCustomFormId() {
		return $this->getData('customFormId');
	}

	/**
	 * Set the custom form ID of the custom form element.
	 * @param $customFormId int
	 */
	function setCustomFormId($customFormId) {
		$this->setData('customFormId', $customFormId);
	}

	/**
	 * Get sequence of custom form element.
	 * @return float
	 */
	function getSequence() {
		return $this->getData('sequence');
	}

	/**
	 * Set sequence of custom form element.
	 * @param $sequence float
	 */
	function setSequence($sequence) {
		$this->setData('sequence', $sequence);
	}

	/**
	 * Get the type of the custom form element.
	 * @return string
	 */
	function getElementType() {
		return $this->getData('customFormElementType');
	}

	/**
	 * Set the type of the custom form element.
	 * @param $customFormElementType string
	 */
	function setElementType($customFormElementType) {
		$this->setData('customFormElementType', $customFormElementType);
	}

	/**
	 * Get required flag
	 * @return boolean
	 */
	function getRequired() {
		return $this->getData('required');
	}

	/**
	 * Set required flag
	 * @param $viewable boolean
	 */
	function setRequired($required) {
		$this->setData('required', $required);
	}

	/**
	 * Get question.
	 * @param $locale string
	 * @return string
	 */
	function getQuestion($locale) {
		return $this->getData('question', $locale);
	}

	/**
	 * Set question.
	 * @param $question string
	 * @param $locale string
	 */
	function setQuestion($question, $locale) {
		$this->setData('question', $question, $locale);
	}

	/**
	 * Get description.
	 * @param $locale string
	 * @return string
	 */
	function getDescription($locale) {
		return $this->getData('description', $locale);
	}

	/**
	 * Set description.
	 * @param $description string
	 * @param $locale string
	 */
	function setDescription($description, $locale) {
		$this->setData('description', $description, $locale);
	}

	/**
	 * Get possible response.
	 * @param $locale string
	 * @return string
	 */
	function getPossibleResponses($locale) {
		return $this->getData('possibleResponses', $locale);
	}

	/**
	 * Set possibleResponse.
	 * @param $possibleResponse string
	 * @param $locale string
	 */
	function setPossibleResponses($possibleResponses, $locale) {
		$this->setData('possibleResponses', $possibleResponses, $locale);
	}

	/**
	 * Get an associative array matching custom form element type codes with locale strings.
	 * (Includes default '' => "Choose One" string.)
	 * @return array customFormElementType => localeString
	 */
	static function &getCustomFormElementTypeOptions() {
		static $customFormElementTypeOptions = array(
			'' => 'manager.customFormElements.chooseType',
			CUSTOM_FORM_ELEMENT_TYPE_SMALL_TEXT_FIELD => 'manager.customFormElements.smalltextfield',
			CUSTOM_FORM_ELEMENT_TYPE_TEXT_FIELD => 'manager.customFormElements.textfield',
			CUSTOM_FORM_ELEMENT_TYPE_TEXTAREA => 'manager.customFormElements.textarea',
			CUSTOM_FORM_ELEMENT_TYPE_CHECKBOXES => 'manager.customFormElements.checkboxes',
			CUSTOM_FORM_ELEMENT_TYPE_RADIO_BUTTONS => 'manager.customFormElements.radiobuttons',
			CUSTOM_FORM_ELEMENT_TYPE_DROP_DOWN_BOX => 'manager.customFormElements.dropdownbox'
		);
		return $customFormElementTypeOptions;
	}

	/**
	 * Get an array of all multiple responses element types.
	 * @return array customFormElementTypes
	 */
	static function &getMultipleResponsesElementTypes() {
		static $multipleResponsesElementTypes = array(CUSTOM_FORM_ELEMENT_TYPE_CHECKBOXES, CUSTOM_FORM_ELEMENT_TYPE_RADIO_BUTTONS, CUSTOM_FORM_ELEMENT_TYPE_DROP_DOWN_BOX);
		return $multipleResponsesElementTypes;
	}
}

?>
