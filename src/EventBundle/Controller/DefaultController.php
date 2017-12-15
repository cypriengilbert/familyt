<?php

namespace EventBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/event/new")
     */
    public function indexAction()
    {
        
        $form = $this->get('form.factory')->create('EventBundle\Form\EventType', $newEvent);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($newEvent);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Evenement bien enregistrée.');
            return $this->redirect($this->generateUrl('commande', array(
                
                'form' => $form->createView()
            )));
        }
        return $this->render('EventBundle:Default:index.html.twig');
    }
}
