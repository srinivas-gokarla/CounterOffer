<?php

namespace Hyugan\CounterOffer\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use RectorPrefix202304\Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Hyugan\CounterOffer\Model\getRecords;
use Hyugan\CounterOffer\Model\Email;

/**
 * Class SomeCommand
 */
class CliCommand extends Command
{
    const NAME = 'name';
    const PRODUCTS = 'products';
    const CUSTOMERS = 'customers';
    const CATEGORIES = 'categories';
    const REVIEWS = 'reviews';
    const EMAILS = 'emails';


    private State $state;
    private Email $email;
    private getRecords $getRecordsFactory;


    /**
     * @param State $state
     * @param string|null $name
     */
    public function __construct(
        State             $state,
        Email             $email,
        getRecords $getRecordsFactory,
        string            $name = null)
    {
        $this->state = $state;
        parent::__construct($name);
        $this->email = $email;
        $this->getRecordsFactory = $getRecordsFactory;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('hyugan:helper');
        $this->setDescription('This is my first console command.');

        $this->addOption(
            self::EMAILS,
            null,
            InputOption::VALUE_OPTIONAL
        );

        $this->addOption(
            self::NAME,
            'nm',
            InputOption::VALUE_OPTIONAL,
            'Name'
        );

        $this->addOption(
            self::PRODUCTS,
            'p',
            InputOption::VALUE_OPTIONAL,
            'Products'
        );

        $this->addArgument(
            self::CUSTOMERS,
            InputArgument::OPTIONAL,
            'get Customers info'
        );
        $this->addArgument(
            self::CATEGORIES,
            InputArgument::OPTIONAL,
            'get Customers Info');

        $this->addArgument(
            self::REVIEWS,
            InputArgument::OPTIONAL,
            'get Reviews Info');

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->state->setAreaCode(Area::AREA_ADMINHTML);

            if ($name = $input->getOption(self::NAME)) {
                $output->writeln('<info>Provided name is `' . $name . '`</info>');
            }

            if ($size = $input->getOption(self::PRODUCTS)) {
                $products = $this->getRecordsFactory->create()->getProductCollection($size);
                foreach ($products as $product) {
                    echo $product->getSku() . PHP_EOL;
                }
            }


            if ($input->getArgument(self::CATEGORIES)) {
                $categories = $this->getRecordsFactory->create()->getCategoryCollection();
                foreach ($categories as $category) {
                    echo $category->getName() . PHP_EOL;
                }
            }

            if ($input->getArgument(self::CUSTOMERS)) {
                $customers = $this->getRecordsFactory->create()->getCustomerInfo();
                foreach ($customers as $customer) {
                    echo $customer->getName() . PHP_EOL;
                }
            }

            if ($input->getArgument(self::REVIEWS)) {
                $reviews = $this->getRecordsFactory->create()->getReviewCollection();
                foreach ($reviews as $review) {
                    echo $review->getDetail() . PHP_EOL;
                }
            }
            if ($input->getOption(self::EMAILS)) {
                $this->email->sendEmail();
                $output->writeln('<info>successfully email send.</info>');
            }
//        $output->writeln('<info>Success Message.</info>');
//        $output->writeln('<error>An error encountered.</error>');
//        $output->writeln('<comment>Some Comment.</comment>');
        } catch (LocalizedException $e) {
            echo $e->getMessage();
        }
        return 1;
    }
}
