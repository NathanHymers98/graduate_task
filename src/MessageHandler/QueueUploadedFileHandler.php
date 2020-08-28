<?php


namespace App\MessageHandler;


use App\Entity\Product;
use App\Message\QueueUploadedFile;
use App\Serializer\Normalizer\ProductNormalizer;
use App\Service\ObjectValidator;
use App\Service\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class QueueUploadedFileHandler implements MessageHandlerInterface
{
    private $entityManager;
    private $serializer;
    private $validator;
    private $normalizer;
    private $productNormalizer;
    private $uploaderHelper;
    private $validatorInterface;


    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer, ObjectValidator $validator, NormalizerInterface $normalizer, ProductNormalizer $productNormalizer, UploaderHelper $uploaderHelper, ValidatorInterface $validatorInterface)
    {

        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->normalizer = $normalizer;
        $this->productNormalizer = $productNormalizer;
        $this->uploaderHelper = $uploaderHelper;
        $this->validatorInterface = $validatorInterface;

    }

    public function __invoke( QueueUploadedFile $queueUploadedFile)
    {
        $uploadedFile = $queueUploadedFile->getProduct();

        $directory = $this->uploaderHelper->uploadFile($uploadedFile);

        $data = $this->serializer->decode(file_get_contents($directory), 'csv'); // serializing the csv data into an array

        foreach ($data as $item) { // Looping over each item in the array transforming them into Product objects, then validating them before persisting them to the database
            $product = $this->normalizer->denormalize($item, Product::class);
            $this->validator->standardCheck($this->validatorInterface, $product);
            $this->validator->validateDiscontinued($product);
            $this->entityManager->persist($product);
            $this->entityManager->flush();
        }
    }
}