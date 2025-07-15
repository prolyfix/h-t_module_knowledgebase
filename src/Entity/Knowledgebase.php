<?php

namespace Prolyfix\KnowledgebaseBundle\Entity;

use Prolyfix\KnowledgebaseBundle\Entity\Category;
use App\Entity\TimeData;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bundle\SecurityBundle\Security;
use Prolyfix\KnowledgebaseBundle\Repository\KnowledgebaseRepository;
use App\Attribute\SearchableEntity;
use App\Attribute\SearchableField;

#[ORM\Entity(repositoryClass: KnowledgebaseRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['module_configuration_value:read']],
    denormalizationContext: ['groups' => ['module_configuration_value:write']],
)]
#[SearchableEntity(controller: 'Prolyfix\KnowledgebaseBundle\Controller\Admin\KnowledgebaseCrudController')]
class Knowledgebase extends TimeData
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['module_configuration_value:write'])]
    #[SearchableField]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[SearchableField]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'knowledgebases')]
    private ?Category $category = null;

    public function __construct()
    {
        parent::__construct();
    }
    public function getId(): ?int
    {
        return $this->id;
    }


    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
