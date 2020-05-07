<?php

/**
 * @file classes/reviewForm/ReviewFormElement.inc.php
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2000-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class ReviewFormElement
 * @ingroup reviewForm
 * @see ReviewFormElementDAO
 *
 * @brief Basic class describing a review form element.
 *
 */

import('lib.pkp.classes.customForm.CustomFormElement');

define('REVIEW_FORM_ELEMENT_TYPE_SMALL_TEXT_FIELD',	CUSTOM_FORM_ELEMENT_TYPE_SMALL_TEXT_FIELD);
define('REVIEW_FORM_ELEMENT_TYPE_TEXT_FIELD',		CUSTOM_FORM_ELEMENT_TYPE_TEXT_FIELD);
define('REVIEW_FORM_ELEMENT_TYPE_TEXTAREA',		CUSTOM_FORM_ELEMENT_TYPE_TEXTAREA);
define('REVIEW_FORM_ELEMENT_TYPE_CHECKBOXES',		CUSTOM_FORM_ELEMENT_TYPE_CHECKBOXES);
define('REVIEW_FORM_ELEMENT_TYPE_RADIO_BUTTONS',	CUSTOM_FORM_ELEMENT_TYPE_RADIO_BUTTONS);
define('REVIEW_FORM_ELEMENT_TYPE_DROP_DOWN_BOX',	CUSTOM_FORM_ELEMENT_TYPE_DROP_DOWN_BOX);

class ReviewFormElement extends CustomFormElement {

	//
	// Get/set methods
	//

	/**
	 * get included
	 * @return boolean
	 */
	function getIncluded() {
		return $this->getData('included');
	}

	/**
	 * set included
	 * @param $included boolean
	 */
	function setIncluded($included) {
		$this->setData('included', $included);
	}
}

