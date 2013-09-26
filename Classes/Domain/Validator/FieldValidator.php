<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Alexander Bigga <alexander.bigga@slub-dresden.de>, SLUB Dresden
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 *
 *
 * @package slub_forms
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Tx_SlubForms_Domain_Validator_FieldValidator extends Tx_Extbase_Validation_Validator_AbstractValidator {

	/**
	 * emailRepository
	 *
	 * @var Tx_SlubForms_Domain_Repository_FieldsetsRepository
	 * @inject
	 */
	protected $fieldsetsRepository;

	/**
	 * Object Manager
	 *
	 * @var Tx_Extbase_Object_ObjectManagerInterface
	 * @inject
	 */
	protected $objectManager;


	/**
	 * Return variable
	 *
	 * @var bool
	 */
	private $isValid = true;

	/**
	 * Get session data
	 *
	 * @return
	 */
	public function getSessionData($key) {

		return $GLOBALS["TSFE"]->fe_user->getKey("ses", $key);

	}

        /**
	 * Validation of given Params
	 *
	 * @param array $field
	 * @return bool
	 */
	public function isValid($field) {
		//~ t3lib_utility_Debug::debug($field, 'isValid:... ');
		// should be usually only one fieldset
		foreach($field as $getfieldset => $getfields) {

			// get fieldset
			$fieldset = $this->fieldsetsRepository->findByUid($getfieldset);
			// get all (possible) fields of fieldset
			$allfields = $fieldset->getFields();

			// step through all possible fields and compare with submitted values
			foreach($allfields as $id => $singleField) {
				//~ t3lib_utility_Debug::debug($singleField->getTitle().' '.$singleField->getUid(), 'isValid: ... ');

				// check for senderEmail
				if ($singleField->getIsSenderEmail()) {
					if (!t3lib_div::validEmail($getfields[$singleField->getUid()])) {
						// seems to be no valid email address
						$error = $this->objectManager->get('Tx_Extbase_Error_Error', 'val_email', 1100);
						$this->result->forProperty('senderEmail')->addError($error);
						$this->isValid = false;
					}
				}

				//~ t3lib_utility_Debug::debug($singleField->getType(), 'isValid: getType... ');

				// check for file upload
				if ($singleField->getType() == 'File') {

					if (isset($_FILES['tx_slubforms_sf'])) {
						// get field configuration
						$config = $this->configToArray($singleField->getConfiguration());
						//~ t3lib_utility_Debug::debug($_FILES['tx_slubforms_sf']['size'], 'isValid: size... ');
						//~ t3lib_utility_Debug::debug($_FILES['tx_slubforms_sf']['size']['field'][$getfieldset][$singleField->getUid()], 'isValid: ... ');
						if ($config['file-accept-size'] < $_FILES['tx_slubforms_sf']['size'][$getfieldset][$singleField->getUid()]) {
							// seems to be no valid email address
							$error = $this->objectManager->get('Tx_Extbase_Error_Error', 'val_file_size', 1200);
							$this->result->forProperty('file')->addError($error);
							$this->isValid = false;
						}

						//~ if (!empty($config['file-accept-mimetypes'])) {
							//~ // e.g. file-mimetypes = audio/*, image/*, application/
							//~ $config['file-accept-size'];
						//~ }

						//~ $fileName = $basicFileFunctions->getUniqueName (
							//~ $_FILES['tx_slubforms_sf']['name']['field']['5']['13'],
							//~ t3lib_div::getFileAbsFileName('uploads/tx_slubforms/')
						//~ );
						//~ t3lib_utility_Debug::debug($fileName, '$createAction 1 $fileName:... ');
//~
						//~ t3lib_div::upload_copy_move (
							//~ $_FILES['tx_slubforms_sf']['tmp_name']['field']['5']['13'],
							//~ $fileName
						//~ );
						//~ t3lib_utility_Debug::debug($fileName, '$createAction 2 $fileName:... ');
//~
					//~ // check for file attachement
					//~ if ($fileSize > 102400) {
							//~ die ('File is too big!');
					//~ }
					}

				}

			}

			//~ t3lib_utility_Debug::debug($getfields, 'isValid: getfields ... ');

		}

//			t3lib_utility_Debug::debug($newSubscriber->getEditcode(), 'getEditcode:... ');
		//~ if (strlen($newEmail->getSenderName())<3) {
			//~ $error = $this->objectManager->get('Tx_Extbase_Error_Error', 'val_name', 1000);
			//~ $this->result->forProperty('senderName')->addError($error);
			//~ // usually $this->addError is enough but this doesn't set the CSS errorClass in the form-viewhelper :-(
//~ //			$this->addError('val_name', 1000);
//~
			//~ $this->isValid = false;
		//~ }
		//~ if (!t3lib_div::validEmail($newEmail->getSenderEmail())) {
//~ t3lib_utility_Debug::debug($newEmail->getSenderEmail(), '$getSenderEmail is empty:... ');
			//~ $error = $this->objectManager->get('Tx_Extbase_Error_Error', 'val_email', 1100);
			//~ $this->result->forProperty('senderEmail')->addError($error);
//~ //			$this->addError('val_email', 1100);
//~
			//~ $this->isValid = false;
		//~ }
		//~ if ($newEmail->getEditcode() != $this->getSessionData('editcode')) {
			//~ $error = $this->objectManager->get('Tx_Extbase_Error_Error', 'val_editcode', 1140);
			//~ $this->result->forProperty('editcode')->addError($error);
//~ //			$this->addError('val_editcode', 1140);
			//~ $this->isValid = false;
		//~ }
//~ return true;

		return $this->isValid;
  	}

	/**
	 *
	 * @param string $config
	 *
	 * @return array configuration
	 *
	 */
	private function configToArray($config) {

		$configSplit = explode("\n", $config);
		foreach ($configSplit as $id => $configLine) {
			$settingPair = explode("=", $configLine);
			$configArray[trim($settingPair[0])] = trim($settingPair[1]);
		}
		return $configArray;
	}
}
?>
