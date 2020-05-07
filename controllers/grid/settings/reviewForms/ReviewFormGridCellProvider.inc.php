<?php
/**
 * @file controllers/grid/settings/reviewForms/ReviewFormGridCellProvider.inc.php
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class ReviewFormGridCellProvider
 * @ingroup controllers_grid_settings_reviewForms
 *
 * @brief Subclass for review form column's cell provider
 */

import('lib.pkp.controllers.grid.settings.customForms.CustomFormGridCellProvider');

class ReviewFormGridCellProvider extends CustomFormGridCellProvider {

	//
	// Overridden methods from CustomFormGridCellProvider
	//
	protected function createCustomFormContext($request) {
		import('lib.pkp.classes.context.reviewForms.ReviewFormContext');
		return new ReviewFormContext($request);
	}

	/**
	 * @copydoc CustomFormGridCellProvider::getTemplateVarsFromRowColumn()
	 */
	function getTemplateVarsFromRowColumn($row, $column) {
		$element = $row->getData();
		$columnId = $column->getId();
		assert(is_a($element, 'ReviewForm') && !empty($columnId));
		switch ($columnId) {
			case 'inReview':
				return array('label' => $element->getIncompleteCount());
			case 'completed':
				return array('label' => $element->getCompleteCount());
		}
		return parent::getTemplateVarsFromRowColumn($row, $column);
	}
}

