<?php

/**
 * @file controllers/grid/settings/customForms/CustomFormElementGridRow.inc.php 
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2000-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class CustomFormElementGridRow
 * @ingroup controllers_grid_settings_customForms
 *
 * @brief CustomFormElements grid row definition
 */
import('lib.pkp.classes.controllers.grid.GridRow');
import('lib.pkp.classes.linkAction.request.RemoteActionConfirmationModal');
import('lib.pkp.classes.context.customForms.CustomFormContextTrait');

class CustomFormElementGridRow extends GridRow {
	use CustomFormContextTrait;

	//
	// Overridden methods from GridRow
	//
	/**
	 * @copydoc GridRow::initialize()
	 */
	function initialize($request, $template = null) {
		parent::initialize($request, $template);

		$customFormContext = $this->getCustomFormContext($request);

		// add grid row actions: edit, delete

		$element = parent::getData();
		assert(is_a($element, $customFormContext->getCustomFormElementClassName()));
		$rowId = $this->getId();

		$router = $request->getRouter();
		if (!empty($rowId) && is_numeric($rowId)) {
			// add 'edit' grid row action
			$this->addAction(
				new LinkAction(
					'edit',
					new AjaxModal(
						$router->url($request, null, null, 'editCustomFormElement', null,
							array_merge(
								$customFormContext->getRequestArgs(),
								array('rowId' => $rowId, 'customFormId' => $element->getCustomFormId())
							)
						),
						__('grid.action.edit'),
						'modal_edit',
						true
					),
				__('grid.action.edit'),
				'edit')
			);
			// add 'delete' grid row action
			$this->addAction(
				new LinkAction(
					'delete',
					new RemoteActionConfirmationModal(
						$request->getSession(),
						__('manager.customFormElements.confirmDelete'),
						null,
						$router->url($request, null, null, 'deleteCustomFormElement', null,
							array_merge(
								$customFormContext->getRequestArgs(),
								array('rowId' => $rowId, 'customFormId' => $element->getCustomFormId())
							)
						)
					),
					__('grid.action.delete'),
					'delete')
			);
		} 
	}
}
?>
