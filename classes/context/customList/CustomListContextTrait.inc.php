<?php

trait CustomListContextTrait {
	protected function createCustomListContext($request) {
		import('lib.pkp.classes.context.customList.CustomListContext');
		return new CustomListContext($request);
	}

	protected function getCustomListContext($request) {
		static $context = null;

		if (!$context)
			$context = $this->createCustomListContext($request);

		return $context;
	}
}

?>
