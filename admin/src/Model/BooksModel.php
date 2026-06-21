<?php
/**
 * @package     Com_BooksList
 * @copyright   (C) 2026 Nickpsal
 * @license     GNU General Public License version 3 or later
 */

namespace Nickpsal\Component\BooksList\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\QueryInterface;

\defined('_JEXEC') or die;

/**
 * Methods supporting a list of books.
 */
class BooksModel extends ListModel
{
	public function __construct($config = [])
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = [
				'id', 'a.id',
				'title', 'a.title',
				'alias', 'a.alias',
				'state', 'a.state',
				'catid', 'a.catid', 'category_title',
				'editor_id', 'a.editor_id', 'editor_name',
				'year', 'a.year',
				'access', 'a.access', 'access_level',
				'language', 'a.language',
				'ordering', 'a.ordering',
				'created', 'a.created',
			];
		}

		parent::__construct($config);
	}

	protected function populateState($ordering = 'a.title', $direction = 'ASC')
	{
		$app = Factory::getApplication();

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$this->setState('filter.state', $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state', '', 'string'));
		$this->setState('filter.catid', $this->getUserStateFromRequest($this->context . '.filter.catid', 'filter_catid', '', 'string'));
		$this->setState('filter.editor_id', $this->getUserStateFromRequest($this->context . '.filter.editor_id', 'filter_editor_id', '', 'string'));
		$this->setState('filter.language', $this->getUserStateFromRequest($this->context . '.filter.language', 'filter_language', '', 'string'));

		parent::populateState($ordering, $direction);
	}

	protected function getStoreId($id = '')
	{
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');
		$id .= ':' . $this->getState('filter.catid');
		$id .= ':' . $this->getState('filter.editor_id');
		$id .= ':' . $this->getState('filter.language');

		return parent::getStoreId($id);
	}

	protected function getListQuery()
	{
		$db    = $this->getDatabase();
		$query = $db->getQuery(true);

		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.title, a.subtitle, a.alias, a.catid, a.editor_id, a.year, a.isbn, '
				. 'a.state, a.access, a.ordering, a.language, a.created, a.hits'
			)
		)
			->from($db->quoteName('#__booklist_books', 'a'));

		// Join category title.
		$query->select($db->quoteName('c.title', 'category_title'))
			->join('LEFT', $db->quoteName('#__categories', 'c') . ' ON ' . $db->quoteName('c.id') . ' = ' . $db->quoteName('a.catid'));

		// Join editor name.
		$query->select($db->quoteName('e.name', 'editor_name'))
			->join('LEFT', $db->quoteName('#__booklist_editors', 'e') . ' ON ' . $db->quoteName('e.id') . ' = ' . $db->quoteName('a.editor_id'));

		// Join access level title.
		$query->select($db->quoteName('ag.title', 'access_level'))
			->join('LEFT', $db->quoteName('#__viewlevels', 'ag') . ' ON ' . $db->quoteName('ag.id') . ' = ' . $db->quoteName('a.access'));

		// Filter by published state.
		$state = $this->getState('filter.state');

		if (is_numeric($state)) {
			$query->where($db->quoteName('a.state') . ' = :state')
				->bind(':state', $state, \Joomla\Database\ParameterType::INTEGER);
		} elseif ($state === '') {
			$query->whereIn($db->quoteName('a.state'), [0, 1]);
		}

		// Filter by category.
		$catid = $this->getState('filter.catid');

		if (is_numeric($catid)) {
			$catid = (int) $catid;
			$query->where($db->quoteName('a.catid') . ' = :catid')
				->bind(':catid', $catid, \Joomla\Database\ParameterType::INTEGER);
		}

		// Filter by editor.
		$editorId = $this->getState('filter.editor_id');

		if (is_numeric($editorId)) {
			$editorId = (int) $editorId;
			$query->where($db->quoteName('a.editor_id') . ' = :editorid')
				->bind(':editorid', $editorId, \Joomla\Database\ParameterType::INTEGER);
		}

		// Filter by language.
		$language = $this->getState('filter.language');

		if ($language !== '') {
			$query->where($db->quoteName('a.language') . ' = :language')
				->bind(':language', $language);
		}

		// Filter by search.
		$search = $this->getState('filter.search');

		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$ids = (int) substr($search, 3);
				$query->where($db->quoteName('a.id') . ' = :id')
					->bind(':id', $ids, \Joomla\Database\ParameterType::INTEGER);
			} else {
				$search = '%' . str_replace(' ', '%', trim($search)) . '%';
				$query->where(
					'(' . $db->quoteName('a.title') . ' LIKE :search1'
					. ' OR ' . $db->quoteName('a.subtitle') . ' LIKE :search2'
					. ' OR ' . $db->quoteName('a.isbn') . ' LIKE :search3)'
				)
					->bind(':search1', $search)
					->bind(':search2', $search)
					->bind(':search3', $search);
			}
		}

		// Ordering.
		$orderCol  = $this->state->get('list.ordering', 'a.title');
		$orderDirn = $this->state->get('list.direction', 'ASC');
		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

		return $query;
	}
}
