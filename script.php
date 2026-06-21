<?php
/**
 * @package     Com_BooksList
 * @copyright   (C) 2026 Nickpsal
 * @license     GNU General Public License version 3 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class () implements ServiceProviderInterface {
	public function register(Container $container): void
	{
		$container->set(
			InstallerScriptInterface::class,
			new class (Factory::getApplication(), Factory::getContainer()->get(DatabaseInterface::class)) implements InstallerScriptInterface {
				private $minimumJoomla = '5.0.0';
				private $minimumPhp     = '8.1.0';
				private $app;
				private $db;

				public function __construct($app, $db)
				{
					$this->app = $app;
					$this->db  = $db;
				}

				public function install(InstallerAdapter $adapter): bool
				{
					$this->app->enqueueMessage(Text::_('COM_BOOKS_LIST_INSTALL_SUCCESS'), 'message');

					return true;
				}

				public function update(InstallerAdapter $adapter): bool
				{
					return true;
				}

				public function uninstall(InstallerAdapter $adapter): bool
				{
					return true;
				}

				public function preflight(string $type, InstallerAdapter $adapter): bool
				{
					if (version_compare(PHP_VERSION, $this->minimumPhp, '<')) {
						$this->app->enqueueMessage(
							sprintf('PHP %s or newer is required to install com_books_list.', $this->minimumPhp),
							'error'
						);

						return false;
					}

					if (version_compare(JVERSION, $this->minimumJoomla, '<')) {
						$this->app->enqueueMessage(
							sprintf('Joomla %s or newer is required to install com_books_list.', $this->minimumJoomla),
							'error'
						);

						return false;
					}

					return true;
				}

				public function postflight(string $type, InstallerAdapter $adapter): bool
				{
					return true;
				}
			}
		);
	}
};
