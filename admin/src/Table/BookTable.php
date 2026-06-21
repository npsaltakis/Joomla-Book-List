<?php
/**
 * @package     Com_BooksList
 * @copyright   (C) 2026 Nickpsal
 * @license     GNU General Public License version 3 or later
 */

namespace Nickpsal\Component\BooksList\Administrator\Table;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Tag\TaggableTableInterface;
use Joomla\CMS\Tag\TaggableTableTrait;
use Joomla\Database\DatabaseDriver;
use Joomla\Event\DispatcherInterface;

\defined('_JEXEC') or die;

/**
 * Book table.
 *
 * @property int    $id
 * @property string $title
 * @property string $alias
 */
class BookTable extends Table implements TaggableTableInterface
{
	use TaggableTableTrait;

	/**
	 * Indicates that columns fully support the NULL value in the database.
	 *
	 * @var boolean
	 */
	protected $_supportNullValue = true;

	/**
	 * The type alias for UCM (tags, content history).
	 *
	 * @var string
	 */
	public $typeAlias = 'com_books_list.book';

	public function __construct(DatabaseDriver $db, DispatcherInterface $dispatcher = null)
	{
		$this->setColumnAlias('published', 'state');

		parent::__construct('#__booklist_books', 'id', $db, $dispatcher);
	}

	/**
	 * Get the type alias for the tags mapping table.
	 *
	 * @return  string
	 */
	public function getTypeAlias()
	{
		return $this->typeAlias;
	}

	/**
	 * Overloaded check function.
	 *
	 * @return  boolean
	 */
	public function check()
	{
		try {
			parent::check();
		} catch (\Exception $e) {
			$this->setError($e->getMessage());

			return false;
		}

		$this->title = htmlspecialchars_decode($this->title, ENT_QUOTES);

		if (trim($this->title) === '') {
			$this->setError(Text::_('COM_BOOKS_LIST_WARNING_PROVIDE_VALID_TITLE'));

			return false;
		}

		if (trim($this->alias) === '') {
			$this->alias = $this->title;
		}

		$this->alias = ApplicationHelper::stringURLSafe($this->alias, $this->language);

		if (trim(str_replace('-', '', $this->alias)) === '') {
			$this->alias = Factory::getDate()->format('Y-m-d-H-i-s');
		}

		// Normalise nullable datetime columns: empty strings must become NULL.
		if (empty($this->publish_up)) {
			$this->publish_up = null;
		}

		if (empty($this->publish_down)) {
			$this->publish_down = null;
		}

		if (empty($this->checked_out_time)) {
			$this->checked_out_time = null;
		}

		if (empty($this->checked_out)) {
			$this->checked_out = null;
		}

		return true;
	}

	/**
	 * Generate a valid alias from the title.
	 *
	 * @return  string
	 */
	protected function generateAlias()
	{
		if (empty($this->alias)) {
			$this->alias = $this->title;
		}

		$this->alias = ApplicationHelper::stringURLSafe($this->alias, $this->language);

		if (trim(str_replace('-', '', $this->alias)) === '') {
			$this->alias = Factory::getDate()->format('Y-m-d-H-i-s');
		}

		return $this->alias;
	}
}
