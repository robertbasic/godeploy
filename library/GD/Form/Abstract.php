<?php

/**
 * GoDeploy deployment application
 * Copyright (C) 2011 the authors listed in AUTHORS file
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright 2011 GoDeploy
 * @author See AUTHORS file
 * @link http://www.godeploy.com/
 */

/**
 * Set up default decorators for forms - abstract so must be extended
 * Instead of the default definition list we'll use this sort of structure:
 *
 * @example
 *   <form ...>
 *     <ol>
 *       <li>
 *         <label><span>...</span></label>
 *         <span class="element">
 *           <input ...>
 *         </span>
 *       </li>
 *       <li>
 *         <span class="element submit">
 *           <input type="submit" ...>
 *         </span>
 *       </li>
 *     </ol>
 *   </form>
 *
 * @author james
 * @abstract
 */
abstract class GD_Form_Abstract extends Zend_Form
{
	/**
	 * Definition of how regular elements should be rendered
	 *
	 * @var array
	 */
	public $elementDecorators = array(
		array('ViewScript', array('viewScript' => 'template/form_field.phtml'))
	);

	/**
	 * Definition of how submit buttons (specifically an instance of any
	 * Zend_Form_Element_Submit element) should be rendered. Essentially the
	 * same as $elementDecorators but without the label.
	 *
	 * @var array
	 */
	public $submitDecorators = array(
		'ViewHelper',
		'Errors',
		array(array('row' => 'HtmlTag'), array('tag' => 'li', 'class' => 'submit')),
	);

	public function __construct($options = null)
	{
		parent::__construct($options);

		$this->addElementPrefixPath('GD_Validate_', '../library/GD/Validate', 'validate');
	}

	/**
	 * Sets the default decorator for the form automagically (called by
	 * Zend_Form). This basically adds the ul tag on.
	 * @see Zend_Form::loadDefaultDecorators()
	 */
	public function loadDefaultDecorators()
	{
		$this->setDecorators(array(
			'FormElements',
			array('Fieldset', array('')),
			array('HtmlTag', array('tag' => 'ol')),
			'Form',
		));
	}

	/**
	 * Sneakily inject our decorators into the element before rendering. I'm
	 * not sure what the impact on performance this will have, but it's easy as
	 * a starting point.
	 *
	 * Once injecting is done, we call parent::render() as normal.
	 *
	 * @see Zend_Form::render()
	 * @param  Zend_View_Interface $view
	 * @return string
	 */
	public function render(Zend_View_Interface $view = null)
	{
		foreach ($this->_elements as $element)
		{
			if ($element instanceof Zend_Form_Element_Image)
			{
				$element->setDecorators($this->submitDecorators);
			}
			else
			{
				$element->setDecorators($this->elementDecorators);
			}
		}

		return parent::render($view);
	}
}