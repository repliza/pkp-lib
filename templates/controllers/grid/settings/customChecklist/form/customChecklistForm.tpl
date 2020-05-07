{**
 * templates/controllers/grid/settings/customChecklist/form/customChecklists.tpl
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2003-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * customChecklists grid form
 *}

<script type="text/javascript">
	$(function() {ldelim}
		// Attach the form handler.
		$('#editCustomChecklistForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>

<form class="pkp_form" id="editCustomChecklistForm" method="post" action="{$formActionUrl|escape}">
{csrf}

{include file="controllers/notification/inPlaceNotification.tpl" notificationId="customChecklistFormNotification"}


{fbvFormArea id="checklist"}
	{fbvFormSection title="grid.customChecklist.column.checklistItem" required="true" for="content"}
		{fbvElement type="textarea" multilingual="true" name="content" id="content" value=$content required="true"}
	{/fbvFormSection}
{/fbvFormArea}
{if $gridId != null}
	<input type="hidden" name="gridId" value="{$gridId|escape}" />
{/if}
{if $rowId != null}
	<input type="hidden" name="rowId" value="{$rowId|escape}" />
{/if}
{if $customChecklistId != null}
	<input type="hidden" name="customChecklistId" value="{$customChecklistId|escape}" />
{/if}
{fbvFormButtons submitText="common.save"}
</form>
