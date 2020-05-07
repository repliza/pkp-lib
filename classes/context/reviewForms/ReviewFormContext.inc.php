<?php

import('lib.pkp.classes.context.customForms.CustomFormContext');

class ReviewFormContext extends CustomFormContext {
	public function __construct($request) {
		parent::__construct($request);
	}

	public function getPrefix() {
		return "Review";
	}

	public function getGridCellProvider() {
		import('lib.pkp.controllers.grid.settings.reviewForms.ReviewFormGridCellProvider');
		return new ReviewFormGridCellProvider();
	}

	public function getElementGridCellProvider() {
		import('lib.pkp.controllers.grid.settings.reviewForms.ReviewFormElementGridCellProvider');
		return new ReviewFormElementGridCellProvider();
	}

	public function createElementForm($customFormId, $customFormElementId = null) {
		import('lib.pkp.controllers.grid.settings.reviewForms.form.ReviewFormElementForm');
		return new ReviewFormElementForm($customFormId, $customFormElementId);
	}

	public function getCustomFormDAO() {
		return DAORegistry::getDAO('ReviewFormDAO');
	}

	public function getCustomFormElementDAO() {
		import('lib.pkp.controllers.grid.settings.reviewForms.form.ReviewFormElements');
		return DAORegistry::getDAO('ReviewFormElementDAO');
	}

	public function getPreviewCustomFormInstance($customFormId) {
		import('lib.pkp.controllers.grid.settings.reviewForms.form.PreviewReviewForm');
		return new PreviewReviewForm($customFormId);
	}

	public function getCustomFormForm($customFormId) {
		import('lib.pkp.controllers.grid.settings.reviewForms.form.ReviewFormForm');
		return new ReviewFormForm($customFormId);
	}

	public function getHandlerName() {
		return 'grid.settings.reviewForms.ReviewFormGridHandler';
	}

	public function importCustomFormElement() {
		import('lib.pkp.classes.reviewForm.ReviewFormElement');
	}

	public function getCustomFormElementClassName() {
		return "ReviewFormElement";
	}

	public function getElementResponseItemListbuilderGridCellProvider() {
		import('lib.pkp.controllers.listbuilder.settings.reviewForms.ReviewFormElementResponseItemListbuilderGridCellProvider');
		return new ReviewFormElementResponseItemListbuilderGridCellProvider();
	}

	protected function resolveAssocType($request) {
		return Application::getContextAssocType();
	}

	protected function resolveAssocId($request) {
		$context = $request->getContext();

		return $context->getId();
	}
}

?>