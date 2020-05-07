<?php

/**
 * @file classes/custom/CustomFormResponse.inc.php
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2000-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class CustomFormResponse
 * @ingroup customForm
 * @see CustomFormResponseDAO
 *
 * @brief Basic class describing a custom form response.
 *
 */

class CustomFormResponse extends DataObject {

	//
	// Get/set methods
	//

	/**
	 * Get assoc ID for this custom form response.
	 * @return int
	 */
	function getAssocId() {
		return $this->getData('assocId');
	}

	/**
	 * Set assoc ID for this custom form response.
	 * @param $assocId int
	 */
	function setAssocId($assocId) {
		$this->setData('assocId', $assocId);
	}

	/**
	 * Get assoc type for this custom form response.
	 * @return int
	 */
	function getAssocType() {
		return $this->getData('assocType');
	}

	/**
	 * Set assoc type for this custom form response.
	 * @param $assocType int
	 */
	function setAssocType($assocType) {
		$this->setData('assocType', $assocType);
	}

	/**
	 * Get ID of custom form element.
	 * @return int
	 */
	function getCustomFormElementId() {
		return $this->getData('customFormElementId');
	}

	/**
	 * Set ID of custom form element.
	 * @param $customFormElementId int
	 */
	function setCustomFormElementId($customFormElementId) {
		$this->setData('customFormElementId', $customFormElementId);
	}

	/**
	 * Get response value.
	 * @return int
	 */
	function getValue() {
		return $this->getData('value');
	}

	/**
	 * Set response value.
	 * @param $value int
	 */
	function setValue($value) {
		$this->setData('value', $value);
	}

	/**
	 * Get response type.
	 * @return string
	 */
	function getResponseType() {
		return $this->getData('type');
	}

	/**
	 * Set response type.
	 * @param $type string
	 */
	function setResponseType($type) {
		$this->setData('type', $type);
	}
}

?>
