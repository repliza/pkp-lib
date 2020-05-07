<?php

/**
 * @file pages/submission/PKPSubmissionHandler.inc.php
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class PKPSubmissionHandler
 * @ingroup pages_submission
 *
 * @brief Base handler for submission requests.
 */

import('classes.handler.Handler');
import('lib.pkp.classes.core.JSONMessage');

abstract class PKPSubmissionHandler extends Handler {

	/**
	 * @copydoc PKPHandler::authorize()
	 */
	function authorize($request, &$args, $roleAssignments) {
		$stepsNumberAndLocaleKeys = $this->getStepsNumberAndLocaleKeys();

		// The policy for the submission handler depends on the
		// step currently requested.
		$step = $this->getStepNumberFromArgs($args, $stepsNumberAndLocaleKeys);

		if (!$this->isStepNumberValid($step, $stepsNumberAndLocaleKeys)) return false;

		// Do we have a submission present in the request?
		$submissionId = (int)$request->getUserVar('submissionId');

		// Are we in step one without a submission present?
		$firstStepNumber = reset($stepsNumberAndLocaleKeys) !== false ? key($stepsNumberAndLocaleKeys) : null;
		if ($step === $firstStepNumber && $submissionId === 0) {
			// Authorize submission creation. Author role not required.
			import('lib.pkp.classes.security.authorization.UserRequiredPolicy');
			$this->addPolicy(new UserRequiredPolicy($request));
			$this->markRoleAssignmentsChecked();
		} else {
			// Authorize editing of incomplete submissions.
			import('lib.pkp.classes.security.authorization.SubmissionAccessPolicy');
			$this->addPolicy(new SubmissionAccessPolicy($request, $args, $roleAssignments, 'submissionId'));
		}

		// Do policy checking.
		if (!parent::authorize($request, $args, $roleAssignments)) return false;

		// Execute additional checking of the step.
		// NB: Move this to its own policy for reuse when required in other places.
		$submission = $this->getAuthorizedContextObject(ASSOC_TYPE_SUBMISSION);

		// Permit if there is no submission set, but request is for initial step.
		if (!is_a($submission, 'Submission') && $step == $firstStepNumber) return true;

		// In all other cases we expect an authorized submission due to
		// the submission access policy above.
		assert(is_a($submission, 'Submission'));

		// Deny if submission is complete (==0 means complete) and at
		// any step other than the "complete" step (the last one)
		$lastStepNumber = end($stepsNumberAndLocaleKeys) !== false ? key($stepsNumberAndLocaleKeys) : null;
		if ($submission->getSubmissionProgress() == 0 && $step != $lastStepNumber) return false;

		// Deny if trying to access a step greater than the current progress
		if ($submission->getSubmissionProgress() != 0 && $step > $submission->getSubmissionProgress()) return false;

		return true;
	}


	//
	// Public Handler Methods
	//
	/**
	 * Redirect to the new submission wizard by default.
	 * @param $args array
	 * @param $request Request
	 */
	function index($args, $request) {
		$request->redirect(null, null, 'wizard');
	}

	/**
	 * Display the tab set for the submission wizard.
	 * @param $args array
	 * @param $request PKPRequest
	 */
	function wizard($args, $request) {
		$this->setupTemplate($request);
		$templateMgr = TemplateManager::getManager($request);
		$step = $this->getStepNumberFromArgs($args);
		$templateMgr->assign('step', $step);

		$templateMgr->assign('sectionId', (int) $request->getUserVar('sectionId')); // to add a sectionId parameter to tab links in template

		$submission = $this->getAuthorizedContextObject(ASSOC_TYPE_SUBMISSION);
		if ($submission) {
			$templateMgr->assign('submissionId', $submission->getId());
			$templateMgr->assign('submissionProgress', (int) $submission->getSubmissionProgress());
		} else {
			$templateMgr->assign('submissionProgress', 1);
		}
		$templateMgr->display('submission/form/index.tpl');
	}

	/**
	 * Display a step for the submission wizard.
	 * Displays submission index page if a valid step is not specified.
	 * @param $args array
	 * @param $request Request
	 * @return JSONMessage JSON object
	 */
	function step($args, $request) {
		$stepsNumberAndLocaleKeys = $this->getStepsNumberAndLocaleKeys();

		$step = $this->getStepNumberFromArgs($args, $stepsNumberAndLocaleKeys);

		$context = $request->getContext();
		$submission = $this->getAuthorizedContextObject(ASSOC_TYPE_SUBMISSION);

		$this->setupTemplate($request);

		$lastStepNumber = end($stepsNumberAndLocaleKeys) !== false ? key($stepsNumberAndLocaleKeys) : null;
		if ( $step < $lastStepNumber ) {
			$formClass = "SubmissionSubmitStep{$step}Form";
			import("classes.submission.form.$formClass");

			$submitForm = new $formClass($context, $submission);
			$submitForm->initData();
			return new JSONMessage(true, $submitForm->fetch($request));
		} elseif($step == $lastStepNumber) {
			$templateMgr = TemplateManager::getManager($request);
			$templateMgr->assign('context', $context);

			// Retrieve the correct url for author review his submission.
			import('classes.core.Services');
			$reviewSubmissionUrl = Services::get('submission')->getWorkflowUrlByUserRoles($submission);
			$router = $request->getRouter();
			$dispatcher = $router->getDispatcher();

			$templateMgr->assign(array(
				'reviewSubmissionUrl' => $reviewSubmissionUrl,
				'submissionId' => $submission->getId(),
				'submitStep' => $step,
				'submissionProgress' => $submission->getSubmissionProgress(),
			));

			return new JSONMessage(true, $templateMgr->fetch('submission/form/complete.tpl'));
		}
	}

	/**
	 * Save a submission step.
	 * @param $args array first parameter is the step being saved
	 * @param $request Request
	 * @return JSONMessage JSON object
	 */
	function saveStep($args, $request) {
		$stepsNumberAndLocaleKeys = $this->getStepsNumberAndLocaleKeys();

		$step = $this->getStepNumberFromArgs($args, $stepsNumberAndLocaleKeys);

		$router = $request->getRouter();
		$context = $router->getContext($request);
		$submission = $this->getAuthorizedContextObject(ASSOC_TYPE_SUBMISSION);

		$this->setupTemplate($request);

		$formClass = "SubmissionSubmitStep{$step}Form";
		import("classes.submission.form.$formClass");

		$submitForm = new $formClass($context, $submission);
		$submitForm->readInputData();

		if (!HookRegistry::call('SubmissionHandler::saveSubmit', array($step, &$submission, &$submitForm))) {
			if ($submitForm->validate()) {
				$nextStep = $this->getNextStepNumber($step);

				$submissionId = $submitForm->execute();

				if ($submissionId) {
					HookRegistry::call(strtolower_codesafe(get_class($this)) . '::stepSaved',
						array($this, $request, $submissionId, $step));
				}

				if (!$submission) {
					return $request->redirectUrlJson($router->url($request, null, null, 'wizard', $nextStep, array('submissionId' => $submissionId), 'step-2'));
				}
				$json = new JSONMessage(true);
				$json->setEvent('setStep', max($nextStep, $submission->getSubmissionProgress()));
				return $json;
			} else {
				// Provide entered tagit fields values
				$tagitKeywords = $submitForm->getData('keywords');
				if (is_array($tagitKeywords)) {
					$tagitFieldNames = $submitForm->_metadataFormImplem->getTagitFieldNames();
					$locales = array_keys($submitForm->supportedLocales);
					$formTagitData = array();
					foreach ($tagitFieldNames as $tagitFieldName) {
						foreach ($locales as $locale) {
							$formTagitData[$locale] = array_key_exists($locale . "-$tagitFieldName", $tagitKeywords) ? $tagitKeywords[$locale . "-$tagitFieldName"] : array();
						}
						$submitForm->setData($tagitFieldName, $formTagitData);
					}
				}
				return new JSONMessage(true, $submitForm->fetch($request));
			}
		}
	}

	//
	// Protected helper methods
	//
	/**
	 * Setup common template variables.
	 * @param $request Request
	 */
	function setupTemplate($request) {
		parent::setupTemplate($request);
		AppLocale::requireComponents(LOCALE_COMPONENT_APP_SUBMISSION, LOCALE_COMPONENT_PKP_SUBMISSION);

		// Get steps information.
		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('steps', $this->getStepsNumberAndLocaleKeys());
	}

	/**
	 * Get the step numbers and their corresponding title locale keys.
	 * @return array
	 */
	abstract function getStepsNumberAndLocaleKeys();

	/**
	 * Validates a step number.
	 * @return bool
	 */
	private function isStepNumberValid($stepNumber, $stepsNumberAndLocaleKeys) {
		return array_key_exists($stepNumber, $stepsNumberAndLocaleKeys);
	}

	/**
	 * Get the step number from args array.
	 * @return int
	 */
	private function getStepNumberFromArgs($args, $stepsNumberAndLocaleKeys = null) {
		if (isset($args[0])) {
			$stepNumber = (int) $args[0];
		} else {
			if (!$stepsNumberAndLocaleKeys)
				$stepsNumberAndLocaleKeys = $this->getStepsNumberAndLocaleKeys();

			reset($stepsNumberAndLocaleKeys);

			$firstStepNumber = key($stepsNumberAndLocaleKeys);

			$stepNumber = $firstStepNumber;
}

		return $stepNumber;
	}

	/**
	 * Get the next step number based on specified step.
	 * @return int
	 */
	private function getNextStepNumber($stepNumber) {
		$nextStepNumber = $stepNumber + 1;

		HookRegistry::call('Submission::getNextStepNumber', array($this, $stepNumber, &$nextStepNumber));

		return $nextStepNumber;
	}
}

