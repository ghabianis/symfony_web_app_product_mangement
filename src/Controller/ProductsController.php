<?php

namespace App\Controller;

use App\Entity\Products;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface as SymfonyHttpClientInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/produits', name: 'products_')]
class ProductsController extends AbstractController
{


/**
 * @Route("/", name="product_list")
 */
public function index(Request $request, EntityManagerInterface $entityManager): Response
{
    // Retrieve the filter parameter from the request
    $filter = $request->query->get('filter');

    // Retrieve the page and page size parameters from the request
    $page = $request->query->getInt('page', 1);
    $pageSize = $request->query->getInt('pageSize', 10);

    // Make the API request to fetch the products
    $client = new \GuzzleHttp\Client();
    $response = $client->request('GET', 'https://tech.dev.ats-digital.com/api/products', [
        'query' => [
            'size' => $pageSize,
            'filter' => $filter,
        ],
    ]);

    // Parse the response
    $data = json_decode($response->getBody(), true);
    $productsData = $data['products'];

    // Apply filtering if a filter is provided
    if ($filter) {
        $filteredProductsData = array_filter($productsData, function ($product) use ($filter) {
            return stripos($product['productName'], $filter) !== false;
        });
    } else {
        $filteredProductsData = $productsData;
    }

    // Implement pagination
    $totalProducts = count($filteredProductsData);
    $offset = ($page - 1) * $pageSize;
    $paginatedProductsData = array_slice($filteredProductsData, $offset, $pageSize);

    // Persist the products to the database
    foreach ($paginatedProductsData as $productData) {
        $product = new Product();
        $product->setName($productData['productName']);
        $product->setDescription($productData['description']);
        $product->setPrice($productData['price']);

        // Set other properties of the product entity
        
        $entityManager->persist($product);
    }
    
    $entityManager->flush();
    
    // Retrieve the products from the database
    $products = $entityManager->getRepository(Product::class)->findAll();

    // Render the template with the filtered and paginated products
    return $this->render('products/index.html.twig', [
        'products' => $products,
        'filter' => $filter,
        'page' => $page,
        'pageSize' => $pageSize,
        'totalProducts' => $totalProducts,
    ]);
}



    /**
     * @Route("/product/edit/{id}", name="product_edit")
     */
public function edit(Request $request, EntityManagerInterface $entityManager, $id)
{
    // Retrieve the product from the database based on the provided $id
    $product = $entityManager->getRepository(Product::class)->find($id);

    // Create the form for editing the product
    $form = $this->createFormBuilder($product)
        ->add('name', TextType::class)
        ->add('description', TextareaType::class)
        ->add('price', NumberType::class)
        ->add('save', SubmitType::class, ['label' => 'Save Changes'])
        ->getForm();

    // Handle form submission
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();

        // Redirect to the product listing page or any other appropriate page
        return $this->redirectToRoute('product_list');
    }

    // Render the template and pass the form object
    return $this->render('product/edit.html.twig', [
        'form' => $form->createView(),
        'product' => $product,
    ]);
}








    #[Route('/{slug}', name: 'details')]
    public function details(Products $product): Response
    {
        return $this->render('products/details.html.twig', compact('product'));
    }
}