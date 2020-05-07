<?php

/**
 * @file classes/customForm/CustomFormDAO.inc.php
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2000-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class CustomFormDAO
 * @ingroup customForm
 * @see CustomrForm
 *
 * @brief Operations for retrieving and modifying CustomForm objects.
 *
 */

import('lib.pkp.classes.customForm.CustomForm');
import('lib.pkp.classes.context.customForms.CustomFormDAOContextTrait');

class CustomFormDAO extends DAO {
	use CustomFormDAOContextTrait;

	/**
	 * Retrieve a custom form by ID.
	 * @param $customFormId int
	 * @param $assocType int optional
	 * @param $assocId int optional
	 * @return CustomForm
	 */
	function getById($customFormId, $assocType = null, $assocId = null) {
		$params = array((int) $customFormId);
		if ($assocType) {
			$params[] = (int) $assocType;
			$params[] = (int) $assocId;
		}

		$customFormDAOContext = $this->getCustomFormDAOContext();

		$result = $this->retrieve (
			'SELECT ' . $this->_getFormsQueryColumns('rf') . '
			FROM	' . $customFormDAOContext->getFormsTableName() . ' rf
			' . $this->_getFormsQueryJoins('rf') . '
			WHERE	rf.' . $customFormDAOContext->getFormIdTableColumnName() . ' = ? AND rf.assoc_type = ? AND rf.assoc_id = ?
			GROUP BY rf.' . $customFormDAOContext->getFormIdTableColumnName() . '',
			$params
		);

		$returner = null;
		if ($result->RecordCount() != 0) {
			$returner = $this->_fromRow($result->GetRowAssoc(false));
		}

		$result->Close();
		return $returner;
	}

	protected function _getFormsQueryColumns($formTableNameAlias) {
		return $formTableNameAlias . '.*';
	}

	protected function _getFormsQueryJoins($formTableNameAlias) {
		return '';
	}

	/**
	 * Construct a new data object corresponding to this DAO.
	 * @return CustomForm
	 */
	function newDataObject() {
		return new CustomForm();
	}

	/**
	 * Internal function to return a CustomForm object from a row.
	 * @param $row array
	 * @return CustomForm
	 */
	function _fromRow($row) {
		$customForm = $this->newDataObject();
		$this->initDataObjectFromRow($customForm, $row);

		HookRegistry::call(get_class($this) . '::_fromRow', array(&$customForm, &$row));

		return $customForm;
	}

	protected function initDataObjectFromRow($customForm, $row) {
		$customFormDAOContext = $this->getCustomFormDAOContext();

		$formIdTableColumnName = $customFormDAOContext->getFormIdTableColumnName();

		$customForm->setId($row[$formIdTableColumnName]);
		$customForm->setAssocType($row['assoc_type']);
		$customForm->setAssocId($row['assoc_id']);
		$customForm->setSequence($row['seq']);
		$customForm->setActive($row['is_active']);

		$this->getDataObjectSettings($customFormDAOContext->getFormSettingsTableName(), $formIdTableColumnName, $row[$formIdTableColumnName], $customForm);
	}

	/**
	 * Check if a custom form exists with the specified ID.
	 * @param $customFormId int
	 * @param $assocType int
	 * @param $assocId int
	 * @return boolean
	 */
	function customFormExists($customFormId, $assocType, $assocId) {
		$customFormDAOContext = $this->getCustomFormDAOContext();

		$result = $this->retrieve(
			'SELECT COUNT(*) FROM ' . $customFormDAOContext->getFormsTableName() . ' WHERE ' . $customFormDAOContext->getFormIdTableColumnName() . ' = ? AND assoc_type = ? AND assoc_id = ?',
			array((int) $customFormId, (int) $assocType, (int) $assocId)
		);
		$returner = isset($result->fields[0]) && $result->fields[0] == 1 ? true : false;

		$result->Close();
		return $returner;
	}

	/**
	 * Get the list of fields for which data can be localized.
	 * @return array
	 */
	function getLocaleFieldNames() {
		return array('title', 'description');
	}

	/**
	 * Update the localized fields for this table
	 * @param $customForm object
	 */
	function updateLocaleFields(&$customForm) {
		$customFormDAOContext = $this->getCustomFormDAOContext();

		$this->updateDataObjectSettings($customFormDAOContext->getFormSettingsTableName(), $customForm, array(
			$customFormDAOContext->getFormIdTableColumnName() => $customForm->getId()
		));
	}

	/**
	 * Insert a new custom form.
	 * @param $customForm CustomForm
	 */
	function insertObject($customForm) {
		$customFormDAOContext = $this->getCustomFormDAOContext();

		$this->update(
			'INSERT INTO ' . $customFormDAOContext->getFormsTableName() . '
				(assoc_type, assoc_id, seq, is_active)
				VALUES
				(?, ?, ?, ?)',
			array(
				(int) $customForm->getAssocType(),
				(int) $customForm->getAssocId(),
				$customForm->getSequence() == null ? 0 : (float) $customForm->getSequence(),
				$customForm->getActive()?1:0
			)
		);

		$customForm->setId($this->getInsertId());
		$this->updateLocaleFields($customForm);

		return $customForm->getId();
	}

	/**
	 * Update an existing custom form.
	 * @param $customForm CustomvForm
	 */
	function updateObject($customForm) {
		$customFormDAOContext = $this->getCustomFormDAOContext();

		$returner = $this->update(
			'UPDATE ' . $customFormDAOContext->getFormsTableName() . '
				SET
					assoc_type = ?,
					assoc_id = ?,
					seq = ?,
					is_active = ?
				WHERE ' . $customFormDAOContext->getFormIdTableColumnName() . ' = ?',
			array(
				(int) $customForm->getAssocType(),
				(int) $customForm->getAssocId(),
				(float) $customForm->getSequence(),
				$customForm->getActive()?1:0,
				(int) $customForm->getId()
			)
		);

		$this->updateLocaleFields($customForm);

		return $returner;
	}

	/**
	 * Delete a custom form.
	 * @param $customForm CustomForm
	 */
	function deleteObject($customForm) {
		return $this->deleteById($customForm->getId());
	}

	/**
	 * Delete a custom form by Id.
	 * @param $customFormId int
	 */
	function deleteById($customFormId) {
		$customFormDAOContext = $this->getCustomFormDAOContext();

		$customFormElementDao = $customFormDAOContext->getCustomFormElementDAO();
		$customFormElementDao->deleteByCustomFormId($customFormId);

		$this->update('DELETE FROM ' . $customFormDAOContext->getFormSettingsTableName() . ' WHERE ' . $customFormDAOContext->getFormIdTableColumnName() . ' = ?', (int) $customFormId);
		$this->update('DELETE FROM ' . $customFormDAOContext->getFormsTableName() . ' WHERE ' . $customFormDAOContext->getFormIdTableColumnName() . ' = ?', (int) $customFormId);
	}

	/**
	 * Delete all custom forms by assoc Id.
	 * @param $assocType int
	 * @param $assocId int
	 */
	function deleteByAssoc($assocType, $assocId) {
		$customForms = $this->getByAssocId($assocType, $assocId);

		while ($customForm = $customForms->next()) {
			$this->deleteById($customForm->getId());
		}
	}

	/**
	 * Get all custom forms by assoc id.
	 * @param $assocType int
	 * @param $assocId int
	 * @param $rangeInfo RangeInfo (optional)
	 * @return DAOResultFactory containing matching customForms
	 */
	function getByAssocId($assocType, $assocId, $rangeInfo = null) {
		$customFormDAOContext = $this->getCustomFormDAOContext();

		$result = $this->retrieveRange(
			'SELECT ' . $this->_getFormsQueryColumns('rf') . '
			FROM	' . $customFormDAOContext->getFormsTableName() . ' rf
			' . $this->_getFormsQueryJoins('rf') . '
			WHERE   rf.assoc_type = ? AND rf.assoc_id = ?
			GROUP BY rf.' . $customFormDAOContext->getFormIdTableColumnName() . '
			ORDER BY rf.seq',
			array((int) $assocType, (int) $assocId), $rangeInfo
		);

		return new DAOResultFactory($result, $this, '_fromRow');
	}

	/**
	 * Get active custom forms for an associated object.
	 * @param $assocType int
	 * @param $assocId int
	 * @param $rangeInfo object RangeInfo object (optional)
	 * @return DAOResultFactory containing matching CustomForms
	 */
	function getActiveByAssocId($assocType, $assocId, $rangeInfo = null) {
		$customFormDAOContext = $this->getCustomFormDAOContext();

		$result = $this->retrieveRange(
			'SELECT	' . $this->_getFormsQueryColumns('rf') . '
			FROM	' . $customFormDAOContext->getFormsTableName() . ' rf
			' . $this->_getFormsQueryJoins('rf') . '
			WHERE	rf.assoc_type = ? AND rf.assoc_id = ? AND rf.is_active = 1
			GROUP BY rf.' . $customFormDAOContext->getFormIdTableColumnName() . '
			ORDER BY rf.seq',
			array((int) $assocType, (int) $assocId), $rangeInfo
		);

		return new DAOResultFactory($result, $this, '_fromRow');
	}

	/**
	 * Sequentially renumber custom form in their sequence order.
	 * @param $assocType int
	 * @param $assocId int
	 */
	function resequenceCustomForms($assocType, $assocId) {
		$customFormDAOContext = $this->getCustomFormDAOContext();

		$result = $this->retrieve(
			'SELECT ' . $customFormDAOContext->getFormIdTableColumnName() . ' FROM ' . $customFormDAOContext->getFormsTableName() . ' WHERE assoc_type = ? AND assoc_id = ? ORDER BY seq',
			array((int) $assocType, (int) $assocId)
		);

		for ($i=1; !$result->EOF; $i++) {
			list($customFormId) = $result->fields;
			$this->update(
				'UPDATE ' . $customFormDAOContext->getFormsTableName() . ' SET seq = ? WHERE ' . $customFormDAOContext->getFormIdTableColumnName() . ' = ?',
				array(
					$i,
					$customFormId
				)
			);

			$result->MoveNext();
		}
		$result->Close();
	}

	/**
	 * Get the ID of the last inserted custom form.
	 * @return int
	 */
	function getInsertId() {
		$customFormDAOContext = $this->getCustomFormDAOContext();

		return $this->_getInsertId($customFormDAOContext->getFormsTableName(), $customFormDAOContext->getFormIdTableColumnName());
	}
}

?>
