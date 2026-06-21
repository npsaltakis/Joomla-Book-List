<?php
/**
 * @package     Com_BooksList
 * @copyright   (C) 2026 Nickpsal
 * @license     GNU General Public License version 3 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\CategoryFactory;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\Extension\Service\Provider\RouterFactory;
use Joomla\CMS\HTML\Registry;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Nickpsal\Component\BooksList\Administrator\Extension\BooksListComponent;

return new class () implements ServiceProviderInterface {
	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  void
	 */
	public function register(Container $container): void
	{
		$container->registerServiceProvider(new CategoryFactory('\\Nickpsal\\Component\\BooksList'));
		$container->registerServiceProvider(new MVCFactory('\\Nickpsal\\Component\\BooksList'));
		$container->registerServiceProvider(new ComponentDispatcherFactory('\\Nickpsal\\Component\\BooksList'));
		$container->registerServiceProvider(new RouterFactory('\\Nickpsal\\Component\\BooksList'));

		$container->set(
			ComponentInterface::class,
			function (Container $container) {
				$component = new BooksListComponent($container->get(ComponentDispatcherFactoryInterface::class));

				$component->setRegistry($container->get(Registry::class));
				$component->setMVCFactory($container->get(MVCFactoryInterface::class));
				$component->setCategoryFactory($container->get('Joomla\\CMS\\Categories\\CategoryFactoryInterface'));

				return $component;
			}
		);
	}
};
