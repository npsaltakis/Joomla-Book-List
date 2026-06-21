<?php
/**
 * @package     Com_BooksList
 * @copyright   (C) 2026 Nickpsal
 * @license     GNU General Public License version 3 or later
 */

namespace Nickpsal\Component\BooksList\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;

\defined('_JEXEC') or die;

/**
 * Author admin model (single item).
 */
class AuthorModel extends AdminModel
{
	public $typeAlias = 'com_books_list.author';

	public function getTable($type = 'Author', $prefix = 'Administrator', $config = [])
	{
		return parent::getTable($type, $prefix, $config);
	}

	public function getForm($data = [], $loadData = true)
	{
		$form = $this->loadForm(
			'com_books_list.author',
			'author',
			['control' => 'jform', 'load_data' => $loadData]
		);

		return empty($form) ? false : $form;
	}

	protected function loadFormData()
	{
		$data = Factory::getApplication()->getUserState('com_books_list.edit.author.data', []);

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}
}
