<?php
/**
 * @package     Com_BooksList
 * @copyright   (C) 2026 Nickpsal
 * @license     GNU General Public License version 3 or later
 */

namespace Nickpsal\Component\BooksList\Site\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ItemModel;
use Joomla\Database\ParameterType;

\defined('_JEXEC') or die;

/**
 * Frontend single book model.
 */
class BookModel extends ItemModel
{
	protected $_item = null;

	protected function populateState()
	{
		$app = Factory::getApplication();

		$this->setState('book.id', $app->getInput()->getInt('id'));
		$this->setState('params', $app->getParams());
	}

	/**
	 * Get the published book matching the state id.
	 *
	 * @param   int|null  $pk  The book id.
	 *
	 * @return  object
	 *
	 * @throws  \Exception
	 */
	public function getItem($pk = null)
	{
		$pk = (int) ($pk ?: $this->getState('book.id'));

		if (isset($this->_item) && $this->_item !== null && $this->_item->id == $pk) {
			return $this->_item;
		}

		$db     = $this->getDatabase();
		$user   = Factory::getApplication()->getIdentity();
		$groups = $user->getAuthorisedViewLevels();

		$query = $db->getQuery(true)
			->select('a.*')
			->select($db->quoteName('c.title', 'category_title') . ', ' . $db->quoteName('c.alias', 'category_alias'))
			->select($db->quoteName('e.name', 'editor_name'))
			->from($db->quoteName('#__booklist_books', 'a'))
			->join('LEFT', $db->quoteName('#__categories', 'c') . ' ON ' . $db->quoteName('c.id') . ' = ' . $db->quoteName('a.catid'))
			->join('LEFT', $db->quoteName('#__booklist_editors', 'e') . ' ON ' . $db->quoteName('e.id') . ' = ' . $db->quoteName('a.editor_id'))
			->where($db->quoteName('a.id') . ' = :id')
			->where($db->quoteName('a.state') . ' = 1')
			->whereIn($db->quoteName('a.access'), $groups)
			->bind(':id', $pk, ParameterType::INTEGER);

		$item = $db->setQuery($query)->loadObject();

		if (empty($item)) {
			throw new \Exception(\Joomla\CMS\Language\Text::_('COM_BOOKS_LIST_ERROR_BOOK_NOT_FOUND'), 404);
		}

		// Authors.
		$aQuery = $db->getQuery(true)
			->select('au.id, au.name, au.lastname, au.alias, au.description')
			->from($db->quoteName('#__booklist_book_author', 'ba'))
			->join('INNER', $db->quoteName('#__booklist_authors', 'au') . ' ON ' . $db->quoteName('au.id') . ' = ' . $db->quoteName('ba.author_id'))
			->where($db->quoteName('ba.book_id') . ' = :id')
			->bind(':id', $pk, ParameterType::INTEGER)
			->order($db->quoteName('ba.ordering') . ' ASC');

		$item->authors = $db->setQuery($aQuery)->loadObjectList();

		$this->_item = $item;

		return $item;
	}

	/**
	 * Increment the hit counter for the book.
	 *
	 * @param   int  $pk  Book id.
	 *
	 * @return  bool
	 */
	public function hit($pk = 0)
	{
		$pk = (int) ($pk ?: $this->getState('book.id'));

		if (!$pk) {
			return false;
		}

		$db    = $this->getDatabase();
		$query = $db->getQuery(true)
			->update($db->quoteName('#__booklist_books'))
			->set($db->quoteName('hits') . ' = ' . $db->quoteName('hits') . ' + 1')
			->where($db->quoteName('id') . ' = :id')
			->bind(':id', $pk, ParameterType::INTEGER);

		$db->setQuery($query)->execute();

		return true;
	}
}
