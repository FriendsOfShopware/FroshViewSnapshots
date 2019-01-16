<?php

namespace FroshViewSnapshots;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class FroshViewSnapshots
 */
class FroshViewSnapshots extends Plugin
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $container->setParameter('frosh_view_snapshots.plugin_dir', $this->getPath());

        parent::build($container);
    }

    /**
     * @param InstallContext $context
     *
     * @throws \Exception
     */
    public function install(InstallContext $context)
    {
        $sql = file_get_contents($this->getPath() . '/Resources/sql/install.sql');

        $this->container->get('dbal_connection')->query($sql);
    }

    /**
     * @param ActivateContext $context
     */
    public function activate(ActivateContext $context)
    {
        $context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);
    }

    /**
     * @param UpdateContext $context
     *
     * @throws \Exception
     */
    public function update(UpdateContext $context)
    {
        $currentVersion = $context->getCurrentVersion();
        $sql = '';

        if (version_compare($currentVersion, '1.1.0', '<')) {
            $sql .= file_get_contents($this->getPath() . '/Resources/sql/update.1.1.0.sql');

            $this->container->get('dbal_connection')->query($sql);
        }

        $context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);
    }

    /**
     * @param UninstallContext $context
     *
     * @throws \Exception
     */
    public function uninstall(UninstallContext $context)
    {
        $sql = file_get_contents($this->getPath() . '/Resources/sql/uninstall.sql');

        $this->container->get('dbal_connection')->query($sql);

        $context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);
    }
}
