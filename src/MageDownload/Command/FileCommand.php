<?php
/**
 * Magedownload CLI
 *
 * PHP version 5
 *
 * @category  MageDownload
 * @package   MageDownload
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magedownload-cli
 */

namespace MageDownload\Command;

use MageDownload\Download;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Download file command
 *
 * @category  MageDownload
 * @package   MageDownload
 * @author    Steve Robbins <steve@steverobbins.com>
 * @copyright 2015 Steve Robbins
 * @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
 * @link      https://github.com/steverobbins/magedownload-cli
 */
class FileCommand extends AbstractCommand
{
    /**
     * Configure command
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('file')
            ->setDescription('Download a release or patch')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'The name of the file to download'
            )
            ->addArgument(
                'destination',
                InputArgument::OPTIONAL,
                'The destination where the file should be downloaded'
            );
        parent::configure();
    }

    /**
     * Execute command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $destination = $this->getDestination();
        $this->output->writeln(sprintf('Downloading to <info>%s</info>...', $destination));
        $download = new Download;
        $result = $download->get(
            $this->input->getArgument('name'),
            $this->getAccountId(),
            $this->getAccessToken()
        );
        $success = file_put_contents($destination, $result);
        if ($success) {
            $this->output->writeln('Complete');
        } else {
            $this->output->writeln('<error>Failed to download file</error>');
        }
    }

    /**
     * Determine where the file should download to
     *
     * @return string
     */
    private function getDestination()
    {
        $dest = $this->input->getArgument('destination');
        if (!$dest) {
            return getcwd() . DIRECTORY_SEPARATOR . $this->input->getArgument('name');
        }
        if (is_dir($dest)) {
            if (substr($dest, -1) !== '/') {
                $dest .= DIRECTORY_SEPARATOR;
            }
            return $dest . $this->input->getArgument('name');
        }
        return $dest;
    }
}
