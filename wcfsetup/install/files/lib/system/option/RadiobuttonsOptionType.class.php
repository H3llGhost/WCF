<?php
namespace wcf\system\option;
use wcf\data\option\Option;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\WCF;
use wcf\system\UserInputException;
use wcf\util\StringUtil;

/**
 * RadiobuttonsOptionType is an implementation of IOptionType for 'input type="radio"' tags.
 *
 * @author	Marcel Werk
 * @copyright	2001-2011 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf
 * @subpackage	system.option
 * @category 	Community Framework
 */
class RadiobuttonsOptionType extends AbstractOptionType implements ISearchableUserOption {
	/**
	 * name of the template that contains the form element of this option type
	 * @var	string
	 */
	public $templateName = 'optionTypeRadiobuttons';

	/**
	 * @see wcf\system\option\IOptionType::getFormElement()
	 */
	public function getFormElement(Option $option, $value) {
		// get options
		$selectOptions = $option->parseSelectOptions();

		$availableOptions = $option->parseMultipleEnableOptions();
		$options = array(
			'disableOptions' => array(),
			'enableOptions' => array()
		);
		
		foreach ($availableOptions as $key => $enableOptions) {
			$optionData = Option::parseEnableOptions($enableOptions);
			
			$options['disableOptions'][$key] = $optionData['disableOptions'];
			$options['enableOptions'][$key] = $optionData['enableOptions'];
		}
		
		WCF::getTPL()->assign(array(
			'disableOptions' => $options['disableOptions'],
			'enableOptions' => $options['enableOptions'],
			'option' => $option,
			'selectOptions' => $selectOptions,
			'value' => $value
		));
		return WCF::getTPL()->fetch($this->templateName);
	}
	
	/**
	 * @see wcf\system\option\IOptionType::validate()
	 */
	public function validate(Option $option, $newValue) {
		if (!empty($newValue)) {
			$options = $option->parseSelectOptions();
			if (!isset($options[$newValue])) {
				throw new UserInputException($option->optionName, 'validationFailed');
			}
		}
	}
	
	/**
	 * @see wcf\system\option\ISearchableUserOption::getSearchFormElement()
	 */
	public function getSearchFormElement(Option $option, $value) {
		return $this->getFormElement($optionData, $value);
	}
	
	/**
	 * @see wcf\system\option\ISearchableUserOption::getCondition()
	 */
	public function getCondition(PreparedStatementConditionBuilder &$conditions, Option $option, $value) {
		$value = StringUtil::trim($value);
		if (!$value) return false;
		
		$conditions->add("option_value.userOption".$option->optionID." = ?", array($value));
		return true;
	}
}
