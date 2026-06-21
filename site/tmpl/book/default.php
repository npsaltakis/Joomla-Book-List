<?php
/**
 * @package     Com_BooksList
 * @copyright   (C) 2026 Nickpsal
 * @license     GNU General Public License version 3 or later
 */

\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Nickpsal\Component\BooksList\Site\Helper\RouteHelper;

/** @var \Nickpsal\Component\BooksList\Site\View\Book\HtmlView $this */

$item = $this->item;
?>
<div class="com-booklist-book booklist-detail">
	<div class="row">
		<?php if (!empty($item->image)) : ?>
			<div class="col-md-4">
				<img class="booklist-detail__cover img-fluid rounded" src="<?php echo $this->escape($item->image); ?>"
					alt="<?php echo $this->escape($item->title); ?>">
			</div>
		<?php endif; ?>

		<div class="<?php echo !empty($item->image) ? 'col-md-8' : 'col-12'; ?>">
			<h1><?php echo $this->escape($item->title); ?></h1>
			<?php if (!empty($item->subtitle)) : ?>
				<p class="lead text-muted"><?php echo $this->escape($item->subtitle); ?></p>
			<?php endif; ?>

			<?php if (!empty($item->authors)) : ?>
				<p class="booklist-detail__authors">
					<strong><?php echo Text::_('COM_BOOKS_LIST_BY'); ?></strong>
					<?php
					$authorLinks = array_map(
						function ($a) {
							$name = $this->escape(trim($a->name . ' ' . $a->lastname));
							$url  = Route::_(RouteHelper::getAuthorRoute((int) $a->id, '*', $a->alias));

							return '<a href="' . $url . '">' . $name . '</a>';
						},
						$item->authors
					);
					echo implode(', ', $authorLinks);
					?>
				</p>
			<?php endif; ?>

			<table class="table table-striped booklist-detail__meta">
				<tbody>
				<?php if (!empty($item->category_title)) : ?>
					<tr><th><?php echo Text::_('COM_BOOKS_LIST_FILTER_CATEGORY'); ?></th>
						<td><a href="<?php echo Route::_(RouteHelper::getCategoryRoute((int) $item->catid)); ?>"><?php echo $this->escape($item->category_title); ?></a></td></tr>
				<?php endif; ?>
				<?php if (!empty($item->editor_name)) : ?>
					<tr><th><?php echo Text::_('COM_BOOKS_LIST_PUBLISHER'); ?></th><td><?php echo $this->escape($item->editor_name); ?></td></tr>
				<?php endif; ?>
				<?php if ($item->year) : ?>
					<tr><th><?php echo Text::_('COM_BOOKS_LIST_YEAR'); ?></th><td><?php echo (int) $item->year; ?></td></tr>
				<?php endif; ?>
				<?php if (!empty($item->isbn)) : ?>
					<tr><th><?php echo Text::_('COM_BOOKS_LIST_ISBN'); ?></th><td><?php echo $this->escape($item->isbn); ?></td></tr>
				<?php endif; ?>
				<?php if ($item->pages) : ?>
					<tr><th><?php echo Text::_('COM_BOOKS_LIST_PAGES'); ?></th><td><?php echo (int) $item->pages; ?></td></tr>
				<?php endif; ?>
				</tbody>
			</table>

			<?php if (!empty($item->url)) : ?>
				<a class="btn btn-success" href="<?php echo $this->escape($item->url); ?>" target="_blank" rel="noopener">
					<?php echo $item->url_label ? $this->escape($item->url_label) : Text::_('COM_BOOKS_LIST_BUY'); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>

	<?php if (!empty($item->description)) : ?>
		<div class="booklist-detail__description mt-4">
			<?php echo HTMLHelper::_('content.prepare', $item->description); ?>
		</div>
	<?php endif; ?>

	<p class="mt-4">
		<a class="btn btn-outline-secondary" href="<?php echo Route::_('index.php?option=com_books_list&view=books'); ?>">
			&laquo; <?php echo Text::_('COM_BOOKS_LIST_BOOKS'); ?>
		</a>
	</p>
</div>
