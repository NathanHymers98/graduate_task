<?php


namespace App\Controller;


use App\Entity\Product;
use App\Form\UploadProductFormType;
use App\Repository\ProductRepository;
use App\Serializer\Normalizer\ProductNormalizer;
use App\Service\ObjectValidator;
use App\Service\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
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
     * @IsGranted("ROLE_USER")
     */
    public function upload(EntityManagerInterface $entityManager, Request $request, SerializerInterface $serializer, ObjectValidator $validator, NormalizerInterface $normalizer, ProductNormalizer $productNormalizer, UploaderHelper $uploaderHelper)
    {

        $form = $this->createForm(UploadProductFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form->get('uploadFile')->getData();

            $directory = $uploaderHelper->uploadFile($uploadedFile);
//            $destination = $this->getParameter('kernel.project_dir') . '/uploads';
//            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME); // gets filename with no extension
//            $newFilename = $originalFilename . '-' . uniqid() . '.' . $uploadedFile->getClientOriginalExtension(); // applies a unique identifier to the original filename
//            $directory = $destination . '/' . $newFilename;
//
//            $uploadedFile->move(
//                $destination,
//                $newFilename
//            );

            $data = $serializer->decode(file_get_contents($directory), 'csv'); // serializing the csv data into an array

            foreach ($data as $item) { // Looping over each item in the array transforming them into Product objects
                $product = $normalizer->denormalize($item, Product::class);
                $validator->standardCheck($product);
                $validator->validateDiscontinued($product);

                $entityManager->persist($product);
                $entityManager->flush();
                }

            $this->addFlash('success', 'Import was successful');

            return $this->redirectToRoute('app_list');
        }

        return $this->render('product/upload.html.twig', [
            'productForm' => $form->createView(),
        ]);

    }

    /**
     * @Route("/list", name="app_list")
     * @IsGranted("ROLE_USER")
     */
    public function listAction(ProductRepository $productRepository)
    {

        $products = $productRepository->findBy(['isSuccessful' => true ]);
        $failedProducts = $productRepository->findBy(['isSuccessful' => false ]);

        return $this->render('product/list.html.twig', [
            'products' => $products,
            'failedProducts' => $failedProducts,
        ]);
    }
}