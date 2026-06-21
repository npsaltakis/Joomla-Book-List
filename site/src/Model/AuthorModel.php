<?php
/**
 * @package     Com_BooksList
 * @copyright   (C) 2026 Nickpsal
 * @license     GNU General Public License version 3 or later
 */

namespace Nickpsal\Component\BooksList\Site\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\ItemModel;
use Joomla\Database\ParameterType;

\defined('_JEXEC') or die;

/**
 * Frontend single author model.
 */
class AuthorModel extends ItemModel
{
	protected $_item = null;

	protected function populateState()
	{
		$app = Factory::getApplication();

		$this->setState('author.id', $app->getInput()->getInt('id'));
		$this->setState('params', $app->getParams());
	}

	/**
	 * Get the published author matching the state id, with their books.
	 *
	 * @param   int|null  $pk  The author id.
	 *
	 * @return  object
	 *
	 * @throws  \Exception
	 */
	public function getItem($pk = null)
	{
		$pk = (int) ($pk ?: $this->getState('author.id'));

		if (isset($this->_item) && $this->_item !== null && $this->_item->id == $pk) {
			return $this->_item;
		}

		$db     = $this->getDatabase();
		$user   = Factory::getApplication()->getIdentity();
		$groups = $user->getAuthorisedViewLevels();

		$query = $db->getQuery(true)
			->select('a.*')
			->from($db->quoteName('#__booklist_authors', 'a'))
			->where($db->quoteName('a.id') . ' = :id')
			->where($db->quoteName('a.state') . ' = 1')
			->whereIn($db->quoteName('a.access'), $groups)
			->bind(':id', $pk, ParameterType::INTEGER);

		$item = $db->setQuery($query)->loadObject();

		if (empty($item)) {
			throw new \Exception(Text::_('COM_BOOKS_LIST_ERROR_AUTHOR_NOT_FOUND'), 404);
		}

		// Books written by this author (published and accessible).
		$bQuery = $db->getQuery(true)
			->select('b.id, b.title, b.alias, b.catid, b.year, b.image, b.language')
			->from($db->quoteName('#__booklist_book_author', 'ba'))
			->join('INNER', $db->quoteName('#__booklist_books', 'b') . ' ON ' . $db->quoteName('b.id') . ' = ' . $db->quoteName('ba.book_id'))
			->where($db->quoteName('ba.author_id') . ' = :id')
			->where($db->quoteName('b.state') . ' = 1')
			->whereIn($db->quoteName('b.access'), $groups)
			->order($db->quoteName('b.year') . ' DESC, ' . $db->quoteName('b.title') . ' ASC')
			->bind(':id', $pk, ParameterType::INTEGER);

		$item->books = $db->setQuery($bQuery)->loadObjectList();

		$this->_item = $item;

		return $item;
	}
}
