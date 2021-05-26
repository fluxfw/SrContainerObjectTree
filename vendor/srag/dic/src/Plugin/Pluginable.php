<?php

namespace srag\DIC\SrContainerObjectTree\Plugin;

/**
 * Interface Pluginable
 *
 * @package srag\DIC\SrContainerObjectTree\Plugin
 */
interface Pluginable
{

    /**
     * @return PluginInterface
     */
    public function getPlugin() : PluginInterface;


    /**
     * @param PluginInterface $plugin
     *
     * @return static
     */
    public function withPlugin(PluginInterface $plugin)/*: static*/ ;
}
