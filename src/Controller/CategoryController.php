<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("/addCategory", name="add_category")
     */
    public function createCategory(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('message', 'category créer avec succès');

            return $this->redirectToRoute('all_category');
        }

        return $this->renderForm('category/createCategory.html.twig', [
            'form' => $form
        ]);
    }

    /**
     * @Route("/category", name="all_category")
     */
    public function getAllCategory(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $categories = $entityManager->getRepository(Category::class)->findAll();

        return $this->render('category/category.html.twig', [
            'categories' => $categories
        ]);
    }


    /**
     * @Route("/editCategory/{categoryId}", name="update_category")
     */
    public function updateCategory(ManagerRegistry $doctrine, Request $request,int $categoryId): Response
    {

        $entityManager = $doctrine->getManager();
        $category = $entityManager->getRepository(Category::class)->find($categoryId);

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();

            $this->addFlash('message', 'Category modifier avec succès');

            return $this->redirectToRoute('all_category');
        }
        return $this->render('category/modifierCategory.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/deleteCategory/{categoryId}", name="delete_category")
     */
    public function deleteCategory(ManagerRegistry $doctrine, int $categoryId): Response
    {
        $entityManager = $doctrine->getManager();
        $category = $entityManager->getRepository(Category::class)->find($categoryId);

        $entityManager->remove($category);
        $entityManager->flush();

        return $this->redirectToRoute('all_category');

    }
}
