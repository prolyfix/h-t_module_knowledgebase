<?php

namespace Prolyfix\KnowledgebaseBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Prolyfix\KnowledgebaseBundle\Entity\Knowledgebase;
use Prolyfix\RssBundle\Entity\News;

final class KnowledgebaseListener
{
    private bool $isProcessing = false;

    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function postPersist(Knowledgebase $knowledgebase, PostPersistEventArgs $args): void
    {
        if ($this->isProcessing || $knowledgebase->isAddToNews() !== true) {
            return;
        }

        $this->createNewsForKnowledgebase($knowledgebase);
    }

    public function preUpdate(Knowledgebase $knowledgebase, PreUpdateEventArgs $args): void
    {
        if ($this->isProcessing) {
            return;
        }

        $changes = $args->getEntityChangeSet();
        if (!isset($changes['addToNews'])) {
            return;
        }

        $newValue = $changes['addToNews'][1] ?? null;
        if ($newValue !== true) {
            return;
        }

        $this->createNewsForKnowledgebase($knowledgebase);
    }

    private function createNewsForKnowledgebase(Knowledgebase $knowledgebase): void
    {
        $this->isProcessing = true;

        try {
            $news = new News();
            $news->setTitle($knowledgebase->getName());
            $news->setContent($knowledgebase->getDescription());
            $news->setLink('/admin?crudAction=detail&crudControllerFqcn=Prolyfix%5CKnowledgebaseBundle%5CController%5CAdmin%5CKnowledgebaseCrudController&entityId=' . $knowledgebase->getId());
            $news->setCreatedBy($knowledgebase->getCreatedBy());
            $news->setTenant($knowledgebase->getTenant());
            $news->setOwner($knowledgebase->getOwner());

            $this->entityManager->persist($news);
            $this->entityManager->flush();
        } finally {
            $this->isProcessing = false;
        }
    }
}
