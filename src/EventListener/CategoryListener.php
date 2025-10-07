<?php
namespace Prolyfix\KnowledgebaseBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Prolyfix\KnowledgebaseBundle\Entity\Category;

class CategoryListener
{
    private bool $lock = false;
    public function __construct(private EntityManagerInterface $entityManager) {
        
    }

    public function prePersist($category, PrePersistEventArgs $args)
    {
        $count = $this->entityManager->getRepository(Category::class)->count();
        if($category->getPosition() === null)
            $category->setPosition($count + 1);
    }

    public function preUpdate($category, PreUpdateEventArgs $args)
    {
        dump($args);
        
        $changes = $args->getEntityChangeSet();
        if(isset($changes['position']) && !$this->lock) {
            $this->lock = true;
            $oldPosition = $changes['position'][0];
            $newPosition =  $changes['position'][1];
            if($oldPosition !== $newPosition) {
                if($newPosition > $oldPosition) {
                    $categories = $this->entityManager->getRepository(Category::class)->createQueryBuilder('c')
                        ->where('c.position >= :oldPosition')
                        ->andWhere('c.position < :newPosition')
                        ->setParameter('oldPosition', $oldPosition)
                        ->setParameter('newPosition', $newPosition)
                        ->getQuery()
                        ->getResult();
                    foreach($categories as $cat) {
                        $cat->setPosition($cat->getPosition() - 1);
                    }
                } else {
                    $categories = $this->entityManager->getRepository(Category::class)->createQueryBuilder('c')
                        ->where('c.position < :oldPosition')
                        ->andWhere('c.position >= :newPosition')
                        ->setParameter('oldPosition', $oldPosition)
                        ->setParameter('newPosition', $newPosition)
                        ->getQuery()
                        ->getResult();
                    foreach($categories as $cat) {
                        $cat->setPosition($cat->getPosition() + 1);
                    }
                }
                $this->entityManager->flush();
            }
            $this->lock = false;
        }
    }
}