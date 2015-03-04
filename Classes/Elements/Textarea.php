<?php

namespace Ameos\AmeosForm\Elements;

class Textarea extends ElementAbstract {
	
	/**
	 * form to html
	 *
	 * @return	string the html
	 */
	public function toHtml() {
		return '<textarea id="' . $this->getHtmlId() . '" name="' . $this->absolutename . '"' . $this->getAttributes() . '>' . $this->getValue() . '</textarea>';
	}	
}