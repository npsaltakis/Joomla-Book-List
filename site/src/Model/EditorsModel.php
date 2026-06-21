<?php
/**
 * @package     Com_BooksList
 * @copyright   (C) 2026 Nickpsal
 * @license     GNU General Public License version 3 or later
 */

namespace Nickpsal\Component\BooksList\Site\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;

\defined('_JEXEC') or die;

/**
 * Frontend list of editors / publishers.
 */
class EditorsModel extends ListModel
{
	protected function populateState($ordering = 'a.name', $direction = 'ASC')
	{
		$app    = Factory::getApplication();
		$params = $app->getParams();

		$this->setState('params', $params);

		$limit = (int) $params->get('editors_per_page', 24);
		$this->setState('list.limit', $limit);
		$this->setState('list.start', $app->getInput()->get('limitstart', 0, 'uint'));

		parent::populateState($ordering, $direction);
	}

	protected function getListQuery()
	{
		$db     = $this->getDatabase();
		$user   = Factory::getApplication()->getIdentity();
		$groups = $user->getAuthorisedViewLevels();

		$query = $db->getQuery(true)
			->select('a.id, a.name, a.alias, a.language')
			->select('COUNT(DISTINCT b.id) AS book_count')
			->from($db->quoteName('#__booklist_editors', 'a'))
			->join(
				'LEFT',
				$db->quoteName('#__booklist_books', 'b') . ' ON ' . $db->quoteName('b.editor_id') . ' = ' . $db->quoteName('a.id')
				. ' AND ' . $db->quoteName('b.state') . ' = 1'
			)
			->where($db->quoteName('a.state') . ' = 1')
			->whereIn($db->quoteName('a.access'), $groups)
			->group($db->quoteName('a.id'))
			->order($db->quoteName('a.name') . ' ASC');

		return $query;
	}
}
