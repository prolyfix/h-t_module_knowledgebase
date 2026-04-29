<?php

namespace Prolyfix\KnowledgebaseBundle\Controller\Admin;

use Prolyfix\HolidayAndTime\Controller\Admin\BaseCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Prolyfix\KnowledgebaseBundle\Entity\Category;
use Prolyfix\KnowledgebaseBundle\Entity\Knowledgebase;
use Prolyfix\RssBundle\Entity\News;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Test\Constraint\ResponseHasCookie;
use Vich\UploaderBundle\Form\Type\VichFileType;


class KnowledgebaseCrudController extends BaseCrudController
{
    public static function getEntityFqcn(): string
    {
        return Knowledgebase::class;
    }


    public function configureFields(string $pageName): iterable
    {
        $fields = [
            AssociationField::new('category')->renderAsNativeWidget(),
            TextField::new('name'),
            TextEditorField::new('description')->addJsFiles(Asset::new('/js/trix-upload.js')->onlyOnForms()),
            BooleanField::new('addToNews')->renderAsSwitch(false),
                        TextField::new('file')
                    ->onlyOnForms()
                    ->setFormType(VichFileType::class)
        ];

        // Add file upload for medium (images/videos only)
        return $fields;
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
            ])
            ;
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

    public function detail(AdminContext $context)
    {
        $response = parent::detail($context);

        $knowledgebase = $context->getEntity()->getInstance();
        if (!$knowledgebase instanceof Knowledgebase) {
            $response->set('knowledgebaseReadsStats', []);

            return $response;
        }

        $link = '/admin?crudAction=detail&crudControllerFqcn=Prolyfix%5CKnowledgebaseBundle%5CController%5CAdmin%5CKnowledgebaseCrudController&entityId=' . $knowledgebase->getId();
        $news = $this->em->getRepository(News::class)->findOneBy(['link' => $link], ['creationDate' => 'DESC']);

        $response->set('knowledgebaseReadsStats', $news?->getReadsStats() ?? []);

        return $response;
    }
}
