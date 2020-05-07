{* note: template is based on reviewFormResponse.tpl *}

{iterate from=customFormElements item=customFormElement}
	{assign var=elementId value=$customFormElement->getId()}
	{assign var=value value=$customFormResponses.$elementId}

	{if in_array($customFormElement->getElementType(), array(CUSTOM_FORM_ELEMENT_TYPE_CHECKBOXES, CUSTOM_FORM_ELEMENT_TYPE_RADIO_BUTTONS))}
		{assign var=list value=true}
	{else}
		{assign var=list value=false}
	{/if}

	{fbvFormSection translate=false title=$customFormElement->getLocalizedQuestion() list=$list required=$customFormElement->getRequired()}
		{assign var=description value=$customFormElement->getLocalizedDescription()}
		{if $description}<div class="description">{$description}</div>{/if}
		{if $customFormElement->getElementType() == CUSTOM_FORM_ELEMENT_TYPE_SMALL_TEXT_FIELD}
			{fbvElement name="`$formFieldNameStem`[$elementId]" type="text" translate=false required=$customFormElement->getRequired() id="`$formFieldNameStem`-$elementId" value=$value inline=true size=$fbvStyles.size.SMALL readonly=$disabled}
		{elseif $customFormElement->getElementType() == CUSTOM_FORM_ELEMENT_TYPE_TEXT_FIELD}
			{fbvElement name="`$formFieldNameStem`[$elementId]" type="text" translate=false required=$customFormElement->getRequired() id="`$formFieldNameStem`-$elementId" value=$value readonly=$disabled}
		{elseif $customFormElement->getElementType() == CUSTOM_FORM_ELEMENT_TYPE_TEXTAREA}
			{fbvElement name="`$formFieldNameStem`[$elementId]" type="textarea" required=$customFormElement->getRequired() id="`$formFieldNameStem`-$elementId" value=$value readonly=$disabled rows=4 cols=40}
		{elseif $customFormElement->getElementType() == CUSTOM_FORM_ELEMENT_TYPE_CHECKBOXES}
			{assign var=possibleResponses value=$customFormElement->getLocalizedPossibleResponses()}
			{foreach name=responses from=$possibleResponses key=responseId item=responseItem}
				{assign var=index value=$smarty.foreach.responses.index}
				{if !empty($customFormResponses[$elementId]) && in_array($index, $customFormResponses[$elementId])}
					{assign var=checked value=true}
				{else}
					{assign var=checked value=false}
				{/if}

				{fbvElement type="checkbox" disabled=$disabled name="`$formFieldNameStem`[$elementId][]" id="`$formFieldNameStem`-$elementId-$index" value=$index checked=$checked label=$responseItem translate=false}
			{/foreach}
		{elseif $customFormElement->getElementType() == CUSTOM_FORM_ELEMENT_TYPE_RADIO_BUTTONS}
			{assign var=possibleResponses value=$customFormElement->getLocalizedPossibleResponses()}
			{foreach name=responses from=$possibleResponses key=responseId item=responseItem}
				{assign var=index value=$smarty.foreach.responses.index}
				{if isset($customFormResponses[$elementId]) && $index == $customFormResponses[$elementId]}
					{assign var=checked value=true}
				{else}
					{assign var=checked value=false}
				{/if}
				{fbvElement type="radio" disabled=$disabled name="`$formFieldNameStem`[$elementId]" id="`$formFieldNameStem`-$elementId-$index" value=$index checked=$checked label=$responseItem translate=false}
			{/foreach}
		{elseif $customFormElement->getElementType() == CUSTOM_FORM_ELEMENT_TYPE_DROP_DOWN_BOX}
			{assign var=possibleResponses value=$customFormElement->getLocalizedPossibleResponses()}
			{fbvElement type="select" subLabelTranslate=false translate=false name="`$formFieldNameStem`[$elementId]" id="`$formFieldNameStem`-$elementId" required=$customFormElement->getRequired() disabled=$disabled defaultLabel="" defaultValue="" from=$possibleResponses selected=$customFormResponses.$elementId size=$fbvStyles.size.MEDIUM}
		{/if}
	{/fbvFormSection}
{/iterate}
