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
    // // Retrieve the filter parameter from the request
    // $filter = $request->query->get('filter');

    // // Retrieve the page and page size parameters from the request
    // $page = $request->query->getInt('page', 1);
    // $pageSize = $request->query->getInt('pageSize', 10);

    // // Make the API request to fetch the products
    // $client = new \GuzzleHttp\Client();
    // $response = $client->request('GET', 'https://tech.dev.ats-digital.com/api/products', [
    //     'query' => [
    //         'size' => $pageSize,
    //         'filter' => $filter,
    //     ],
    // ]);

    // // Parse the response
    // $data = json_decode($response->getBody(), true);
    // $productsData = $data['products'];

    // // Apply filtering if a filter is provided
    // if ($filter) {
    //     $filteredProductsData = array_filter($productsData, function ($product) use ($filter) {
    //         return stripos($product['productName'], $filter) !== false;
    //     });
    // } else {
    //     $filteredProductsData = $productsData;
    // }

    // // Implement pagination
    // $totalProducts = count($filteredProductsData);
    // $offset = ($page - 1) * $pageSize;
    // $paginatedProductsData = array_slice($filteredProductsData, $offset, $pageSize);

    // // Persist the products to the database
    // foreach ($paginatedProductsData as $productData) {
    //     $product = new Product();
    //     $product->setName($productData['productName']);
    //     $product->setDescription($productData['description']);
    //     $product->setPrice($productData['price']);

    //     // Set other properties of the product entity
        
    //     $entityManager->persist($product);
    // }
    
    // $entityManager->flush();
    
    // Retrieve the products from the database
    $products = $entityManager->getRepository(Product::class)->findAll();

    // Render the template with the filtered and paginated products
    return $this->render('products/index.html.twig', [
        'products' => $products,
        // 'filter' => $filter,
        // 'page' => $page,
        // 'pageSize' => $pageSize,
        // 'totalProducts' => $totalProducts,
    ]);
}



    /**
     * @Route("/product/edit/{id}", name="product_edit")
     */

public function edit(Request $request, EntityManagerInterface $entityManager, $id)
{
    // Retrieve the product from the database based on the provided $id
    $product = $entityManager->getRepository(Product::class)->find($id);

    // Handle form submission
    if ($request->isMethod('POST')) {
        $product->setName($request->request->get('name'));
        $product->setDescription($request->request->get('description'));
        $product->setPrice($request->request->get('price'));

        // Update any other properties of the product entity

        $entityManager->flush();

        // Redirect to the product listing page or any other appropriate page
        return $this->redirectToRoute('product_list');
    }

    // Render the template and pass the product object
    return $this->render('product/edit.html.twig', [
        'product' => $product,
    ]);
}


public function productList(Request $request)
{
    $search = $request->query->get('search');

    // Retrieve the products from the database (or any other source)
    $repository = $this->getDoctrine()->getRepository(Product::class);
    $products = $repository->findBy(['name' => $search]);

    return $this->render('product/list.html.twig', [
        'products' => $products,
    ]);
}



    /**
     * @Route("/product/delete/{id}", methods={"DELETE"}, name="product_delete")
     */
    public function delete(Request $request, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        $entityManager->remove($product);
        $entityManager->flush();

        $this->addFlash('success', 'Product deleted successfully');

        return $this->redirectToRoute('product_list');
    }


}