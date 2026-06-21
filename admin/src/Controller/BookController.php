<?php
/**
 * @package     Com_BooksList
 * @copyright   (C) 2026 Nickpsal
 * @license     GNU General Public License version 3 or later
 */

namespace Nickpsal\Component\BooksList\Administrator\Controller;

use Joomla\CMS\MVC\Controller\FormController;

\defined('_JEXEC') or die;

/**
 * Book form controller (single item).
 */
class BookController extends FormController
{
	/**
	 * The URL option for the component (declared explicitly because the element
	 * name "com_books_list" cannot be derived from the namespace "BooksList").
	 *
	 * @var string
	 */
	protected $option = 'com_books_list';
}
