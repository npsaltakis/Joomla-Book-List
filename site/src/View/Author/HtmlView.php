<?php
/**
 * @package     Com_BooksList
 * @copyright   (C) 2026 Nickpsal
 * @license     GNU General Public License version 3 or later
 */

namespace Nickpsal\Component\BooksList\Site\View\Author;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

\defined('_JEXEC') or die;

/**
 * Frontend single author view.
 */
class HtmlView extends BaseHtmlView
{
	protected $item;
	protected $params;
	protected $state;

	public function display($tpl = null)
	{
		/** @var \Nickpsal\Component\BooksList\Site\Model\AuthorModel $model */
		$model = $this->getModel();

		$this->item   = $model->getItem();
		$this->state  = $model->getState();
		$this->params = $this->state->get('params');

		$this->prepareDocument();

		parent::display($tpl);
	}

	protected function prepareDocument(): void
	{
		$title = trim($this->item->name . ' ' . $this->item->lastname);

		$this->getDocument()->setTitle($title);

		if (!empty($this->item->metadesc)) {
			$this->getDocument()->setDescription($this->item->metadesc);
		}

		$this->getDocument()->getWebAssetManager()
			->registerAndUseStyle('com_books_list.site', 'media/com_books_list/css/books_list.css');
	}
}
