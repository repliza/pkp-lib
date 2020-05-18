<?php

class PKPSubmissionChecklistFormImplementation {

	/** @var Form Form that uses this implementation */
	var $_parentForm;

	/**
	 * Constructor.
	 * @param $parentForm Form A form that can use this form.
	 */
	public function __construct($parentForm = null) {
		assert(is_a($parentForm, 'Form'));
		$this->_parentForm = $parentForm;
	}

	public function getCheckboxId($checklistItemId) {
		return "checklist-$checklistItemId";
	}

	/**
	 * Add checks to form.
	 */
	public function addChecks() {
		$localizedSubmissionChecklistSetting = $this->getSubmissionChecklistLocalizedSetting();

		foreach ((array) $localizedSubmissionChecklistSetting as $key => $checklistItem) {
			$checkboxId = $this->getCheckboxId($key);
			$this->_parentForm->addCheck(new FormValidator($this->_parentForm, $checkboxId, 'required', 'submission.submit.checklistErrors'));
		}
	}

	public function getTemplateVar($request, $submission) {
		$localizedSubmissionChecklistSetting = $this->getSubmissionChecklistLocalizedSetting();
		$isPostbackRequest = $this->isPostbackRequest($request);

		$templateVars = $localizedSubmissionChecklistSetting;

		foreach ((array) $templateVars as $key => $checklistItem) {
			$checkboxId = $this->getCheckboxId($key);
			$templateVars[$key]['checkboxId'] = $checkboxId;
		}

		if ($isPostbackRequest) {
			// on postback read checked states from form data
			foreach ((array) $templateVars as $key => $checklistItem) {
				$checkboxId = $this->getCheckboxId($key);
				$templateVars[$key]['checked'] = $this->_parentForm->getData($checkboxId) === '1';
			}
		} else {
			// on other requests determine checked states from stored submission data
			if (isset($submission)) {
				$locale = $submission->getLocale();

				foreach ((array) $templateVars as $key => $checklistItem) {
					// compare checklist item's value stored on previous submit with current configured value
					$submissionChecklistItemContentSettingName = $this->getSubmissionChecklistItemContentSettingName($key);

					$contentFromPreviousSubmit = $submission->getData($submissionChecklistItemContentSettingName, $locale);
					if ($contentFromPreviousSubmit === $checklistItem['content']) {
						// content of checklist item is still the same, render checklist item with the state stored
						$submissionChecklistItemCheckedSettingName = $this->getSubmissionChecklistItemCheckedSettingName($key);

						$checked = $submission->getData($submissionChecklistItemCheckedSettingName);
					} else {
						// content of checklist item has changed, render it as unchecked so that user needs to confirm again
						$checked = false;
					}

					$templateVars[$key]['checked'] = $checked;
				}
			}
		}

		return $templateVars;
	}

	public function getUserVars() {
		$localizedSubmissionChecklistSetting = $this->getSubmissionChecklistLocalizedSetting();
		$userVars = array();

		foreach ((array) $localizedSubmissionChecklistSetting as $key => $checklistItem) {
			$userVars[] = $this->getCheckboxId($key);
		}

		return $userVars;
	}

	public function execute($submission) {
		// get checkbox states which are possibly stale
		$staleSettingNames = array();

		$submissionData =& $submission->getAllData();

		$checkedSettingPrefix = $this->getSubmissionChecklistItemCheckedSettingPrefix();
		$checkedSettings = array_filter($submissionData, function ($key) use ($checkedSettingPrefix) {
				return strpos($key, $checkedSettingPrefix) === 0;
			}, ARRAY_FILTER_USE_KEY);

		$staleSettingNames = array_merge($staleSettingNames, array_keys($checkedSettings));

		$contentSettingPrefix = $this->getSubmissionChecklistItemContentSettingPrefix();
		$contentSettings = array_filter($submissionData, function ($key) use ($contentSettingPrefix) {
				return strpos($key, $contentSettingPrefix) === 0;
			}, ARRAY_FILTER_USE_KEY);

		$staleSettingNames = array_unique(array_merge($staleSettingNames, array_keys($contentSettings)));

		// store state of checkboxes
		$submissionChecklistSetting = $this->getSubmissionChecklistSetting();

		foreach ((array) $submissionChecklistSetting as $locale => $checklistItems) {
			foreach ($checklistItems as $key => $checklistItem) {
				$checkboxId = $this->getCheckboxId($key);

				$checkedSettingName = $this->getSubmissionChecklistItemCheckedSettingName($key);
				$submission->setData($checkedSettingName, $this->_parentForm->getData($checkboxId) === '1');
				$staleSettingNames = array_diff($staleSettingNames, array($checkedSettingName));

				$contentSettingName = $this->getSubmissionChecklistItemContentSettingName($key);
				$submission->setData($contentSettingName, $checklistItem['content'], $locale);
				$staleSettingNames = array_diff($staleSettingNames, array($contentSettingName));
			}
		}

		// remove stale checkbox states
		$submissionDao = Application::getSubmissionDAO();
		$submissionDao->removeDataObjectSettings('submission_settings',
			array('submission_id' => $submission->getId()), $staleSettingNames);
	}

	public function extendSubmissionDAOLocaleFieldNames($submissionDao) {
		$localizedSubmissionChecklistSetting = $this->getSubmissionChecklistLocalizedSetting();
		$localeFieldNames = array();

		foreach ((array) $localizedSubmissionChecklistSetting as $key => $checklistItem) {
			$localeFieldNames[] = $this->getSubmissionChecklistItemContentSettingName($key);
		}

		$submissionDao->extendLocaleFieldNames($localeFieldNames);
	}

	public function extendSubmissionDAOAdditionalFieldNames($submissionDao) {
		$localizedSubmissionChecklistSetting = $this->getSubmissionChecklistLocalizedSetting();
		$additionalFieldNames = array();

		foreach ((array) $localizedSubmissionChecklistSetting as $key => $checklistItem) {
			$additionalFieldNames[] = $this->getSubmissionChecklistItemCheckedSettingName($key);
		}

		$submissionDao->extendAdditionalFieldNames($additionalFieldNames);
	}

	protected function getSubmissionChecklistLocalizedSetting() {
		return $this->_parentForm->context->getLocalizedSetting('submissionChecklist');
	}

	protected function getSubmissionChecklistSetting() {
		return $this->_parentForm->context->getSetting('submissionChecklist');
	}

	protected function getSubmissionChecklistItemCheckedSettingPrefix() {
		return "submissionChecklistItemChecked";
	}

	protected function getSubmissionChecklistItemCheckedSettingName($checklistItemId) {
		return $this->getSubmissionChecklistItemCheckedSettingPrefix() . $checklistItemId;
	}

	protected function getSubmissionChecklistItemContentSettingPrefix() {
		return "submissionChecklistItemContent";
	}

	protected function getSubmissionChecklistItemContentSettingName($checklistItemId) {
		return $this->getSubmissionChecklistItemContentSettingPrefix() . $checklistItemId;
	}

	protected function isPostbackRequest($request) {
		$router = $request->getRouter();
		$isPostbackRequest = is_a($router, 'PKPPageRouter') && $router->getRequestedOp($request) === 'saveStep';
	}

}
