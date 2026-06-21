<?php
/**
 * @package     Com_BooksList
 * @copyright   (C) 2026 Nickpsal
 * @license     GNU General Public License version 3 or later
 */

namespace Nickpsal\Component\BooksList\Administrator\Field;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\Database\DatabaseInterface;

\defined('_JEXEC') or die;

/**
 * Field listing the available editors / publishers.
 */
class EditorlistField extends ListField
{
	protected $type = 'Editorlist';

	protected function getOptions()
	{
		$db    = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->getQuery(true)
			->select($db->quoteName('id', 'value'))
			->select($db->quoteName('name', 'text'))
			->from($db->quoteName('#__booklist_editors'))
			->where($db->quoteName('state') . ' = 1')
			->order($db->quoteName('name') . ' ASC');

		$options = $db->setQuery($query)->loadObjectList();

		return array_merge(parent::getOptions(), $options ?: []);
	}
}
