<?php
/**
 * @package     Com_BooksList
 * @copyright   (C) 2026 Nickpsal
 * @license     GNU General Public License version 3 or later
 */

namespace Nickpsal\Component\BooksList\Site\Service;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

\defined('_JEXEC') or die;

/**
 * Routing class for com_books_list (SEF URLs).
 */
class Router extends RouterView
{
	protected $db;

	public function __construct(SiteApplication $app, AbstractMenu $menu, CategoryFactoryInterface $categoryFactory, DatabaseInterface $db)
	{
		$this->db = $db;

		$books = new RouterViewConfiguration('books');
		$this->registerView($books);

		$authors = new RouterViewConfiguration('authors');
		$this->registerView($authors);

		$editors = new RouterViewConfiguration('editors');
		$this->registerView($editors);

		$book = new RouterViewConfiguration('book');
		$book->setKey('id');
		$this->registerView($book);

		$author = new RouterViewConfiguration('author');
		$author->setKey('id');
		$this->registerView($author);

		parent::__construct($app, $menu);

		$this->attachRule(new MenuRules($this));
		$this->attachRule(new StandardRules($this));
		$this->attachRule(new NomenuRules($this));
	}

	/**
	 * Build the segment for a book id (id:alias).
	 *
	 * @param   string  $id     The book id (possibly id:alias).
	 * @param   array   $query  The current query.
	 *
	 * @return  array
	 */
	public function getBookSegment($id, $query)
	{
		if (strpos($id, ':') === false) {
			$dbId    = (int) $id;
			$dbQuery = $this->db->getQuery(true)
				->select($this->db->quoteName('alias'))
				->from($this->db->quoteName('#__booklist_books'))
				->where($this->db->quoteName('id') . ' = :id')
				->bind(':id', $dbId, ParameterType::INTEGER);
			$alias = $this->db->setQuery($dbQuery)->loadResult();

			if ($alias) {
				$id .= ':' . $alias;
			}
		}

		[$intId, $segment] = explode(':', $id, 2);

		return [(int) $intId => $segment];
	}

	/**
	 * Resolve a book segment back to an id.
	 *
	 * @param   string  $segment  The segment.
	 * @param   array   $query    The query.
	 *
	 * @return  int|false
	 */
	public function getBookId($segment, $query)
	{
		if (strpos($segment, ':') !== false) {
			[$id] = explode(':', $segment, 2);

			return (int) $id;
		}

		return (int) $segment;
	}

	/**
	 * Build the segment for an author id (id:alias).
	 *
	 * @param   string  $id     The author id (possibly id:alias).
	 * @param   array   $query  The current query.
	 *
	 * @return  array
	 */
	public function getAuthorSegment($id, $query)
	{
		if (strpos($id, ':') === false) {
			$dbId    = (int) $id;
			$dbQuery = $this->db->getQuery(true)
				->select($this->db->quoteName('alias'))
				->from($this->db->quoteName('#__booklist_authors'))
				->where($this->db->quoteName('id') . ' = :id')
				->bind(':id', $dbId, ParameterType::INTEGER);
			$alias = $this->db->setQuery($dbQuery)->loadResult();

			if ($alias) {
				$id .= ':' . $alias;
			}
		}

		[$intId, $segment] = explode(':', $id, 2);

		return [(int) $intId => $segment];
	}

	/**
	 * Resolve an author segment back to an id.
	 *
	 * @param   string  $segment  The segment.
	 * @param   array   $query    The query.
	 *
	 * @return  int|false
	 */
	public function getAuthorId($segment, $query)
	{
		if (strpos($segment, ':') !== false) {
			[$id] = explode(':', $segment, 2);

			return (int) $id;
		}

		return (int) $segment;
	}
}
