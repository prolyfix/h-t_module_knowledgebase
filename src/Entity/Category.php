<?php

namespace Prolyfix\KnowledgebaseBundle\Entity;

use App\Entity\TimeData;
use App\Entity\Trait\PositionTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Prolyfix\KnowledgebaseBundle\Entity\Knowledgebase;
use Prolyfix\KnowledgebaseBundle\Repository\CategoryRepository;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category extends TimeData
{
    use PositionTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    /**
     * @var Collection<int, Knowledgebase>
     */
    #[ORM\OneToMany(targetEntity: Knowledgebase::class, mappedBy: 'category')]
    private Collection $knowledgebases;

    public function __construct()
    {
        $this->knowledgebases = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Knowledgebase>
     */
    public function getKnowledgebases(): Collection
    {
        return $this->knowledgebases;
    }

    public function addKnowledgebase(Knowledgebase $knowledgebase): static
    {
        if (!$this->knowledgebases->contains($knowledgebase)) {
            $this->knowledgebases->add($knowledgebase);
            $knowledgebase->setCategory($this);
        }

        return $this;
    }

    public function removeKnowledgebase(Knowledgebase $knowledgebase): static
    {
        if ($this->knowledgebases->removeElement($knowledgebase)) {
            // set the owning side to null (unless already changed)
            if ($knowledgebase->getCategory() === $this) {
                $knowledgebase->setCategory(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
