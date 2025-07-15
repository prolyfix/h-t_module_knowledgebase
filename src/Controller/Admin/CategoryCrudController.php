<?php

namespace Prolyfix\KnowledgebaseBundle\Controller\Admin;

use BcMath\Number;
use Prolyfix\KnowledgebaseBundle\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            NumberField::new('position')
                ->setCustomOption('min', 0)
                ->setCustomOption('max', 100)
                ->setCustomOption('step', 1)
                ->setTemplatePath('admin/field/position.html.twig'),
            TextField::new('name'),
            

        ];
    }
    
}
