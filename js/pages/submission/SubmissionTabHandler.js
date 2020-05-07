/**
 * @defgroup js_controllers_tab_catalogEntry
 */
/**
 * @file js/pages/submission/SubmissionTabHandler.js
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2000-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class SubmissionTabHandler
 * @ingroup js_pages_submission
 *
 * @brief A subclass of TabHandler for handling the submission tabs.
 */
(function($) {

	/** @type {Object} */
	$.pkp.pages.submission =
			$.pkp.pages.submission || {};



	/**
	 * @constructor
	 *
	 * @extends $.pkp.controllers.TabHandler
	 *
	 * @param {jQueryObject} $tabs A wrapped HTML element that
	 *  represents the tabbed interface.
	 * @param {Object} options Handler options.
	 */
	$.pkp.pages.submission.SubmissionTabHandler =
			function($tabs, options) {

		this.parent($tabs, options);

		this.submissionSteps_ = options.submissionSteps;
		this.submissionProgress_ = options.submissionProgress;
		this.cancelUrl_ = options.cancelUrl;
		this.cancelConfirmText_ = options.cancelConfirmText;

		// Attach handlers.
		this.bind('setStep', this.setStepHandler);
		this.bind('formCanceled', this.formCanceledHandler);

		this.getHtmlElement().tabs('option', 'disabled',
				this.getDisabledSteps(this.submissionSteps_, this.submissionProgress_));
	};
	$.pkp.classes.Helper.inherits(
			$.pkp.pages.submission.SubmissionTabHandler,
			$.pkp.controllers.TabHandler);


	//
	// Private Properties
	//
	/**
	 * The submission steps
	 * @private
	 * @type {Array}
	 */
	$.pkp.pages.submission.SubmissionTabHandler.
			prototype.submissionSteps_ = null;


	/**
	 * The submission's progress
	 * @private
	 * @type {number?}
	 */
	$.pkp.pages.submission.SubmissionTabHandler.
			prototype.submissionProgress_ = null;


	/**
	 * The cancel URL
	 * @private
	 * @type {string?}
	 */
	$.pkp.pages.submission.SubmissionTabHandler.
			prototype.cancelUrl_ = null;


	/**
	 * The cancel confirmation text
	 * @private
	 * @type {string?}
	 */
	$.pkp.pages.submission.SubmissionTabHandler.
			prototype.cancelConfirmText_ = null;


	//
	// Public methods
	//
	/**
	 * This listens for events from the contained form. It moves to the
	 * next tab.
	 *
	 * @param {HTMLElement} sourceElement The parent DIV element
	 *  which contains the tabs.
	 * @param {Event} event The triggered event (gridRefreshRequested).
	 * @param {number} submissionProgress The new submission progress.
	 */
	$.pkp.pages.submission.SubmissionTabHandler.prototype.
			setStepHandler = function(sourceElement, event, submissionProgress) {

		this.getHtmlElement().tabs('option', 'disabled',
				this.getDisabledSteps(this.submissionSteps_, submissionProgress));

		var submissionProgressInt = parseInt(submissionProgress, 10),
				nextStepTabIndex = this.submissionSteps_.indexOf(submissionProgressInt);
		this.getHtmlElement().tabs('option', 'active', nextStepTabIndex);
	};


	/**
	 * Handle form cancellation events.
	 * @param {HTMLElement} sourceElement The parent DIV element
	 *  which contains the tabs.
	 * @param {Event} event The triggered event (gridRefreshRequested).
	 * @param {number} submissionProgress The new submission progress.
	 */
	$.pkp.pages.submission.SubmissionTabHandler.prototype.
			formCanceledHandler = function(sourceElement, event, submissionProgress) {

		if (confirm(this.cancelConfirmText_)) {
			window.location = this.cancelUrl_;
		}
	};


	/**
	 * Get a list of permitted tab indexes for the given submission step
	 * number.
	 * @param {Array} submissionSteps The submission steps
	 * @param {number} submissionProgress The submission step number (1-based) or
	 * 0 for completion.
	 * @return {Object} An array of permissible tab indexes (0-based).
	 */
	$.pkp.pages.submission.SubmissionTabHandler.prototype.
			getDisabledSteps = function(submissionSteps, submissionProgress) {

		var submissionProgressInt = parseInt(submissionProgress, 10),
				lastStepNumber = null,
				currentTabIndex = -1,
				i = -1,
				result = [];

		if (submissionProgressInt == 0) {
			return []; // Completed
		}

		if (submissionSteps.length > 0) {
			lastStepNumber = submissionSteps[submissionSteps.length - 1];
		}

		if (submissionProgressInt == lastStepNumber) {
			return [];
		}

		currentTabIndex = submissionSteps.indexOf(submissionProgressInt);
		if (currentTabIndex != -1) {
			for (i = currentTabIndex + 1; i < submissionSteps.length; ++i) {
				result.push(i);
			}

			return result;
		} else {
			throw new Error('Illegal submission step number!');
		}
	};


}(jQuery));
