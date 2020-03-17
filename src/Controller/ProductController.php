<?php


namespace App\Controller;


use App\Entity\Product;
use App\Form\UploadProductFormType;
use App\Repository\ProductRepository;
use App\Serializer\Normalizer\ProductNormalizer;
use App\Service\ObjectValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;



class ProductController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function homepage()
    {
        return $this->render('index/home.html.twig');
    }

    /**
     * @Route("/upload", name="app_upload")
     */
    public function upload(EntityManagerInterface $entityManager, Request $request, SerializerInterface $serializer, ObjectValidator $validator, NormalizerInterface $normalizer, ProductNormalizer $productNormalizer)
    {

        $form = $this->createForm(UploadProductFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form->get('uploadFile')->getData();

            $destination = $this->getParameter('kernel.project_dir') . '/uploads';
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME); // gets filename with no extension
            $newFilename = $originalFilename . '-' . uniqid() . '.' . $uploadedFile->getClientOriginalExtension(); // applies a unique identifier to the original filename
            $directory = $destination . '/' . $newFilename;

            $uploadedFile->move(
                $destination,
                $newFilename
            );

            $data = $serializer->decode(file_get_contents($directory), 'csv'); // serializing the csv data into an array

            foreach ($data as $item) { // Looping over each item in the array transforming them into Product objects
                $product = $normalizer->denormalize($item, Product::class);
                $validator->standardCheck($product);
                $validator->validateDiscontinued($product);
                }
            $successfulProducts = $validator->getSuccessfulImport();
            foreach ($successfulProducts as $successItem) {
                $entityManager->persist($successItem);
                $entityManager->flush();
            }

            $this->addFlash('success', 'CSV Imported');

            return $this->redirectToRoute('app_list');
        }


        return $this->render('product/upload.html.twig', [
            'productForm' => $form->createView(),
        ]);

    }

    /**
     * @Route("/list", name="app_list")
     */
    public function listAction(ProductRepository $productRepository)
    {
        $products = $productRepository->findAll();

        return $this->render('product/list.html.twig', [
            'products' => $products
        ]);
    }
}