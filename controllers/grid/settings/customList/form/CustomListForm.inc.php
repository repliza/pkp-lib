<?php

/**
 * @file controllers/grid/settings/customChecklist/form/CustomListForm.inc.php
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2003-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class CustomChecklistForm
 * @ingroup controllers_grid_settings_customList_form
 *
 * @brief Form for adding/edditing a customList
 * stores/retrieves from an associative array
 */

import('lib.pkp.classes.form.Form');
import('lib.pkp.classes.context.customList.CustomListContextTrait');

class CustomListForm extends Form {
	use CustomListContextTrait;

	/** @var int The id for the custom list row being edited **/
	var $customListRowId;

	/**
	 * Constructor.
	 */
	function __construct($customListRowId = null, $template = 'controllers/grid/settings/customList/form/customListForm.tpl') {
		$this->customListRowId = $customListRowId;
		parent::__construct($template);

		$request = Application::get()->getRequest();
		$customListContext = $this->getCustomListContext($request);

		// Validation checks for this form
		$this->addCheck(new FormValidatorPost($this));
		$this->addCheck(new FormValidatorCSRF($this));
	}

	protected function getUserVars() {
		return array();
	}

	public function getLocaleFieldNames() {
		return array();
	}

	/**
	 * Initialize form data from current settings.
	 * @param $args array
	 * @param $request PKPRequest
	 */
	function initData($args) {
		$request = Application::get()->getRequest();
		$customListContext = $this->getCustomListContext($request);
		$customListSetting = $customListContext->getSetting();

		$this->_data = [];

		$localeFieldNames = $this->getLocaleFieldNames();

		$userVars = $this->getUserVars();
		foreach ($userVars as $userVar) {

			$isLocalized = in_array($userVar, $localeFieldNames);
			if ($isLocalized) {

				$v = array();
				// prepare localizable array for this checklist Item
				foreach (AppLocale::getSupportedLocales() as $locale => $name) {
					$v[$locale] = null;
				}

				// if editing, set the user var value
				// use of '$userVar' as key is for backwards compatibility
				if ( isset($this->customListRowId) ) {
					foreach (AppLocale::getSupportedLocales() as $locale => $name) {
						$customListRows =& $customListSetting[$locale];

						if ( !isset($customListRows[$this->customListRowId][$userVar])) {
							$v[$locale] = '';
						} else {
							$v[$locale] = $customListRows[$this->customListRowId][$userVar];
						}
					}
				}

			} else {
				$v = null;

				if ( isset($this->customListRowId) ) {
					$locale = AppLocale::getPrimaryLocale();

					if ( isset($customListSetting[$locale])) {
						$customListRows =& $customListSetting[$locale];

						if ( isset($customListRows[$this->customListRowId][$userVar])) {
							$v = $customListRows[$this->customListRowId][$userVar];
						}
					}
				}
			}
			// assign the data to the form
			$this->_data[$userVar] = $v;
		}

		// grid related data
		$this->_data['gridId'] = $args['gridId'];
		$this->_data['rowId'] = isset($args['rowId']) ? $args['rowId'] : null;
	}

	/**
	 * Fetch
	 * @param $request PKPRequest
	 * @see Form::fetch()
	 */
	function fetch($request) {
		AppLocale::requireComponents(LOCALE_COMPONENT_APP_MANAGER);

		$router = $request->getRouter();
		$customListContext = $this->getCustomListContext($request);

		$this->_data['formActionUrl']	= $router->url($request, null, $customListContext->getGridHandlerComponent(), 'updateItem', null,
				$customListContext->getRequestArgs()
			);

		return parent::fetch($request);
	}

	/**
	 * Assign form data to user-submitted data.
	 * @see Form::readInputData()
	 */
	function readInputData() {
		$this->readUserVars($this->getUserVars());
		$this->readUserVars(array('customListId'));
		$this->readUserVars(array('gridId', 'rowId'));
	}

	/**
	 * @copydoc Form::execute()
	 */
	function execute(...$functionArgs) {
		$request = Application::get()->getRequest();
		$customListContext = $this->getCustomListContext($request);
		$customListSetting = $customListContext->getSetting();
		$locale = AppLocale::getPrimaryLocale();
		//FIXME: a bit of kludge to get unique customChecklist id's
		$this->customListRowId = ($this->customListRowId != null ? $this->customListRowId:(max(array_keys($customListSetting[$locale])) + 1));

		$localeFieldNames = $this->getLocaleFieldNames();

		$order = 0;
		foreach ($customListSetting[$locale] as $customListRow) {
			if ($customListRow['order'] > $order) {
				$order = $customListRow['order'];
			}
		}
		$order++;

		$userVars = $this->getUserVars();
		foreach ($userVars as $userVar) {

			$userVarData = $this->getData($userVar);
			foreach (AppLocale::getSupportedLocales() as $locale => $name) {
				$customListRows =& $customListSetting[$locale];

				if (in_array($userVar, $localeFieldNames)) {
					if (isset($userVarData[$locale])) {
						$v = $userVarData[$locale];
					} else {
						$v = null;
					}

				} else {
					$v = $userVarData;
				}

				$customListRows[$this->customListRowId][$userVar] = $v;
				$customListRows[$this->customListRowId]['order'] = $order;
			}
		}

		$customListContext->updateSetting($customListSetting, 'object', true);
		parent::execute(...$functionArgs);
		return true;
	}
}

?>
