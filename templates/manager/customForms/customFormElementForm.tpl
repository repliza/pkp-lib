{**
 * templates/manager/customForms/customFormElementForm.tpl
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2003-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Form to create/modify a custom form element.
 *
 *}
<script>
	$(function() {ldelim}
		// Attach the form handler.
		$('#customFormElementForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>

<script type="text/javascript">
<!--
function togglePossibleResponses(newValue, multipleResponsesElementTypesString) {ldelim}
	if (multipleResponsesElementTypesString.indexOf(';'+newValue+';') != -1) {ldelim}
		document.getElementById('customFormElementForm').addResponse.disabled=false;
	{rdelim} else {ldelim}
		if (document.getElementById('customFormElementForm').addResponse.disabled == false) {ldelim}
			alert({translate|json_encode key="manager.customFormElement.changeType"});
		{rdelim}
		document.getElementById('customFormElementForm').addResponse.disabled=true;
	{rdelim}
{rdelim}
// -->
</script>

<form class="pkp_form" id="customFormElementForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT component="grid.settings.customForms.CustomFormElementsGridHandler" op="updateCustomFormElement" assocType=$assocType assocId=$assocId anchor="possibleResponses"}">
	{csrf}
	{fbvElement id="customFormId" type="hidden" name="customFormId" value=$customFormId}
	{fbvElement id="customFormElementId" type="hidden" name="customFormElementId" value=$customFormElementId}

	{include file="controllers/notification/inPlaceNotification.tpl" notificationId="customFormElementsNotification"}

	{fbvFormArea id="customFormElementForm"}

		<!-- question -->
		{fbvFormSection title="manager.customFormElements.question" required=true for="question"}
			{fbvElement type="textarea" id="question" value=$question multilingual=true rich=true}
		{/fbvFormSection}

		<!-- description -->
		{fbvFormSection title="manager.customFormElements.description" for="description"}
			{fbvElement type="textarea" id="description" value=$description multilingual=true rich=true}
		{/fbvFormSection}

		<!-- required checkbox -->
		{fbvFormSection for="required" list=true}
			{if $required}
				{assign var="checked" value=true}
			{else}
				{assign var="checked" value=false}
			{/if}
			{fbvElement type="checkbox" id="required" label="$requiredCheckboxLabel" checked=$checked inline="true"}
		{/fbvFormSection}

		<!-- element type drop-down -->
		{fbvFormSection for="elementType" list=true}
			<!-- when user makes a selection (onchange), warn them if necessary. -->
			<!-- also display/hide options list builder if appropriate. -->
			<!-- look to see how this is done elsewhere under the new JS framework -->
			{fbvElement type="select" label="manager.customFormElements.elementType" id="elementType" defaultLabel="" from=$customFormElementTypeOptions selected=$elementType size=$fbvStyles.size.MEDIUM required=true}
		{/fbvFormSection}

		<!-- Options listbuilder. Activated for some element types. -->
		<div id="elementOptions" class="full left">
			<div id="elementOptionsContainer" class="full left">
				{capture assign="elementOptionsUrl"}{url router=$smarty.const.ROUTE_COMPONENT component="$customFormElementResponseItemListbuilderHandler" op="fetch" customFormId=$customFormId customFormElementId=$customFormElementId assocType=$assocType assocId=$assocId escape=false}{/capture}
				{load_url_in_div id="elementOptionsListbuilderContainer" url=$elementOptionsUrl}
			</div>
		</div>
		<!-- required field text -->
		<p><span class="formRequired">{translate key="common.requiredField"}</span></p>

		<!-- submit button -->
		{fbvFormButtons id="customFormElementFormSubmit" submitText="common.save"}
	{/fbvFormArea}
</form>
