<?php

namespace Prolyfix\KnowledgebaseBundle\Controller\Admin;

use App\Controller\Admin\BaseCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Prolyfix\KnowledgebaseBundle\Entity\Category;
use Prolyfix\KnowledgebaseBundle\Entity\Knowledgebase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Test\Constraint\ResponseHasCookie;

class KnowledgebaseCrudController extends BaseCrudController
{
    public static function getEntityFqcn(): string
    {
        return Knowledgebase::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('category')->renderAsNativeWidget(),
            TextField::new('name'),
            TextEditorField::new('description')->addJsFiles(Asset::new('/js/trix-upload.js')->onlyOnForms()),
        ];
    }
    public function configureCrud(\EasyCorp\Bundle\EasyAdminBundle\Config\Crud $crud): \EasyCorp\Bundle\EasyAdminBundle\Config\Crud
    {
        return $crud
            ->setPageTitle('index', 'Knowledgebase')
            ->overrideTemplates([
                'crud/index' => '@ProlyfixKnowledgebase/knowledgebase/index.html.twig',
            ])
                        ->overrideTemplates([
                'crud/detail' => '@ProlyfixKnowledgebase/knowledgebase/detail.html.twig',
            ]);
    }
    public function index(AdminContext $context)
    {
        $response = parent::index($context);
        // Fetch the list of categories
        $categories = $this->em->getRepository(Category::class)->findBy([], ['position' => 'ASC']);
        // Pass the categories to the template
        $response->set('categories', $categories);
        return $response;   
    }
}
