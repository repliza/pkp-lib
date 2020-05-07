{**
 * templates/manager/customForms/previewCustomForm.tpl
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2003-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Preview of a custom form.
 *
 *}
<h3>{$title|escape}</h3>
<p>{$description}</p>

<script type="text/javascript">
	$(function() {ldelim}
		// Attach the form handler.
		$('#previewCustomForm').pkpHandler(
			'$.pkp.controllers.form.AjaxFormHandler',
			{ldelim}
				trackFormChanges: false
			{rdelim}
		);
	{rdelim});
</script>

<form class="pkp_form" id="previewCustomForm" method="post" action="#">
	{include file="manager/customForms/customFormResponse.tpl"}
</form>
