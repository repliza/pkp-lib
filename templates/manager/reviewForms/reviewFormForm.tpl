{**
 * templates/manager/reviewForms/reviewFormForm.tpl
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Form to create/modify a review form.
 *
 *}

<script>
	$(function() {ldelim}
		// Attach the form handler.
		$('#reviewFormForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>

<form class="pkp_form" id="reviewFormForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT component="grid.settings.reviewForms.ReviewFormGridHandler" op="updateCustomForm"}">
	{csrf}

	{if $customFormId}
		{fbvElement id="customFormId" type="hidden" name="customFormId" value=$customFormId}
	{/if}

	{include file="controllers/notification/inPlaceNotification.tpl" notificationId="reviewFormsNotification"}

	{fbvFormArea id="reviewFormForm"}
		{fbvFormSection title="manager.customForms.title" required=true for="title"}
			{fbvElement type="text" id="title" value=$title multilingual=true required=true}
		{/fbvFormSection}
		{fbvFormSection title="manager.customForms.description" for="description"}
			{fbvElement type="textarea" id="description" value=$description multilingual=true rich=true}
		{/fbvFormSection}
		<p><span class="formRequired">{translate key="common.requiredField"}</span></p>
		{fbvFormButtons id="reviewFormFormSubmit" submitText="common.save"}
	{/fbvFormArea}
</form>
