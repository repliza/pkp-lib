<?php
/**
 * @file controllers/grid/settings/customForms/CustomFormGridCellProvider.inc.php
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2003-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class CustomFormGridCellProvider
 * @ingroup controllers_grid_settings_customForms
 *
 * @brief Subclass for custom form column's cell provider
 */

import('lib.pkp.classes.controllers.grid.GridCellProvider');
import('lib.pkp.classes.context.customForms.CustomFormContextTrait');

class CustomFormGridCellProvider extends GridCellProvider {
	use CustomFormContextTrait;

	/**
	 * Extracts variables for a given column from a data element
	 * so that they may be assigned to template before rendering.
	 * @param $row GridRow
	 * @param $column GridColumn
	 * @return array
	 */
	function getTemplateVarsFromRowColumn($row, $column) {
		$element = $row->getData();
		$columnId = $column->getId();
		assert(is_a($element, 'CustomForm') && !empty($columnId));
		switch ($columnId) {
			case 'name':
				return array('label' => $element->getLocalizedTitle());
			case 'active':
				return array('selected' => $element->getActive());
		}
		return parent::getTemplateVarsFromRowColumn($row, $column);
	}

	/**
	 * @see GridCellProvider::getCellActions()
	 */
	function getCellActions($request, $row, $column, $position = GRID_ACTION_POSITION_DEFAULT) {
		$customFormContext = $this->getCustomFormContext($request);

		switch ($column->getId()) {
			case 'active':
				$element = $row->getData(); /* @var $element DataObject */

				$router = $request->getRouter();
				import('lib.pkp.classes.linkAction.LinkAction');

				if ($element->getActive()) return array(new LinkAction(
					'deactivateCustomForm',
					new RemoteActionConfirmationModal(
						$request->getSession(),
						__('manager.' . $customFormContext->getPrefixLowercased() . 'Forms.confirmDeactivate'),
						null,
						$router->url(
							$request,
							null,
							$customFormContext->getHandlerName(),
							'deactivateCustomForm',
							null,
							array_merge(
								$customFormContext->getRequestArgs(),
								array('customFormKey' => $element->getId())
							)
						)
					)
				));
				else return array(new LinkAction(
					'activateCustomForm',
					new RemoteActionConfirmationModal(
						$request->getSession(),
						__('manager.' . $customFormContext->getPrefixLowercased() . 'Forms.confirmActivate'),
						null,
						$router->url(
							$request,
							null,
							$customFormContext->getHandlerName(),
							'activateCustomForm',
							null,
							array_merge(
								$customFormContext->getRequestArgs(),
								array('customFormKey' => $element->getId())
							)
						)
					)
				));
		}
		return parent::getCellActions($request, $row, $column, $position);
	}
}

?>
