<?php
/**
 * @package     Com_BooksList
 * @copyright   (C) 2026 Nickpsal
 * @license     GNU General Public License version 3 or later
 */

namespace Nickpsal\Component\BooksList\Administrator\View\Books;

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

\defined('_JEXEC') or die;

/**
 * View class for a list of books.
 */
class HtmlView extends BaseHtmlView
{
	protected $items;
	protected $pagination;
	protected $state;
	public $filterForm;
	public $activeFilters;

	public function display($tpl = null)
	{
		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->state         = $this->get('State');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		if (\count($errors = $this->get('Errors'))) {
			throw new \RuntimeException(implode("\n", $errors), 500);
		}

		$this->addToolbar();

		parent::display($tpl);
	}

	protected function addToolbar(): void
	{
		$canDo = ContentHelper::getActions('com_books_list');

		ToolbarHelper::title(Text::_('COM_BOOKS_LIST_MANAGER_BOOKS'), 'book');

		$toolbar = $this->getDocument()->getToolbar();

		if ($canDo->get('core.create')) {
			$toolbar->addNew('book.add');
		}

		if ($canDo->get('core.edit.state')) {
			$dropdown = $toolbar->dropdownButton('status-group')
				->text('JTOOLBAR_CHANGE_STATUS')
				->toggleSplit(false)
				->icon('icon-ellipsis-h')
				->buttonClass('btn btn-action')
				->listCheck(true);

			$childBar = $dropdown->getChildToolbar();
			$childBar->publish('books.publish')->listCheck(true);
			$childBar->unpublish('books.unpublish')->listCheck(true);
			$childBar->trash('books.trash')->listCheck(true);
		}

		if ($canDo->get('core.delete')) {
			$toolbar->delete('books.delete')
				->text('JTOOLBAR_EMPTY_TRASH')
				->message('JGLOBAL_CONFIRM_DELETE')
				->listCheck(true);
		}

		if ($canDo->get('core.admin') || $canDo->get('core.options')) {
			$toolbar->preferences('com_books_list');
		}
	}
}
