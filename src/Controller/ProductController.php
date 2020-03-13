<?php


namespace App\Controller;


use App\Entity\Product;
use App\Form\UploadProductFormType;
use App\Service\ObjectValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
    public function upload(EntityManagerInterface $entityManager, Request $request, SerializerInterface $serializer, ObjectValidator $validator)
    {

        $form = $this->createForm(UploadProductFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form->get('uploadFile')->getData();

            $destination = $this->getParameter('kernel.project_dir').'/uploads';
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME); // gets filename with no extension
            $newFilename = $originalFilename.'-'.uniqid().'.'.$uploadedFile->getClientOriginalExtension();
            $directory = $destination.'/'.$newFilename;

            $uploadedFile->move(
                $destination,
                $newFilename
            );

            $data = $serializer->decode(file_get_contents($directory), 'csv');

            foreach ($data as $item) {
                if(!array_key_exists('Stock', $item) || !array_key_exists('Cost in GBP', $item)){
                    $validator->setFailedImport($item);
                    continue;
                }
                $product = (new Product())
                    ->setProductCode($item['Product Code'] ?? null)
                    ->setProductName($item['Product Name'] ?? null)
                    ->setProductDescription($item['Product Description'] ?? null)
                    ->setProductStock($item['Stock'] ?? null)
                    ->setNetCost($item['Cost in GBP'] ?? null)
                    ->setIsDiscontinued($item['Discontinued'] ?? null)
                ;
//            $uploadedProductCollection = [];
//            foreach ($data as $key => $results) { // Adding each product entity to the collection
//                $uploadedProductCollection[] = $product->createFormArray($results);
//            }
//
//            foreach ($uploadedProductCollection as $item) { // Iterating over the collection the same amount of times as there are objects inside it.
                //$validator->emptyEntry($product);
                $validator->validateDiscontinued($product);
                $validator->checkLowCostAndStock($product);
                $validator->checkHighCost($product);
                $successfulProducts = $validator->getSuccessfulImport();
                foreach ($successfulProducts as $successItem ) {
                    $entityManager->persist($successItem);
                    $entityManager->flush();
                }
//                $failedProducts = $validator->getFailedImport();
//                foreach ($failedProducts as $failedItem) {
//                    $entityManager->persist($failedItem);
//                    $entityManager->flush();
//                }


            }

            $this->addFlash('success', 'File uploaded');

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('product/upload.html.twig', [
            'productForm' => $form->createView(),
        ]);
        }
}