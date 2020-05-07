<?php

/**
 * @file controllers/grid/settings/customList/CustomListGridRow.inc.php
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2000-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class CustomListGridRow
 * @ingroup controllers_grid_settings_customList
 *
 * @brief Handle customList grid row requests.
 */

import('lib.pkp.classes.controllers.grid.GridRow');
import('lib.pkp.classes.context.customList.CustomListContextTrait');

class CustomListGridRow extends GridRow {
	use CustomListContextTrait;

	//
	// Overridden template methods
	//
	/**
	 * @copydoc GridRow::initialize()
	 */
	function initialize($request, $template = null) {
		parent::initialize($request, $template);

		// Is this a new row or an existing row?
		$rowId = $this->getId();
		if (isset($rowId) && is_numeric($rowId)) {
			$router = $request->getRouter();
			$actionArgs = array(
				'gridId' => $this->getGridId(),
				'rowId' => $rowId
			);

			$customListContext = $this->getCustomListContext($request);
			$actionArgs = array_merge($customListContext->getRequestArgs(), $actionArgs);

			import('lib.pkp.classes.linkAction.request.AjaxModal');
			$this->addAction(
				new LinkAction(
					'editCustomList',
					new AjaxModal(
						$router->url($request, null, null, 'editItem', null, $actionArgs),
						__('grid.action.edit'),
						'modal_edit',
						true),
					__('grid.action.edit'),
					'edit')
			);

			import('lib.pkp.classes.linkAction.request.RemoteActionConfirmationModal');
			$this->addAction(
				new LinkAction(
					'deleteCustomList',
					new RemoteActionConfirmationModal(
						$request->getSession(),
						__('common.confirmDelete'),
						__('grid.action.delete'),
						$router->url($request, null, null, 'deleteItem', null, $actionArgs),
						'modal_delete'),
					__('grid.action.delete'),
					'delete')
			);
		}
	}
}

?>
