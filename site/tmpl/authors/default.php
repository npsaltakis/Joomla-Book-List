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

/** @var \Nickpsal\Component\BooksList\Site\View\Authors\HtmlView $this */
?>
<div class="com-booklist-authors booklist-authors">
	<h1><?php echo $this->escape($this->params->get('page_heading', Text::_('COM_BOOKS_LIST_AUTHORS'))); ?></h1>

	<?php if (empty($this->items)) : ?>
		<p class="alert alert-info"><?php echo Text::_('COM_BOOKS_LIST_NO_AUTHORS'); ?></p>
	<?php else : ?>
		<div class="row">
			<?php foreach ($this->items as $author) : ?>
				<?php $link = Route::_(RouteHelper::getAuthorRoute((int) $author->id, $author->language, $author->alias)); ?>
				<?php $fullname = trim($author->name . ' ' . $author->lastname); ?>
				<div class="col-6 col-md-4 col-lg-3 mb-4">
					<a href="<?php echo $link; ?>" class="booklist-authors__item card h-100 text-decoration-none">
						<?php if (!empty($author->image)) : ?>
							<div class="booklist-authors__photo-wrap">
								<img class="booklist-authors__photo card-img-top" src="<?php echo $this->escape($author->image); ?>"
									alt="<?php echo $this->escape($fullname); ?>">
							</div>
						<?php endif; ?>
						<div class="card-body text-center">
							<div class="booklist-authors__name"><?php echo $this->escape($fullname); ?></div>
							<small class="text-muted"><?php echo Text::plural('COM_BOOKS_LIST_N_BOOKS', (int) $author->book_count); ?></small>
						</div>
					</a>
				</div>
			<?php endforeach; ?>
		</div>

		<?php echo $this->pagination->getListFooter(); ?>
	<?php endif; ?>
</div>
