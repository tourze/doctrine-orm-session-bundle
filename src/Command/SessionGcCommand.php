<?php

declare(strict_types=1);

namespace Tourze\DoctrineORMSessionBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tourze\DoctrineORMSessionBundle\Repository\SessionRepository;

#[AsCommand(
    name: 'doctrine:session:gc',
    description: '清理过期的 session 记录',
)]
class SessionGcCommand extends Command
{
    public function __construct(
        private readonly SessionRepository $sessionRepository
    ) {
        parent::__construct();
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $io->title('清理过期 Session');
        
        try {
            $count = $this->sessionRepository->removeExpiredSessions();
            
            if ($count > 0) {
                $io->success(sprintf('成功清理 %d 个过期的 session 记录', $count));
            } else {
                $io->info('没有发现过期的 session 记录');
            }
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('清理失败: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
