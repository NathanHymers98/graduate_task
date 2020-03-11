<?php


namespace App\Controller;


use App\Entity\Product;
use App\Form\UploadProductFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
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
    public function upload(EntityManagerInterface $entityManager, Request $request, SerializerInterface $serializer)
    {
        $product = new Product();
        $form = $this->createForm(UploadProductFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form->get('uploadFile')->getData();

            $destination = $this->getParameter('kernel.project_dir').'/uploads';
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME); // gets filename with no extension
            $newFilename = $originalFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();
            $directory = $destination.'/'.$newFilename;

            $uploadedFile->move(
                $destination,
                $newFilename
            );

            dd($data =$serializer->decode(file_get_contents($directory), 'csv'));



            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', 'File uploaded');

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('product/upload.html.twig', [
            'productForm' => $form->createView(),
        ]);
        }
}