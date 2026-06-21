<?php
/**
 * @package     Com_BooksList
 * @copyright   (C) 2026 Nickpsal
 * @license     GNU General Public License version 3 or later
 */

namespace Nickpsal\Component\BooksList\Site\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;

\defined('_JEXEC') or die;

/**
 * Frontend list of books.
 */
class BooksModel extends ListModel
{
	public function __construct($config = [])
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = [
				'a.title', 'a.year', 'a.created', 'a.ordering',
			];
		}

		parent::__construct($config);
	}

	protected function populateState($ordering = 'a.title', $direction = 'ASC')
	{
		$app    = Factory::getApplication();
		$params = $app->getParams();

		$this->setState('filter.search', $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'string'));
		$this->setState('filter.catid', $app->getUserStateFromRequest($this->context . '.filter.catid', 'filter_catid', '', 'uint'));
		$this->setState('filter.author', $app->getUserStateFromRequest($this->context . '.filter.author', 'filter_author', '', 'uint'));
		$this->setState('filter.editor', $app->getUserStateFromRequest($this->context . '.filter.editor', 'filter_editor', '', 'uint'));
		$this->setState('filter.year', $app->getUserStateFromRequest($this->context . '.filter.year', 'filter_year', '', 'uint'));

		// Menu item may force a category.
		if ($menuCat = (int) $params->get('catid', 0)) {
			$this->setState('menu.catid', $menuCat);
		}

		$this->setState('params', $params);

		// Frontpage mode: show a fixed number of books and no pagination.
		$frontpage = (int) $params->get('frontpage', 0);

		if ($frontpage) {
			$limit = (int) $params->get('frontpage_limit', 6);
			$this->setState('list.limit', $limit);
		} else {
			$limit = (int) $params->get('books_per_page', $app->get('list_limit', 12));
			$this->setState('list.limit', $app->getUserStateFromRequest('global.list.limit', 'limit', $limit, 'uint'));
		}

		parent::populateState($ordering, $direction);

		// Force the configured limit and first page regardless of the request.
		if ($frontpage) {
			$this->setState('list.limit', $limit);
			$this->setState('list.start', 0);
		}
	}

	protected function getStoreId($id = '')
	{
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.catid');
		$id .= ':' . $this->getState('filter.author');
		$id .= ':' . $this->getState('filter.editor');
		$id .= ':' . $this->getState('filter.year');

		return parent::getStoreId($id);
	}

	protected function getListQuery()
	{
		$db     = $this->getDatabase();
		$user   = Factory::getApplication()->getIdentity();
		$groups = $user->getAuthorisedViewLevels();
		$query  = $db->getQuery(true);

		$query->select(
			'a.id, a.title, a.subtitle, a.alias, a.catid, a.editor_id, a.year, a.isbn, '
			. 'a.pages, a.image, a.description, a.hits, a.vote_sum, a.vote_count, a.language'
		)
			->from($db->quoteName('#__booklist_books', 'a'))
			->where($db->quoteName('a.state') . ' = 1')
			->whereIn($db->quoteName('a.access'), $groups);

		// Category title + alias for routing.
		$query->select($db->quoteName('c.title', 'category_title') . ', ' . $db->quoteName('c.alias', 'category_alias'))
			->join('LEFT', $db->quoteName('#__categories', 'c') . ' ON ' . $db->quoteName('c.id') . ' = ' . $db->quoteName('a.catid'));

		$query->select($db->quoteName('e.name', 'editor_name'))
			->join('LEFT', $db->quoteName('#__booklist_editors', 'e') . ' ON ' . $db->quoteName('e.id') . ' = ' . $db->quoteName('a.editor_id'));

		// Language filter.
		if (Factory::getApplication()->getLanguageFilter()) {
			$query->whereIn($db->quoteName('a.language'), [Factory::getApplication()->getLanguage()->getTag(), '*'], ParameterType::STRING);
		}

		// Category (menu forced or user filter).
		$catid = (int) ($this->getState('menu.catid') ?: $this->getState('filter.catid'));

		if ($catid) {
			$query->where($db->quoteName('a.catid') . ' = :catid')
				->bind(':catid', $catid, ParameterType::INTEGER);
		}

		$editor = (int) $this->getState('filter.editor');

		if ($editor) {
			$query->where($db->quoteName('a.editor_id') . ' = :editor')
				->bind(':editor', $editor, ParameterType::INTEGER);
		}

		$year = (int) $this->getState('filter.year');

		if ($year) {
			$query->where($db->quoteName('a.year') . ' = :year')
				->bind(':year', $year, ParameterType::INTEGER);
		}

		// Author filter (join the link table).
		$author = (int) $this->getState('filter.author');

		if ($author) {
			$query->join('INNER', $db->quoteName('#__booklist_book_author', 'ba') . ' ON ' . $db->quoteName('ba.book_id') . ' = ' . $db->quoteName('a.id'))
				->where($db->quoteName('ba.author_id') . ' = :author')
				->bind(':author', $author, ParameterType::INTEGER);
		}

		$search = $this->getState('filter.search');

		if (!empty($search)) {
			$search = '%' . str_replace(' ', '%', trim($search)) . '%';
			$query->where(
				'(' . $db->quoteName('a.title') . ' LIKE :s1'
				. ' OR ' . $db->quoteName('a.subtitle') . ' LIKE :s2'
				. ' OR ' . $db->quoteName('a.isbn') . ' LIKE :s3)'
			)
				->bind(':s1', $search)
				->bind(':s2', $search)
				->bind(':s3', $search);
		}

		$orderCol  = $this->state->get('list.ordering', 'a.title');
		$orderDirn = $this->state->get('list.direction', 'ASC');
		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

		return $query;
	}

	/**
	 * Load the authors for each book in the current list, keyed by book id.
	 *
	 * @return  array<int,array>
	 */
	public function getAuthorsForItems(): array
	{
		$items = $this->getItems();

		if (empty($items)) {
			return [];
		}

		$ids = array_map(static fn ($i) => (int) $i->id, $items);
		$db  = $this->getDatabase();

		$query = $db->getQuery(true)
			->select('ba.book_id, au.id, au.name, au.lastname, au.alias')
			->from($db->quoteName('#__booklist_book_author', 'ba'))
			->join('INNER', $db->quoteName('#__booklist_authors', 'au') . ' ON ' . $db->quoteName('au.id') . ' = ' . $db->quoteName('ba.author_id'))
			->whereIn($db->quoteName('ba.book_id'), $ids)
			->order($db->quoteName('ba.ordering') . ' ASC');

		$rows   = $db->setQuery($query)->loadObjectList();
		$result = [];

		foreach ($rows as $row) {
			$result[(int) $row->book_id][] = $row;
		}

		return $result;
	}
}
