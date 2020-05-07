<?php

import('lib.pkp.classes.context.customForms.BaseCustomFormContext');

class CustomFormContext extends BaseCustomFormContext {
	public function __construct($request) {
		$this->assocType = $this->resolveAssocType($request);
		$this->assocId = $this->resolveAssocId($request);
	}

	public function getRequestArgs()
	{
		return array(
			'assocType' => $this->assocType,
			'assocId' => $this->assocId
		);
	}

	public function getAssocType() {
		return $this->assocType;
	}

	public function getAssocId() {
		return $this->assocId;
	}

	public function getGridCellProvider() {
		import('lib.pkp.controllers.grid.settings.customForms.CustomFormGridCellProvider');
		return new CustomFormGridCellProvider();
	}

	public function getElementGridCellProvider() {
		import('lib.pkp.controllers.grid.settings.customForms.CustomFormElementGridCellProvider');
		return new CustomFormElementGridCellProvider();
	}

	public function createElementForm($customFormId, $customFormElementId = null) {
		import('lib.pkp.controllers.grid.settings.customForms.form.CustomFormElementForm');
		return new CustomFormElementForm($customFormId, $customFormElementId);
	}

	public function getCustomFormDAO() {
		return DAORegistry::getDAO('CustomFormDAO');
	}

	public function getPreviewCustomFormInstance($customFormId) {
		import('lib.pkp.controllers.grid.settings.customForms.form.PreviewCustomForm');
		return new PreviewCustomForm($customFormId);
	}

	public function getCustomFormForm($customFormId) {
		import('lib.pkp.controllers.grid.settings.customForms.form.CustomFormForm');
		return new CustomFormForm($customFormId);
	}

	public function getHandlerName() {
		return 'grid.settings.customForms.CustomFormGridHandler';
	}

	public function importCustomFormElement() {
		import('lib.pkp.classes.customForm.CustomFormElement');
	}

	public function getCustomFormElementClassName() {
		return "CustomFormElement";
	}

	public function getElementResponseItemListbuilderGridCellProvider() {
		import('lib.pkp.controllers.listbuilder.settings.customForms.CustomFormElementResponseItemListbuilderGridCellProvider');
		return new CustomFormElementResponseItemListbuilderGridCellProvider();
	}

	public function getFormIdTableColumnName() {
		return $this->getPrefixLowercased() . "_form_id";
	}

	public function getFormsTableName() {
		return $this->getPrefixLowercased() . '_forms';
	}

	public function getFormSettingsTableName() {
		return $this->getPrefixLowercased() . '_form_settings';
	}

	protected function resolveAssocType($request) {
		$result = intval($request->getUserVar('assocType'));

		if ($result == 0)
			$result = Application::getContextAssocType();

		return $result;
	}

	protected function resolveAssocId($request) {
		$result = intval($request->getUserVar('assocId'));

		if ($result == 0) {
			$context = $request->getContext();

			$result = $context->getId();
		}

		return $result;
	}

	private $assocType;
	private $assocId;
}

?>