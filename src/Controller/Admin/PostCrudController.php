<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PostCrudController extends AbstractCrudController
{
    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

    /**
     * @param Crud $crud
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setSearchFields(['title'])
            ->setAutofocusSearch();
    }

    /**
     * @param Filters $filters
     * @return Filters
     */
    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add('createdAt')
            ->add('publishedAt')
            ->add('author');
    }

    /**
     * @param string $pageName
     * @return iterable
     */
    public function configureFields(string $pageName): iterable
    {
        # hideOnIndex() hides the body field on index action (url)
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('title'),
            SlugField::new('slug')->setTargetFieldName('title'),
            TextareaField::new('body')->hideOnIndex(),
            DateTimeField::new('publishedAt'),
            AssociationField::new('author')->autocomplete(),
        ];
    }
}
