<?php

declare(strict_types=1);

namespace App\Command;

use App\Command\Exception\NoOrdersException;
use App\Command\Exception\OrderNotFoundException;
use App\Entity\Order;
use App\Entity\OrderLine;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use function Sodium\add;

class OrderCommand extends Command
{
    protected static $defaultName = 'app:store:order';

    /**
     * @var OrderRepository
     */
    private $repository;
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(OrderRepository $repository, EntityManagerInterface $manager)
    {
        $this->repository = $repository;
        $this->manager = $manager;
        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setDescription('Manage orders')
            ->addArgument(
                'user',
                InputArgument::OPTIONAL,
                'Find order performed by a given user',
                null
            )
            ->addOption(
                'list',
                'l',
                InputOption::VALUE_NONE,
                'List all orders'
            )
            ->addOption(
                'id',
                'i',
                InputOption::VALUE_REQUIRED,
                'The order Id',
                null
            )
            ->addOption(
                'status',
                's',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Update the order status. It s an array where 1st element is the order id and 2nd is the status'
            );
    }

    /**
     * @param InputInterface  $in  the input instance to get all options and arguments
     * @param OutputInterface $out the output used to render styled render
     *
     * @return int exist status code
     */
    protected function execute(InputInterface $in, OutputInterface $out): int
    {
        //<editor-fold desc="Retrieve options & arguments">

        $list = $in->getOption('list');
        $id = $in->getOption('id');
        $user = $in->getArgument('user');
        $update = $in->getOption('status');

        //</editor-fold>

        $styler = new SymfonyStyle($in, $out);
        $styler->title('Order Management System');

        //<editor-fold desc="Manage options">

        try {
            if (null !== $id) {
                $this->describeOrder((int)$id, $styler);
            }
            if (true === $list) {
                $this->displayOrders($styler, $user);
            }
            if (!empty($update)) {
                $this->updateStatus((int)$update[0], (int)$update[1]);
            }
        } catch (NoOrdersException|OrderNotFoundException $e) {
            $styler->error($e->getMessage());

            return $e->getCode();
        }

        //</editor-fold>

        return 0;
    }

    /**
     * Use this to initialize fields if needed.
     *
     * @param InputInterface  $input  the input instance to get all options and arguments
     * @param OutputInterface $output the output used to render styled render
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        //@TODO do some logic
    }

    /**
     * Use this to ask the user for further infos.
     *
     * @param InputInterface  $input  the input instance to get all options and arguments
     * @param OutputInterface $output the output used to render styled render
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $options = $input->getOptions();
        if (false === $options['list'] && null === $options['id']) {
            $questionHelper = $this->getHelper('question');
            $question = new ConfirmationQuestion('Do you want to search for an order ? ', false);
            $confirm = $questionHelper->ask($input, $output, $question);
            if (true === $confirm) {
                $question = new Question('Enter id: ');
                $id = $questionHelper->ask($input, $output, $question);
                $input->setOption('id', $id);
            }
        }
    }

    /**
     * returns all orders.
     *
     * @param SymfonyStyle $styler the symfony styler
     * @param string|null  $user   the user name
     *
     * @throws NoOrdersException
     */
    private function displayOrders(SymfonyStyle $styler, string $user = null): void
    {
        $styler->section('This is where the orders will be managed');
        $headers = ['ID', 'USER', 'CREATED AT', 'STATUS', 'TOTAL'];
        $rows = [];
        $orders = $this->repository->findOrdersByName($user);
        if (null === $orders) {
            throw new NoOrdersException();
        }
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
                $order->computeTotal()
            ];
        }
        $styler->table($headers, $rows);
    }

    /**
     * Describe the given order.
     *
     * @param int          $id     the order id
     * @param SymfonyStyle $styler the command styler
     *
     * @throws OrderNotFoundException
     */
    private function describeOrder(int $id, SymfonyStyle $styler): void
    {
        $styler->section('Order details');
        $headers = ['ID', 'QTY', 'PRODUCT', 'UP', 'TOTAL'];
        /** @var Order|null $order */
        $order = $this->repository->find($id);
        if (null === $order) {
            throw new OrderNotFoundException();
        }
        $rows = [];
        /** @var OrderLine $line */
        foreach ($order->getOrderLines() as $line) {
            $rows[] = [
                $line->getId(),
                $line->getQuantity(),
                $line->getProduct()->getLabel(),
                $line->getProduct()->getUnitPrice(),
                $line->computeTotal(),
            ];
        }
        $styler->table($headers, $rows);
    }

    /**
     * Updates the order status.
     *
     * @param int $id     Order id
     * @param int $status Order status
     *
     * @throws OrderNotFoundException
     */
    private function updateStatus(int $id, int $status)
    {
        /** @var Order|null $order */
        $order = $this->repository->find($id);
        if (null === $order) {
            throw new OrderNotFoundException();
        }
        $order->setStatus($status);
        $this->manager->persist($order);
    }
}
