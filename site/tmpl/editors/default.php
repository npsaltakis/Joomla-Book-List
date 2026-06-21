<?php
/**
 * @package     Com_BooksList
 * @copyright   (C) 2026 Nickpsal
 * @license     GNU General Public License version 3 or later
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Nickpsal\Component\BooksList\Site\Helper\RouteHelper;

/** @var \Nickpsal\Component\BooksList\Site\View\Editors\HtmlView $this */
?>
<div class="com-booklist-editors booklist-editors">
	<h1><?php echo $this->escape($this->params->get('page_heading', Text::_('COM_BOOKS_LIST_EDITORS'))); ?></h1>

	<?php if (empty($this->items)) : ?>
		<p class="alert alert-info"><?php echo Text::_('COM_BOOKS_LIST_NO_EDITORS'); ?></p>
	<?php else : ?>
		<ul class="list-group booklist-editors__list">
			<?php foreach ($this->items as $editor) : ?>
				<?php $link = Route::_(RouteHelper::getEditorRoute((int) $editor->id, $editor->language)); ?>
				<li class="list-group-item d-flex justify-content-between align-items-center">
					<a href="<?php echo $link; ?>"><?php echo $this->escape($editor->name); ?></a>
					<span class="badge bg-secondary rounded-pill"><?php echo (int) $editor->book_count; ?></span>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php echo $this->pagination->getListFooter(); ?>
	<?php endif; ?>
</div>
