<?php

namespace AccountingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use WishBundle\Entity\wish;
use AccountingBundle\Entity\Expense;
use WishBundle\Entity\Comment;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
class DefaultController extends Controller
{
    
    /**
     * @Route("/expense/add/", name="addExpense")
     * @Method({"POST"})
     */
     public function addExpenseAction(Request $request)
     {
        $user = $this->getUser();
        
        if($request->isXMLHttpRequest()){
            $expense = new Expense();
            
            $amount = $request->request->get('amount');
            $comment = $request->request->get('comment');
            
            $contributors = $request->request->get('contributors');
            $wish_id = $request->request->get('wish');
            $repository    = $this->getDoctrine()->getManager()->getRepository('WishBundle:wish');
            $wish = $repository->findOneBy(array('id' => $wish_id));
            foreach ($contributors as $value) {
                $repository    = $this->getDoctrine()->getManager()->getRepository('UserBundle:User');
                $contributor = $repository->findOneBy(array('id' => $value));
                $expense->addContributor($contributor);
    
            }
            $expense->setType('test');
            $expense->setWish($wish);
            $expense->setDate(new \Datetime('now'));     
            $expense->setAuthor($user);        
            $expense->setDescription($comment);
            $expense->setAmount($amount);
            $em = $this->getDoctrine()->getManager();
            $em->persist($expense);
            $em->flush();

            $arrayCollection[] = array(
                'id' => $expense->getId(),
                'description' => $expense->getDescription(),
                'author' => $expense->getAuthor()->getFirstname(),  
                'date' => $expense->getDate()->format('d/m/Y'),
                'amount' => $expense->getAmount(),
                
               );
       
       $response = new JsonResponse($arrayCollection);
    
       return $response;
           
        }else{
            return new Response("Erreur", 400);
        }
     }

      /**
     * @Route("/expense/remove/", name="removeExpense")
     * @Method({"POST"})
     */
     public function removeExpenseAction(Request $request)
     {
        if($request->isXMLHttpRequest()){
            $user = $this->getUser();
            $id = $request->request->get('id');
            $repository    = $this->getDoctrine()->getManager()->getRepository('AccountingBundle:Expense');
            $expense = $repository->findOneBy(array('id' => $id));
            $em = $this->getDoctrine()->getManager();
            $em->remove($expense);
            $em->flush();
       
            
      
           $response = new Response(200);
   
           return $response;
           } else
           {
               return new Response("Erreur", 400);
           }
     }
    
    /**
    * @Route("/expenses/{event}", name="expensesList")
    */
    public function expensesListAction($event)
    {
        $user = $this->getUser();
        $family = $user->getFamilies();

        $nbNotif = $this->forward('NotifBundle:Default:countNotif', array(
            'user_id'  => $user->getId(),
        ))->getContent();


        $repositoryExpense    = $this->getDoctrine()->getManager()->getRepository('AccountingBundle:Expense');
        $repositoryEvent    = $this->getDoctrine()->getManager()->getRepository('EventBundle:Event');
        $event = $repositoryEvent->findOneBy(array('id' => $event));
        $family_members = $event->getFamily()->getMembers();
        $expenses = [];
        $expense_table = [];

        foreach ($family_members as $value) {
            $expense_table[$value->getUsername()] = 0;
            $expenses[$value->getUsername()] = $repositoryExpense->findBy(array('author' => $value));
            
            
        }
        foreach ($family_members as $value) {
            foreach ($expenses[$value->getUsername()] as $expense) {
                $contributors = $expense->getContributors();
                $nb_contributors = count($contributors);
                $expense_table[$expense->getAuthor()->getUsername()] -= $expense->getAmount();
                foreach ($contributors as $contributor) {
                    $expense_table[$contributor->getUsername()] += ($expense->getAmount()/$nb_contributors);
                }
            }
    }
    $members_accounting =[]; 
    $family_members = $family[0]->getMembers(); 
    $repository    = $this->getDoctrine()->getManager()->getRepository('EventBundle:Event');
    $event_entity = $repository->findOneBy(array('id' => $event));
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
    $brother_families = $this->getAllChildFamily();

 //   $repartition = json_decode($this->expensesRepartitionAction(1));

        return $this->render('AccountingBundle:Default:Account.html.twig', array("nbNotif" => $nbNotif,"brother_families" => $brother_families,'expenses' => $expenses, "user" => $this->getUser(),'family_members'=>$family_members, 'expense_table' => $expense_table, 'family_members_admin' => $members_accounting));
    }


     /**
    * @Route("/expenses/resume/{event}", name="expensesResume")
    */
    public function expensesResumeAction($event)
    {
        $user = $this->getUser();
        $family = $user->getFamilies();
        $repositoryExpense    = $this->getDoctrine()->getManager()->getRepository('AccountingBundle:Expense');
        $repositoryEvent    = $this->getDoctrine()->getManager()->getRepository('EventBundle:Event');
        $event = $repositoryEvent->findOneBy(array('id' => $event));
        $family_members = $event->getFamily()->getMembers();
        $expenses = [];
        $expense_table = [];
        foreach ($family_members as $value) {
            $expense_table[$value->getUsername()] = 0;
            $expenses[$value->getUsername()] = $repositoryExpense->findBy(array('author' => $value));
        }
        foreach ($family_members as $value) {
            foreach ($expenses[$value->getUsername()] as $expense) {
                $contributors = $expense->getContributors();
                $nb_contributors = count($contributors);
                $expense_table[$expense->getAuthor()->getUsername()] -= $expense->getAmount();
                foreach ($contributors as $contributor) {
                    $expense_table[$contributor->getUsername()] += ($expense->getAmount()/$nb_contributors);
                }
            }
        }
        $expenses_final = [];
        foreach ($expense_table as $key => $value) {
            array_push($expenses_final,array("name"=>$key, "amount" => -round($value,2)));
                
        }
        $expense_table = array(
            'dps'=>$expenses_final);
        return new JsonResponse($expense_table);
    } 


     /**
    * @Route("/expenses/repartition/{event}", name="expensesRepartition")
    */
    public function expensesRepartitionAction($event)
    {
        $user = $this->getUser();
        $family = $user->getFamilies();
        $nb_count0 = 0 ;
        $x = 1;
        $repositoryExpense    = $this->getDoctrine()->getManager()->getRepository('AccountingBundle:Expense');
        $repositoryEvent    = $this->getDoctrine()->getManager()->getRepository('EventBundle:Event');
        $event = $repositoryEvent->findOneBy(array('id' => $event));
        $family_members = $event->getFamily()->getMembers();
        $expenses = [];
        $expense_table = [];
        $transaction = [];
        foreach ($family_members as $value) {
            $expense_table[$value->getUsername()] = 0;
            $expenses[$value->getUsername()] = $repositoryExpense->findBy(array('author' => $value));
        }
        
       
       
        foreach ($family_members as $value) {
            foreach ($expenses[$value->getUsername()] as $expense) {
                $contributors = $expense->getContributors();
                $nb_contributors = count($contributors);
                $expense_table[$expense->getAuthor()->getUsername()] -= round($expense->getAmount(),2);
                foreach ($contributors as $contributor) {
                    $expense_table[$contributor->getUsername()] += round(($expense->getAmount()/$nb_contributors),2);
                }
            }
        }
        
        foreach ($expense_table as $key => $value) {
            if($value == 0){
                unset($expense_table[$key]);
            }elseif($value > 0){
                $nb_count0 += 1;
            }
        }
        
        while (min($expense_table) != 0 and max($expense_table) != 0 ) {

           foreach ($expense_table as $key => $value) {
                if($value < 0){
                $key2 = array_search(-$value, $expense_table);
                if($key2){

                    $expense_final = array('amount' => abs($value), 'receiver' => $key, 'sender' => $key2);
                    array_push($transaction, $expense_final);
                    $expense_table[$key] += abs($value);
                    $expense_table[$key2] = 0;
                }
             }
           } 
           $expense_max = max($expense_table);
           $index_max = array_keys($expense_table, $expense_max); // doit 130
           $expense_min = min($expense_table);
           $index_min = array_keys($expense_table, $expense_min); // doit recup 115


           if($index_min[0] != $index_max[0]){
                if(abs($expense_min) <= abs($expense_max)){
                    $expense_final = array('amount' => abs($expense_min), 'receiver' => $index_min[0], 'sender' => $index_max[0]);
                    array_push($transaction, $expense_final);
                    $expense_table[$index_min[0]] = 0;
                    $expense_table[$index_max[0]] += $expense_min;

                  }
                else{
                    $expense_final = array('amount' => abs($expense_max), 'receiver' => $index_min[0], 'sender' => $index_max[0]);
                    array_push($transaction, $expense_final);
                    $expense_table[$index_min[0]] += $expense_max;
                    $expense_table[$index_max[0]] = 0;

                }


                } 
           
                  
        }
        

        return new JsonResponse($transaction);
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
