<?php

/**
 * @defgroup customForm Custom Form
 * Implements custom forms, which are forms that can be created and customized
 * by the manager and presented where needed.
 */

/**
 * @file classes/customForm/CustomForm.inc.php
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2000-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class CustomForm
 * @ingroup customForm
 * @see CustomFormDAO
 *
 * @brief Base class describing a custom form.
 *
 */

class CustomForm extends DataObject {

	/**
	 * Get localized title.
	 * @return string
	 */
	function getLocalizedTitle() {
		return $this->getLocalizedData('title');
	}

	/**
	 * Get localized description.
	 * @return string
	 */
	function getLocalizedDescription() {
		return $this->getLocalizedData('description');
	}

	//
	// Get/set methods
	//

	/**
	 * Get the associated type.
	 * @return int
	 */
	function getAssocType() {
		return $this->getData('assocType');
	}

	/**
	 * Set the associated type.
	 * @param $assocType int
	 */
	function setAssocType($assocType) {
		$this->setData('assocType', $assocType);
	}

	/**
	 * Get the Id of the associated type.
	 * @return int
	 */
	function getAssocId() {
		return $this->getData('assocId');
	}

	/**
	 * Set the Id of the associated type.
	 * @param $assocId int
	 */
	function setAssocId($assocId) {
		$this->setData('assocId', $assocId);
	}

	/**
	 * Get sequence of custom form.
	 * @return float
	 */
	function getSequence() {
		return $this->getData('sequence');
	}

	/**
	 * Set sequence of custom form.
	 * @param $sequence float
	 */
	function setSequence($sequence) {
		$this->setData('sequence', $sequence);
	}

	/**
	 * Get active flag
	 * @return int
	 */
	function getActive() {
		return $this->getData('active');
	}

	/**
	 * Set active flag
	 * @param $active int
	 */
	function setActive($active) {
		$this->setData('active', $active);
	}

	/**
	 * Get title.
	 * @param $locale string
	 * @return string
	 */
	function getTitle($locale) {
		return $this->getData('title', $locale);
	}

	/**
	 * Set title.
	 * @param $title string
	 * @param $locale string
	 */
	function setTitle($title, $locale) {
		$this->setData('title', $title, $locale);
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
	 * Determines whether the form is editable.
	 * @return bool
	 */
	function isEditable() {
		return true;
	}

	/**
	 * Determines whether the form is deletable.
	 * @return bool
	 */
	function isDeletable() {
		return true;
	}
}

?>
