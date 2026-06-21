<?php
/**
 * @package     Com_BooksList
 * @copyright   (C) 2026 Nickpsal
 * @license     GNU General Public License version 3 or later
 */

namespace Nickpsal\Component\BooksList\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\Database\ParameterType;

\defined('_JEXEC') or die;

/**
 * Book admin model (single item).
 */
class BookModel extends AdminModel
{
	/**
	 * The type alias for content history / UCM.
	 *
	 * @var string
	 */
	public $typeAlias = 'com_books_list.book';

	/**
	 * Returns a Table object.
	 *
	 * @param   string  $type    The table type.
	 * @param   string  $prefix  The class prefix.
	 * @param   array   $config  Configuration array.
	 *
	 * @return  \Joomla\CMS\Table\Table
	 */
	public function getTable($type = 'Book', $prefix = 'Administrator', $config = [])
	{
		return parent::getTable($type, $prefix, $config);
	}

	/**
	 * Get the form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True to load form data.
	 *
	 * @return  Form|false
	 */
	public function getForm($data = [], $loadData = true)
	{
		$form = $this->loadForm(
			'com_books_list.book',
			'book',
			['control' => 'jform', 'load_data' => $loadData]
		);

		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Load form data from the session or the item.
	 *
	 * @return  mixed
	 */
	protected function loadFormData()
	{
		$app  = Factory::getApplication();
		$data = $app->getUserState('com_books_list.edit.book.data', []);

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Get a single book item, including its authors and tags.
	 *
	 * @param   integer  $pk  The id of the item.
	 *
	 * @return  object|false
	 */
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);

		if ($item === false) {
			return false;
		}

		if (!empty($item->id)) {
			$db    = $this->getDatabase();
			$query = $db->getQuery(true)
				->select($db->quoteName('author_id'))
				->from($db->quoteName('#__booklist_book_author'))
				->where($db->quoteName('book_id') . ' = :id')
				->bind(':id', $item->id, ParameterType::INTEGER)
				->order($db->quoteName('ordering') . ' ASC');

			$item->authors = $db->setQuery($query)->loadColumn();
		}

		return $item;
	}

	/**
	 * Save the book and its author relations.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean
	 */
	public function save($data)
	{
		$authors = [];

		if (isset($data['authors'])) {
			$authors = array_values(array_filter(array_map('intval', (array) $data['authors'])));
			unset($data['authors']);
		}

		if (!parent::save($data)) {
			return false;
		}

		$bookId = (int) $this->getState($this->getName() . '.id');

		if ($bookId) {
			$this->storeAuthors($bookId, $authors);
		}

		return true;
	}

	/**
	 * Replace the author relations for a book.
	 *
	 * @param   integer  $bookId   The book id.
	 * @param   array    $authors  Array of author ids.
	 *
	 * @return  void
	 */
	protected function storeAuthors(int $bookId, array $authors): void
	{
		$db = $this->getDatabase();

		$delete = $db->getQuery(true)
			->delete($db->quoteName('#__booklist_book_author'))
			->where($db->quoteName('book_id') . ' = :id')
			->bind(':id', $bookId, ParameterType::INTEGER);
		$db->setQuery($delete)->execute();

		$ordering = 0;

		foreach ($authors as $authorId) {
			$row = (object) [
				'book_id'   => $bookId,
				'author_id' => $authorId,
				'ordering'  => $ordering++,
			];
			$db->insertObject('#__booklist_book_author', $row);
		}
	}

	/**
	 * Also remove author relations when books are deleted.
	 *
	 * @param   array  &$pks  The primary keys to delete.
	 *
	 * @return  boolean
	 */
	public function delete(&$pks)
	{
		$pks = (array) $pks;

		if (parent::delete($pks)) {
			$db = $this->getDatabase();

			foreach ($pks as $pk) {
				$pk    = (int) $pk;
				$query = $db->getQuery(true)
					->delete($db->quoteName('#__booklist_book_author'))
					->where($db->quoteName('book_id') . ' = :id')
					->bind(':id', $pk, ParameterType::INTEGER);
				$db->setQuery($query)->execute();
			}

			return true;
		}

		return false;
	}
}
