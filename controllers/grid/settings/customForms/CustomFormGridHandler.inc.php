<?php

/**
 * @file controllers/grid/settings/customForms/CustomFormGridHandler.inc.php
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2000-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class CustomFormGridHandler
 * @ingroup controllers_grid_settings_customForms
 *
 * @brief Handle custom form grid requests.
 */

import('lib.pkp.classes.controllers.grid.GridHandler');

import('lib.pkp.controllers.grid.settings.customForms.CustomFormGridRow');

import('lib.pkp.classes.context.customForms.CustomFormContextTrait');

class CustomFormGridHandler extends GridHandler {
	use CustomFormContextTrait;

	/**
	 * Constructor
	 */
	function __construct() {
		parent::__construct();
		$this->addRoleAssignment(
			array(ROLE_ID_MANAGER),
			array('fetchGrid', 'fetchRow', 'createCustomForm', 'editCustomForm', 'updateCustomForm',
				'customFormBasics', 'customFormElements', 'copyCustomForm',
				'customFormPreview', 'activateCustomForm', 'deactivateCustomForm', 'deleteCustomForm',
				'saveSequence')
		);
	}

	protected function beforeDeleteCustomForm($customFormId) {
	}

	//
	// Implement template methods from PKPHandler.
	//
	/**
	 * @copydoc GridHandler::initialize()
	 */
	function initialize($request, $args = null) {
		parent::initialize($request, $args);

		$customFormContext = $this->getCustomFormContext($request);

		// Load user-related translations.
		AppLocale::requireComponents(

			LOCALE_COMPONENT_APP_MANAGER,
			LOCALE_COMPONENT_PKP_USER,
			LOCALE_COMPONENT_PKP_MANAGER
		);

		// Basic grid configuration.
		$this->setTitle('manager.' . $customFormContext->getPrefixLowercased() . 'Forms');

		// Grid actions.
		$router = $request->getRouter();

		import('lib.pkp.classes.linkAction.request.AjaxModal');
		$this->addAction(
			new LinkAction(
				'createCustomForm',
				new AjaxModal(
					$router->url($request, null, null, 'createCustomForm', null, $customFormContext->getRequestArgs()),
					__('manager.' . $customFormContext->getPrefixLowercased() . 'Forms.create'),
					'modal_add_item',
					true
					),
				__('manager.' . $customFormContext->getPrefixLowercased() . 'Forms.create'),
				'add_item')
		);

		//
		// Grid columns.
		//
		$gridCellProvider = $customFormContext->getGridCellProvider();

		// custom form name.
		$this->addColumn(
			new GridColumn(
				'name',
				'manager.customForms.title',
				null,
				null,
				$gridCellProvider
			)
		);

		// custom form 'activate/deactivate'
		// if ($element->getActive()) {
		$this->addColumn(
			new GridColumn(
				'active',
				'common.active',
				null,
				'controllers/grid/common/cell/selectStatusCell.tpl',
				$gridCellProvider
			)
		);
	}

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

		return parent::authorize($request, $args, $roleAssignments);
	}

	//
	// Implement methods from GridHandler.
	//
	/**
	 * @see GridHandler::getRowInstance()
	 * @return UserGridRow
	 */
	protected function getRowInstance() {
		return new CustomFormGridRow();
	}

	/**
	 * @see GridHandler::loadData()
	 * @param $request PKPRequest
	 * @return array Grid data.
	 */
	protected function loadData($request, $filter) {
		$customFormContext = $this->getCustomFormContext($request);

		// Get all custom forms.
		$customFormDao = $customFormContext->getCustomFormDAO();
		$customForms = $customFormDao->getByAssocId($customFormContext->getAssocType(), $customFormContext->getAssocId());

		return $customForms->toAssociativeArray();
	}

	/**
	 * @copydoc GridHandler::getRequestArgs()
	 */
	function getRequestArgs() {
		$request = Application::get()->getRequest();
		$customFormContext = $this->getCustomFormContext($request);

		return array_merge($customFormContext->getRequestArgs(), parent::getRequestArgs());
	}

	/**
	 * @copydoc GridHandler::setDataElementSequence()
	 */
	function setDataElementSequence($request, $rowId, $gridDataElement, $newSequence) {
		$customFormContext = $this->getCustomFormContext($request);

		$customFormDao = $customFormContext->getCustomFormDAO(); /* @var $customFormDao CustomFormDAO */
		$gridDataElement->setSequence($newSequence);
		$customFormDao->updateObject($gridDataElement);
	}

	/**
	 * @see lib/pkp/classes/controllers/grid/GridHandler::getDataElementSequence()
	 */
	function getDataElementSequence($customForm) {
		return $customForm->getSequence();
	}

	/**
	 * @see GridHandler::addFeatures()
	 */
	function initFeatures($request, $args) {
		import('lib.pkp.classes.controllers.grid.feature.OrderGridItemsFeature');
		return array(new OrderGridItemsFeature());
	}


	//
	// Public grid actions.
	//
	/**
	 * Preview a custom form.
	 * @param $args array
	 * @param $request PKPRequest
	 * @return JSONMessage JSON object
	 */
	function customFormPreview($args, $request) {
		// Identify the custom form ID.
		$customFormId = (int) $request->getUserVar('customFormId');

		// Get custom form object
		$customFormContext = $this->getCustomFormContext($request);
		$customFormDao = $customFormContext->getCustomFormDAO();
		$customForm = $customFormDao->getById($customFormId, $customFormContext->getAssocType(), $customFormContext->getAssocId());

		$previewCustomForm = $customFormContext->getPreviewCustomFormInstance($customFormId);
		$previewCustomForm->initData();
		return new JSONMessage(true, $previewCustomForm->fetch($request));
	}

	/**
	 * Add a new custom form.
	 * @param $args array
	 * @param $request PKPRequest
	 * @return JSONMessage JSON object
	 */
	function createCustomForm($args, $request) {
		$customFormContext = $this->getCustomFormContext($request);

		// Form handling.
		$customFormForm = $customFormContext->getCustomFormForm(null);
		$customFormForm->initData();
		return new JSONMessage(true, $customFormForm->fetch($request));
	}

	/**
	 * Edit an existing custom form.
	 * @param $args array
	 * @param $request PKPRequest
	 * @return JSONMessage JSON object
	 */
	function editCustomForm($args, $request) {
		$customFormContext = $this->getCustomFormContext($request);

		$customFormDao = $customFormContext->getCustomFormDAO();
		$customForm = $customFormDao->getById(
			$request->getUserVar('rowId'),
			$customFormContext->getAssocType(), $customFormContext->getAssocId()
		);

		// Display 'editCustomForm' tabs
		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign(array(
			'preview' => $request->getUserVar('preview'),
			'customFormId' => $customForm->getId(),
			'canEdit' => $customForm->isEditable(),
			'assocType' => $customFormContext->getAssocType(),
			'assocId' => $customFormContext->getAssocId()
		));
		return new JSONMessage(true, $templateMgr->fetch('controllers/grid/settings/' . $customFormContext->getPrefixLowercased() . 'Forms/edit' . $customFormContext->getPrefix() . 'Form.tpl'));
	}

	/**
	 * Edit an existing custom form's basics (title, description)
	 * @param $args array
	 * @param $request PKPRequest
	 * @return JSONMessage JSON object
	 */
	function customFormBasics($args, $request) {
		$customFormContext = $this->getCustomFormContext($request);

		// Identify the custom form Id
		$customFormId = (int) $request->getUserVar('customFormId');

		// Form handling
		$customFormForm = $customFormContext->getCustomFormForm($customFormId);
		$customFormForm->initData();
		return new JSONMessage(true, $customFormForm->fetch($request));
	}


	/**
	 * Display a list of the custom form elements within a custom form.
	 * @param $args array
	 * @param $request PKPRequest
	 * @return JSONMessage JSON object
	 */
	function customFormElements($args, $request) {
		$customFormContext = $this->getCustomFormContext($request);

		$templateMgr = TemplateManager::getManager($request);
		$dispatcher = $request->getDispatcher();
		return $templateMgr->fetchAjax(
			'customFormElementsGridContainer',
			$dispatcher->url(
				$request, ROUTE_COMPONENT, null,
				'grid.settings.' . $customFormContext->getPrefixLowercased() . 'Forms.' . $customFormContext->getPrefix() . 'FormElementsGridHandler', 'fetchGrid', null,
				array_merge($customFormContext->getRequestArgs(), array('customFormId' => (int) $request->getUserVar('customFormId')))
			)
		);
	}

	/**
	 * Update an existing custom form.
	 * @param $args array
	 * @param $request PKPRequest
	 * @return JSONMessage JSON message
	 */
	function updateCustomForm($args, $request) {
		// Identify the custom form Id.
		$customFormId = (int) $request->getUserVar('customFormId');

		// Get custom form object
		$customFormContext = $this->getCustomFormContext($request);
		$customFormDao = $customFormContext->getCustomFormDAO();
		$customForm = $customFormDao->getById($customFormId, $customFormContext->getAssocType(), $customFormContext->getAssocId());

		// Form handling.
		$customFormForm = $customFormContext->getCustomFormForm(!isset($customFormId) || empty($customFormId) ? null : $customFormId);
		$customFormForm->readInputData();

		if ($customFormForm->validate()) {
			$customFormForm->execute();

			// Create the notification.
			$notificationMgr = new NotificationManager();
			$user = $request->getUser();
			$notificationMgr->createTrivialNotification($user->getId());

			return DAO::getDataChangedEvent($customFormId);

		}

		return new JSONMessage(false);
	}

	/**
	 * Copy a custom form.
	 * @param $args array
	 * @param $request PKPRequest
	 * @return JSONMessage JSON object
	 */
	function copyCustomForm($args, $request) {
		// Identify the current custom form
		$customFormId = (int) $request->getUserVar('rowId');

		// Get custom form object
		$customFormContext = $this->getCustomFormContext($request);
		$customFormDao = $customFormContext->getCustomFormDAO();
		$customForm = $customFormDao->getById($customFormId, $customFormContext->getAssocType(), $customFormContext->getAssocId());

		if ($request->checkCSRF() && isset($customForm)) {
			$customForm->setActive(0);
			$customForm->setSequence(REALLY_BIG_NUMBER);
			$newCustomFormId = $customFormDao->insertObject($customForm);
			$customFormDao->resequenceCustomForms($customFormContext->getAssocType(), $customFormContext->getAssocId());

			$customFormElementDao = $customFormContext->getCustomFormElementDAO();
			$customFormElements = $customFormElementDao->getByCustomFormId($customFormId);
			while ($customFormElement = $customFormElements->next()) {
				$customFormElement->setCustomFormId($newCustomFormId);
				$customFormElement->setSequence(REALLY_BIG_NUMBER);
				$customFormElementDao->insertObject($customFormElement);
				$customFormElementDao->resequenceCustomFormElements($newCustomFormId);
			}

			// Create the notification.
			$notificationMgr = new NotificationManager();
			$user = $request->getUser();
			$notificationMgr->createTrivialNotification($user->getId());

			return DAO::getDataChangedEvent($newCustomFormId);
		}

		return new JSONMessage(false);
	}

	/**
	 * Activate a custom form.
	 * @param $args array
	 * @param $request PKPRequest
	 * @return JSONMessage JSON object
	 */
	function activateCustomForm($args, $request) {
		// Identify the current custom form
		$customFormId = (int) $request->getUserVar('customFormKey');

		// Get custom form object
		$customFormContext = $this->getCustomFormContext($request);
		$customFormDao = $customFormContext->getCustomFormDAO();
		$customForm = $customFormDao->getById($customFormId, $customFormContext->getAssocType(), $customFormContext->getAssocId());

		if ($request->checkCSRF() && isset($customForm) && !$customForm->getActive()) {
			$customForm->setActive(1);
			$customFormDao->updateObject($customForm);

			// Create the notification.
			$notificationMgr = new NotificationManager();
			$user = $request->getUser();
			$notificationMgr->createTrivialNotification($user->getId());

			return DAO::getDataChangedEvent($customFormId);
		}

		return new JSONMessage(false);
	}


	/**
	 * Deactivate a custom form.
	 * @param $args array
	 * @param $request PKPRequest
	 * @return JSONMessage JSON object
	 */
	function deactivateCustomForm($args, $request) {

		// Identify the current custom form
		$customFormId = (int) $request->getUserVar('customFormKey');

		// Get custom form object
		$customFormContext = $this->getCustomFormContext($request);
		$customFormDao = $customFormContext->getCustomFormDAO();
		$customForm = $customFormDao->getById($customFormId, $customFormContext->getAssocType(), $customFormContext->getAssocId());

		if ($request->checkCSRF() && isset($customForm) && $customForm->getActive()) {
			$customForm->setActive(0);
			$customFormDao->updateObject($customForm);

			// Create the notification.
			$notificationMgr = new NotificationManager();
			$user = $request->getUser();
			$notificationMgr->createTrivialNotification($user->getId());

			return DAO::getDataChangedEvent($customFormId);
		}

		return new JSONMessage(false);
	}

	/**
	 * Delete a custom form.
	 * @param $args array
	 * @param $request PKPRequest
	 * @return JSONMessage JSON object
	 */
	function deleteCustomForm($args, $request) {
		// Identify the current custom form
		$customFormId = (int) $request->getUserVar('rowId');

		// Get custom form object
		$customFormContext = $this->getCustomFormContext($request);
		$customFormDao = $customFormContext->getCustomFormDAO();
		$customForm = $customFormDao->getById($customFormId, $customFormContext->getAssocType(), $customFormContext->getAssocId());

		if ($request->checkCSRF() && isset($customForm) && $customForm->isDeletable()) {
			$this->beforeDeleteCustomForm($customFormId);

			$customFormDao->deleteById($customFormId);

			// Create the notification.
			$notificationMgr = new NotificationManager();
			$user = $request->getUser();
			$notificationMgr->createTrivialNotification($user->getId());

			return DAO::getDataChangedEvent($customFormId);
		}

		return new JSONMessage(false);
	}
}

?>
