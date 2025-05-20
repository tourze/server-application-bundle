<?php

namespace ServerApplicationBundle\MessageHandler;

use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use ServerApplicationBundle\Message\NodeHealthCheckMessage;
use ServerApplicationBundle\Repository\ApplicationRepository;
use ServerApplicationBundle\Service\ApplicationTypeFetcher;
use ServerNodeBundle\Enum\NodeStatus;
use ServerNodeBundle\Repository\NodeRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

#[AsMessageHandler]
class NodeHealthCheckHandler
{
    public function __construct(
        private readonly NodeRepository $nodeRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly ApplicationTypeFetcher $typeFetcher,
        private readonly ApplicationRepository $applicationRepository,
    ) {
    }

    public function __invoke(NodeHealthCheckMessage $message): void
    {
        $node = $this->nodeRepository->find($message->getNodeId());
        if (!$node) {
            throw new UnrecoverableMessageHandlingException('找不到节点信息');
        }

        $now = Carbon::now();
        foreach ($this->applicationRepository->findByNode($node) as $application) {
            $component = $this->typeFetcher->getApplicationByCode($application->getType());
            $res = $component->healthCheck($application, $now);
            if (null === $res) {
                $application->setOnline(null);
                $this->entityManager->persist($application);
                continue;
            }
            $application->setOnline($res);
            $this->entityManager->persist($application);
            $this->entityManager->flush();
        }

        // 如果一个节点，所有有状态的服务都是正常的，那么他就是正常的
        $serviceCount = 0;
        $onlineCount = 0;
        foreach ($this->applicationRepository->findByNode($node) as $application) {
            if (null !== $application->isOnline()) {
                ++$serviceCount;
                if ($application->isOnline()) {
                    ++$onlineCount;
                }
            }
        }
        if (0 === $serviceCount) {
            $node->setStatus(NodeStatus::MAINTAIN);
        } else {
            if ($serviceCount === $onlineCount) {
                $node->setStatus(NodeStatus::ONLINE);
            } else {
                $node->setStatus(NodeStatus::OFFLINE);
            }
        }
        $this->entityManager->persist($node);
        $this->entityManager->flush();
    }
}
