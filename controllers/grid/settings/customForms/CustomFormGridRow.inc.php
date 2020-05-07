<?php

/**
 * @file controllers/grid/settings/customForms/CustomFormGridRow.inc.php
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2000-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class CustomFormGridRow
 * @ingroup controllers_grid_settings_customForms
 *
 * @brief CustomForm grid row definition
 */

import('lib.pkp.classes.controllers.grid.GridRow');
import('lib.pkp.classes.linkAction.request.RemoteActionConfirmationModal');
import('lib.pkp.classes.context.customForms.CustomFormContextTrait');

class CustomFormGridRow extends GridRow {
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

		// Is this a new row or an existing row?
		$element = $this->getData();
		assert(is_a($element, $customFormContext->getPrefix() . 'Form'));

		$rowId = $this->getId();

		if (!empty($rowId) && is_numeric($rowId)) {
			// Only add row actions if this is an existing row
			$router = $request->getRouter();

			// determine whether or not this Custom Form is editable.
			$canEdit = $element->isEditable();

			// if custom form is editable, add 'edit' grid row action
			if($canEdit) {
				$this->addAction(
					new LinkAction(
						'edit',
						new AjaxModal(
							$router->url($request, null, null, 'editCustomForm', null,
								array_merge(
									$customFormContext->getRequestArgs(),
									array('rowId' => $rowId)
								)
							),
							__('grid.action.edit'),
							'modal_edit',
							true
						),
					__('grid.action.edit'),
					'edit')
				);
			}

			// if custom form is not editable, add 'copy' grid row action
			$this->addAction(
				new LinkAction(
					'copy',
					new RemoteActionConfirmationModal(
						$request->getSession(),
						__('manager.' . $customFormContext->getPrefixLowercased() . 'Forms.confirmCopy'),
						null,
						$router->url($request, null, null, 'copyCustomForm', null,
							array_merge(
								$customFormContext->getRequestArgs(),
								array('rowId' => $rowId)
							)
						)
						),
					__('grid.action.copy'),
					'copy'
					)
			);

			// add 'preview' grid row action
			$this->addAction(
				new LinkAction(
					'preview',
					new AjaxModal(
						$router->url($request, null, null, 'editCustomForm', null, 
							array_merge(
								$customFormContext->getRequestArgs(),
								array('rowId' => $rowId, 'preview' => 1)
							)
						),
						__('grid.action.preview'),
						'preview',
						true
					),
					__('grid.action.preview'),
					'preview'
				)
			);

			// if custom form is editable, add 'delete' grid row action.
			if($canEdit) {
				$this->addAction(
					new LinkAction(
						'delete',
						new RemoteActionConfirmationModal(
							$request->getSession(),
							__('manager.' . $customFormContext->getPrefixLowercased() . 'Forms.confirmDelete'),
							null,
							$router->url($request, null, null, 'deleteCustomForm', null,
								array_merge(
									$customFormContext->getRequestArgs(),
									array('rowId' => $rowId)
								)
							)
						),
						__('grid.action.delete'),
						'delete')
				);
			}
		}
	}
}

?>
