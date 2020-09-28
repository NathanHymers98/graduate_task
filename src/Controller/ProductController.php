<?php
declare(strict_types=1);

namespace App\Controller;


use App\Form\UploadProductFormType;
use App\Message\QueueUploadedFile;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

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
    public function upload(Request $request, MessageBusInterface $messageBus)
    {

        $form = $this->createForm(UploadProductFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form->get('uploadFile')->getData();

            $message = new QueueUploadedFile($uploadedFile);
            $messageBus->dispatch($message);

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
    public function listAction(ProductRepository $productRepository, Request $request) // Shows two tables, one with successful products and another with failed products
    {
        $q = $request->query->get('q');
        $products = $productRepository->findAllWithSearch($q, true);
        $failedProducts = $productRepository->findAllWithSearch($q, false);

        $successfulCount = count($products);
        $failedCount = count($failedProducts);

        return $this->render('product/list.html.twig', [
            'products' => $products,
            'failedProducts' => $failedProducts,
            'failedCount' => $failedCount,
            'successfulCount' => $successfulCount

        ]);
    }

    /**
     * @Route("/resetdatabase", name="app_cleardb")
     * @IsGranted("ROLE_USER")
     */
    public function resetDatabase(EntityManagerInterface $entityManager, ProductRepository $productRepository, UserRepository $userRepository){ // Allows me to easily reset the database for testing purposes
        $prodEntities = $productRepository->findAll();
        $userEntities = $userRepository->findAll();

        foreach ($prodEntities as $prodEntity) {
            $entityManager->remove($prodEntity);
        }
        foreach ($userEntities as $userEntity) {
            $entityManager->remove($userEntity);
        }
        $entityManager->flush();

        return $this->render('index/home.html.twig');
    }
}