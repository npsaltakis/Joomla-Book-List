<?php
/**
 * @package     Com_BooksList
 * @copyright   (C) 2026 Nickpsal
 * @license     GNU General Public License version 3 or later
 */

namespace Nickpsal\Component\BooksList\Administrator\Table;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\Event\DispatcherInterface;

\defined('_JEXEC') or die;

/**
 * Author table.
 */
class AuthorTable extends Table
{
	protected $_supportNullValue = true;

	public $typeAlias = 'com_books_list.author';

	public function __construct(DatabaseDriver $db, DispatcherInterface $dispatcher = null)
	{
		$this->setColumnAlias('published', 'state');

		parent::__construct('#__booklist_authors', 'id', $db, $dispatcher);
	}

	public function check()
	{
		try {
			parent::check();
		} catch (\Exception $e) {
			$this->setError($e->getMessage());

			return false;
		}

		$this->lastname = htmlspecialchars_decode($this->lastname, ENT_QUOTES);

		if (trim($this->lastname) === '' && trim($this->name) === '') {
			$this->setError(Text::_('COM_BOOKS_LIST_WARNING_PROVIDE_VALID_AUTHOR'));

			return false;
		}

		if (trim($this->alias) === '') {
			$this->alias = trim($this->name . ' ' . $this->lastname);
		}

		$this->alias = ApplicationHelper::stringURLSafe($this->alias, $this->language);

		if (trim(str_replace('-', '', $this->alias)) === '') {
			$this->alias = Factory::getDate()->format('Y-m-d-H-i-s');
		}

		return true;
	}
}
