<?php

namespace NotifBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

use NotifBundle\Entity\Authorization;
use NotifBundle\Entity\Notification;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;



class DefaultController extends Controller
{
    /**
     * @Route("/notif/send/{user_id}/{entity_type}/{entity_id}", name="sendNotif")
     */

    
    public function SendNotifAction($user_id, $entity_type, $entity_id)
    {
        $repository    = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
        $user = $repository->findOneBy(array('id' => $user_id));
        $is_author = false;
        $is_new = true;

        if($entity_type == 'comment'){
            $repository    = $this->getDoctrine()->getManager()->getRepository('WishBundle:Comment');
            $entity = $repository->findOneBy(array('id' => $entity_id));
            $repository    = $this->getDoctrine()->getManager()->getRepository('NotifBundle:Notification');
            $existing_notifs = $repository->findBy(array('owner' => $user, 'origin' => 'comment', 'isSaw' => false, 'linked_entity'=>$entity_id));
            if(count($existing_notifs) > 0){
                $is_new = false;
            }
            if($entity->getAuthor() == $user){                
                $is_author = true;
            }
        }
        elseif($entity_type == 'new_member'){
            $repository    = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
            $entity = $repository->findOneBy(array('id' => $entity_id));
        }
        
        else{
            $entity = null;
        }

       

        foreach ($user->getAuthorizations() as $key => $authorization) {
            if($authorization->getIsAuthorized() == true and $is_author == false and $is_new == true ){
                
                $notification = new Notification();
                $notification->setOwner($user);
                $notification->setDate(new \Datetime('now'));
                $notification->setType($authorization->getType());
                $notification->setOrigin($entity_type);
                $notification->setLinkedEntity($entity_id);
                $notification->setIsSaw(false);
                $em = $this->getDoctrine()->getManager();
                $em->persist($notification);
                $em->flush();

               
                if($notification->getType() == 'email'){
                    $message = \Swift_Message::newInstance()->setSubject('Nouvelle interaction sur Famil.yt !')->setFrom('admin@famil.yt')->setTo($user->getEmail())->setBody($this->renderView('emails/notification.html.twig', array(
                       'user' => $user,
                       'entity_type' => $entity_type,
                       'entity' => $entity,
                    )), 'text/html');
                    // $this->get('mailer')->send($message);
                    
                }
            }
        }
    return new Response('ok', 200);    
    }


    /**
    * @Route("/authorization/edit", name="editAuthorization")
    * @Method({"POST"})
    */
    public function editAuthorizationAction(Request $request)
    {     
        $user = $this->getUser();

        if($request->isXMLHttpRequest()){
            $auth_id = $request->request->get('id');
            $repository    = $this->getDoctrine()->getManager()->getRepository('NotifBundle:Authorization');
            $authorization = $repository->findOneBy(array('id' => $auth_id));
            $authorization->setIsAuthorized(!$authorization->getIsAuthorized());

            $em = $this->getDoctrine()->getManager();
            $em->persist($authorization);
            $em->flush();

            $arrayCollection = array(
                     'id' => $auth_id,
                     'isAuthorized' => $authorization->getIsAuthorized(),
                 );
            
            $response = new JsonResponse($arrayCollection);
         
            return $response;
        }else{
            return new Response("Erreur", 400);
        }
     }

     /**
     * @Route("/notif/count/{user_id}", name="countNotif")
     */
    public function countNotifAction($user_id)
    {
        $repository    = $this->getDoctrine()->getManager()->getRepository('NotifBundle:Notification');
        $notifications = $repository->findBy(array('owner' => $user_id, 'isSaw' => false));

        return new Response(count($notifications), 200);
    }

    /**
     * @Route("/notif/read/", name="readNotif")
     */
     public function readNotifAction(Request $request)
     {
        $user = $this->getUser();
        if($request->isXMLHttpRequest()){
            $repository    = $this->getDoctrine()->getManager()->getRepository('NotifBundle:Notification');
            $notifications = $repository->findBy(array('owner' => $user->getId(), 'isSaw' => false));


            $arrayCollection = [];
            foreach ($notifications as $value) {
                $value->setIsSaw(true);
                $em = $this->getDoctrine()->getManager();
                $em->remove($value);
                $em->flush();
                $arrayCollection[] = array(
                    'id' => $value->getId(),
                    'date' => $value->getDate(),
                    'origin' => $value->getOrigin(),  
                    'entity' =>$value->getLinkedEntity(),
                );
            }

            return new JsonResponse($arrayCollection);
            
        }
        else{
            return new Response("Erreur", 400);
        }
         
       
         
     }
}
