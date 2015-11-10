<?php

/*
 * This file is part of the Rhein Neckar Rocks Crawler project.
 *
 * (c) Rhein Neckar Rocks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RNRocks;

use bitExpert\Disco\Annotations\Bean;
use \bitExpert\Disco\Annotations\Configuration;
use RNRocks\CliApplication;
use RNRocks\CrawlerCommand;
use RNRocks\Repository\RepositoryConfiguration;

/**
 * @Configuration
 */
class CrawlerConfiguration
{
    use RepositoryConfiguration;

    /**
     * @Bean
     * @return CliApplication
     */
    public function application()
    {
        $bean = new CliApplication($this->command());
        return $bean;
    }

    /**
     * @Bean
     * @return CrawlerCommand
     */
    public function command()
    {
        $beanFactory = \bitExpert\Disco\BeanFactoryRegistry::getInstance();

        $bean = new CrawlerCommand();
        $bean->setBeanFactory($beanFactory);
        return $bean;
    }
}
