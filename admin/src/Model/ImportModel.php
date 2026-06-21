<?php
/**
 * @package     Com_BooksList
 * @copyright   (C) 2026 Nickpsal
 * @license     GNU General Public License version 3 or later
 */

namespace Nickpsal\Component\BooksList\Administrator\Model;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\Database\ParameterType;
use Nickpsal\Component\BooksList\Administrator\Helper\SpreadsheetReader;

\defined('_JEXEC') or die;

/**
 * Import model: turns an uploaded spreadsheet into book records.
 */
class ImportModel extends BaseDatabaseModel
{
	/**
	 * Recognised header aliases mapped to internal book columns.
	 *
	 * @var array<string,string>
	 */
	protected $headerMap = [
		'title'        => 'title',
		'subtitle'     => 'subtitle',
		'isbn'         => 'isbn',
		'issn'         => 'issn',
		'year'         => 'year',
		'pages'        => 'pages',
		'language'     => 'language_book',
		'price'        => 'price',
		'description'  => 'description',
		'url'          => 'url',
		'category'     => '__category',
		'editor'       => '__editor',
		'publisher'    => '__editor',
		'author'       => '__authors',
		'authors'      => '__authors',
	];

	/**
	 * Import books from an uploaded file.
	 *
	 * @param   array  $file  The $_FILES entry.
	 *
	 * @return  int  Number of imported books.
	 *
	 * @throws  \RuntimeException
	 */
	public function import(array $file): int
	{
		if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
			throw new \RuntimeException('COM_BOOKS_LIST_IMPORT_ERROR_NOFILE');
		}

		$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

		if (!in_array($extension, ['xlsx', 'csv'], true)) {
			throw new \RuntimeException('COM_BOOKS_LIST_IMPORT_ERROR_TYPE');
		}

		$rows = SpreadsheetReader::read($file['tmp_name'], $extension);

		if (count($rows) < 2) {
			return 0;
		}

		// First row = header. Build a column index -> book field map.
		$header = array_shift($rows);
		$map    = [];

		foreach ($header as $idx => $name) {
			$key = strtolower(trim((string) $name));

			if (isset($this->headerMap[$key])) {
				$map[$idx] = $this->headerMap[$key];
			}
		}

		if (!in_array('title', $map, true)) {
			throw new \RuntimeException('COM_BOOKS_LIST_IMPORT_ERROR_NOTITLE');
		}

		$count = 0;

		foreach ($rows as $row) {
			if ($this->importRow($row, $map)) {
				$count++;
			}
		}

		return $count;
	}

	/**
	 * Import a single data row.
	 *
	 * @param   array  $row  Cell values.
	 * @param   array  $map  Column index -> field map.
	 *
	 * @return  bool  True if a book was created.
	 */
	protected function importRow(array $row, array $map): bool
	{
		$data    = [];
		$authors = '';
		$catName = '';
		$editor  = '';

		foreach ($map as $idx => $field) {
			$value = $row[$idx] ?? '';

			switch ($field) {
				case '__authors':
					$authors = (string) $value;
					break;
				case '__category':
					$catName = trim((string) $value);
					break;
				case '__editor':
					$editor = trim((string) $value);
					break;
				default:
					$data[$field] = $value;
			}
		}

		$data['title'] = trim((string) ($data['title'] ?? ''));

		if ($data['title'] === '') {
			return false;
		}

		$db   = $this->getDatabase();
		$user = Factory::getApplication()->getIdentity();
		$now  = Factory::getDate()->toSql();

		$book = (object) [
			'title'         => $data['title'],
			'subtitle'      => trim((string) ($data['subtitle'] ?? '')),
			'alias'         => ApplicationHelper::stringURLSafe($data['title']) ?: Factory::getDate()->format('Y-m-d-H-i-s'),
			'isbn'          => trim((string) ($data['isbn'] ?? '')),
			'issn'          => trim((string) ($data['issn'] ?? '')),
			'year'          => (int) ($data['year'] ?? 0),
			'pages'         => (int) ($data['pages'] ?? 0),
			'language_book' => trim((string) ($data['language_book'] ?? '')),
			'price'         => (float) str_replace(',', '.', (string) ($data['price'] ?? 0)),
			'description'   => (string) ($data['description'] ?? ''),
			'url'           => trim((string) ($data['url'] ?? '')),
			'catid'         => $catName !== '' ? $this->resolveCategory($catName) : 0,
			'editor_id'     => $editor !== '' ? $this->resolveEditor($editor) : 0,
			'state'         => 1,
			'access'        => 1,
			'language'      => '*',
			'created'       => $now,
			'created_by'    => (int) ($user->id ?? 0),
			'modified'      => $now,
		];

		$db->insertObject('#__booklist_books', $book, 'id');
		$bookId = (int) $book->id;

		if ($authors !== '') {
			$this->attachAuthors($bookId, $authors);
		}

		return true;
	}

	/**
	 * Find or create an editor by name.
	 *
	 * @param   string  $name  Editor name.
	 *
	 * @return  int
	 */
	protected function resolveEditor(string $name): int
	{
		$db    = $this->getDatabase();
		$query = $db->getQuery(true)
			->select($db->quoteName('id'))
			->from($db->quoteName('#__booklist_editors'))
			->where($db->quoteName('name') . ' = :name')
			->bind(':name', $name)
			->setLimit(1);

		$id = (int) $db->setQuery($query)->loadResult();

		if ($id) {
			return $id;
		}

		$editor = (object) [
			'name'  => $name,
			'alias' => ApplicationHelper::stringURLSafe($name) ?: md5($name),
			'state' => 1,
			'access' => 1,
			'language' => '*',
		];
		$db->insertObject('#__booklist_editors', $editor, 'id');

		return (int) $editor->id;
	}

	/**
	 * Find or create a category (under the component root) by title.
	 *
	 * @param   string  $title  Category title.
	 *
	 * @return  int
	 */
	protected function resolveCategory(string $title): int
	{
		$db    = $this->getDatabase();
		$query = $db->getQuery(true)
			->select($db->quoteName('id'))
			->from($db->quoteName('#__categories'))
			->where($db->quoteName('extension') . ' = ' . $db->quote('com_books_list'))
			->where($db->quoteName('title') . ' = :title')
			->bind(':title', $title)
			->setLimit(1);

		$id = (int) $db->setQuery($query)->loadResult();

		if ($id) {
			return $id;
		}

		// Create the category using the core Categories table (nested set).
		$category = Factory::getApplication()
			->bootComponent('com_categories')
			->getMVCFactory()
			->createTable('Category', 'Administrator');

		$category->setLocation(1, 'last-child');
		$category->bind([
			'title'     => $title,
			'alias'     => ApplicationHelper::stringURLSafe($title) ?: md5($title),
			'extension' => 'com_books_list',
			'published' => 1,
			'access'    => 1,
			'params'    => '{}',
			'metadata'  => '{}',
			'language'  => '*',
		]);

		if ($category->check() && $category->store()) {
			$category->rebuildPath($category->id);

			return (int) $category->id;
		}

		return 0;
	}

	/**
	 * Parse an authors string ("Last Name; Last2 Name2") and link them.
	 *
	 * @param   int     $bookId   The new book id.
	 * @param   string  $authors  Raw authors string.
	 *
	 * @return  void
	 */
	protected function attachAuthors(int $bookId, string $authors): void
	{
		$db    = $this->getDatabase();
		$parts = preg_split('/[;,]/', $authors);
		$order = 0;

		foreach ($parts as $part) {
			$part = trim($part);

			if ($part === '') {
				continue;
			}

			$authorId = $this->resolveAuthor($part);

			if ($authorId) {
				$link = (object) [
					'book_id'   => $bookId,
					'author_id' => $authorId,
					'ordering'  => $order++,
				];
				$db->insertObject('#__booklist_book_author', $link);
			}
		}
	}

	/**
	 * Find or create an author from a "name lastname" string.
	 *
	 * @param   string  $full  The author full name.
	 *
	 * @return  int
	 */
	protected function resolveAuthor(string $full): int
	{
		// Heuristic: last token is the last name, the rest is the first name.
		$tokens   = preg_split('/\s+/', $full);
		$lastname = array_pop($tokens);
		$name     = implode(' ', $tokens);

		$db    = $this->getDatabase();
		$query = $db->getQuery(true)
			->select($db->quoteName('id'))
			->from($db->quoteName('#__booklist_authors'))
			->where($db->quoteName('lastname') . ' = :ln')
			->where($db->quoteName('name') . ' = :nm')
			->bind(':ln', $lastname)
			->bind(':nm', $name)
			->setLimit(1);

		$id = (int) $db->setQuery($query)->loadResult();

		if ($id) {
			return $id;
		}

		$author = (object) [
			'name'     => $name,
			'lastname' => $lastname,
			'alias'    => ApplicationHelper::stringURLSafe(trim($name . ' ' . $lastname)) ?: md5($full),
			'state'    => 1,
			'access'   => 1,
			'language' => '*',
		];
		$db->insertObject('#__booklist_authors', $author, 'id');

		return (int) $author->id;
	}
}
