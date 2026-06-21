<?php
/**
 * @package     Com_BooksList
 * @copyright   (C) 2026 Nickpsal
 * @license     GNU General Public License version 3 or later
 */

namespace Nickpsal\Component\BooksList\Site\View\Authors;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

\defined('_JEXEC') or die;

/**
 * Frontend authors list view.
 */
class HtmlView extends BaseHtmlView
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $params;

	public function display($tpl = null)
	{
		/** @var \Nickpsal\Component\BooksList\Site\Model\AuthorsModel $model */
		$model = $this->getModel();

		$this->items      = $model->getItems();
		$this->pagination = $model->getPagination();
		$this->state      = $model->getState();
		$this->params     = $this->state->get('params');

		if (\count($errors = $model->getErrors())) {
			throw new \RuntimeException(implode("\n", $errors), 500);
		}

		$this->prepareDocument();

		parent::display($tpl);
	}

	protected function prepareDocument(): void
	{
		$title = $this->params->get('page_title', '');

		if (empty($title)) {
			$title = Text::_('COM_BOOKS_LIST_AUTHORS');
		}

		$this->getDocument()->setTitle($title);
		$this->getDocument()->getWebAssetManager()
			->registerAndUseStyle('com_books_list.site', 'media/com_books_list/css/books_list.css');
	}
}
