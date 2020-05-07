{**
 * templates/controllers/grid/settings/reviewForms/editReviewForm.tpl
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * The edit/preview a review form tabset.
 *}
<script type="text/javascript">
	// Attach the JS file tab handler.
	$(function() {ldelim}
		$('#editReviewFormTabs').pkpHandler(
				'$.pkp.controllers.TabHandler',
				{ldelim}
					{if !$canEdit}disabled: [0, 1],{/if}
					selected: {if $preview}2{else}0{/if}
				{rdelim}
		);
	{rdelim});
</script>
<div id="editReviewFormTabs" class="pkp_controllers_tab">
	<ul>
		<li><a href="{url router=$smarty.const.ROUTE_COMPONENT op="customFormBasics" customFormId=$customFormId}">{translate key="manager.customForms.edit"}</a></li>
		<li><a href="{url router=$smarty.const.ROUTE_COMPONENT op="customFormElements" customFormId=$customFormId}">{translate key="manager.customFormElements"}</a></li>
		<li><a href="{url router=$smarty.const.ROUTE_COMPONENT op="customFormPreview" customFormId=$customFormId}">{translate key="manager.customForms.preview"}</a></li>
	</ul>
</div>
