<?php

/**
 * @file classes/customForm/CustomFormResponseDAO.inc.php
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2000-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class CustomFormResponseDAO
 * @ingroup customForm
 * @see CustomFormResponse
 *
 * @brief Operations for retrieving and modifying CustomFormResponse objects.
 *
 */

import ('lib.pkp.classes.customForm.CustomFormResponse');
import('lib.pkp.classes.context.customForms.CustomFormDAOContextTrait');

class CustomFormResponseDAO extends DAO {
	use CustomFormDAOContextTrait;

	/**
	 * Retrieve a custom form response.
	 * @param $assocType int Assoc ID (per $assocType)
	 * @param $assocId int ASSOC_TYPE_...
	 * @param $customFormElementId int
	 * @return CustomFormResponse
	 */
	function &getCustomFormResponse($assocType, $assocId, $customFormElementId) {
		$customFormDAOContext = $this->getCustomFormDAOContext();

		$sql = 'SELECT * FROM ' . $customFormDAOContext->getFormResponsesTableName() . ' WHERE assoc_type = ? AND assoc_id = ? AND ' . $customFormDAOContext->getFormElementIdTableColumnName() . ' = ?';
		$params = array((int) $assocType, (int) $assocId, $customFormElementId);
		$result = $this->retrieve($sql, $params);

		$returner = null;
		if ($result->RecordCount() != 0) {
			$returner =& $this->_returnCustomFormResponseFromRow($result->GetRowAssoc(false));
		}

		$result->Close();
		return $returner;
	}

	/**
	 * Construct a new data object corresponding to this DAO.
	 * @return CustomFormResponse
	 */
	function newDataObject() {
		return new CustomFormResponse();
	}

	/**
	 * Internal function to return a CustomFormResponse object from a row.
	 * @param $row array
	 * @return CustomFormResponse
	 */
	function &_returnCustomFormResponseFromRow($row) {
		$responseValue = $this->convertFromDB($row['response_value'], $row['response_type']);
		$customFormResponse = $this->newDataObject();

		$customFormDAOContext = $this->getCustomFormDAOContext();

		$customFormResponse->setAssocType($row['assoc_type']);
		$customFormResponse->setAssocId($row['assoc_id']);
		$customFormResponse->setCustomFormElementId($row[$customFormDAOContext->getFormElementIdTableColumnName()]);
		$customFormResponse->setValue($responseValue);
		$customFormResponse->setResponseType($row['response_type']);

		$className = get_class($this);
		$classNamePrefix = substr($className, 0, strpos($className, 'FormResponseDAO'));

		HookRegistry::call($className . '::_return' . $classNamePrefix . 'FormResponseFromRow', array(&$customFormResponse, &$row));

		return $customFormResponse;
	}

	/**
	 * Insert a new custom form response.
	 * @param $customFormResponse CustomFormResponse
	 */
	function insertObject($customFormResponse) {
		$responseType = $customFormResponse->getResponseType();
		$responseValue = $this->convertToDB($customFormResponse->getValue(), $responseType);

		$customFormDAOContext = $this->getCustomFormDAOContext();

		$this->update(
			'INSERT INTO ' . $customFormDAOContext->getFormResponsesTableName() . '
				(' . $customFormDAOContext->getFormElementIdTableColumnName() . ', assoc_type, assoc_id, response_type, response_value)
				VALUES
				(?, ?, ?, ?, ?)',
			array(
				$customFormResponse->getCustomFormElementId(),
				(int) $customFormResponse->getAssocType(),
				(int) $customFormResponse->getAssocId(),
				$customFormResponse->getResponseType(),
				$responseValue
			)
		);
	}

	/**
	 * Update an existing custom form response.
	 * @param $customFormResponse CustomFormResponse
	 */
	function updateObject($customFormResponse) {
		$responseType = $customFormResponse->getResponseType();
		$responseValue = $this->convertToDB($customFormResponse->getValue(), $responseType);

		$customFormDAOContext = $this->getCustomFormDAOContext();

		$returner = $this->update(
			'UPDATE ' . $customFormDAOContext->getFormResponsesTableName() . '
				SET
					response_type = ?,
					response_value = ?
				WHERE ' . $customFormDAOContext->getFormElementIdTableColumnName() . ' = ? AND assoc_type = ? AND assoc_id = ?',
			array(
				$customFormResponse->getResponseType(),
				$responseValue,
				$customFormResponse->getCustomFormElementId(),
				(int) $customFormResponse->getAssocType(),
				(int) $customFormResponse->getAssocId()
			)
		);

		return $returner;
	}

	/**
	 * Delete a custom form response.
	 * @param $customFormResponse customFormResponse
	 */
	function deleteObject(&$customFormResponse) {
		return $this->deleteByAssocAndCustomFormElement($customFormResponse->getAssocType(), $customFormResponse->getAssocId(), $customFormResponse->getCustomFormElementId());
	}

	/**
	 * Delete a custom form response by association and custom form element ID.
	 * @param $assocType int Assoc ID (per $assocType)
	 * @param $assocId int ASSOC_TYPE_...
	 * @param $customFormElementId int
	 */
	function deleteByAssocAndCustomFormElement($assocType, $assocId, $customFormElementId) {
		$customFormDAOContext = $this->getCustomFormDAOContext();

		return $this->update(
			'DELETE FROM ' . $customFormDAOContext->getFormResponsesTableName() . ' WHERE assoc_type = ? AND assoc_id = ? AND ' . $customFormDAOContext->getFormElementIdTableColumnName() . ' = ?',
			array((int) $assocType,	(int) $assocId, $customFormElementId)
		);
	}

	/**
	 * Delete a custom form response by association.
	 * @param $assocType int Assoc ID (per $assocType)
	 * @param $assocId int ASSOC_TYPE_...
	 */
	function deleteByAssoc($assocType, $assocId) {
		$customFormDAOContext = $this->getCustomFormDAOContext();

		return $this->update(
			'DELETE FROM ' . $customFormDAOContext->getFormResponsesTableName() . ' WHERE assoc_type = ? AND assoc_id = ?',
			array((int) $assocType,	(int) $assocId)
		);
	}

	/**
	 * Delete responses by custom form element ID
	 * @param $customFormElementId int
	 */
	function deleteByCustomFormElementId($customFormElementId) {
		$customFormDAOContext = $this->getCustomFormDAOContext();

		return $this->update(
			'DELETE FROM ' . $customFormDAOContext->getFormResponsesTableName() . ' WHERE ' . $customFormDAOContext->getFormElementIdTableColumnName() . ' = ?',
			$customFormElementId
		);
	}

	/**
	 * Retrieve all custom form responses for a custom form in an associative array.
	 * @param $assocType int Assoc ID (per $assocType)
	 * @param $assocId int ASSOC_TYPE_...
	 * @return array custom_form_element_id => array(custom form response for this element)
	 */
	function &getCustomFormResponseValues($assocType, $assocId) {
		$returner = array();

		$customFormDAOContext = $this->getCustomFormDAOContext();

		$result = $this->retrieveRange(
			'SELECT * FROM ' . $customFormDAOContext->getFormResponsesTableName() . ' WHERE assoc_type = ? AND assoc_id = ?',
			array((int) $assocType,	(int) $assocId)
		);

		while (!$result->EOF) {
			$row = $result->GetRowAssoc(false);
			$customFormResponse =& $this->_returnCustomFormResponseFromRow($row);
			$returner[$customFormResponse->getCustomFormElementId()] = $customFormResponse->getValue();
			$result->MoveNext();
		}

		$result->Close();
		return $returner;
	}

	/**
	 * Check if a custom form response for the custom.
	 * @param $assocType int Assoc ID (per $assocType)
	 * @param $assocId int ASSOC_TYPE_...
	 * @param $customFormElementId int optional
	 * @return boolean
	 */
	function customFormResponseExists($assocType, $assocId, $customFormElementId = null) {
		$customFormDAOContext = $this->getCustomFormDAOContext();

		$sql = 'SELECT COUNT(*) FROM ' . $customFormDAOContext->getFormResponsesTableName() . ' WHERE assoc_type = ? AND assoc_id = ?';
		$params = array((int) $assocType,	(int) $assocId);
		if ($customFormElementId !== null) {
			$sql .= ' AND ' . $customFormDAOContext->getFormElementIdTableColumnName() . ' = ?';
			$params[] = $customFormElementId;
		}
		$result = $this->retrieve($sql, $params);

		$returner = isset($result->fields[0]) && $result->fields[0] > 0 ? true : false;

		$result->Close();
		return $returner;
	}
}

?>
