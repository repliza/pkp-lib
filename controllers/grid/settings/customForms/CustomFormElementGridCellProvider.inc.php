<?php
/**
 * @file controllers/grid/settings/customForms/CustomFormElementGridCellProvider.inc.php
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2003-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class CustomFormElementGridCellProvider
 * @ingroup controllers_grid_settings_customForms
 *
 * @brief Subclass for custom form element column's cell provider
 */
import('lib.pkp.classes.controllers.grid.GridCellProvider');
import('lib.pkp.classes.context.customForms.CustomFormContextTrait');

class CustomFormElementGridCellProvider extends GridCellProvider {
	use CustomFormContextTrait;

	/**
	 * Extracts variables for a given column from a data element
	 * so that they may be assigned to template before rendering.
	 * @param $row GridRow
	 * @param $column GridColumn
	 * @return array
	 */
	function getTemplateVarsFromRowColumn($row, $column) {
		$request = Application::get()->getRequest();
		$customFormContext = $this->getCustomFormContext($request);

		$element = $row->getData();
		$columnId = $column->getId();
		assert(is_a($element, $customFormContext->getCustomFormElementClassName()) && !empty($columnId));
		switch ($columnId) {
			case 'question':
				$label = $element->getLocalizedQuestion();
				return array('label' => $label);
				break;
			default:
				assert(false);
				break;
		}
	}
}
?>
