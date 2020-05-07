<?php

import('lib.pkp.classes.context.customForms.CustomFormDAOContext');

class ReviewFormDAOContext extends CustomFormDAOContext {

	public function getPrefix() {
		return "Review";
	}

	public function getCustomFormElementDAO() {
		import('lib.pkp.controllers.grid.settings.reviewForms.form.ReviewFormElements');
		return DAORegistry::getDAO('ReviewFormElementDAO');
	}

	public function getFormResponseDAOClassName() {
		return 'ReviewFormResponseDAO';
	}

}

?>