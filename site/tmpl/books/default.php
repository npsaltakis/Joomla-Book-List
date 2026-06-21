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
use Nickpsal\Component\BooksList\Site\Helper\BooksListHelper;
use Nickpsal\Component\BooksList\Site\Helper\RouteHelper;

/** @var \Nickpsal\Component\BooksList\Site\View\Books\HtmlView $this */

$params    = $this->params;
$showCover = (int) $params->get('show_cover', 1);
$showFilt  = (int) $params->get('show_filters', 1);

$selCat    = (int) $this->state->get('filter.catid');
$selAuthor = (int) $this->state->get('filter.author');
$selEditor = (int) $this->state->get('filter.editor');
$selYear   = (int) $this->state->get('filter.year');
$search    = $this->state->get('filter.search');
?>
<div class="com-booklist booklist">
	<?php if ($this->params->get('show_page_heading')) : ?>
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	<?php endif; ?>

	<?php if ($showFilt) : ?>
		<form action="<?php echo Route::_('index.php?option=com_books_list&view=books'); ?>" method="post" class="booklist-filters mb-4">
			<input type="text" name="filter_search" class="form-control" style="max-width:240px"
				value="<?php echo $this->escape($search); ?>" placeholder="<?php echo Text::_('COM_BOOKS_LIST_SEARCH_PLACEHOLDER'); ?>">

			<select name="filter_catid" class="form-select" style="max-width:200px">
				<option value=""><?php echo Text::_('COM_BOOKS_LIST_FILTER_CATEGORY'); ?></option>
				<?php foreach (BooksListHelper::getCategories() as $cat) : ?>
					<option value="<?php echo (int) $cat->id; ?>" <?php echo $selCat === (int) $cat->id ? 'selected' : ''; ?>>
						<?php echo str_repeat('— ', max(0, (int) $cat->level - 1)) . $this->escape($cat->title); ?>
					</option>
				<?php endforeach; ?>
			</select>

			<select name="filter_author" class="form-select" style="max-width:200px">
				<option value=""><?php echo Text::_('COM_BOOKS_LIST_FILTER_AUTHOR'); ?></option>
				<?php foreach (BooksListHelper::getAuthors() as $a) : ?>
					<option value="<?php echo (int) $a->id; ?>" <?php echo $selAuthor === (int) $a->id ? 'selected' : ''; ?>>
						<?php echo $this->escape($a->title); ?>
					</option>
				<?php endforeach; ?>
			</select>

			<select name="filter_editor" class="form-select" style="max-width:200px">
				<option value=""><?php echo Text::_('COM_BOOKS_LIST_FILTER_EDITOR'); ?></option>
				<?php foreach (BooksListHelper::getEditors() as $e) : ?>
					<option value="<?php echo (int) $e->id; ?>" <?php echo $selEditor === (int) $e->id ? 'selected' : ''; ?>>
						<?php echo $this->escape($e->title); ?>
					</option>
				<?php endforeach; ?>
			</select>

			<select name="filter_year" class="form-select" style="max-width:130px">
				<option value=""><?php echo Text::_('COM_BOOKS_LIST_FILTER_YEAR'); ?></option>
				<?php foreach (BooksListHelper::getYears() as $y) : ?>
					<option value="<?php echo (int) $y; ?>" <?php echo $selYear === (int) $y ? 'selected' : ''; ?>><?php echo (int) $y; ?></option>
				<?php endforeach; ?>
			</select>

			<button type="submit" class="btn btn-primary"><?php echo Text::_('COM_BOOKS_LIST_SEARCH'); ?></button>
		</form>
	<?php endif; ?>

	<?php if (empty($this->items)) : ?>
		<div class="alert alert-info"><?php echo Text::_('COM_BOOKS_LIST_NO_BOOKS'); ?></div>
	<?php else : ?>
		<div class="booklist-grid">
			<?php foreach ($this->items as $item) : ?>
				<?php
				$link    = Route::_(RouteHelper::getBookRoute($item->id, $item->catid, $item->language, $item->alias));
				$authors = $this->authorsByBook[$item->id] ?? [];
				?>
				<article class="booklist-card">
					<?php if ($showCover) : ?>
						<a class="booklist-card__cover" href="<?php echo $link; ?>">
							<?php if (!empty($item->image)) : ?>
								<img src="<?php echo $this->escape($item->image); ?>" alt="<?php echo $this->escape($item->title); ?>" loading="lazy">
							<?php else : ?>
								<span class="booklist-card__nocover d-flex align-items-center justify-content-center" style="height:280px;background:#f2f2f2">
									<span class="icon-book" aria-hidden="true"></span>
								</span>
							<?php endif; ?>
						</a>
					<?php endif; ?>
					<div class="booklist-card__body">
						<h3 class="booklist-card__title">
							<a href="<?php echo $link; ?>"><?php echo $this->escape($item->title); ?></a>
						</h3>
						<?php if (!empty($item->subtitle)) : ?>
							<div class="booklist-card__meta"><?php echo $this->escape($item->subtitle); ?></div>
						<?php endif; ?>
						<?php if ($authors) : ?>
							<div class="booklist-card__meta">
								<?php echo Text::_('COM_BOOKS_LIST_BY'); ?>
								<?php echo implode(', ', array_map(fn ($a) => $this->escape(trim($a->name . ' ' . $a->lastname)), $authors)); ?>
							</div>
						<?php endif; ?>
						<div class="booklist-card__meta">
							<?php if ($item->year) : ?><span><?php echo (int) $item->year; ?></span><?php endif; ?>
							<?php if (!empty($item->editor_name)) : ?> · <span><?php echo $this->escape($item->editor_name); ?></span><?php endif; ?>
						</div>
					</div>
				</article>
			<?php endforeach; ?>
		</div>

		<div class="mt-4">
			<?php echo $this->pagination->getListFooter(); ?>
		</div>
	<?php endif; ?>
</div>
