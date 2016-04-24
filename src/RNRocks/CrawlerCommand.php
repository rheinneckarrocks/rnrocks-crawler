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

use bitExpert\Disco\BeanFactory;
use bitExpert\Disco\BeanFactoryAware;
use RNRocks\Repository\SculpinUsergroupRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CrawlerCommand extends Command implements BeanFactoryAware
{
    /**
     * @var BeanFactory
     */
    protected $beanFactory;

    /**
     * {@inheritDoc}
     */
    public function setBeanFactory(BeanFactory $beanFactory)
    {
        $this->beanFactory = $beanFactory;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('crawler')
            ->setDescription('Crawler to automate the event retrieval process')
            ->setDefinition(
                array(
                    new InputArgument('sourceDir', InputArgument::REQUIRED, 'The source directory containing the sculpin files'),
                    new InputArgument('outputDir', InputArgument::REQUIRED, 'The directory where to write the files')
                )
            );
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sourceDir = $input->getArgument('sourceDir');
        $outputDir = $input->getArgument('outputDir');

        $curDate = strtotime(date('Y-m-d'));
        $usergroupRepository = new SculpinUsergroupRepository($sourceDir);
        $usergroups = $usergroupRepository->getUsergroups();
        foreach ($usergroups as $usergroup) {
            /** @var Usergroup $usergroup */
            $output->writeln(sprintf('Processing user group "%s"', $usergroup->getSlug()));

            if (!$this->beanFactory->has($usergroup->getRepoType())) {
                $output->writeln(sprintf('No implementation found for repository type "%s".', $usergroup->getRepoType()));
                continue;
            }

            $output->writeln(sprintf('Removing events for user group "%s"', $usergroup->getSlug()));
            $removedEvents = 0;
            $iterator = new \DirectoryIterator($outputDir);
            foreach ($iterator as $fileInfo) {
                if (false !== strpos($fileInfo->getFilename(), '_'.$usergroup->getSlug())) {
                    @unlink($fileInfo->getRealPath());
                    $removedEvents++;
                }
            }
            $output->writeln(sprintf('Removed "%s" events for user group "%s"', $removedEvents, $usergroup->getSlug()));

            $eventRepository = $this->beanFactory->get($usergroup->getRepoType());
            $usergroupEvents = $eventRepository->getEvents($usergroup->getEventLink());
            $output->writeln(sprintf('Found "%s" new events for user group "%s"', count($usergroupEvents), $usergroup->getSlug()));
            foreach ($usergroupEvents as $usergroupEvent) {
                /** @var Event $usergroupEvent */
                if (strtotime($usergroupEvent->getDate()) >= $curDate) {
                    // write event to disk in the format sculpin expects it
                    $outputFile = $outputDir . '/' . $usergroupEvent->getDate() . '_' . $usergroup->getSlug() . '.md';
                    $content = '---'. "\n";
                    $content .= 'title: '.$this->strip($usergroupEvent->getTitle()). "\n";
                    $content .= 'date: '.$usergroupEvent->getDate(). "\n";
                    $content .= 'location: '.$this->strip($usergroupEvent->getLocation()). "\n";
                    $content .= 'link: '.$usergroupEvent->getLink(). "\n";
                    $content .= 'usergroup: '.$usergroup->getSlug(). "\n";
                    $content .= 'type: usergroup'. "\n";
                    $content .= '---'. "\n";

                    file_put_contents($outputFile, $content);
                    $output->writeln(sprintf('Dumped event "%s" for user group "%s"', $usergroupEvent->getTitle(), $usergroup->getSlug()));
                }
            }
        }

        $output->writeln('Removing past non-usergroup events');
        $removedEvents = 0;
        // clean up old events that are not connected to a specific user group (e.g. a conference)
        $yesterday = strtotime('-1 day');
        foreach ($iterator as $fileInfo) {
            $matches = [];
            if (preg_match('#^([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})_.+$#', $fileInfo->getFilename(), $matches)) {
                if (isset($matches[1])) {
                    if (strtotime($matches[1]) <= $yesterday) {
                        @unlink($fileInfo->getRealPath());
                        $removedEvents++;
                    }
                }
            }
        }

        $output->writeln(sprintf('Removed "%s" past events', $removedEvents));
    }

    /**
     * Helper method to strip some not wanted characters from the given $string.
     *
     * @param $string
     * @return string
     */
    private function strip($string)
    {
        $string = html_entity_decode($string);
        $string = preg_replace('#\s{2,}#m', ' ', $string);
        $string = str_replace(['"', "'", "\n", '#'], '', $string);

        return $string;
    }
}
