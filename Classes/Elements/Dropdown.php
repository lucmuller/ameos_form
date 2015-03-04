<?php

namespace Ameos\AmeosForm\Elements;

class Dropdown extends ElementAbstract {
	
	/**
	 * form to html
	 *
	 * @return	string the html
	 */
	public function toHtml() {
		if($this->isMultiple()) {
			$output = '<select id="' . $this->getHtmlId() . '" name="' . $this->absolutename . '[]"' . $this->getAttributes() . '>';
		} else {
			$output = '<select id="' . $this->getHtmlId() . '" name="' . $this->absolutename . '"' . $this->getAttributes() . '>';
		}

		if(isset($this->configuration['placeholder'])) {
			$output.= '<option value="">' . $this->configuration['placeholder'] . '</option>';
		}

		$currentValue = $this->getValue();
		if(!is_array($currentValue)) {
			$currentValue = [$currentValue];
		}
		if(is_array($this->configuration['items'])) {
			foreach($this->configuration['items'] as $value => $label) {
				$selected = in_array($value, $currentValue) ? ' selected="selected"' : '';
				$output.= '<option value="' . $value . '"' . $selected . '>' . $label . '</option>';
			}
		} elseif(is_object($this->configuration['items']) && is_a($this->configuration['items'], '\\TYPO3\\CMS\\Extbase\\Persistence\\Generic\\QueryResult')) {
			$optionLabelFieldMethod = 'get' . ucfirst($this->configuration['optionLabelField']);
			$optionValueFieldMethod = 'get' . ucfirst($this->configuration['optionValueField']);

			foreach($currentValue as $key => $value) {
				if(is_object($value) && is_a($value, '\\TYPO3\\CMS\\Extbase\\DomainObject\\AbstractEntity')) {
					$currentValue[$key] = $this->getValue()->$optionValueFieldMethod();
				}
			}

			foreach($this->configuration['items'] as $model) {				
				$selected = in_array($model->$optionValueFieldMethod(), $currentValue) ? ' selected="selected"' : '';
				$output.= '<option value="' . $model->$optionValueFieldMethod() . '"' . $selected . '>' . $model->$optionLabelFieldMethod() . '</option>';
			}			
		}
		$output.= '</select>';
		return $output;
	}

	/**
	 * return html attribute
	 * @return string html attribute
	 */
	public function getAttributes() {
		$output = parent::getAttributes();
		$output.= isset($this->configuration['multiple']) && $this->configuration['multiple'] == TRUE  ? ' multiple="multiple"' : 	'';
		return $output;
	}

	/**
	 * return true if it's a multiple dropdown
	 * @return bool
	 */
	public function isMultiple() {
		return 	isset($this->configuration['multiple']) && $this->configuration['multiple'] == TRUE;
	}
	
	/**
	 * return where clause
	 *
	 * @return	bool|array FALSE if no search. Else array with search type and value
	 */
	public function getClause() {
		$value = $this->getValue();
		if(!empty($value)) {
			if($this->overrideClause !== FALSE) {
				return parent::getClause();
			} else {
				if ($this->isMultiple()) {
					return [
						'elementname'  => $this->getName(),
						'elementvalue' => $this->getValue(),
						'field' => $this->getSearchField(),
						'type'  => 'in',
						'value' => $value
					];
				} else {
					return [
						'elementname'  => $this->getName(),
						'elementvalue' => $this->getValue(),
						'field' => $this->getSearchField(),
						'type'  => 'equals',
						'value' => $value
					];
				}
			}
		}
		return FALSE;
	}
}