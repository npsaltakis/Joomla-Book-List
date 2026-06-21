<?php
/**
 * @package     Com_BooksList
 * @copyright   (C) 2026 Nickpsal
 * @license     GNU General Public License version 3 or later
 */

namespace Nickpsal\Component\BooksList\Site\Helper;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;

\defined('_JEXEC') or die;

/**
 * Frontend helper: filter option lists and display utilities.
 */
class BooksListHelper
{
	/**
	 * Published categories belonging to the component.
	 *
	 * @return  array
	 */
	public static function getCategories(): array
	{
		$db    = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->getQuery(true)
			->select($db->quoteName(['id', 'title', 'level']))
			->from($db->quoteName('#__categories'))
			->where($db->quoteName('extension') . ' = ' . $db->quote('com_books_list'))
			->where($db->quoteName('published') . ' = 1')
			->order($db->quoteName('lft') . ' ASC');

		return $db->setQuery($query)->loadObjectList() ?: [];
	}

	/**
	 * Published authors that have at least one book.
	 *
	 * @return  array
	 */
	public static function getAuthors(): array
	{
		$db    = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->getQuery(true)
			->select('DISTINCT a.id, TRIM(CONCAT(a.name, ' . $db->quote(' ') . ', a.lastname)) AS title')
			->from($db->quoteName('#__booklist_authors', 'a'))
			->join('INNER', $db->quoteName('#__booklist_book_author', 'ba') . ' ON ' . $db->quoteName('ba.author_id') . ' = ' . $db->quoteName('a.id'))
			->where($db->quoteName('a.state') . ' = 1')
			->order('title ASC');

		return $db->setQuery($query)->loadObjectList() ?: [];
	}

	/**
	 * Published editors that have at least one book.
	 *
	 * @return  array
	 */
	public static function getEditors(): array
	{
		$db    = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->getQuery(true)
			->select('DISTINCT e.id, e.name AS title')
			->from($db->quoteName('#__booklist_editors', 'e'))
			->join('INNER', $db->quoteName('#__booklist_books', 'b') . ' ON ' . $db->quoteName('b.editor_id') . ' = ' . $db->quoteName('e.id'))
			->where($db->quoteName('e.state') . ' = 1')
			->where($db->quoteName('b.state') . ' = 1')
			->order('e.name ASC');

		return $db->setQuery($query)->loadObjectList() ?: [];
	}

	/**
	 * Distinct publication years present in published books.
	 *
	 * @return  array
	 */
	public static function getYears(): array
	{
		$db    = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->getQuery(true)
			->select('DISTINCT ' . $db->quoteName('year'))
			->from($db->quoteName('#__booklist_books'))
			->where($db->quoteName('state') . ' = 1')
			->where($db->quoteName('year') . ' > 0')
			->order($db->quoteName('year') . ' DESC');

		return $db->setQuery($query)->loadColumn() ?: [];
	}
}
