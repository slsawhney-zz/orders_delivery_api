<?php

namespace App;

use Slim\App;
use App\Loader\ServiceLoader;
use Symfony\Component\Finder\Finder;

/**
 * {@inheritdoc}
 */
class Application extends App
{
    public function __construct($container = [])
    {
        parent::__construct($container);
        $this->loadServices();
    }

    private function loadServices()
    {
        $serviceLoader = new ServiceLoader(new Finder());
        $serviceLoader->loadServices(
            $this,
            $this->getContainer()->get('settings')['service_directories'] // Load all services in given Directory
        );
    }

    /**
     * Get root directory.
     *
     * @return string
     *
     * @throws ConstantNotSetException
     */
    public function getRootDir()
    {
        if (!defined('APP_ROOT')) {
            throw new ConstantNotSetException('Application root not defined.');
        }

        return APP_ROOT;
    }
}
