<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Torsten Schrade <schradt@uni-mainz.de>
*
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
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
 * Usage:
 * 
 * <xslt:transform transformations="{0: {stylesheet: 'path/to/stylesheet.xsl', setProfiling: 1, registerPHPFunctions: 1, transformToURI: 'path/to/file.xml'}}" />
 * 
 * The transformations property of the view helper expects an array of configurations, just as you would normally do it from TypoScript.
 * Only the four configuration keys from above are allowed; no setParameter/removeParameter; you are in a Fluid context, this should not be necessary
 * 
 * @author Torsten Schrade
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package dabase
 */

class Tx_CobjXslt_ViewHelpers_TransformViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * @var Tx_Extbase_Configuration_ConfigurationManagerInterface
	 */
	protected $configurationManager;

	/**
	 * @var tslib_cObj
	 */
	protected $contentObject;

	/**
	 * @param Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
		$this->contentObject = $this->configurationManager->getContentObject();
		$this->contentObject->start(array(),'');
	}

	/**
	 * Fluid view helper wrapper for the XSLT content object. Calls the content object class directly.
	 * 
	 * @param mixed $source
	 * @param array $transformations
	 * @param string $return
	 * 
	 * @return mixed
	 */
	public function render($source=NULL, $transformations=array()) {

		$content = '';

		if ($source === NULL) {
			$source = $this->renderChildren();
		}

		if (count($transformations) > 0 && array_key_exists('stylesheet', $transformations[0])) {
			$configuration = array();
			foreach ($transformations as $key => $transformation) {
				$i = $key+1;
				$configuration['transformations.'][$i]['stylesheet'] = $transformation['stylesheet'];
				if (array_key_exists('setProfiling', $transformation)) $configuration['transformations.'][$i]['setProfiling'] = (int) $transformation['setProfiling'];
				if (array_key_exists('registerPHPFunctions', $transformation)) $configuration['transformations.'][$i]['registerPHPFunctions'] = (int) $transformation['registerPHPFunctions'];
				if (array_key_exists('transformToURI', $transformation)) $configuration['transformations.'][$i]['transformToURI'] = $transformation['transformToURI'];
			}
			$configuration['source'] = trim($source);
			$content = $this->contentObject->cObjHookObjectsArr['XSLT']->cObjGetSingleExt('XSLT', $configuration, '', $this->contentObject);
		}

		return $content;
	}
}
?>