<?php

trait CustomFormDAOContextTrait {
	protected function createCustomFormDAOContext() {
		import('lib.pkp.classes.context.customForms.CustomFormDAOContext');
		return new CustomFormDAOContext();
	}

	protected function getCustomFormDAOContext() {
		static $context = null;
		
		if (!$context)
			$context = $this->createCustomFormDAOContext();

		return $context;
	}
}

?>