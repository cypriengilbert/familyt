<?php

namespace GalleryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/gallery")
     */
    public function indexAction()
    {
        return $this->render('GalleryBundle:Default:index.html.twig');
    }
}
