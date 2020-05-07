<?php

trait CustomFormContextTrait {
	protected function createCustomFormContext($request) {
		import('lib.pkp.classes.context.customForms.CustomFormContext');
		return new CustomFormContext($request);
	}

	protected function getCustomFormContext($request) {
		static $context = null;
		
		if (!$context)
			$context = $this->createCustomFormContext($request);

		return $context;
	}
}

?>