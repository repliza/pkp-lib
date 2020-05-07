<?php
/**
 * @file controllers/listbuilder/settings/customForms/CustomFormElementResponseItemListbuilderHandler.inc.php
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2000-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class CustomFormElementResponseItemListbuilderHandler
 * @ingroup controllers_listbuilder_settings_customForms
 *
 * @brief Custom form element response item listbuilder handler
 */

import('lib.pkp.controllers.listbuilder.settings.SetupListbuilderHandler');
import('lib.pkp.classes.context.customForms.CustomFormContextTrait');

class CustomFormElementResponseItemListbuilderHandler extends SetupListbuilderHandler {
	use CustomFormContextTrait;

	/** @var int Custom form element ID **/
	var $_customFormElementId;

	//
	// Overridden template methods
	//
	/**
	 * @copydoc SetupListbuilderHandler::initialize()
	 */
	function initialize($request, $args = null) {
		parent::initialize($request, $args);
		AppLocale::requireComponents(LOCALE_COMPONENT_PKP_MANAGER);
		$this->_customFormElementId = (int) $request->getUserVar('customFormElementId');

		// Basic configuration
		$this->setTitle('grid.customFormElement.responseItems');
		$this->setSourceType(LISTBUILDER_SOURCE_TYPE_TEXT);
		$this->setSaveType(LISTBUILDER_SAVE_TYPE_EXTERNAL);
		$this->setSaveFieldName('possibleResponses');

		// Possible response column
		$customFormContext = $this->getCustomFormContext($request);

		$responseColumn = new MultilingualListbuilderGridColumn($this, 'possibleResponse', 'manager.customFormElements.possibleResponse', null, null, null, null, array('tabIndex' => 1));
	 	$responseColumn->setCellProvider($customFormContext->getElementResponseItemListbuilderGridCellProvider());
		$this->addColumn($responseColumn);
	}

	/**
	 * @copydoc GridHandler::loadData()
	 */
	protected function loadData($request, $filter = null) {
		$customFormContext = $this->getCustomFormContext($request);

		$customFormElementDao = $customFormContext->getCustomFormElementDAO();
		$customFormElement = $customFormElementDao->getById($this->_customFormElementId);
		$formattedResponses = array();
		if ($customFormElement) {
			$possibleResponses = $customFormElement->getPossibleResponses(null);
			foreach ((array) $possibleResponses as $locale => $values) {
				foreach ($values as $rowId => $value) {
					// WARNING: Listbuilders don't like 0 row IDs; offsetting
					// by 1. This is reversed in the saving code.
					$formattedResponses[$rowId+1][0]['content'][$locale] = $value;
				}
			}
		}
		return $formattedResponses;
	}

	/**
	 * @copydoc SetupListbuilderHandler::getRequestArgs()
	 */
	function getRequestArgs() {
		$request = Application::get()->getRequest();
		$customFormContext = $this->getCustomFormContext($request);

		return array_merge($customFormContext->getRequestArgs(), parent::getRequestArgs());
	}

	/**
	 * @copydoc GridHandler::getRowDataElement
	 */
	protected function getRowDataElement($request, &$rowId) {
		// Fallback on the parent if an existing rowId is found
		if ( !empty($rowId) ) {
			return parent::getRowDataElement($request, $rowId);
		}

		// If we're bouncing a row back upon a row edit
		$rowData = $this->getNewRowId($request);
		if ($rowData) {
			return array(array('content' => $rowData['possibleResponse']));
		}

		// If we're generating an empty row to edit
		return array(array('content' => array()));
	}

	/**
	 * @copydoc ListbuilderHandler::fetch()
	 */
	function fetch($args, $request) {
		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('availableOptions', true);
		return $this->fetchGrid($args, $request);
	}
}

?>
