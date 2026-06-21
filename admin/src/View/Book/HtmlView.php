<?php
/**
 * @package     Com_BooksList
 * @copyright   (C) 2026 Nickpsal
 * @license     GNU General Public License version 3 or later
 */

namespace Nickpsal\Component\BooksList\Administrator\View\Book;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

\defined('_JEXEC') or die;

/**
 * View class for editing a single book.
 */
class HtmlView extends BaseHtmlView
{
	protected $form;
	protected $item;
	protected $state;

	public function display($tpl = null)
	{
		$this->form  = $this->get('Form');
		$this->item  = $this->get('Item');
		$this->state = $this->get('State');

		if (\count($errors = $this->get('Errors'))) {
			throw new \RuntimeException(implode("\n", $errors), 500);
		}

		$this->addToolbar();

		parent::display($tpl);
	}

	protected function addToolbar(): void
	{
		Factory::getApplication()->getInput()->set('hidemainmenu', true);

		$isNew = ($this->item->id == 0);

		ToolbarHelper::title(
			Text::_($isNew ? 'COM_BOOKS_LIST_MANAGER_BOOK_NEW' : 'COM_BOOKS_LIST_MANAGER_BOOK_EDIT'),
			'book'
		);

		$toolbar = $this->getDocument()->getToolbar();

		$toolbar->apply('book.apply');

		$saveGroup = $toolbar->dropdownButton('save-group');
		$saveGroup->configure(
			function ($childBar) use ($isNew) {
				$childBar->save('book.save');
				$childBar->save2new('book.save2new');

				if (!$isNew) {
					$childBar->save2copy('book.save2copy');
				}
			}
		);

		$toolbar->cancel('book.cancel');
	}
}
