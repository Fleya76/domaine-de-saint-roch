<?php

namespace App\Controller;

use DateTime;
use App\Entity\Video;
use App\Form\VideoType;
use App\Service\FileUploader;
use App\Repository\VideoRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/video")
 */
class VideoController extends AbstractController
{
    /**
     * @Route("/", name="video_index", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function index(VideoRepository $videoRepository): Response
    {
        return $this->render('video/videos.html.twig', [
            'videos' => array_reverse($videoRepository->findAll()),
        ]);
    }

    /**
     * @Route("/overview", name="video_overview", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function overview(VideoRepository $videoRepository): Response
    {
        return $this->render('video/overview.html.twig', [
            'videos' => array_reverse($videoRepository->findAll()),
        ]);
    }


    /**
     * @Route("/new", name="video_new", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function new(Request $request, FileUploader $fileUploader): Response
    {
        $video = new Video();
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form['path']->getData();           
            if($file){
                $res = $fileUploader->upload($file);
                if(is_string($res)){
                    // $article->setImage($res);
                    $video->setPath($res);

                }else{
                    // $message = $res->getMessage();
                    $video->setPath('');
                }
            }else{
                // $article->setImage(''); 
                $video->setPath('');
            }

            $video->setCreatedAt(new DateTime());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($video);
            $entityManager->flush();

            $this->addFlash('success', 'Votre vidéo à bien été téléchargé');

            return $this->redirectToRoute('video_show', array(
                'id' => $video->getId()
            ));
        }

        return $this->render('video/new.html.twig', [
            'video' => $video,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="video_show", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function show(Video $video): Response
    {
        return $this->render('video/show.html.twig', [
            'video' => $video,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="video_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function edit(Request $request, Video $video): Response
    {
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('video_index');
            $this->addFlash('success', 'La vidéo a bien été modifié');

        }

        return $this->render('video/edit.html.twig', [
            'video' => $video,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="video_delete")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function delete(Request $request, Video $video): Response
    {
        $this->addFlash('warning', 'La vidéo a bien été supprimé');


        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($video);
        $entityManager->flush();

        return $this->redirectToRoute('video_index');
    }
}
