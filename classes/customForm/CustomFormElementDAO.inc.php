<?php

/**
 * @file classes/customForm/CustomFormElementDAO.inc.php
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2000-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class CustomFormElementDAO
 * @ingroup customForm
 * @see CustomFormElement
 *
 * @brief Operations for retrieving and modifying CustomFormElement objects.
 *
 */

import ('lib.pkp.classes.customForm.CustomFormElement');
import('lib.pkp.classes.context.customForms.CustomFormDAOContextTrait');

class CustomFormElementDAO extends DAO {
	use CustomFormDAOContextTrait;

	/**
	 * Returns the map with key value pairs of {db column name} => {db value}
	 * @param $customFormElement customFormElement
	 * @return array
	 */
	protected function _getDbValueMap($customFormElement) {
		$customFormDAOContext = $this->getCustomFormDAOContext();

		return array(
			$customFormDAOContext->getFormIdTableColumnName() => (int) $customFormElement->getCustomFormId(),
			'seq' => $customFormElement->getSequence() == null ? 0 : (float) $customFormElement->getSequence(),
			'element_type' => (int) $customFormElement->getElementType(),
			'required' => (int) $customFormElement->getRequired()
		);
	}

	/**
	 * Retrieve a custom form element by ID.
	 * @param $customFormElementId int custom form element ID
	 * @param $customFormId int optional
	 * @return CustomFormElement
	 */
	function getById($customFormElementId, $customFormId = null) {
		$params = array((int) $customFormElementId);
		if ($customFormId) {
			$params[] = (int) $customFormId;
		}

		$customFormDAOContext = $this->getCustomFormDAOContext();

		$result = $this->retrieve(
			'SELECT	*
			FROM	' . $customFormDAOContext->getFormElementsTableName() . '
			WHERE	' . $customFormDAOContext->getFormElementIdTableColumnName() . ' = ?
			' . ($customFormId?' AND ' . $customFormDAOContext->getFormIdTableColumnName() . ' = ?':''),
			$params
		);

		$returner = null;
		if ($result->RecordCount() != 0) {
			$returner = $this->_fromRow($result->GetRowAssoc(false));
		}

		$result->Close();
		return $returner;
	}

	/**
	 * Construct a new data object corresponding to this DAO.
	 * @return CustomFormElement
	 */
	function newDataObject() {
		return new CustomFormElement();
	}

	/**
	 * Internal function to return a CustomFormElement object from a row.
	 * @param $row array
	 * @return CustomFormElement
	 */
	function _fromRow($row) {
		$customFormDAOContext = $this->getCustomFormDAOContext();

		$formElementIdTableColumnName = $customFormDAOContext->getFormElementIdTableColumnName();

		$customFormElement = $this->newDataObject();
		$customFormElement->setId($row[$formElementIdTableColumnName]);
		$customFormElement->setCustomFormId($row[$customFormDAOContext->getFormIdTableColumnName()]);
		$customFormElement->setSequence($row['seq']);
		$customFormElement->setElementType($row['element_type']);
		$customFormElement->setRequired($row['required']);

		$this->getDataObjectSettings($customFormDAOContext->getFormElementSettingsTableName(), $formElementIdTableColumnName, $row[$formElementIdTableColumnName], $customFormElement);

		HookRegistry::call(get_class($this) . '::_fromRow', array(&$customFormElement, &$row));

		return $customFormElement;
	}

	/**
	 * Get the list of fields for which data can be localized.
	 * @return array
	 */
	function getLocaleFieldNames() {
		return array('question', 'description', 'possibleResponses');
	}

	/**
	 * Update the localized fields for this table
	 * @param $customFormElement object
	 */
	function updateLocaleFields($customFormElement) {
		$customFormDAOContext = $this->getCustomFormDAOContext();

		$this->updateDataObjectSettings($customFormDAOContext->getFormElementSettingsTableName(), $customFormElement, array(
			$customFormDAOContext->getFormElementIdTableColumnName() => (int) $customFormElement->getId()
		));
	}

	/**
	 * Insert a new custom form element.
	 * @param $customFormElement customFormElement
	 * @return int Custom form element ID
	 */
	function insertObject($customFormElement) {
		$map = $this->_getDbValueMap($customFormElement);
		$columnNames = array_keys($map);
		$values = array_values($map);
		$placeholders = array_fill(0, count($columnNames), '?');

		$customFormDAOContext = $this->getCustomFormDAOContext();

		$this->update(
			'INSERT INTO ' . $customFormDAOContext->getFormElementsTableName() . '
				(' . implode(', ', $columnNames) . ')
			VALUES
				(' . implode(', ', $placeholders) . ')',
			$values
		);

		$customFormElement->setId($this->getInsertId());
		$this->updateLocaleFields($customFormElement);
		return $customFormElement->getId();
	}

	/**
	 * Update an existing custom form element.
	 * @param $customFormElement CustomFormElement
	 */
	function updateObject($customFormElement) {
		$map = $this->_getDbValueMap($customFormElement);

		$columnNames = array_keys($map);

		$values = array_values($map);
		$values[] = (int) $customFormElement->getId();

		$customFormDAOContext = $this->getCustomFormDAOContext();

		$returner = $this->update(
			'UPDATE ' . $customFormDAOContext->getFormElementsTableName() . '
				SET	' . implode(' = ?, ', $columnNames) . ' = ?
				WHERE	' . $customFormDAOContext->getFormElementIdTableColumnName() . ' = ?',
			$values
		);
		$this->updateLocaleFields($customFormElement);
		return $returner;
	}

	/**
	 * Delete a custom form element.
	 * @param $customFormElement customFormElement
	 */
	function deleteObject($customFormElement) {
		return $this->deleteById($customFormElement->getId());
	}

	/**
	 * Delete a custom form element by ID.
	 * @param $customFormElementId int
	 */
	function deleteById($customFormElementId) {
		$customFormDAOContext = $this->getCustomFormDAOContext();

		$customFormResponseDao = DAORegistry::getDAO($customFormDAOContext->getFormResponseDAOClassName());
		$customFormResponseDao->deleteByCustomFormElementId($customFormElementId);

		$this->update('DELETE FROM ' . $customFormDAOContext->getFormElementSettingsTableName() . ' WHERE ' . $customFormDAOContext->getFormElementIdTableColumnName() . ' = ?', (int) $customFormElementId);
		return $this->update('DELETE FROM ' . $customFormDAOContext->getFormElementsTableName() . ' WHERE ' . $customFormDAOContext->getFormElementIdTableColumnName() . ' = ?', (int) $customFormElementId);
	}

	/**
	 * Delete custom form elements by custom form ID
	 * to be called only when deleting a custom form.
	 * @param $customFormId int
	 */
	function deleteByCustomFormId($customFormId) {
		$customFormElements = $this->getByCustomFormId($customFormId);
		while ($customFormElement = $customFormElements->next()) {
			$this->deleteById($customFormElement->getId());
		}
	}

	/**
	 * Delete a custom form element setting
	 * @param $customFormElementId int
	 * @param $settingName string
	 * @param $locale string
	 */
	function deleteSetting($customFormElementId, $name, $locale = null) {
		$params = array((int) $customFormElementId, $name);
		if ($locale) $params[] = $locale;

		$customFormDAOContext = $this->getCustomFormDAOContext();

		$this->update(
			'DELETE FROM ' . $customFormDAOContext->getFormElementSettingsTableName() . '
			WHERE ' . $customFormDAOContext->getFormElementIdTableColumnName() . ' = ? AND setting_name = ?
			' . ($locale?' AND locale = ?':''),
			$params
		);
	}

	/**
	 * Retrieve all elements for a custom form.
	 * @param $customFormId int
	 * @param $rangeInfo object RangeInfo object (optional)
	 * @param $included boolean True for only included comments; false for non-included; null for both
	 * @return DAOResultFactory containing CustomFormElements ordered by sequence
	 */
	function getByCustomFormId($customFormId, $rangeInfo = null, $included = null) {
		$customFormDAOContext = $this->getCustomFormDAOContext();

		$result = $this->retrieveRange(
			'SELECT *
			FROM ' . $customFormDAOContext->getFormElementsTableName() . '
			WHERE ' . $customFormDAOContext->getFormIdTableColumnName() . ' = ?
			' . ($included === true?' AND included = 1':'') . '
			' . ($included === false?' AND included = 0':'') . '
			ORDER BY seq',
			(int) $customFormId, $rangeInfo
		);

		return new DAOResultFactory($result, $this, '_fromRow');
	}

	/**
	 * Retrieve ids of all required elements for a custom form.
	 * @param $customFormId int
	 * return array
	 */
	function getRequiredCustomFormElementIds($customFormId) {
		$customFormDAOContext = $this->getCustomFormDAOContext();

		$result = $this->retrieve(
			'SELECT ' . $customFormDAOContext->getFormElementIdTableColumnName() . ' FROM ' . $customFormDAOContext->getFormElementsTableName() . ' WHERE ' . $customFormDAOContext->getFormIdTableColumnName() . ' = ? AND required = 1 ORDER BY seq',
			$customFormId
		);

		$requiredCustomFormElementIds = array();
		while (!$result->EOF) {
			$requiredCustomFormElementIds[] = $result->fields[0];
			$result->MoveNext();
		}

		$result->Close();
		return $requiredCustomFormElementIds;
	}

	/**
	 * Check if a custom form element exists with the specified ID.
	 * @param $customFormElementId int
	 * @param $customFormId int optional
	 * @return boolean
	 */
	function customFormElementExists($customFormElementId, $customFormId = null) {
		$params = array((int) $customFormElementId);
		if ($customFormId) $params[] = (int) $customFormId;

		$customFormDAOContext = $this->getCustomFormDAOContext();

		$result = $this->retrieve(
			'SELECT	COUNT(*)
			FROM	' . $customFormDAOContext->getFormElementsTableName() . '
			WHERE	' . $customFormDAOContext->getFormElementIdTableColumnName() . ' = ?
				' . ($customFormId?' AND ' . $customFormDAOContext->getFormIdTableColumnName() . ' = ?':''),
			$params
		);

		$returner = isset($result->fields[0]) && $result->fields[0] == 1 ? true : false;

		$result->Close();
		return $returner;
	}

	/**
	 * Sequentially renumber a custom form elements in their sequence order.
	 * @param $customFormId int
	 */
	function resequenceCustomFormElements($customFormId) {
		$customFormDAOContext = $this->getCustomFormDAOContext();

		$result = $this->retrieve(
			'SELECT ' . $customFormDAOContext->getFormElementIdTableColumnName() . '  FROM ' . $customFormDAOContext->getFormElementsTableName() . ' WHERE ' . $customFormDAOContext->getFormIdTableColumnName() . ' = ? ORDER BY seq',
			(int) $customFormId
		);

		for ($i=1; !$result->EOF; $i++) {
			list($customFormElementId) = $result->fields;
			$this->update(
				'UPDATE ' . $customFormDAOContext->getFormElementsTableName() . ' SET seq = ? WHERE ' . $customFormDAOContext->getFormElementIdTableColumnName() . ' = ?',
				array(
					$i,
					$customFormElementId
				)
			);

			$result->MoveNext();
		}
		$result->Close();
	}

	/**
	 * Get the ID of the last inserted custom form element.
	 * @return int
	 */
	function getInsertId() {
		$customFormDAOContext = $this->getCustomFormDAOContext();

		return $this->_getInsertId($customFormDAOContext->getFormElementsTableName(), $customFormDAOContext->getFormElementIdTableColumnName());
	}
}

?>
