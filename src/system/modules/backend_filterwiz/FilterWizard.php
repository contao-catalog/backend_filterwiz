<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight webCMS
 * Copyright (C) 2005 Leo Feyer
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at http://www.gnu.org/licenses/.
 *
 * PHP version 5
 * @copyright  Thyon Design 2008 
 * @author     John Brand <john.brand@thyon.com> 
 * @package    FilterWizard 
 * @license    LGPL
 * @filesource
 */


/**
 * Class FilterWizard
 *
 * Provide methods to handle Settings tables.
 * @copyright  Thyon Design 2008 
 * @author     John Brand <john.brand@thyon.com> 
 * @package    FilterWizard 
 */
class FilterWizard extends Widget
{

	/**
	 * Submit user input
	 * @var boolean
	 */
	protected $blnSubmitInput = true;


	/**
	 * Settings
	 * @var array
	 */
	protected $arrSettings = array();

	/**
	 * Settings
	 * @var array
	 */
	protected $arrLabels;

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'be_widget';

	/**
	 * Add specific attributes
	 * @param string
	 * @param mixed
	 */
	public function __set($strKey, $varValue)
	{
		switch ($strKey)
		{
			case 'mandatory':
				$this->arrConfiguration['mandatory'] = $varValue ? true : false;
				break;

			case 'options':
				$this->arrOptions = deserialize($varValue);
				break;

			case 'radio':
			case 'checkbox':
				$this->arrSettings[$strKey] = deserialize($varValue);
				break;

			case 'labels':
				$this->arrLabels = $varValue;
				break;

			default:
				parent::__set($strKey, $varValue);
				break;
		}
	}


	/**
	 * Generate the widget and return it as string
	 * @return string
	 */
	public function generate()
	{

		// Add a "no entries found" message if there are no options
		if (!is_array($this->arrOptions) || !count($this->arrOptions))
		{
			return '<p class="tl_noopt">'.$GLOBALS['TL_LANG']['MSC']['noResult'].'</p>';
		}

		$arrButtons = array('up', 'down');

		// Change the order
		if ($this->Input->get('cmd_'.$this->strField) && is_numeric($this->Input->get('cid')) && $this->Input->get('id') == $this->currentRecord)
		{
			$this->import('Database');
			switch ($this->Input->get('cmd_'.$this->strField))
			{
				case 'up':
					$this->varValue = array_move_up($this->varValue, $this->Input->get('cid'));
					break;

				case 'down':
					$this->varValue = array_move_down($this->varValue, $this->Input->get('cid'));
					break;

			}

			$this->Database->prepare("UPDATE " . $this->strTable . " SET " . $this->strField . "=? WHERE id=?")
						   ->execute(serialize($this->varValue), $this->currentRecord);

			$this->redirect(preg_replace('/&(amp;)?cid=[^&]*/i', '', preg_replace('/&(amp;)?cmd_'.$this->strField.'=[^&]*/i', '', $this->Environment->request)));
		}

		
		$return = '	<table cellpadding="2" cellspacing="2" id="ctrl_'.$this->strId.'" class="tl_chmod" summary="Table holds filter fields">
	<thead>
    <tr>
			<th></th>';
		if (count($this->arrOptions) > 1)
		{
			$return .= '
			<th></th>';
		}

		// preformat values
		$varValue = is_array($this->varValue) ? $this->varValue : array($this->varValue);
		$arrValues = array();
		foreach ($varValue as $k=>$v)
		{
			$arrValues[] = is_array($v) ? key($v) : $v;
		}	

		if ($this->arrSettings['checkbox'])
		{
			foreach ($this->arrSettings['checkbox'] as $checkbox)
			{
				$return .= '
			<th scope="col">'. ($this->arrLabels[$checkbox] ? $this->arrLabels[$checkbox] : $checkbox).'</th>';
			}
		}

		if ($this->arrSettings['radio'])
		{
			foreach ($this->arrSettings['radio'] as $radio)
			{
				$return .= '
			<th scope="col">'. ($this->arrLabels[$radio] ? $this->arrLabels[$radio] : $radio).'</th>';
			}
		}
		$return .= '
		</tr>
	</thead>';

		// place selected, sorted items at the top
		$arrOptions = array();
		$tmpOptions = $this->arrOptions;
		$checkOptions = array();
		foreach ($this->arrOptions as $i=>$option) 
		{
			$pos = array_search($option['value'], $arrValues);
			if ($pos !== false)
			{
				$checkOptions[$pos] = $this->arrOptions[$i];
				unset($tmpOptions[$i]);
			}
		}
		ksort($checkOptions);
		$this->arrOptions = array_merge($checkOptions,$tmpOptions);
		
		if (count($this->arrOptions))
		{
			$return .= '
	<tbody>';
		}
		
		foreach ($this->arrOptions as $k=>$v)
		{
			$return .= '
		<tr>
			<td scope="row" class="th">'.$v['label'].'</td>
';
			$buttons = '';
			// Add buttons if more than 1 option
			if (count($this->arrOptions) > 1) 
			{
				$return .= 
'			<td class="button">';
				foreach ($arrButtons as $button)
				{
					$buttons .= '<a href="'.$this->addToUrl('&amp;cmd_'.$this->strField.'='.$button.'&amp;cid='.$k.'&amp;id='.$this->currentRecord).'" title="'.specialchars($GLOBALS['TL_LANG'][$this->strTable][$button][0]).'" onclick="BackendFilterWizard.filterWizard(this, \''.$button.'\', \'ctrl_'.$this->strId.'\'); return false">'.$this->generateImage($button.'.gif', $GLOBALS['TL_LANG'][$this->strTable][$button][0], 'class="tl_filterwizard_img"').'</a> ';
				}
				$return .= $buttons . '</td>';
			}


			if ($this->arrSettings['checkbox'])
			{
				foreach ($this->arrSettings['checkbox'] as $key=>$setting)
				{
					$value = $setting;
					$checkboxvalue = $varValue[$k][$v['value']] ? $varValue[$k][$v['value']]['checkbox'] : '';
					$return .= '
			<td><input type="checkbox" name="'.$this->strName.'['.$k.']['.$v['value'].'][checkbox]" value="'.specialchars($value).'"'.$this->optionChecked($value, $checkboxvalue).' onfocus="Backend.getScrollOffset();" /></td>';
				}
			}
					
			if ($this->arrSettings['radio'])
			{
				// Add radio buttons
				foreach ($this->arrSettings['radio'] as $key=>$setting)
				{
					$value = $setting;
					$radiovalue = $varValue[$k][$v['value']] ? $varValue[$k][$v['value']]['radio'] : 'none';
					$return .= '
			<td><input type="radio" name="'.$this->strName.'['.$k.']['.$v['value'].'][radio]" value="'.specialchars($value).'"'.$this->optionChecked($value, $radiovalue).' onfocus="Backend.getScrollOffset();" /></td>';
				}
			}

			$return .= '
    </tr>';
		}
		if (count($this->arrOptions))
		{
			$return .= '
	</tbody>';
		}

		return $return.'
  </table>';
	}
}

?>