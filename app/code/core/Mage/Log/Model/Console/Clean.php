<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Validation;

class Mage_Log_Model_Console_Clean extends Mage_Log_Model_Console_Abstract
{
    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('log:clean')
            ->setDescription('Clean Logs')
            ->addOption(
                'days',
                'd',
                InputOption::VALUE_OPTIONAL,
                'Save log, days.'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $days = $input->getOption('days');

        if (!$days) {
            $helper = $this->getHelper('question');
            $question = new Question('Save log, days. (Minimum 1 day, if defined - ignoring system value): ', '0');
            $validation = Validation::createCallable(new Regex([
                'pattern' => '/^[0-9]$/',
                'message' => 'Numeric input required.',
            ]));
            $question->setValidator($validation);
            $question->setMaxAttempts(3);
            $days = $helper->ask($input, $output, $question);
        }

        if ($days > 0) {
            Mage::app()->getStore()->setConfig(Mage_Log_Model_Log::XML_LOG_CLEAN_DAYS, $days);
        }
        $this->_getLog()->clean();
        $output->writeln('Log cleaned');

        return Command::SUCCESS;
    }
}
