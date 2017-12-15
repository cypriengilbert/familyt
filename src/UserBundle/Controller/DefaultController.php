<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use UserBundle\Entity\User;
use UserBundle\Entity\Family;
use UserBundle\Entity\Address;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="myProfile")
     */
    public function myAccountAction(Request $request)
    {
        $user = $this->getUser();   
        $families = $user->getFamilies();
        $brother_families = $this->getAllChildFamily();
        $invitation_pending = [];
        $authorizations = $user->getAuthorizations();

        foreach ($families as $family) {
            $repository   = $this->getDoctrine()->getManager()->getRepository('InvitationBundle:Invit');
            $invitation_pending[$family->getId()] = $repository->findBy(array('family' => $family, 'isAccepted' => false));
        }

        $form     = $this->get('form.factory')->create('UserBundle\Form\UserImageType', $user);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            
            $request->getSession()->getFlashBag()->add('notice', 'Photo bien enregistrée.');
            return $this->render('UserBundle:Default:profile.html.twig', array('authorizations'=>$authorizations,'invitation_pending' => $invitation_pending,"brother_families" => $brother_families ,"user" => $this->getUser(), 'families'=> $families, 'form'=>$form->createView()));
            
        }
        return $this->render('UserBundle:Default:profile.html.twig', array('authorizations'=>$authorizations,'invitation_pending' => $invitation_pending,"brother_families" => $brother_families ,"user" => $this->getUser(), 'families'=> $families, 'form'=>$form->createView()));
        
    }

        /**
     * @Route("/profile/{email}/", name="viewProfile")
     */
     public function viewProfileAction($email)
     {
        $repository    = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
        $repositoryEvent    = $this->getDoctrine()->getManager()->getRepository('EventBundle:Event');
        $owner = $repository->findOneBy(array('email' => $email));
        $families = $owner->getFamilies();
        $events = [];
        $shortLink = [];
        $isEventVisible = [];
        $isSameFamily = false;
        $family_members = $families[0]->getMembers(); 
        foreach ($families as $family) {
           $events = array_merge($events, $repositoryEvent->findBy(array('family' => $family)));
        }
        $brother_families = $this->getAllChildFamily();

        foreach ($families as $family_owner) {
           foreach ($this->getUser()->getFamilies() as $family_user) {
                if($family_user == $family_owner){
                    $isSameFamily = true;
                    foreach ($events as $event) {
                        $repositoryInvitation    = $this->getDoctrine()->getManager()->getRepository('WishBundle:Invitation');
                        $invitation = $repositoryInvitation->findOneBy(array('event' => $event, 'user' => $owner));                        
                       if($family_owner == $event->getFamily()){
                           $isEventVisible[$event->getId()] = 1;
                       }
                       elseif($invitation !== null){
                        $isEventVisible[$event->getId()] = 2;
                        $shortLink[$event->getId()] = $invitation->getShortner();
                       }
                       else{
                        $isEventVisible[$event->getId()] = 0;
                       }
                    }
                }
               
            }
        }

        
        if($isSameFamily == true){
            return $this->render('UserBundle:Default:viewProfile.html.twig', array('shortLink' => $shortLink,'isEventVisible' => $isEventVisible, "brother_families" => $brother_families, 'events' => $events, "user" => $owner,'family_members'=>$family_members, 'families'=> $families));
            
        }
        else{
            return $this->render('error.html.twig', array('error' =>"Oups, vous n'avez pas l'autorisation d'être ici", 'brother_families' => $brother_families));            
            
        }
         
         
     }

     /**
     * @Route("/profil/edit", name="editProfile")
     */
     public function editAccountAction(Request $request)
     {
        if($request->isXMLHttpRequest()){
         $user = $this->getUser();
         $user->setFirstName($request->request->get('firstname'));
         $user->setLastName($request->request->get('lastname'));
         $user->setPhone($request->request->get('phone'));
         $user->setPhoneHome($request->request->get('phone_home'));
         $user->setEmail($request->request->get('email'));
         
         $em = $this->getDoctrine()->getManager();
         $em->persist($user);
         $em->flush();

         $arrayCollection[] = array(
            'id' => $user->getId(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastName(), 
            'phone' => $user->getPhone(),
            'phone_home' => $user->getPhoneHome(),
            'email' => $user->getEmail(),
            
            
            );
   
        $response = new JsonResponse($arrayCollection);

        return $response;
        } else
        {
            return new Response("Erreur", 400);
        }
         
     }

    /**
    * @Route("/profil/set-child", name="setChild")
    */
     public function setChildAction(Request $request)
     {
        if($request->isXMLHttpRequest()){
        
        
        $user_id = $request->request->get('id');
        $repository = $this->getDoctrine()->getManager()->getRepository('Userundle:User');
        $user = $repository->findOneBy(array('id' => $user_id));

        $user->setIsChild(!$user->getIsChild());
         
         $em = $this->getDoctrine()->getManager();
         $em->persist($user);
         $em->flush();

         $arrayCollection[] = array(
            'id' => $user->getId(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastName(), 
            'phone' => $user->getPhone(),
            'phone_home' => $user->getPhoneHome(),
            'email' => $user->getEmail(),
            
            );
   
        $response = new JsonResponse($arrayCollection);

        return $response;
        } else
        {
            return new Response("Erreur", 400);
        }
         
     }

       /**
     * @Route("/address/add/", name="addAddress")
     * @Method({"POST"})
     */
     public function addAddressAction(Request $request)
     {
        $user = $this->getUser();
        if($request->isXMLHttpRequest()){
            $street = $request->request->get('street');
            $details = $request->request->get('details');
            $zipcode = $request->request->get('zipcode');
            $city = $request->request->get('city');
            

            $address = new Address();
            $address->setStreet($street);     
            $address->setDetails($details);        
            $address->setZipcode($zipcode);
            $address->setCity($city);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($address);
            $em->flush();
            $user->addAddress($address);
            $em->persist($user);
            $em->flush();

            $arrayCollection[] = array(
                     'id' => $address->getId(),
                     'street' => $address->getStreet(),
                     'details' => $address->getDetails(),  
                     'zipcode' => $address->getZipcode(),
                     'city' => $address->getCity(),
                     
                    );
            
            $response = new JsonResponse($arrayCollection);
         
            return $response;
        }else{
            return new Response("Erreur", 400);
        }
     }

     /**
     * @Route("/profil/address/edit", name="editAddress")
     */
     public function editAddressAction(Request $request)
     {
        if($request->isXMLHttpRequest()){
         $user = $this->getUser();
         $id = $request->request->get('id');
         $repository    = $this->getDoctrine()->getManager()->getRepository('UserBundle:Address');
         $address = $repository->findOneBy(array('id' => $id));

         $address->setStreet($request->request->get('street'));
         $address->setDetails($request->request->get('details'));
         $address->setCity($request->request->get('city'));
         $address->setZipcode($request->request->get('zipcode'));
         
         $em = $this->getDoctrine()->getManager();
         $em->persist($address);
         $em->flush();

         $arrayCollection[] = array(

            'id' => $address->getId(),
            'street' => $address->getStreet(),
            'details' => $address->getDetails(), 
            'city' => $address->getCity(),
            'zipcode' => $address->getZipcode(),
            
            
            );
   
        $response = new JsonResponse($arrayCollection);

        return $response;
        } else
        {
            return new Response("Erreur", 400);
        }
         
     }
     /**
     * @Route("/profil/address/remove", name="removeAddress")
     */
     public function removeAddressAction(Request $request)
     {
        if($request->isXMLHttpRequest()){
         $user = $this->getUser();
         $id = $request->request->get('id');
         $repository    = $this->getDoctrine()->getManager()->getRepository('UserBundle:Address');
         $address = $repository->findOneBy(array('id' => $id));
         $em = $this->getDoctrine()->getManager();
         $em->remove($address);
         $em->flush();
    
         
   
        $response = new Response(200);

        return $response;
        } else
        {
            return new Response("Erreur", 400);
        }
         
     }

    /**
     * @Route("/family/add/", name="newFamily")
     * @Method({"POST"})
     */
     public function newFamilyAction(Request $request)
     {
        $user = $this->getUser();
        if($request->isXMLHttpRequest()){
            $name = $request->request->get('name');
            $family = new Family();
            $family->setName($name);     
            $family->setAdmin($user);        
            
            $family->addMember($user);
            $user->addFamily($family);
            $em = $this->getDoctrine()->getManager();
            $em->persist($family);
            $em->flush();
            $em->persist($user);
            $em->flush();
           
           

            $arrayCollection[] = array(
                     'id' => $family->getId(),
                     'name' => $family->getName(),
                    );
            
            $response = new JsonResponse($arrayCollection);
         
            return $response;
        }else{
            return new Response("Erreur", 400);
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

    

     
}
