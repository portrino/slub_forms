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
 * Validation results view helper
 *
 * = Examples =
 *

 *
 * @api
 * @scope prototype
 */
class Tx_SlubForms_ViewHelpers_Form_FieldHasValueViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * Check for Prefill/Post values and set it manually
	 *
	 * @param Tx_SlubForms_Domain_Model_Fields $field
	 *
	 * @return string Rendered string
	 * @api
	 */
	public function render($field) {


		// return already posted values e.g. in case of validation errors
		if ($this->controllerContext->getRequest()->getOriginalRequest()) {
			$postedArguments = $this->controllerContext->getRequest()->getOriginalRequest()->getArguments();
			// should be usually only one fieldset
			foreach($postedArguments['field'] as $fieldsetid => $postedFields) {

				if (!empty($postedFields[$field->getUid()]))
					return $postedFields[$field->getUid()];

			}

		}

		// get field configuration
		$config = $this->configToArray($field->getConfiguration());
		if (!empty($config['prefill'])) {
			// e.g. fe_users:username
			//  or  value:1
			// first value is database "value" or "fe_users"
			$settingPair = explode(":", $config['prefill']);
			switch (trim($settingPair[0])) {
				case 'fe_users':
					if (!empty($GLOBALS['TSFE']->fe_user->user[ trim($settingPair[1]) ])) {
						return $GLOBALS['TSFE']->fe_user->user[ trim($settingPair[1]) ];
					}
					break;
				case 'value':
					if (!empty($settingPair[1]))
						return trim($settingPair[1]);
					break;
			}
		}

		// check for prefill by GET parameter
		if ($this->controllerContext->getRequest()->hasArgument('prefill')) {
			$prefilljson = $this->controllerContext->getRequest()->getArgument('prefill');
			$prefill = json_decode($prefilljson);
			if (strlen($field->getShortname()) > 0) {
				//~ $shortname =  trim($config['shortname']);
				if (!empty($prefill->{$field->getShortname()}))
					return $prefill->{$field->getShortname()};
			}
		}

		return;
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
