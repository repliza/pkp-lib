<?php

import('lib.pkp.classes.context.customChecklist.CustomChecklistContext');

class SubmissionChecklistContext extends CustomChecklistContext {
	public function __construct($request) {
		parent::__construct();

		$router = $request->getRouter();
		$this->journal = $router->getContext($request);
	}

	public function getPrefix() {
		return "Submission";
	}

	public function getSetting($locale = null) {
		return $this->journal->getData('submissionChecklist', $locale);
	}

	public function updateSetting($value, $type = null, $isLocalized = false) {
		$this->journal->updateSetting('submissionChecklist', $value, $type, $isLocalized);
	}

	public function getGridTitleLocaleKey() {
		return 'manager.setup.submissionPreparationChecklist';
	}

	public function getCustomListForm($customChecklistId) {
		import('lib.pkp.controllers.grid.settings.submissionChecklist.form.SubmissionChecklistForm');
		$submissionChecklistForm = new SubmissionChecklistForm($customChecklistId);

		return $submissionChecklistForm;
	}

	private $journal;
}

?>
