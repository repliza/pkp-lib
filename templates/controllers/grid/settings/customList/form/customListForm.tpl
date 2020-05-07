{**
 * templates/controllers/grid/settings/customList/form/customListForm.tpl
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2003-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * customLists grid form
 *}

<script type="text/javascript">
	$(function() {ldelim}
		// Attach the form handler.
		$('#editCustomListForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>

<form class="pkp_form" id="editCustomListForm" method="post" action="{$formActionUrl|escape}">
{csrf}

{include file="controllers/notification/inPlaceNotification.tpl" notificationId="customListFormNotification"}


{if $gridId != null}
	<input type="hidden" name="gridId" value="{$gridId|escape}" />
{/if}
{if $rowId != null}
	<input type="hidden" name="rowId" value="{$rowId|escape}" />
{/if}
{if $customListId != null}
	<input type="hidden" name="customListId" value="{$customListId|escape}" />
{/if}
{fbvFormButtons submitText="common.save"}
</form>
