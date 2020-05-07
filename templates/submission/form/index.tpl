{**
 * templates/submission/form/index.tpl
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Main template for the author's submission pages.
 *}
{strip}
{assign var=pageTitle value="submission.submit.title"}
{include file="common/header.tpl"}

{assign var="selectedStepIndex" value=0}

{assign var="i" value=0}
{foreach from=$steps key=step item=stepLocaleKey}
	{if $step eq $submissionProgress}
		{assign var="selectedStepIndex" value=$i}
	{/if}
	{assign var="i" value=$i+1}
{/foreach}
{/strip}

<script type="text/javascript">
	// Attach the JS file tab handler.
	$(function() {ldelim}
		$('#submitTabs').pkpHandler(
			'$.pkp.pages.submission.SubmissionTabHandler',
			{ldelim}
				submissionSteps: [{foreach from=$steps key=step item=stepLocaleKey}{$step}, {/foreach}],
				submissionProgress: {$submissionProgress},
				selected: {$selectedStepIndex},
				cancelUrl: {url|json_encode page="submissions" escape=false},
				cancelConfirmText: {translate|json_encode key="submission.submit.cancelSubmission"}
			{rdelim}
		);
	{rdelim});
</script>

<div id="submitTabs" class="pkp_controllers_tab">
	<ul>
		{assign var="i" value=1}
		{foreach from=$steps key=step item=stepLocaleKey}
			<li><a name="step-{$step|escape}" href="{url op="step" path=$step submissionId=$submissionId sectionId=$sectionId}">{$i++}. {translate key=$stepLocaleKey}</a></li>
		{/foreach}
	</ul>
</div>

{include file="common/footer.tpl"}
