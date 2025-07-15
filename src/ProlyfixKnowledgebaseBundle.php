<?php

namespace Prolyfix\KnowledgebaseBundle;

use App\Entity\Module\ModuleRight;
use App\Module\ModuleBundle;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use Prolyfix\KnowledgebaseBundle\Entity\Category;
use Prolyfix\KnowledgebaseBundle\Entity\Knowledgebase;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ProlyfixKnowledgebaseBundle extends ModuleBundle
{
    
    private $authorizationChecker;
    public function setAuthorizationChecker(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public static function getTables(): array
    {
        return [
            Knowledgebase::class,
            Category::class,
        ];
    }
    

    const IS_MODULE = true;
    public static function getShortName(): string
    {
        return 'KnowledgebaseBundle';
    }
    public static function getModuleName(): string
    {
        return 'Knowledgebase';
    }
    public static function getModuleDescription(): string
    {
        return 'Knowledgebase Module';
    }
    public static function getModuleType(): string
    {
        return 'module';
    }
    public static function getModuleConfiguration(): array
    {
        return [];
    }

    public static function getModuleRights(): array
    {
        return [
            (new ModuleRight())
                ->setModuleAction(['list', 'show', 'edit', 'new', 'delete'])
                ->setCoverage('user')
                ->setRole('ROLE_USER')
                ->setEntityClass(Knowledgebase::class),
        ];
    }

    public  function getMenuConfiguration(): array
    {
        return ['knowledgebase' => [
            MenuItem::linkToCrud('Knowledgebase List', 'fas fa-list', Knowledgebase::class),
            MenuItem::linkToCrud('Categorie List', 'fas fa-list', Category::class),
        ]];
    }

    public static function getUserConfiguration(): array
    {
        return [];
    }

    public static function getModuleAccess(): array
    {
        return [];
    }

}