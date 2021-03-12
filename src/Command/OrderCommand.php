<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class OrderCommand extends Command
{
    protected static $defaultName = 'app:store:order';
    /**
     * @var OrderRepository
     */
    private $repository;

    public function __construct(OrderRepository $repository)
    {
        $this->repository = $repository;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Manage orders')
            ->addArgument('name', InputArgument::REQUIRED, 'Description')
            ->addArgument('age', InputArgument::REQUIRED, 'Description')
            ->addOption(
                'list',
                'l',
                InputOption::VALUE_NONE,
                'List all orders'
            );
    }

    protected function execute(InputInterface $in, OutputInterface $out): int
    {
        $option = $in->getOption('list');
        $name = $in->getArgument('name');
        $styler = new SymfonyStyle($in, $out);

        if (null === $name) {
            $styler->error('You have not  set the name. The command will exit');

            return 20;
        }

        $styler->title('Order Management System');
        $styler->section('This is where the orders will be managed');
        $headers = ['ID', 'USER', 'CREATED AT', 'STATUS'];
        $rows = [];

        dump($name);
        if (true === $option) {
            $orders = $this->repository->findAll();
            /** @var Order $order */
            foreach ($orders as $order) {
                $rows [] = [
                    $order->getId(),
                    \sprintf(
                        '%s %s',
                        $order->getShipping()->getFirstName(),
                        $order->getShipping()->getLastName()
                    ),
                    $order->getCreateAt()->format('Y-m-d H:i:s'),
                    $order->getStatus(),
                ];
            }
        }
        $styler->table($headers, $rows);

        return 0;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $styler = new SymfonyStyle($input, $output);
        $name = $styler->ask('What is your name ?');
        $input->setArgument('nom', $name);
    }
}