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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/video")
 */
class VideoController extends AbstractController
{
    /**
     * @Route("/", name="video_index", methods={"GET"})
     */
    public function index(VideoRepository $videoRepository): Response
    {
        return $this->render('video/videos.html.twig', [
            'videos' => $videoRepository->findAll(),
        ]);
    }

    /**
     * @Route("/", name="video_overview", methods={"GET"})
     */
    public function overview(VideoRepository $videoRepository): Response
    {
        return $this->render('video/overview.html.twig', [
            'videos' => $videoRepository->findAll(),
        ]);
    }


    /**
     * @Route("/new", name="video_new", methods={"GET","POST"})
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
     */
    public function show(Video $video): Response
    {
        return $this->render('video/show.html.twig', [
            'video' => $video,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="video_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Video $video): Response
    {
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('video_index');
        }

        return $this->render('video/edit.html.twig', [
            'video' => $video,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="video_delete")
     */
    public function delete(Request $request, Video $video): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($video);
        $entityManager->flush();

        return $this->redirectToRoute('video_index');
    }
}
