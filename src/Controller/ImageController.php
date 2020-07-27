<?php


namespace App\Controller;


use App\Entity\Image;
use App\Entity\Trick;
use App\Service\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends BaseController
{
    /**
     * @Route("/tricks/{id}/images", name="tricks_add_images", methods={"POST"})
     * @param Trick $trick
     * @param Request $request
     * @param UploaderHelper $uploaderHelper
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function uploadTrickImage(Trick $trick, Request $request, UploaderHelper $uploaderHelper, EntityManagerInterface $entityManager)
    {
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('image');

        if (!$uploadedFile) {
            return $this->redirectToRoute('tricks_edit', [
                'id' => $trick->getId()
            ]);
        }

        $filename = $uploaderHelper->uploadTrickImage($uploadedFile);

        $image = new Image($trick);
        $image->setFilename($filename);
        $image->setCaption($uploadedFile->getClientOriginalName() ?? $filename);

        $entityManager->persist($image);
        $entityManager->flush();

        return $this->redirectToRoute('tricks_edit', [
            'id' => $trick->getId()
        ]);
    }

    /**
     * @Route("/tricks/image/{id}/download", name="tricks_image_download")
     * @param Image $image
     */
    public function downloadImage(Image $image)
    {
        dd($image);
    }

}