<?php
// src/Controller/CategoryController.php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\CategoryRepository;
use App\Repository\ProgramRepository;
use App\Entity\Category;
use Symfony\Component\HttpFoundation\Request;

#[Route('/category', name: 'category_')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoryRepository $categoryRepository)
    {
        return $this->render('category/index.html.twig', ['categories' => $categoryRepository->findAll()]);
    }
    #[Route('/new', name: 'new')]
    public function new (Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->save($category, true);
            $this->addFlash('success', 'The new category has been created');
            return $this->redirectToRoute('category_index', [], Response::HTTP_SEE_OTHER);
        } else {
            $this->addFlash('danger', 'The new category hasn\'t been created');
        }
        // Render the form (best practice)
        return $this->renderView('category/new.html.twig', [
            'form' => $form,
        ]);
    }
    #[Route('/{name}/', name: 'show')]
    public function show(CategoryRepository $categoryRepository, string $name, ProgramRepository $programRepository)
    {
        $category = $categoryRepository->findOneByName(['name' => $name]);
        $program = $programRepository->findOneBy(['category' => $category], ['id' => 'DESC'], 3, 0);
        if (!$category) {
            throw $this->createNotFoundException(
                'No category with name : ' . $name . ' found in category\'s table.'
            );
        } else {
            return $this->render('category/show.html.twig', ['category' => $category, 'program' => $program]);
        }
    }
}