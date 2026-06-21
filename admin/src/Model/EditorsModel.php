<?php
/**
 * @package     Com_BooksList
 * @copyright   (C) 2026 Nickpsal
 * @license     GNU General Public License version 3 or later
 */

namespace Nickpsal\Component\BooksList\Administrator\Model;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;

\defined('_JEXEC') or die;

/**
 * Methods supporting a list of editors.
 */
class EditorsModel extends ListModel
{
	public function __construct($config = [])
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = [
				'id', 'a.id',
				'name', 'a.name',
				'state', 'a.state',
				'ordering', 'a.ordering',
			];
		}

		parent::__construct($config);
	}

	protected function populateState($ordering = 'a.name', $direction = 'ASC')
	{
		$this->setState('filter.search', $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search'));
		$this->setState('filter.state', $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state', '', 'string'));

		parent::populateState($ordering, $direction);
	}

	protected function getStoreId($id = '')
	{
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');

		return parent::getStoreId($id);
	}

	protected function getListQuery()
	{
		$db    = $this->getDatabase();
		$query = $db->getQuery(true);

		$query->select('a.id, a.name, a.alias, a.state, a.ordering, a.access')
			->from($db->quoteName('#__booklist_editors', 'a'));

		$query->select('COUNT(b.id) AS book_count')
			->join('LEFT', $db->quoteName('#__booklist_books', 'b') . ' ON ' . $db->quoteName('b.editor_id') . ' = ' . $db->quoteName('a.id'))
			->group('a.id, a.name, a.alias, a.state, a.ordering, a.access');

		$state = $this->getState('filter.state');

		if (is_numeric($state)) {
			$state = (int) $state;
			$query->where($db->quoteName('a.state') . ' = :state')
				->bind(':state', $state, ParameterType::INTEGER);
		} elseif ($state === '') {
			$query->whereIn($db->quoteName('a.state'), [0, 1]);
		}

		$search = $this->getState('filter.search');

		if (!empty($search)) {
			$search = '%' . str_replace(' ', '%', trim($search)) . '%';
			$query->where($db->quoteName('a.name') . ' LIKE :s1')->bind(':s1', $search);
		}

		$orderCol  = $this->state->get('list.ordering', 'a.name');
		$orderDirn = $this->state->get('list.direction', 'ASC');
		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

		return $query;
	}
}
