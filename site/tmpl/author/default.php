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

/** @var \Nickpsal\Component\BooksList\Site\View\Author\HtmlView $this */

$item = $this->item;
?>
<div class="com-booklist-author booklist-author">
	<div class="row">
		<?php if (!empty($item->image)) : ?>
			<div class="col-md-3">
				<img class="booklist-author__photo img-fluid rounded" src="<?php echo $this->escape($item->image); ?>"
					alt="<?php echo $this->escape(trim($item->name . ' ' . $item->lastname)); ?>">
			</div>
		<?php endif; ?>

		<div class="<?php echo !empty($item->image) ? 'col-md-9' : 'col-12'; ?>">
			<h1><?php echo $this->escape(trim($item->name . ' ' . $item->lastname)); ?></h1>

			<?php if (!empty($item->description)) : ?>
				<div class="booklist-author__bio">
					<?php echo HTMLHelper::_('content.prepare', $item->description); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<?php if (!empty($item->books)) : ?>
		<div class="booklist-author__books mt-4">
			<h2><?php echo Text::_('COM_BOOKS_LIST_AUTHOR_BOOKS'); ?></h2>
			<div class="row">
				<?php foreach ($item->books as $book) : ?>
					<?php $link = Route::_(RouteHelper::getBookRoute((int) $book->id, (int) $book->catid, $book->language, $book->alias)); ?>
					<div class="col-6 col-md-3 mb-4">
						<a href="<?php echo $link; ?>" class="booklist-author__book text-decoration-none">
							<?php if (!empty($book->image)) : ?>
								<img class="img-fluid rounded mb-2" src="<?php echo $this->escape($book->image); ?>"
									alt="<?php echo $this->escape($book->title); ?>">
							<?php endif; ?>
							<div class="booklist-author__book-title"><?php echo $this->escape($book->title); ?></div>
							<?php if ($book->year) : ?>
								<small class="text-muted"><?php echo (int) $book->year; ?></small>
							<?php endif; ?>
						</a>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>

	<p class="mt-4">
		<a class="btn btn-outline-secondary" href="<?php echo Route::_('index.php?option=com_books_list&view=books'); ?>">
			&laquo; <?php echo Text::_('COM_BOOKS_LIST_BOOKS'); ?>
		</a>
	</p>
</div>
