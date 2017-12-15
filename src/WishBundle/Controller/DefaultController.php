<?php

namespace WishBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use WishBundle\Entity\wish;
use WishBundle\Entity\Comment;
use WishBundle\Entity\Invitation;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    /**
     * @Route("/wish/me", name="wishList")
     */
    public function wishListAction()
    {
        $event=1;
        $user = $this->getUser(); 
        $families = $user->getFamilies();
        $family_members = $families[0]->getMembers();
        $repository    = $this->getDoctrine()->getManager()->getRepository('WishBundle:wish');
        $wishes = $repository->findBy(array('author' =>$this->getUser(), 'isCouple' => false, 'event' => $event));
        $wishes_couple = [];
        $wishes_family = [];
        $isHiddenCouple = false;
        $isHiddenFamily = false;
        $nbNotif = $this->forward('NotifBundle:Default:countNotif', array(
            'user_id'  => $user->getId(),
        ))->getContent();

        foreach ($families as $family) {
            if($family->getMother() and $family->getMother() == $user){
                $wishes_couple_mother = $repository->findBy(array('author' => $family->getMother(), 'isCouple' => true, 'event' => $event));
                $wishes_couple_father = $repository->findBy(array('author' => $family->getFather(), 'isCouple' => true, 'event' => $event));
                $wishes_couple = array_merge($wishes_couple_mother, $wishes_couple_father);
                
                /*foreach ($wishes_couple as $key => $wish_couple) {
                    foreach ($wishes as $key2 => $wish) {
                       if($wish == $wish_couple){
                           $is_doble[$key] = 1;
                       }
                       else{
                        $is_doble[$key] = 0;
                       }
                    }
                    if($is_doble[$key] == 0){
                       array_push($wishes, $wish_couple);
                    }
                }*/
                $isHiddenCouple = true;
                
            }elseif($family->getFather() == $user){
                $wishes_couple_mother = $repository->findBy(array('author' => $family->getMother(), 'isCouple' => true, 'event' => $event));
                $wishes_couple_father = $repository->findBy(array('author' => $family->getFather(), 'isCouple' => true, 'event' => $event));
                $wishes_couple = array_merge($wishes_couple_mother, $wishes_couple_father);

               /* foreach ($wishes_couple as $key => $wish_couple){
                    foreach ($wishes as $key2 => $wish) {
                       if($wish == $wish_couple){
                        
                           $is_doble[$key] = 1;
                       }
                       else{
                        $is_doble[$key] = 0;
                       }
                    }
                    if($is_doble[$key] == 0){
                       array_push($wishes, $wish_couple);
                    }
                }*/

                $isHiddenCouple = true;
                
            }
            /*foreach ($family->getMembers() as $member) {
                $wishes_family = $repository->findBy(array('author' => $member, 'isFamily' => true));
                foreach ($wishes_family as $key => $wish_family) {
                    foreach ($wishes as $key2 => $wish) {
                       if($wish == $wish_family){
                           $is_doble_f[$key] = 1;
                       }
                       else{
                        $is_doble_f[$key] = 0;
                       }
                    }
                    if($is_doble_f[$key] == 0){
                       array_push($wishes, $wish_family);
                    }
                }
                $isHiddenFamily= true;
                
            }*/
        }
        
        $wishes = array_merge($wishes, $wishes_couple);
        $members_zero = [];
        $brother_families = $this->getAllChildFamily();
        $repository    = $this->getDoctrine()->getManager()->getRepository('WishBundle:Invitation');
        $shareLink = $repository->findOneBy(array('user' => $user, 'isFamily' =>false));
        $shareLinkFamily = $repository->findOneBy(array('user'=>$user, 'isFamily' => true)) ;

        if($shareLink){
            $shareLink =  $shareLink->getShortner();
        }
        else{
            $shareLink = [];
        }
        if($shareLinkFamily){
            $shareLinkFamily =  $shareLinkFamily->getShortner();
        }
        else{
            $shareLinkFamily = [];
        }
        return $this->render('WishBundle:Default:wishList.html.twig', array(
            'shareLink'=> $shareLink,
            'shareLinkFamily'=>$shareLinkFamily,
            'brother_families' => $brother_families,
            'members_zero' => $members_zero,
            'wishes' => $wishes,
            "user" => $this->getUser(),
            'family_members'=>$family_members,
            'isHiddenCouple' => $isHiddenCouple,
            'isHiddenFamily'=>$isHiddenFamily,
            'nbNotif' => $nbNotif,
        ));
    }


    /**
     * @Route("/add/famille", name="famille")
     */
     public function addFamilleAction(Request $request)
     {
                     
         $repository    = $this->getDoctrine()->getManager()->getRepository('UserBundle:user');
         $users = $repository->findAll();
         $repository    = $this->getDoctrine()->getManager()->getRepository('UserBundle:Family');
         $famille = $repository->findOneBy(array('id' => '1'));

         foreach ($users as $user) {
             $user->addFamily($famille);    
             $em = $this->getDoctrine()->getManager();
             $em->persist($user);
             $em->flush(); 
         }
         $response = new Response(200);
         
            return $response;
     }

     
    /**
     * @Route("/wish/shared/create", name="wishCreateInvitation")
     * @Method({"POST"})
     */
     public function wishCreateInvitationAction(Request $request)
     {
        $user = $this->getUser();
        if($request->isXMLHttpRequest()){

            $invitation = new Invitation();
            $invitation->setUser($user);
            $owner = $user;
            $repository    = $this->getDoctrine()->getManager()->getRepository('EventBundle:Event'); 
            $event = $repository->findOneBy(array('id' => 1));                       
            $invitation->setEvent($event);
            $isFamily = $request->request->get('isFamily'); 
            $invitation->setIsFamily($isFamily);
            if($isFamily == true){
                $ownerFamilies = $owner->getFamilies();

                foreach ($ownerFamilies as $family) {
                    if($family->getMother() and $family->getMother() == $user){
                        
                        $ownerFamilyMembers = $family->getMembers();
                    }
                    elseif($family->getFather() and $family->getFather() == $user){
                        $ownerFamilyMembers = $family->getMembers();
                    }
                }
                foreach ($ownerFamilyMembers as $member) {
                    $invitation_family = new Invitation();
                    $invitation_family->setUser($member);
                    $invitation_family->setEvent($event);
                    $invitation_family->setIsFamily($isFamily);
                    $shortner = $this->generateRandomString(6);
                    $invitation_family->setShortner($shortner);


                    $em = $this->getDoctrine()->getManager();
                    $em->persist($invitation_family);
                    $em->flush();
                }

            }
            else{
                
                $shortner = $this->generateRandomString(6);
                $invitation->setShortner($shortner);
                $em = $this->getDoctrine()->getManager();
                $em->persist($invitation);
                $em->flush();

            }
            

            $arrayCollection[] = array(
                     'shortner' => $shortner,
                     'isFamily' => $isFamily,
                    );
            
            $response = new JsonResponse($arrayCollection);
         
            return $response;
        }else{
            return new Response("Erreur", 400);
        }
     }


     /**
     * @Route("/wish/shared/{shortner}", name="wishListInvit")
     */
     public function wishListInvitAction($shortner)
     {
        $repository    = $this->getDoctrine()->getManager()->getRepository('WishBundle:Invitation');
        $shareLink = $repository->findOneBy(array('shortner' => $shortner));
        $repository    = $this->getDoctrine()->getManager()->getRepository('EventBundle:Event'); 
        $event = $repository->findOneBy(array('id' => 1)); 
        $user = $this->getUser();
        
        
        $auth_checker = $this->get('security.authorization_checker');
        $isRoleUser = $auth_checker->isGranted('ROLE_USER');
        $owner = $shareLink->getUser();
        $ownerFamilies = $owner->getFamilies();
        $brother_families = [];
        $belongFamily = false;
        $repository    = $this->getDoctrine()->getManager()->getRepository('WishBundle:wish');            
        $wishes = $repository->findBy(array('author' => $owner, 'event' => $event));
        $ownerFamilyLinks = [];
        if($isRoleUser == true){
            if($user->getEmail() == $shareLink->getUser()->getEmail()){
                return new RedirectResponse($this->generateUrl('wishList'));
            }
            $UserFamilies = $user->getFamilies();
            $belongFamily = false;
            $brother_families = $this->getAllChildFamily();
            foreach ($UserFamilies as $family) {
                foreach ($family->getMembers() as $member) {
                    if($member == $owner){
                        return new RedirectResponse($this->generateUrl('wishListByEmail',['email'=>$owner->getEmail()]));
                        
    
                        break;
                    }
                }
            }
            foreach ($UserFamilies as $family) {
                if($event->getFamily() == $family){
                    $belongFamily = true;
                    break;
                }
                else{
                    $belongFamily = false;
                }
            }
        }
        
      



        
        $nbNotif = $this->forward('NotifBundle:Default:countNotif', array(
            'user_id'  => $user->getId(),
        ))->getContent();
        $ownerFamilyMembers =[];
        if ($shareLink->getIsFamily() == true){
            
            foreach ($ownerFamilies as $family) {
                if($family->getMother() and $family->getMother() == $owner){
                    
                    $ownerFamilyMembers = $family->getMembers();
                }
                elseif($family->getFather() and $family->getFather() == $owner){
                    $ownerFamilyMembers = $family->getMembers();   
                }
                elseif(count($ownerFamilies) == 1){
                    $ownerFamilyMembers = $family->getMembers();   
                }
            }
            $repository    = $this->getDoctrine()->getManager()->getRepository('WishBundle:Invitation');
            foreach ($ownerFamilyMembers as $member) {
                $invitation = $event = $repository->findOneBy(array('user' => $member, 'event' => 1)); 
                array_push($ownerFamilyLinks, array('id' => $member->getId(), 'shortner' => $invitation->getShortner(), 'firstname' => $member->getFirstname(), 'picture' => $member->getImageName()));
           //     $ownerFamilyLinks[$member->getId()] = array('shortner' => $invitation->getShortner(), 'firstname' => $member->getFirstname(), 'picture' => $member->getImageName());
            }
            return $this->render('WishBundle:Default:wishListShared.html.twig', array(
                'owner'=>$owner,
                'wishes' => $wishes,
                'belongFamily' => $belongFamily,
                'brother_families' => $brother_families,
                'family_zero_members' => $ownerFamilyLinks,
                'nbNotif' => $nbNotif,
            ));
            
        }
        else{           
            
            return $this->render('WishBundle:Default:wishListShared.html.twig', array(
                'owner'=>$owner,
                'wishes' => $wishes,
                'belongFamily' => $belongFamily,
                'brother_families' => $brother_families,
                'nbNotif' => $nbNotif,
            ));
            

        }
     }

    /**
     * @Route("/wish/{email}", name="wishListByEmail")
     */
     public function wishListByEmailAction($email)
     {
         $event=1;
        
         $repository    = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
         $user = $repository->findOneBy(array('emailCanonical' => $email));

        
       
         $repository    = $this->getDoctrine()->getManager()->getRepository('EventBundle:Event');
         $event_entity = $repository->findOneBy(array('id' => $event));
        $access_granted = false;
        $brother_families = $this->getAllChildFamily();
        $userOnline = $this->getUser();
        foreach ($userOnline->getFamilies() as $family) {
            if($family == $event_entity->getFamily()){
                $access_granted = true;
                break;
            }
            foreach ($family->getMembers() as $member) {
                if($member == $user){
                    $access_granted = true;
                    break;
                }
            }
        }

        $nbNotif = $this->forward('NotifBundle:Default:countNotif', array(
            'user_id'  => $userOnline->getId(),
        ))->getContent();
        if($access_granted){

       
        
        $families_owner = $user->getFamilies();
        $families = $userOnline->getFamilies();
        $members_accounting =[]; 
        $family_members = $families[0]->getMembers(); 
        
        $family_accounting =  $event_entity->getFamily();
        $repository    = $this->getDoctrine()->getManager()->getRepository('UserBundle:Family');
        $families_accounting_father = $repository->findBy(array('family_father' => $family_accounting));
        $families_accounting_mother = $repository->findBy(array('family_mother' => $family_accounting));
        foreach ($families_accounting_father as $value) {
          array_push($members_accounting, $value->getFather()); 
        }
        foreach ($families_accounting_mother as $value) {
            array_push($members_accounting, $value->getMother()); 
          }
          array_push($members_accounting, $family_accounting->getFather() );
       
        $repository    = $this->getDoctrine()->getManager()->getRepository('WishBundle:wish');
        $wishes = $repository->findBy(array('author' => $user, 'event' => $event, 'isCouple' => false));
        
        
        $wishes_couple = [];
        $wishes_family = [];
        $isHiddenCouple = false;
        $isHiddenFamily = false;
        $wishes_couple_mother = [];
        foreach ($families_owner as $family) {
            
            if($family->getMother() and $family->getMother() == $user){
                
                $wishes_couple_mother = $repository->findBy(array('author' => $family->getMother(), 'isCouple' => true, 'event' => $event));
                $wishes_couple_father = $repository->findBy(array('author' => $family->getFather(), 'isCouple' => true, 'event' => $event));
                $wishes_couple = array_merge($wishes_couple_mother, $wishes_couple_father);
                $isHiddenCouple = true;
            }elseif($family->getFather() == $user){
                
                $wishes_couple_mother = $repository->findBy(array('author' => $family->getMother(), 'isCouple' => true, 'event' => $event));
                $wishes_couple_father = $repository->findBy(array('author' => $family->getFather(), 'isCouple' => true, 'event' => $event));
                $wishes_couple = array_merge($wishes_couple_mother, $wishes_couple_father);
              $isHiddenCouple = true;
              
                
              
            }
           /* foreach ($family->getMembers() as $member) {
                $wishes_family = $repository->findBy(array('author' => $member, 'isFamily' => 1));
                $isHiddenFamily= true;
            }*/
        }
        $wishes = array_merge($wishes, $wishes_couple);
        
        if ($user->getIsChild() == false){
            $repository    = $this->getDoctrine()->getManager()->getRepository('UserBundle:Family');
            $family_zero = $repository->findOneBy(array('mother' => $user));
            if($family_zero == null){
                $family_zero = $repository->findOneBy(array('father' => $user));
            }
            $members_z = $family_zero->getMembers();
            $members_zero =[];
            foreach ($members_z as $value) {
                if($value->getIsChild() == true or $family_zero->getFather() === $value or $family_zero->getMother() === $value){
                    
                    array_push($members_zero, $value);
                   
                }
                
            }
            
            
        }
        else{
            $family_zero = $user->getFamilies();
            $members_zero = $family_zero[0]->getMembers();
            
        }
        
        
        
        $repository    = $this->getDoctrine()->getManager()->getRepository('EventBundle:Event'); 
        $event = $repository->findOneBy(array('id' => 1)); 
        $repository    = $this->getDoctrine()->getManager()->getRepository('WishBundle:Invitation');
        $shareLink = $repository->findOneBy(array('user' => $user, 'isFamily' =>false));
        $shareLinkFamily = $repository->findOneBy(array('user'=>$user, 'isFamily' => true)) ;


        if($shareLinkFamily and $user == $userOnline ){
            $shareLinkFamily =  $shareLinkFamily->getShortner();
        }
        else{
            $shareLinkFamily = null;
        }

        if($shareLink and $user == $userOnline and $shareLinkFamily !== null){
            $shareLink =  $shareLink->getShortner();
        }
        else{
            $shareLink = null;
        }
       

         return $this->render('WishBundle:Default:wishList.html.twig', array(
             'event' => $event,
             'brother_families' => $brother_families,
             'members_zero' => $members_zero,
             'wishes' => $wishes,
             "user" => $user,
             'family_members'=>$members_accounting,
             'isHiddenCouple' => $isHiddenCouple,
             'isHiddenFamily'=>$isHiddenFamily,
             'shareLink' => $shareLink,
             'shareLinkFamily' => $shareLinkFamily,
             'nbNotif' => $nbNotif,
            ));
        }
        else
        {
            return $this->render('error.html.twig', array(
                'error' =>"Oups, vous n'avez pas l'autorisation d'être ici", 
                'brother_families' => $brother_families,
                'nbNotif' => $nbNotif,
            ));            
        }
     }

    /**
     * @Route("/wish/add", name="addWish")
     */
     public function addWishAction(Request $request)
     {
        $user = $this->getUser();
        $json = $request->request->get('wish');
        $editedWish = json_decode($json);
        $wish = new wish();
        $wish->setDate(new \Datetime('now'));     
        $wish->setAuthor($user);        
        $wish->setDescription($editedWish->description);
        
        $wish->setStatus($editedWish->status);
        if($editedWish->url == 'null'){
            $wish->setUrl(null);
        }
        else{
            $wish->setUrl($editedWish->url);
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($wish);
        $em->flush();



         return $this->redirect($this->generateUrl('wishList'));
     }
    /**
     * @Route("/wish/add/ajax", name="addWishAjax")
     * @Method({"POST"})
     */
     public function addWishAjaxAction(Request $request)
     {
        $user = $this->getUser();
        if($request->isXMLHttpRequest()){
            $description = $request->request->get('description');
            $isFamily = $request->request->get('isFamily');
            $owner = $request->request->get('user');
            $repository    = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
            $owner = $repository->findOneBy(array('id' => $owner));
            $repository    = $this->getDoctrine()->getManager()->getRepository('EventBundle:Event');
            $event = $repository->findOneBy(array('id' => 1));
            $isCouple = $request->request->get('isCouple');
            $wish = new wish();
            $wish->setDate(new \Datetime('now'));     
            $wish->setAuthor($owner);   
            $wish->setEvent($event);
            if($isCouple == "true"){
                $wish->setIsCouple(true);
                
            }else{
                $wish->setIsCouple(false);
            }
            if($isFamily == "true"){
                $wish->setIsFamily(true);
            }else{
                $wish->setIsFamily(false);
            }
            $wish->setDescription($description);
            $wish->setStatus(true);
            $wish->setUrl('');
            $em = $this->getDoctrine()->getManager();
            $em->persist($wish);
            $em->flush();

            $arrayCollection[] = array(
                     'id' => $wish->getId(),
                     'description' => $wish->getDescription(),
                     'status' => $wish->getStatus(), 
                     'isCouple' => $wish->getIsCouple(),  
                     'isFamily' => $wish->getIsFamily(),  
                     'url'=> $wish->getUrl(),
                     
                    );
            
            $response = new JsonResponse($arrayCollection);
         
            return $response;
        }else{
            return new Response("Erreur", 400);
        }
     }


     /**
     * @Route("/comment/remove", name="removeComment")
     * @Method({"POST"})
     */
     public function removeCommentAction(Request $request)
     {
        $user = $this->getUser();
        if($request->isXMLHttpRequest()){
            $comment_id = $request->request->get('comment');
            $repository    = $this->getDoctrine()->getManager()->getRepository('WishBundle:Comment');
            $comment = $repository->findOneBy(array('id' => $comment_id));

            if($comment->getAuthor() == $user){
                $em = $this->getDoctrine()->getManager();
                $em->remove($comment);
                $em->flush();

                $arrayCollection = array(
                    'statut' => 1,
                    'response' => 'comment removed',
                   );
            }
            else{
                $arrayCollection[] = array(
                    'statut' => 0,
                    'response' => 'Vous ne pouvez pas supprimer le commentaire des autres ! ',
                );
            }
            $response = new JsonResponse($arrayCollection);
            return $response;
        }
        else{
            return new Response("Erreur", 400);
        }
    }

      /**
     * @Route("/comment/edit", name="editComment")
     * @Method({"POST"})
     */
     public function editCommentAction(Request $request)
     {
        $user = $this->getUser();
        if($request->isXMLHttpRequest()){
            $comment_id = $request->request->get('comment');
            $repository    = $this->getDoctrine()->getManager()->getRepository('WishBundle:Comment');
            $comment = $repository->findOneBy(array('id' => $comment_id));

            if($comment->getAuthor() == $user){
                $comment->setContent($request->request->get('value'));
                $comment->setIsEdited(true);
                $em = $this->getDoctrine()->getManager();
                $em->persist($comment);
                $em->flush();

                $arrayCollection = array(
                    'statut' => 1,
                    'response' => 'comment edited',
                    'value' => $comment->getContent(),
                   );
            }
            else{
                $arrayCollection[] = array(
                    'statut' => 0,
                    'response' => 'Vous ne pouvez pas modifier le commentaire des autres ! ',
                );
            }
            $response = new JsonResponse($arrayCollection);
            return $response;
        }
        else{
            return new Response("Erreur", 400);
        }
    }

    /**
     * @Route("/comment/add/ajax", name="addComment")
     * @Method({"POST"})
     */
     public function addCommentAction(Request $request)
     {
        $user = $this->getUser();
        if($request->isXMLHttpRequest()){
            $id = $request->request->get('id');
            $content = $request->request->get('content');
            
            $repository    = $this->getDoctrine()->getManager()->getRepository('WishBundle:wish');
            $wish = $repository->findOneBy(array('id' => $id));

            $comment = new Comment();
            $comment->setDate(new \Datetime('now'));     
            $comment->setAuthor($user);        
            $comment->setContent($content);
            $comment->setWish($wish);
            $comment->setIsEdited(false);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            $notifReceivers = [];

            array_push($notifReceivers, $wish->getAuthor());
            foreach ($wish->getComments() as $key => $value) {
                if($value->getAuthor() != $comment->getAuthor()){
                    if($value->getAuthor()->getIsChild() == false){
                        array_push($notifReceivers, $value->getAuthor());
                    }
                   // else{
                   //     array_push($notifReceivers, $value->getAuthor());
                   // }
                    
                } 
            }

           
            foreach ($notifReceivers as $key => $value) {
                $sendNotifResponse = $this->forward('NotifBundle:Default:SendNotif', array(
                    'user_id'  => $value->getId(),
                    'entity_type' => 'comment',
                    'entity_id' => $comment->getId(),
                ));
            }

            

            $arrayCollection[] = array(
                     'id' => $comment->getId(),
                     'content' => $comment->getContent(),
                     'date' => $comment->getDate(),  
                     'author' => $comment->getAuthor()->getUsername(),
                     'isEdited' => false,
                    );
            
            $response = new JsonResponse($arrayCollection);
         
            return $response;
        }else{
            return new Response("Erreur", 400);
        }
     }

   

     /**
     * @Route("/wish/edit/ajax", name="editWishAjax")
     * @Method({"POST"})
     */
     public function editWishAjaxAction(Request $request)
     {
         
        $user = $this->getUser();
        if($request->isXMLHttpRequest()){
            $description = $request->request->get('description');
            $url = $request->request->get('url');
            $id = $request->request->get('id');   
            $repository    = $this->getDoctrine()->getManager()->getRepository('WishBundle:wish');
            $wish = $repository->findOneBy(array('id' => $id));
            $wish->setDescription($description);
            $wish->setUrl($url);
            $em = $this->getDoctrine()->getManager();
            $em->persist($wish);
            $em->flush();

            $arrayCollection[] = array(
                     'id' => $wish->getId(),
                     'description' => $wish->getDescription(),
                     'status' => $wish->getStatus(),  
                     'url' =>$wish->getUrl()
                 );
            
            $response = new JsonResponse($arrayCollection);
         
            return $response;
        }else{
            return new Response("Erreur", 400);
        }
     }

          /**
     * @Route("/wish/edit/status", name="changeStatus")
     * @Method({"POST"})
     */
     public function changeStatusAction(Request $request)
     {
         
        $user = $this->getUser();
        if($request->isXMLHttpRequest()){
            $id = $request->request->get('id');   
            $repository    = $this->getDoctrine()->getManager()->getRepository('WishBundle:wish');
            $wish = $repository->findOneBy(array('id' => $id));
            $wish->setStatus(!$wish->getStatus());
            if($wish->getStatus() == true){
                $wish->setBuyer(null);
            }
            else{
                $wish->setBuyer($user);
                $user = $user->getUsername();
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($wish);
            $em->flush();

            $arrayCollection[] = array(
                     'status' => $wish->getStatus(),
                    'buyer' => $user
                 );
            
            $response = new JsonResponse($arrayCollection);
         
            return $response;
        }else{
            return new Response("Erreur", 400);
        }
     }

    /**
     * @Route("/comments/", name="comments")
     * @Method({"POST"})
     */
     public function commentsAction(Request $request)
     {
         
            $user = $this->getUser();
        
            if($request->isXMLHttpRequest()){
            $id = $request->request->get('id');   
            $repository    = $this->getDoctrine()->getManager()->getRepository('WishBundle:wish');
            $wish = $repository->findOneBy(array('id' => $id));
            $comments = $wish->getComments();
            
             
            $arrayCollection = [];
            foreach ($comments as $value) {
                $arrayCollection[] = array(
                    'id' => $value->getId(),
                    'date' => $value->getDate(),
                    'author' => $value->getAuthor()->getUsername(),  
                    'content' =>$value->getContent(),
                    'isEdited' =>$value->getIsEdited(),
                );
            }
            
            $response = new JsonResponse($arrayCollection);
         
            return $response;
            }else{
                return new Response("Erreur", 400);
            }
        
     }

    /**
     * @Route("/wish/remove/ajax", name="removeWishAjax")
     * @Method({"POST"})
     */
     public function removeWishAjaxAction(Request $request)
     {
        $user = $this->getUser();
        if($request->isXMLHttpRequest()){
            $id = $request->request->get('id');
            $repository    = $this->getDoctrine()->getManager()->getRepository('WishBundle:wish');
            $wish = $repository->findOneBy(array('id' => $id));
           
            $em = $this->getDoctrine()->getManager();
            $em->remove($wish);
            $em->flush();
            $response = new Response();
            $response->setStatusCode(200);
            return $response;
            
        }else{
            return new Response("Erreur", 400);
        }
     }
     



    /**
     * @Route("/wish/{id}/remove", name="removeWish")
     */
     public function removeWishAction($id)
     {
        $repository    = $this->getDoctrine()->getManager()->getRepository('WishBundle:wish');
        $wish = $repository->findOneBy(array('id' => $id));
        $em = $this->getDoctrine()->getManager();
        $em->remove($wish);
        $em->flush();
        return $this->redirect($this->generateUrl('wishList'));
     }


     /**
     * @Route("/wish/{id}/view", name="viewWish")
     */
     public function viewWishAction($id)
     {
        $repository    = $this->getDoctrine()->getManager()->getRepository('WishBundle:wish');
        $wish = $repository->findOneBy(array('id' => $id));
        return $this->render('WishBundle:Default:editWish.html.twig', array('wish' => $wish));
        
        $data =  $this->get('serializer')->serialize($wish, 'json'); 
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
     }

    /**
    * @Route("/wish/{id}/available", name="setAvailable")
    */
    public function availableWishAction($id)
    {
        $repository    = $this->getDoctrine()->getManager()->getRepository('WishBundle:wish');
        $wish = $repository->findOneBy(array('id' => $id));
        if($wish->getStatus() == true){
            $wish->setStatus(false);
        }else{
            $wish->setStatus(true);
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($wish);
        $em->flush();
        $data =  $this->get('serializer')->serialize($wish, 'json'); 
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
     }

    /**
    * @Route("/wish/{id}/edit", name="editWish")
    */
    public function editWishAction(Request $request, $id)
    {
        $json = $request->request->get('wish');
        $editedWish = json_decode($json);
        $repository    = $this->getDoctrine()->getManager()->getRepository('WishBundle:wish');
        $wish = $repository->findOneBy(array('id' => $id));
        $wish->setDescription($editedWish->description);
        $wish->setStatus($editedWish->status);
        if($editedWish->url == 'null'){
            $wish->setUrl(null);
        }
        else{
            $wish->setUrl($editedWish->url);
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($wish);
        $em->flush();
        $data =  $this->get('serializer')->serialize($wish, 'json'); 
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
      //  return $response;
      return $this->render('WishBundle:Default:editWish.html.twig', array('wish' => $wish));
      
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


     private function generateRandomString($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        
        $repository    = $this->getDoctrine()->getManager()->getRepository('WishBundle:Invitation'); 
        $isUnique = $repository->findOneBy(array('shortner' => $randomString));  
        while(empty($isUnique) == false){
                
                $repository    = $this->getDoctrine()->getManager()->getRepository('WishBundle:Invitation'); 
                $randomString = '';
                for ($i = 0; $i < $length; $i++) {
                    $randomString .= $characters[rand(0, $charactersLength - 1)];
                }
                $isUnique = $repository->findOneBy(array('shortner' => $randomString));  
                
        }
        return $randomString;

        
    }

    

}
