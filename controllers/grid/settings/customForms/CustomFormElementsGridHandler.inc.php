<?php

/**
 * @file controllers/grid/settings/customForms/CustomFormElementsGridHandler.inc.php
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2000-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class CustomFormElementsGridHandler
 * @ingroup controllers_grid_settings_customForms
 *
 * @brief Handle custom form element grid requests.
 */

import('lib.pkp.classes.controllers.grid.GridHandler');
import('lib.pkp.controllers.grid.settings.customForms.CustomFormElementGridRow');
import('lib.pkp.classes.context.customForms.CustomFormContextTrait');

class CustomFormElementsGridHandler extends GridHandler {
	use CustomFormContextTrait;

	/** @var int Custom form ID */
	var $customFormId;

	/**
	 * Constructor
	 */
	function __construct() {
		parent::__construct();
		$this->addRoleAssignment(array(
			ROLE_ID_MANAGER),
			array('fetchGrid', 'fetchRow', 'saveSequence',
				'createCustomFormElement', 'editCustomFormElement', 'deleteCustomFormElement', 'updateCustomFormElement')
		);
	}

	//
	// Implement template methods from PKPHandler.
	//
	/**
	 * @see PKPHandler::authorize()
	 */
	function authorize($request, &$args, $roleAssignments) {
		import('lib.pkp.classes.security.authorization.PolicySet');
		$rolePolicy = new PolicySet(COMBINING_PERMIT_OVERRIDES);

		import('lib.pkp.classes.security.authorization.RoleBasedHandlerOperationPolicy');
		foreach($roleAssignments as $role => $operations) {
			$rolePolicy->addPolicy(new RoleBasedHandlerOperationPolicy($request, $role, $operations));
		}
		$this->addPolicy($rolePolicy);

		$this->customFormId = (int) $request->getUserVar('customFormId');
		$customFormContext = $this->getCustomFormContext($request);
		$customFormDao = $customFormContext->getCustomFormDAO();
		if (!$customFormDao->customFormExists($this->customFormId, $customFormContext->getAssocType(), $customFormContext->getAssocId())) return false;

		return parent::authorize($request, $args, $roleAssignments);
	}

	/**
	 * @copydoc GridHandler::initialize()
	 */
	function initialize($request, $args = null) {
		parent::initialize($request, $args);

		$customFormContext = $this->getCustomFormContext($request);

		// Load user-related translations.
		AppLocale::requireComponents(
			LOCALE_COMPONENT_APP_ADMIN,
			LOCALE_COMPONENT_APP_MANAGER,
			LOCALE_COMPONENT_APP_COMMON,
			LOCALE_COMPONENT_PKP_MANAGER,
			LOCALE_COMPONENT_PKP_USER
		);

		// Grid actions.
		$router = $request->getRouter();

		import('lib.pkp.classes.linkAction.request.AjaxModal');

		// Create Custom Form Element link
		$this->addAction(
			new LinkAction(
				'createCustomFormElement',
				new AjaxModal(
					$router->url($request, null, null, 'createCustomFormElement', null,
						array_merge(
							$customFormContext->getRequestArgs(),
							array('customFormId' => $this->customFormId)
						)
					),
					__('manager.customFormElements.create'),
					'modal_add_item',
					true
					),
				__('manager.customFormElements.create'),
				'add_item'
			)
		);


		//
		// Grid columns.
		//
		$customFormElementGridCellProvider = $customFormContext->getElementGridCellProvider();

		// Custom form element name.
		$this->addColumn(
			new GridColumn(
				'question',
				'manager.customFormElements.question',
				null,
				null,
				$customFormElementGridCellProvider,
				array('html' => true, 'maxLength' => 220)
			)
		);

		// Basic grid configuration.
		$this->setTitle('manager.customFormElements');
	}

	//
	// Implement methods from GridHandler.
	//
	/**
	 * @see GridHandler::addFeatures()
	 */
	function initFeatures($request, $args) {
		$features = parent::initFeatures($request, $args);

		import('lib.pkp.classes.controllers.grid.feature.OrderGridItemsFeature');
		$features[] = new OrderGridItemsFeature();

		return $features;
	}

	/**
	 * @see GridHandler::getRowInstance()
	 * @return UserGridRow
	 */
	protected function getRowInstance() {
		return new CustomFormElementGridRow();
	}

	/**
	 * @see GridHandler::loadData()
	 * @param $request PKPRequest
	 * @return array Grid data.
	 */
	protected function loadData($request, $filter) {
		$customFormContext = $this->getCustomFormContext($request);

		// Get custom form elements.
		//$rangeInfo = $this->getRangeInfo('customFormElements');
		$customFormElementDao = $customFormContext->getCustomFormElementDAO();
		$customFormElements = $customFormElementDao->getByCustomFormId($this->customFormId, null); //FIXME add range info?

		return $customFormElements->toAssociativeArray();
	}

	/**
	 * @copydoc GridHandler::getRequestArgs()
	 */
	function getRequestArgs() {
		$request = Application::get()->getRequest();
		$customFormContext = $this->getCustomFormContext($request);

		return array_merge($customFormContext->getRequestArgs(), array('customFormId' => $this->customFormId), parent::getRequestArgs());
	}

	/**
	 * @see lib/pkp/classes/controllers/grid/GridHandler::getDataElementSequence()
	 */
	function getDataElementSequence($gridDataElement) {
		// https://github.com/pkp/pkp-lib/issues/39265, remove this comment on upgrade when issue has officially been fixed
		return $gridDataElement->getSequence();
	}

	/**
	 * @copydoc GridHandler::setDataElementSequence()
	 */
	function setDataElementSequence($request, $rowId, $gridDataElement, $newSequence) {
		$customFormContext = $this->getCustomFormContext($request);

		$customFormElementDao = $customFormContext->getCustomFormElementDAO(); /* @var $customFormElementDao CustomFormElementDAO */
		$gridDataElement->setSequence($newSequence);
		$customFormElementDao->updateObject($gridDataElement);
	}


	//
	// Public grid actions.
	//
	/**
	 * Add a new custom form element.
	 * @param $args array
	 * @param $request PKPRequest
	 * @return JSONMessage JSON object
	 */
	function createCustomFormElement($args, $request) {
		$customFormContext = $this->getCustomFormContext($request);

		// Form handling
		$customFormElementForm = $customFormContext->createElementForm($this->customFormId);
		$customFormElementForm->initData();
		return new JSONMessage(true, $customFormElementForm->fetch($request));
	}

	/**
	 * Edit an existing custom form element.
	 * @param $args array
	 * @param $request PKPRequest
	 * @return JSONMessage JSON object
	 */
	function editCustomFormElement($args, $request) {
		// Identify the custom form element Id
		$customFormElementId = (int) $request->getUserVar('rowId');

		// Display form
		$customFormContext = $this->getCustomFormContext($request);

		$customFormElementForm = $customFormContext->createElementForm($this->customFormId, $customFormElementId);
		$customFormElementForm->initData();
		return new JSONMessage(true, $customFormElementForm->fetch($request));
	}

	protected function beforeUpdateCustomFormElement($args, $request, $customFormElementId) {
		$customFormContext = $this->getCustomFormContext($request);

		$customFormElementDao = $customFormContext->getCustomFormElementDAO();

		if ($customFormElementId && !$customFormElementDao->customFormElementExists($customFormElementId, $this->customFormId)) {
			fatalError('Invalid custom form information!');
		}
	}

	/**
	 * Save changes to a custom form element.
	 * @param $args array
	 * @param $request PKPRequest
	 * @return JSONMessage JSON object
	 */
	function updateCustomFormElement($args, $request) {
		$customFormElementId = (int) $request->getUserVar('customFormElementId');

		$customFormContext = $this->getCustomFormContext($request);

		$customFormDao = $customFormContext->getCustomFormDAO();

		$customForm = $customFormDao->getById($this->customFormId, $customFormContext->getAssocType(), $customFormContext->getAssocId());

		$this->beforeUpdateCustomFormElement($args, $request, $customFormElementId);

		$customFormElementForm = $customFormContext->createElementForm($this->customFormId, $customFormElementId);
		$customFormElementForm->readInputData();

		if ($customFormElementForm->validate()) {
			$customFormElementId = $customFormElementForm->execute();

			// Create the notification.
			$notificationMgr = new NotificationManager();
			$user = $request->getUser();
			$notificationMgr->createTrivialNotification($user->getId());

			return DAO::getDataChangedEvent($customFormElementId);
		}

		return new JSONMessage(false);
	}

	protected function canDeleteCustomFormElement($args, $request) {
		return $request->checkCSRF();
	}

	/**
	 * Delete a custom form element.
	 * @param $args array
	 * @param $request PKPRequest
	 * @return JSONMessage JSON object
	 */
	function deleteCustomFormElement($args, $request) {
		$customFormElementId = (int) $request->getUserVar('rowId');

		$customFormContext = $this->getCustomFormContext($request);

		$customFormDao = $customFormContext->getCustomFormDAO();

		if ($this->canDeleteCustomFormElement($args, $request)) {
			$customFormElementDao = $customFormContext->getCustomFormElementDAO();
			$customFormElementDao->deleteById($customFormElementId);
			return DAO::getDataChangedEvent($customFormElementId);
		}

		return new JSONMessage(false);
	}
}

?>
