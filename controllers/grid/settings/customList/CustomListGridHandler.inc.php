<?php

/**
 * @file controllers/grid/settings/customList/CustomListGridHandler.inc.php
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2000-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class CustomListGridHandler
 * @ingroup controllers_grid_settings_customList
 *
 * @brief Handle customList grid requests.
 */

import('lib.pkp.controllers.grid.settings.SetupGridHandler');
import('lib.pkp.controllers.grid.settings.customList.CustomListGridRow');
import('lib.pkp.classes.context.customList.CustomListContextTrait');

class CustomListGridHandler extends SetupGridHandler {
	use CustomListContextTrait;

	/**
	 * Constructor
	 */
	function __construct() {
		parent::__construct();
		$this->addRoleAssignment(array(ROLE_ID_MANAGER),
				array('fetchGrid', 'fetchRow', 'addItem', 'editItem', 'updateItem', 'deleteItem', 'saveSequence'));
	}

	protected function getIdStem() {
		return 'List';
	}

	protected function getAddItemLinkTitleLocaleKey() {
		return 'grid.action.addItem';
	}

	protected function getAddItemModalTitleLocaleKey() {
		return 'grid.action.addItem';
	}

	//
	// Overridden template methods
	//
	/**
	 * @copydoc SetupGridHandler::initialize()
	 */
	function initialize($request, $args = null) {
		parent::initialize($request, $args);

		$customListContext = $this->getCustomListContext($request);

		// Basic grid configuration
		$this->setId($customListContext->getPrefixLowercased() . $this->getIdStem());
		$this->setTitle($customListContext->getGridTitleLocaleKey());

		// Add grid-level actions
		import('lib.pkp.classes.linkAction.request.AjaxModal');
		$router = $request->getRouter();
		$this->addAction(
			new LinkAction(
				'addItem',
				new AjaxModal(
					$router->url($request, null, null, 'addItem', null,
						array_merge(
							$customListContext->getRequestArgs(),
							array('gridId' => $this->getId())
						)
					),
					__($this->getAddItemModalTitleLocaleKey()),
					'modal_add_item',
					true),
				__($this->getAddItemLinkTitleLocaleKey()),
				'add_item')
		);
	}


	//
	// Overridden methods from GridHandler
	//
	/**
	 * @copydoc GridHandler::initFeatures()
	 */
	function initFeatures($request, $args) {
		import('lib.pkp.classes.controllers.grid.feature.OrderGridItemsFeature');
		return array(new OrderGridItemsFeature());
	}

	// Overridden to adjust grid postback urls (ensures that order feature and refresh is working)
	function getRequestArgs() {
		$request = Application::get()->getRequest();
		$customListContext = $this->getCustomListContext($request);

		$requestArgs = array_merge(
			$customListContext->getRequestArgs(),
			parent::getRequestArgs()
		);

		return $requestArgs;
	}

	/**
	 * @copydoc GridHandler::getRowInstance()
	 */
	protected function getRowInstance() {
		return new CustomListGridRow();
	}

	/**
	 * @copydoc GridHandler::loadData()
	 */
	protected function loadData($request, $filter) {
		// Elements to be displayed in the grid
		$customListContext = $this->getCustomListContext($request);
		$customListSetting = $customListContext->getSetting();

		return $customListSetting[AppLocale::getLocale()];
	}


	//
	// Public grid actions.
	//
	/**
	 * An action to add a new customList
	 * @param $args array
	 * @param $request PKPRequest
	 */
	function addItem($args, $request) {
		// Calling editItem with an empty row id will add a new row.
		return $this->editItem($args, $request);
	}

	/**
	 * An action to edit a customList
	 * @param $args array
	 * @param $request PKPRequest
	 * @return JSONMessage JSON object
	 */
	function editItem($args, $request) {
		$customListContext = $this->getCustomListContext($request);
		$customListRowId = isset($args['rowId']) ? $args['rowId'] : null;
		$customListForm = $customListContext->getCustomListForm($customListRowId);

		$customListForm->initData($args, $request);

		return new JSONMessage(true, $customListForm->fetch($request));
	}

	/**
	 * Update a customList
	 * @param $args array
	 * @param $request PKPRequest
	 * @return JSONMessage JSON object
	 */
	function updateItem($args, $request) {
		// -> customListRowId must be present and valid
		// -> htmlId must be present and valid

		$customListContext = $this->getCustomListContext($request);
		$customListRowId = isset($args['rowId']) ? $args['rowId'] : null;
		$customListForm = $customListContext->getCustomListForm($customListRowId);
		$customListForm->readInputData();

		if ($customListForm->validate()) {
			$customListForm->execute($args, $request);
			return DAO::getDataChangedEvent($customListForm->customListRowId);
		} else {
			return new JSONMessage(false);
		}
	}

	/**
	 * Delete a customList
	 * @param $args array
	 * @param $request PKPRequest
	 * @return JSONMessage JSON object
	 */
	function deleteItem($args, $request) {
		if (!$request->checkCSRF()) return new JSONMessage(false);

		$rowId = $request->getUserVar('rowId');

		$customListContext = $this->getCustomListContext($request);

		// get all of the custom list items
		$customListSetting = $customListContext->getSetting();

		foreach (AppLocale::getSupportedLocales() as $locale => $name) {
			$customListRows =& $customListSetting[$locale];
			if ( isset($customListRows[$rowId]) ) {
				unset($customListRows[$rowId]);
			} else {
				// only fail if the currently displayed locale was not set
				// (this is the one that needs to be removed from the currently displayed grid)
				if ( $locale == AppLocale::getLocale() ) {
					return new JSONMessage(false, __('manager.setup.errorDeletingCustomList'));
				}
			}
		}

		$customListContext->updateSetting($customListSetting, 'object', true);
		return DAO::getDataChangedEvent($rowId);
	}

	/**
	 * @copydoc GridHandler::getDataElementSequence()
	 */
	function getDataElementSequence($gridDataElement) {
		return $gridDataElement['order'];
	}

	/**
	 * @copydoc GridHandler::setDataElementSequence()
	 */
	function setDataElementSequence($request, $rowId, $gridDataElement, $newSequence) {
		$customListContext = $this->getCustomListContext($request);

		// Get all of the custom list items.
		$customListSetting = $customListContext->getSetting();
		$locale = AppLocale::getLocale();

		$customListRows =& $customListSetting[$locale];
		if (isset($customListRows[$rowId])) {
			$customListRows[$rowId]['order'] = $newSequence;
		}

		$orderMap = array();
		foreach ($customListRows as $id => $item) {
			$orderMap[$id] = $item['order'];
		}

		asort($orderMap);

		// Build the new order list object.
		$orderedItems = array();
		foreach ($orderMap as $id => $order) {
			if (isset($customListRows[$id])) {
				$orderedItems[$locale][$id] = $customListRows[$id];
			}
		}

		$customListContext->updateSetting($orderedItems, 'object', true);
	}
}

?>
