<?php
/**
 * @package     Com_BooksList
 * @copyright   (C) 2026 Nickpsal
 * @license     GNU General Public License version 3 or later
 */

namespace Nickpsal\Component\BooksList\Site\View\Books;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

\defined('_JEXEC') or die;

/**
 * Frontend books list view.
 */
class HtmlView extends BaseHtmlView
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $params;
	protected $authorsByBook = [];

	public function display($tpl = null)
	{
		/** @var \Nickpsal\Component\BooksList\Site\Model\BooksModel $model */
		$model = $this->getModel();

		$this->items         = $model->getItems();
		$this->pagination    = $model->getPagination();
		$this->state         = $model->getState();
		$this->params        = $this->state->get('params');
		$this->authorsByBook = $model->getAuthorsForItems();

		if (\count($errors = $model->getErrors())) {
			throw new \RuntimeException(implode("\n", $errors), 500);
		}

		$this->prepareDocument();

		parent::display($tpl);
	}

	protected function prepareDocument(): void
	{
		$app   = Factory::getApplication();
		$title = $this->params->get('page_title', '');

		if (empty($title)) {
			$title = Text::_('COM_BOOKS_LIST_BOOKS');
		}

		$this->getDocument()->setTitle($title);
		$this->getDocument()->getWebAssetManager()
			->registerAndUseStyle('com_books_list.site', 'media/com_books_list/css/books_list.css')
			->registerAndUseScript('com_books_list.site', 'media/com_books_list/js/books_list.js');
	}
}
