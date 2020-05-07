{**
 * templates/manager/customForms/customFormForm.tpl
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2003-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Form to create/modify a custom form.
 *
 *}

<script>
	$(function() {ldelim}
		// Attach the form handler.
		$('#customFormForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>

<form class="pkp_form" id="customFormForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT component="grid.settings.customForms.CustomFormGridHandler" op="updateCustomForm" assocType=$assocType assocId=$assocId}">
	{csrf}

	{if $customFormId}
		{fbvElement id="customFormId" type="hidden" name="customFormId" value=$customFormId}
	{/if}

	{include file="controllers/notification/inPlaceNotification.tpl" notificationId="customFormsNotification"}

	{fbvFormArea id="customFormForm"}
		{fbvFormSection title="manager.customForms.title" required=true for="title"}
			{fbvElement type="text" id="title" value=$title multilingual=true required=true}
		{/fbvFormSection}
		{fbvFormSection title="manager.customForms.description" for="description"}
			{fbvElement type="textarea" id="description" value=$description multilingual=true rich=true}
		{/fbvFormSection}
		<p><span class="formRequired">{translate key="common.requiredField"}</span></p>
		{fbvFormButtons id="customFormFormSubmit" submitText="common.save"}
	{/fbvFormArea}
</form>
