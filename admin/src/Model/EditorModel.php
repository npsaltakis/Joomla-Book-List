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
 * Editor admin model (single item).
 */
class EditorModel extends AdminModel
{
	public $typeAlias = 'com_books_list.editor';

	public function getTable($type = 'Editor', $prefix = 'Administrator', $config = [])
	{
		return parent::getTable($type, $prefix, $config);
	}

	public function getForm($data = [], $loadData = true)
	{
		$form = $this->loadForm(
			'com_books_list.editor',
			'editor',
			['control' => 'jform', 'load_data' => $loadData]
		);

		return empty($form) ? false : $form;
	}

	protected function loadFormData()
	{
		$data = Factory::getApplication()->getUserState('com_books_list.edit.editor.data', []);

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}
}
