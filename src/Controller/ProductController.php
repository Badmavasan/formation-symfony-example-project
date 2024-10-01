<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
    public function index(Request $request, ProductRepository $productRepository): Response
    {

        // Retrieve all products
        $products = $productRepository->findAll();

        // Handle the form for adding a product
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Save the new product to the database
            $productRepository->save($product);

            // Redirect to the same page
            return $this->redirectToRoute('app_product');
        }

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/product/insert', name: 'app_product_insert')]
    public function insert(Request $request, ProductRepository $productRepository): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $productRepository->save($product);

            return $this->render('product/insert.html.twig', [
                'controller_name' => 'ProductController',
                'insertion' => 'Good'
            ]);
        }

        return $this->render('product/insert.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/product/update/{id}', name: 'app_product_update', methods: 'GET')]
    public function update(int $id, EntityManagerInterface $entityManager, ProductRepository $productRepository){
        // Récupérer product id (post / get) : DONE

        // vérifier au niveau de la base de données si le product ayant le id $id existe

        $product = $productRepository->find($id);


        // Si ça existe => mise à jour des données
        if (!$product) {
            return $this->render('product/update.html.twig', [
                'controller_name' => 'ProductController',
                'update' => 'Failed, Product not found'
            ]);
        }

        $product->setName("Dell XPS 2023");

        // Envoyer à la base de données les modifications

        $entityManager->flush();

        return $this->render('product/update.html.twig', [
            'controller_name' => 'ProductController',
            'update' => 'Good'
        ]);
    }

    #[Route('/products/delete/{id}', name: 'product_delete')]
    public function delete(Product $product, ProductRepository $productRepository): Response
    {
        // Delete the product
        $productRepository->delete($product);

        // Redirect to the same page
        return $this->redirectToRoute('app_product');
    }
}
