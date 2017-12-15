<?php

namespace InvitationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use UserBundle\Entity\Family;
use InvitationBundle\Entity\Invit;
use FOS\UserBundle\Event\FormEvent;
use UserBundle\Entity\User;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Event\FilterUserResponseEvent;
class DefaultController extends Controller
{
    /**
     * @Route("/invitation/create", name="newInvit")
     */
    public function newInvitAction(Request $request)
    {
        $user = $this->getUser();
        if($request->isXMLHttpRequest()){
            $guest_email =  $request->request->get('email');
            $family_id = $request->request->get('family_id');
            $repository    = $this->getDoctrine()->getManager()->getRepository('UserBundle:Family');
            $family = $repository->findOneBy(array('id' => $family_id));
            
            $invit = new Invit();
            $invit->setFamily($family);
            $invit->setSender($user);
            $invit->setReceiver($guest_email);
            $invit->setIsAccepted(false);
            $em = $this->getDoctrine()->getManager();
            $em->persist($invit);
            $em->flush(); 

            $this->sendInvitationMail($invit);
            

            
            $arrayCollection = array(
                    'id' => $invit->getId(),
                    'email' => $invit->getReceiver(),
                    'family' => $invit->getFamily()->getId(),    
                    'status' => $invit->getIsAccepted(),                
                );
           
            $response = new JsonResponse($arrayCollection);
         
            return $response;
            }else{
                return new Response("Erreur", 400);
            }
    }

    /**
     * @Route("/invitation/reSend", name="reSendInvit")
     */
     public function reSendInvitAction(Request $request)
     {
         $user = $this->getUser();
         if($request->isXMLHttpRequest()){
             $invit_id =  $request->request->get('id');
             $repository    = $this->getDoctrine()->getManager()->getRepository('InvitationBundle:Invit');
             $invit = $repository->findOneBy(array('id' => $invit_id));
             
 
             $this->sendInvitationMail($invit);
 
             
             $arrayCollection = array(
                     'id' => $invit->getId(),
                     'email' => $invit->getReceiver(),
                     'family' => $invit->getFamily()->getId(),    
                     'status' => $invit->getIsAccepted(),                
                 );
            
             $response = new JsonResponse($arrayCollection);
          
             return $response;
             }else{
                 return new Response("Erreur", 400);
             }
     }

     /**
     * @Route("/invitation/remove", name="removeInvit")
     */
     public function removeInvitAction(Request $request)
     {
         $user = $this->getUser();
         if($request->isXMLHttpRequest()){
             $invit_id =  $request->request->get('id');
             $repository    = $this->getDoctrine()->getManager()->getRepository('InvitationBundle:Invit');
             $invit = $repository->findOneBy(array('id' => $invit_id));
             
             $em = $this->getDoctrine()->getManager();
             $em->remove($invit);
             $em->flush();            
             $response = new Response('OK', 200);
          
             return $response;
             }else{
                 return new Response("Erreur", 400);
             }
     }

     /**
     * @Route("/invitation/{id}", name="acceptInvit")
     */
     public function acceptInvitAction(Request $request, $id)
     {
        $user = $this->getUser();
        $brother_families = $this->getAllChildFamily();
        $repository    = $this->getDoctrine()->getManager()->getRepository('InvitationBundle:Invit');
        $invit = $repository->findOneBy(array('id' => $id));
        $isAlreadyMember = false;
        if(TRUE === $this->get('security.authorization_checker')->isGranted('ROLE_USER') and $invit->getIsAccepted() == false){
            if($user->getEmail() == $invit->getReceiver()){
                $families = $user->getFamilies();
                foreach ($families as $family) {
                    if($family == $invit->getFamily()){
                        return new Response('Vous êtes déjà membre');
                    }
                }
                $user->addFamily($invit->getFamily());
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush(); 
                return new RedirectResponse($this->generateUrl('myProfile'));
            }
            else{
                return new Response(404);
            }
        }
        elseif($invit->getIsAccepted() == true){
            $url = $this->generateUrl('myProfile', array('new'=>true));
            $response = new RedirectResponse($url);
            return $response;
        }
        else{
            $user = new User();
            $dispatcher = $this->get('event_dispatcher');
            
            $form     = $this->get('form.factory')->create('UserBundle\Form\NewUserType', $user);
            if ($form->handleRequest($request)->isValid()) {
                $user->setUsername($user->getEmail());
                $user->setIsChild(false);
                $user->setEnabled(true);
                $user->addFamily($invit->getFamily());
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                $invit->setIsAccepted(true);
                $em->persist($invit);
                $em->flush();

                $request->getSession()->getFlashBag()->add('notice', 'Utilisateur bien enregistré.');
                
                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);


                if (null === $response = $event->getResponse()) {
                    $url = $this->generateUrl('myProfile', array('new'=>true));
                    $response = new RedirectResponse($url);
                }

                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                return $response;
                
            }
            return $this->render('UserBundle:Default:registrationFromInvitation.html.twig', array('brother_families'=>$brother_families,'form' => $form->createView()));
        
        }
     }

     private function getAllChildFamily(){
        $user = $this->getUser();      
        $family_zero = null;
        $repository    = $this->getDoctrine()->getManager()->getRepository('EventBundle:Event');
        $event = $repository->findOneBy(array('id' => 1));
        $event_family = $event->getFamily();

        $repository    = $this->getDoctrine()->getManager()->getRepository('UserBundle:Family');
        $family_zero_mother = $repository->findBy(array('family_mother' => $event_family));
        if($family_zero == null){
            $family_zero_father = $repository->findBy(array('family_father' => $event_family));
        }
        return array_merge($family_zero_father,$family_zero_mother);
     }

     private function sendInvitationMail ($invit){

        $message = \Swift_Message::newInstance()->setSubject($invit->getSender()->getFirstname().' vous invite à rejoindre sa famille sur Famil.yt !')->setFrom('admin@famil.yt')->setTo($invit->getReceiver())->setBody('Bonjour <br><br> Vous avez reçu une invitation de la part de '.$invit->getSender()->getFirstname().' pour rejoindre la famille '.$invit->getFamily()->getName().' sur Famil.yt <br><br> Pour accepter cette invitation, cliquez sur ce lien : <a href="gilbert.famil.yt/web/invitation/'.$invit->getId().'">accepter</a>', 'text/html');
        $this->get('mailer')->send($message);
     }

}
