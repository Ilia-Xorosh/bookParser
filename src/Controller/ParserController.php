<?php

namespace App\Controller;

use App\Entity\Books;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Services\FileUploader;

class ParserController extends AbstractController
{

    public function getFile(EntityManagerInterface $entityManager)
    {
        $package = new Package(new EmptyVersionStrategy());
        $path = $package->getUrl('books.json');
        $json = file_get_contents($path);
        $data = json_decode($json, true);
        $count = $this->createBooks($entityManager, $data);
        return $count;
    }

    private function createBooks(EntityManagerInterface $entityManager, $data)
    {
        $count = 0;
        foreach ($data as $book) {
            if ($this->getBook($entityManager, $book['isbn'])) continue;
            $count++;
            $books = new Books();
            $books->setTitle($book['title'] ?? '');
            $books->setIsbn($book['isbn']);
            $books->setPageCount($book['pageCount'] ?? '');
            $books->setPublishedDate(substr($book['publishedDate']['$date'] ?? '', 0, 10));
            $books->setThumbnailUrl($this->fileUploader($book['thumbnailUrl'] ?? ''));
            $books->setShortDescription($book['shortDescription'] ?? '');
            $books->setLongDescription($book['longDescription'] ?? '');
            $books->setStatus($book['status'] ?? '');
            $books->setAuthors($book['authors'] ?? []);
            $books->setCategories($book['categories'] ?? []);
            $entityManager->persist($books);
            $entityManager->flush();
        }
        return $count;
    }

    private function fileUploader ($thumbnailUrl)
    {
        $fileUploader = new FileUploader();
        return $fileUploader->upload($thumbnailUrl);
    }

    private function getBook(EntityManagerInterface $entityManager, $isbn)
    {
        $repository = $entityManager->getRepository(Books::class);
        $book = $repository->findOneBy(['isbn' => $isbn]);
        return $book;
    }
}