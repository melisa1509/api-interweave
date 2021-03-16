<?php

 
namespace App\Controller;
 
use App\Entity\Grant;
use App\Form\GrantType;
use App\Entity\GrantUpdate;
use App\Form\GrantUpdateType;
use App\Entity\GrantAmbassador;
use App\Entity\GrantGroup;
use App\Form\GrantAmbassadorType;
use App\Form\GrantAmbassadorUpdateType;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Nelmio\UserDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use JMS\Serializer\SerializationContext;
 
/**
 * Class GrantController
 *
 * @Route("/grant")
 */
class GrantController extends FOSRestController
{
    
    /**
     * @Rest\Post("/", name="grants_index")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets all grants."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to get all grants."
     * )
     *
     * @SWG\Parameter(
     *     name="id_ambassador",
     *     in="path",
     *     type="string",
     *     description="The ID of Ambassador"
     * )
     * 
     * @SWG\Parameter(
     *     name="role",
     *     in="path",
     *     type="string",
     *     description="The role of user"
     * )
     *
     *
     * @SWG\Tag(name="Grant")
     */
    public function indexAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $grants = [];
        $message = "";
 
        try {
            $code = 200;
            $error = false;
 
            $role = $request->request->get('role');
            $id = $request->request->get('id_ambassador');
            if($role == 'ROLE_ADMIN' or $role == 'ROLE_LANGUAGE_ADMIN'){
                $grants = $em->getRepository("App:Grant")->findBy(array(), array("state" => "ASC"));
            }
            else if($role == 'ROLE_EMBASSADOR' or $role == 'ROLE_STUDENT_EMBASSADOR'){
                $grants = $em->getRepository("App:Grant")->findBy(array('embassador' => $id));
            }
            else{
                $grants = $em->getRepository("App:Grant")->findAll();
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get all Grant - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $grants : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

     /**
     * @Rest\Get("/deadline", name="grants_deadline")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets all grants."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to get all grants."
     * )
     *    
     *
     *
     * @SWG\Tag(name="Grant")
     */
    public function deadlineAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $deadline = [];
        $message = "";
 
        try {
            $code = 200;
            $error = false;
            $deadline = date("Y-m-t");
            
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get deadline Grant - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $deadline : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Post("/ambassadorlist", name="grants_ambassador_list")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets all grants."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to get all grants."
     * )
     *
     * @SWG\Parameter(
     *     name="state",
     *     in="path",
     *     type="string",
     *     description="The state of grants"
     * )
     *     
     *
     *
     * @SWG\Tag(name="Grant")
     */
    public function listAmbassadorAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $grantsambassador = [];
        $message = "";
 
        try {
            $code = 200;
            $error = false;
 
            $state = $request->request->get('state');
            $grantsambassador = $em->getRepository("App:GrantAmbassador")->findBy(array("state" => $state));
           
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get all Grant - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $grantsambassador : $message,
        ];
 
        return new Response($serializer->serialize($response, "json", SerializationContext::create()->setGroups(array('grant_ambassador_list'))));
    }

     /**
     * @Rest\Post("/active", name="grant_active", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets all active Grants."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to get all active grants."
     * )
     *    
     * @SWG\Parameter(
     *     name="language",
     *     in="path",
     *     type="string",
     *     description="The language of user"
     * )
     * 
     * @SWG\Parameter(
     *     name="id_user",
     *     in="path",
     *     type="string",
     *     description="The id of user"
     * )
     *
     * @SWG\Tag(name="Grant")
     */
    public function activeAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $grants_available = [];
        $grants_ambassador = [];
        $deadline_month = date("m-Y");
        $datem = "";
        
        $message = "";
 
        try {
            $code = 200;
            $error = false;

            $language = $request->request->get('language');
            $id_user = $request->request->get('id_user');
 
            $grants = $em->getRepository("App:Grant")->findBy(array("language" => $language ), array("state" => "ASC"));

            foreach ($grants as $grant) {
                $available = true;
                $grantsambassador = $em->getRepository("App:GrantAmbassador")->findBy(array("ambassador" => $id_user, "grant" => $grant->getId()), array("createdAt" => "DESC"));
                foreach ($grantsambassador as $ga) {
                    if($ga->getCreatedAt()->format('m-Y')  == $deadline_month){
                        $available = false;
                    }
                    $grants_ambassador[] = $ga;
                    $datem = $ga->getCreatedAt()->format('m-Y');
                }

                if($available && $grant->getState() === "state.available"){
                    $grants_available[] = $grant;
                }
                
            }

            $grants = [                 
                'grants_available'   => $grants_available,
                'grants_ambassador'  => $grants_ambassador,
            ];
 
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get all Admins - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $grants : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }


     /**
     * @Rest\Post("/new", name="grant_new")
     *
     * @SWG\Response(
     *     response=201,
     *     description="Grant was successfully registered"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="Grant was not successfully registered"
     * )
     *
     * @SWG\Parameter(
     *     name="id_administrator",
     *     in="body",
     *     type="string",
     *     description="Id Administrator",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="title",
     *     in="body",
     *     type="string",
     *     description="The title of Grant",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="description",
     *     in="body",
     *     type="string",
     *     description="The description of Grant",
     *     schema={}
     * )
     * 
     * * @SWG\Parameter(
     *     name="language",
     *     in="body",
     *     type="string",
     *     description="The language of Grant",
     *     schema={}
     * )
     * 
     * * @SWG\Parameter(
     *     name="state",
     *     in="body",
     *     type="string",
     *     description="The state of Grant",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="date",
     *     in="body",
     *     type="date",
     *     description="The date",
     *     schema={}
     * )
     *       
     *    
     * @SWG\Tag(name="Grant")
     */ 

    public function newAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        $message = "";
        $id_administrator = $request->request->get('id_administrator');
        $grant=new Grant();
        //Create a form
        $form=$this->createForm(GrantType::class, $grant);
        $form->submit($request->request->all());
        
        
        try {
            $code = 200;
            $error = false;

            $administrator = $em->getRepository("App:User")->find($id_administrator);
 
            if (is_null($administrator)) {
                $code = 500;
                $error = true;
                $message = "The Administrator does not exist";
            }

            $grant->setAdministrator($administrator);


            $em->persist($grant);
            $em->flush();
  
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to register the Grant - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $grant : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

     /**
     * @Rest\Post("/newgrantgroups", name="grant_new_groups")
     *
     * @SWG\Response(
     *     response=201,
     *     description="Grant was successfully registered"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="Grant was not successfully registered"
     * )
     *
     * @SWG\Parameter(
     *     name="id_grant_ambassador",
     *     in="body",
     *     type="string",
     *     description="Id Grant Ambassador",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="groups",
     *     in="body",
     *     type="string",
     *     description="The array Id Groups",
     *     schema={}
     * )
     * 
     
     *       
     *    
     * @SWG\Tag(name="Grant")
     */ 

    public function newGrantGroupsAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        $message = "";
        $id_grant_ambassador = $request->request->get('id_grant_ambassador');
        $groups = explode(',', $request->request->get('groups'));

        
        try {
            $code = 200;
            $error = false;

            $grant_ambassador = $em->getRepository("App:GrantAmbassador")->find($id_grant_ambassador);
 
            if (is_null($grant_ambassador)) {
                $code = 500;
                $error = true;
                $message = "The Grant Ambassador does not exist";
            }

            $em->getRepository("App:GrantGroup")->deleteGrantGroups($id_grant_ambassador);
            
            foreach ($groups as $group) {
                $grant=new GrantGroup();
                $id_group =  explode("-", $group);

                $grp = $em->getRepository("App:Groupe")->find($id_group[1]);

                $grant->setGroup($grp);
                $grant->setGrantAmbassador($grant_ambassador);
                $em->persist($grant);
                $em->flush();

            }
            
  
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to register the Grant Group - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $grant_ambassador : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Post("/newupdate", name="grant_new_update")
     *
     * @SWG\Response(
     *     response=201,
     *     description="Grant update was successfully registered"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="Grant update was not successfully registered"
     * )
     *
     * @SWG\Parameter(
     *     name="id_grant",
     *     in="body",
     *     type="string",
     *     description="Id Grant",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="id_user",
     *     in="body",
     *     type="string",
     *     description="Id User",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="description",
     *     in="body",
     *     type="string",
     *     description="The description of Grant",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="file",
     *     in="body",
     *     type="string",
     *     description="The file",
     *     schema={}
     * )
     *   
     *    
     * @SWG\Tag(name="Grant")
     */ 

    public function newUpdateAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        $message = "";
        $id_grant_ambassador = $request->request->get('id_grant_ambassador');
        $id_user = $request->request->get('id_user');
        $grantupdate=new GrantUpdate();
        //Create a form
        $form=$this->createForm(GrantUpdateType::class, $grantupdate);
        $form->submit($request->request->all());
        
        
        try {
            $code = 200;
            $error = false;

            $grantambassador = $em->getRepository("App:GrantAmbassador")->find($id_grant_ambassador);
            $user = $em->getRepository("App:User")->find($id_user);
 
            if (is_null($grantambassador)) {
                $code = 500;
                $error = true;
                $message = "The Grant ambassador does not exist";
            }

            if (is_null($user)) {
                $code = 500;
                $error = true;
                $message = "The User does not exist";
            }

            $grantupdate->setGrantAmbassador($grantambassador);     
            $grantupdate->setUser($user);                 

            $em->persist($grantupdate);
            $em->flush();
  
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to register the Grant - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $grantupdate : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

     /**
     * @Rest\Post("/newgroup", name="grant_new_group")
     *
     * @SWG\Response(
     *     response=201,
     *     description="Grant update was successfully registered"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="Grant update was not successfully registered"
     * )
     *
     * @SWG\Parameter(
     *     name="id_grant_ambassador",
     *     in="body",
     *     type="string",
     *     description="Id Grant Ambassador",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="id_group",
     *     in="body",
     *     type="string",
     *     description="Id Group",
     *     schema={}
     * )
     *      
     *   
     *    
     * @SWG\Tag(name="Grant")
     */ 

    public function newGroupAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        $message = "";
        $id_grant_ambassador = $request->request->get('id_grant_ambassador');
        $id_group = $request->request->get('id_group');

        $grantgroup=new GrantGroup();
                
        
        try {
            $code = 200;
            $error = false;

            $grantambassador = $em->getRepository("App:GrantAmbassador")->find($id_grant_ambassador);
            $group = $em->getRepository("App:Groupe")->find($id_group);
 
            if (is_null($grantambassador)) {
                $code = 500;
                $error = true;
                $message = "The Grant ambassador does not exist";
            }

            if (is_null($group)) {
                $code = 500;
                $error = true;
                $message = "The Group does not exist";
            }

            $grantgroup->setGrantAmbassador($grantambassador);     
            $grantgroup->setGroup($group);                 

            $em->persist($grantgroup);
            $em->flush();
  
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to register the Grant - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $grantgroup : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Post("/newambassador", name="grant_new_ambassador")
     *
     * @SWG\Response(
     *     response=201,
     *     description="Grant ambassador was successfully registered"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="Grant ambassador was not successfully registered"
     * )
     *
     * @SWG\Parameter(
     *     name="id_grant",
     *     in="body",
     *     type="integer",
     *     description="Id Grant",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="id_ambassador",
     *     in="body",
     *     type="integer",
     *     description="Id Ambassador",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="code",
     *     in="body",
     *     type="string",
     *     description="The code of Ambassador",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="state",
     *     in="body",
     *     type="string",
     *     description="The state of Grant Aplication",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="amount",
     *     in="body",
     *     type="integer",
     *     description="The amount of money assigned",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="number",
     *     in="body",
     *     type="integer",
     *     description="The number of participants ",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="question1",
     *     in="body",
     *     type="string",
     *     description="The question 1 ",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="question2",
     *     in="body",
     *     type="string",
     *     description="The question 2 ",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="question3",
     *     in="body",
     *     type="string",
     *     description="The question 3 ",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="question4",
     *     in="body",
     *     type="string",
     *     description="The question 4 ",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="question5",
     *     in="body",
     *     type="string",
     *     description="The question 5 ",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="question6",
     *     in="body",
     *     type="string",
     *     description="The question 6 ",
     *     schema={}
     * )
     * 
      * @SWG\Parameter(
     *     name="question7",
     *     in="body",
     *     type="string",
     *     description="The question 7 ",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="file",
     *     in="body",
     *     type="string",
     *     description="The file",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="file2",
     *     in="body",
     *     type="string",
     *     description="The file 2",
     *     schema={}
     * )
     *   
     *    
     * @SWG\Tag(name="Grant")
     */ 

    public function newAmbassadorAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        $message = "";
        $id_grant = $request->request->get('id_grant');
        $id_ambassador = $request->request->get('id_ambassador');

        $grantambassador = $em->getRepository("App:GrantAmbassador")->findOneBy(array("grant" => $id_grant, "ambassador" => $id_ambassador));
        if (is_null($grantambassador) ) {
            $grantambassador=new GrantAmbassador();
        }
            
        //Create a form
        $form=$this->createForm(GrantAmbassadorType::class, $grantambassador);
        $form->submit($request->request->all());
        
        
        try {
            $code = 200;
            $error = false;

            $grant = $em->getRepository("App:Grant")->find($id_grant);
            $ambassador = $em->getRepository("App:User")->find($id_ambassador);
            
 
            if (is_null($grant) ) {
                $code = 500;
                $error = true;
                $message = "The Grant does not exist";
            }

            if (is_null($ambassador) ) {
                $code = 500;
                $error = true;
                $message = "The Ambassador does not exist";
            }

            $grantambassador->setGrant($grant); 
            $grantambassador->setAmbassador($ambassador);             
            $grantambassador->setState("state.development");     
            $grantambassador->setAmount(0);  
            $grantambassador->setCorrection(" ");             

            $em->persist($grantambassador);
            $em->flush();
  
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to register the Grant - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $grantambassador : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Get("/show/{id}.{_format}", name="grant_show", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets grant info based on passed ID parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The grant with the passed ID parameter was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The Grant ID"
     * )
     *
     * @SWG\Tag(name="Grant")
     */
    public function showAction(Request $request, $id ) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $grant = [];
        $message = "";
        
        try {
            $code = 200;
            $error = false;
 
            $grant_id = $id;
            $grant = $em->getRepository("App:Grant")->find($grant_id);
 
            if (is_null($grant)) {
                $code = 500;
                $error = true;
                $message = "The Grant does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get the current Grant- Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $grant : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Get("/showambassador/{id}.{_format}", name="grant_show_ambassador", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets grant ambassador info based on passed ID parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The grant ambassador with the passed ID parameter was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The Grant Ambassador ID"
     * )
     *
     * @SWG\Tag(name="Grant")
     */
    public function showAmbassadorAction(Request $request, $id ) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $grant = [];
        $message = "";
        
        try {
            $code = 200;
            $error = false;
 
            $grant_id = $id;
            $grant = $em->getRepository("App:GrantAmbassador")->find($grant_id);
 
            if (is_null($grant)) {
                $code = 500;
                $error = true;
                $message = "The Grant Ambassador does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get the current Grant Ambassador- Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $grant : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Get("/showgroup/{id}.{_format}", name="grant_show_group", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets grant ambassador info based on passed ID parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The grant ambassador with the passed ID parameter was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The Grant Ambassador ID"
     * )
     *
     * @SWG\Tag(name="Grant")
     */
    public function showGroupAction(Request $request, $id ) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $groups = [];
        $message = "";
        
        try {
            $code = 200;
            $error = false;
 
            $grant_ambassador_id = $id;
            $grantgroups = $em->getRepository("App:GrantGroup")->findBy(array("grantambassador" => $grant_ambassador_id));
            foreach ($grantgroups as $ga) {
                $groups[]= $ga->getGroup();
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get the current Grant Ambassador- Error: {$ex->getMessage()}";
        }

        $list = [
            'groups' => $groups,
            'grantgroups' => $grantgroups
        ];
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $list : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Get("/showuser/{id}.{_format}", name="grant_show_user", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets user grant info based on passed ID parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The grant user with the passed ID parameter was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The user ID"
     * )
     *
     * @SWG\Tag(name="Grant")
     */
    public function showUserAction(Request $request, $id ) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $grantgroup = [];
        $message = "";
        
        try {
            $code = 200;
            $error = false;
 
            $user_id = $id;
            $grantgroup = $em->getRepository("App:GrantGroup")->userGrant($user_id);
           
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get the current Grant Ambassador- Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $grantgroup : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Post("/ambassadorapplication/{id}.{_format}", name="grant_ambassador_application", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets grant ambassador info based on passed ID parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The grant ambassador with the passed ID parameter was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The Grant Ambassador ID"
     * )
     *
     * @SWG\Tag(name="Grant")
     */
    public function ambassadorApplicationAction(Request $request, $id ) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $grantsambassador = [];
        $message = "";
        
        try {
            $code = 200;
            $error = false;
 
            $grant_id = $id;
            $grantsambassador = $em->getRepository("App:GrantAmbassador")->findBy(array("grant" => $id));
           
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get the current Grant Ambassador- Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $grantsambassador : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Get("/showupdate/{id}.{_format}", name="grant_show_updates", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets grant info based on passed ID parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The grant with the passed ID parameter was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The Grant ID"
     * )
     *
     * @SWG\Tag(name="Grant")
     */
    public function showUpdateAction(Request $request, $id ) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $grantupdates = [];
        $message = "";
        
        try {
            $code = 200;
            $error = false;
 
            $grant_ambassador_id = $id;
            $grant_ambassador = $em->getRepository("App:GrantAmbassador")->find($grant_ambassador_id);
            $grantupdates = $em->getRepository("App:GrantUpdate")->findBy(array("grantambassador" => $grant_ambassador_id));
 
            if (is_null($grant_ambassador)) {
                $code = 500;
                $error = true;
                $message = "The Grant does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get the current Grant- Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $grantupdates : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }
    

    /**
     * @Rest\Put("/edit/{id}.{_format}", name="grant_edit", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="The grant was edited successfully."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to edit the grant."
     * )
     *
     * @SWG\Parameter(
     *     name="id_ambassador",
     *     in="body",
     *     type="string",
     *     description="Id Ambassador",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="description",
     *     in="body",
     *     type="string",
     *     description="The description of Grant",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="date",
     *     in="body",
     *     type="date",
     *     description="The date",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="participants_number",
     *     in="body",
     *     type="string",
     *     description="Number of Participants",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="amount",
     *     in="body",
     *     type="string",
     *     description="Amount of Money",
     *     schema={}
     * )
     *
     * @SWG\Tag(name="Grant")
     */
    public function editAction(Request $request, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $grant = [];
        $message = "";

        try {
            $code = 200;
            $error = false;
            $grant = $em->getRepository("App:Grant")->find($id);
 
            if (!is_null($grant)) {
                $form = $this->createForm(GrantType::class, $grant);
                $form->submit($request->request->all());

                $requestDate = strtotime($request->request->get('date'));
                $formatDate = date('Y-m-d', $requestDate);
                $grant->setDate(new \DateTime($formatDate, (new \DateTimeZone('America/New_York'))));              

 
                $em->persist($grant);
                $em->flush();
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to get a grant - Error: You must to provide fields user or the grant id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to edit the grant - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $grant : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Put("/editupdate/{id}.{_format}", name="grant_edit_update", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="The grant was edited successfully."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to edit the grant."
     * )
     *
     * @SWG\Parameter(
     *     name="description",
     *     in="body",
     *     type="string",
     *     description="The description of Grant",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="file",
     *     in="body",
     *     type="string",
     *     description="The file",
     *     schema={}
     * )
     *
     * @SWG\Tag(name="Grant")
     */
    public function editUpdateAction(Request $request, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $grantupdate = [];
        $message = "";

        try {
            $code = 200;
            $error = false;
            $grantupdate = $em->getRepository("App:GrantUpdate")->find($id);
 
            if (!is_null($grantupdate)) {
                $form = $this->createForm(GrantUpdateType::class, $grantupdate);
                $form->submit($request->request->all());
              
                $em->persist($grantupdate);
                $em->flush();
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to get a grant update - Error: You must to provide fields user or the grant id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to edit the grant update - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $grantupdate : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Put("/editambassador/{id}.{_format}", name="grant_edit_ambassador", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="The grant was edited successfully."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to edit the grant."
     * )
     *
      * @SWG\Parameter(
     *     name="id_grant",
     *     in="body",
     *     type="integer",
     *     description="Id Grant",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="id_ambassador",
     *     in="body",
     *     type="integer",
     *     description="Id Ambassador",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="code",
     *     in="body",
     *     type="string",
     *     description="The code of Ambassador",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="state",
     *     in="body",
     *     type="string",
     *     description="The state of Grant Aplication",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="amount",
     *     in="body",
     *     type="integer",
     *     description="The amount of money assigned",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="number",
     *     in="body",
     *     type="integer",
     *     description="The number of participants ",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="question1",
     *     in="body",
     *     type="string",
     *     description="The question 1 ",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="question2",
     *     in="body",
     *     type="string",
     *     description="The question 2 ",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="question3",
     *     in="body",
     *     type="string",
     *     description="The question 3 ",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="question4",
     *     in="body",
     *     type="string",
     *     description="The question 4 ",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="question5",
     *     in="body",
     *     type="string",
     *     description="The question 5 ",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="question6",
     *     in="body",
     *     type="string",
     *     description="The question 6 ",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="question7",
     *     in="body",
     *     type="string",
     *     description="The question 7 ",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="file",
     *     in="body",
     *     type="string",
     *     description="The file",
     *     schema={}
     * )
     *
     * @SWG\Tag(name="Grant")
     */
    public function editAmbassadorAction(Request $request, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $grantambassador = [];
        $message = "";

        try {
            $code = 200;
            $error = false;
            $grantambassador = $em->getRepository("App:GrantAmbassador")->find($id);
 
            if (!is_null($grantambassador)) {
                $form = $this->createForm(GrantAmbassadorType::class, $grantambassador);
                $form->submit($request->request->all());
              
                $em->persist($grantambassador);
                $em->flush();
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to get a grant update - Error: You must to provide fields user or the grant id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to edit the grant update - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $grantambassador : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Put("/revisionambassador/{id}.{_format}", name="grant_revision_ambassador", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="The grant was sent to revision successfully."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to sent to revision grant."
     * )
     *
     *
     * @SWG\Tag(name="Grant")
     */
    public function revisionAmbassadorAction(Request $request, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $grant = [];
        $message = "";

        try {
            $code = 200;
            $error = false;
            $grant_ambassador = $em->getRepository("App:GrantAmbassador")->find($id);
 
            if (!is_null($grant_ambassador)) {               
                $grant_ambassador->setState("state.revision");              

                $em->persist($grant_ambassador);
                $em->flush();
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to get a grant - Error: You must to provide fields user or the grant id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to edit the grant - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $grant_ambassador : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Put("/correctionambassador/{id}.{_format}", name="grant_correction_ambassador", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="The grant was sent to correction successfully."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to sent to correction grant."
     * )
     *
     *
     * @SWG\Tag(name="Grant")
     */
    public function correctionAmbassadorAction(Request $request, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $grant = [];
        $message = "";

        try {
            $code = 200;
            $error = false;
            $grant_ambassador = $em->getRepository("App:GrantAmbassador")->find($id);
 
            if (!is_null($grant_ambassador)) {               
                $grant_ambassador->setState("state.correction");      

                $grant_ambassador->setCorrection($request->request->get('correction'));
                $em->persist($grant_ambassador);
                $em->flush();
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to edit correction a grant - Error: You must to provide fields user or the grant ambassador id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to edit the grant - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $grant_ambassador : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Put("/approveambassador/{id}.{_format}", name="grant_approve_ambassador", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="The grant was sent to approve successfully."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to sent to approve grant."
     * )
     *
     *
     * @SWG\Tag(name="Grant")
     */
    public function approveAmbassadorAction(Request $request, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $grant = [];
        $message = "";

        try {
            $code = 200;
            $error = false;
            $grant_ambassador = $em->getRepository("App:GrantAmbassador")->find($id);
 
            if (!is_null($grant_ambassador)) {

                $grant_ambassador->setState("state.approved");  
                $grant_ambassador->setCorrection($request->request->get('correction'));       
                $grant_ambassador->setAmount($request->request->get('amount'));     

                $em->persist($grant_ambassador);
                $em->flush();
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to get a grant - Error: You must to provide fields user or the grant id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to edit the grant - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $grant_ambassador : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Put("/rejectambassador/{id}.{_format}", name="grant_reject_ambassador", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="The grant was sent to approve successfully."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to sent to approve grant."
     * )
     *
     *
     * @SWG\Tag(name="Grant")
     */
    public function rejectAmbassadorAction(Request $request, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $grant = [];
        $message = "";

        try {
            $code = 200;
            $error = false;
            $grant_ambassador = $em->getRepository("App:GrantAmbassador")->find($id);
 
            if (!is_null($grant_ambassador)) {

                $grant_ambassador->setState("state.reject");  
                $grant_ambassador->setCorrection($request->request->get('correction'));            

                $em->persist($grant_ambassador);
                $em->flush();
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to get a grant - Error: You must to provide fields user or the grant id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to edit the grant - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $grant_ambassador : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

     /**
     * @Rest\Delete("/delete/{id}.{_format}", name="grant_delete", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="grant was successfully removed"
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="An error was occurred trying to remove the grant"
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The Grant ID"
     * )
     *
     * @SWG\Tag(name="Grant")
     */
    public function deleteAction(Request $request, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        try {
            $code = 200;
            $error = false;
            $grant = $em->getRepository("App:Grant")->find($id);
 
            if (!is_null($grant)) {
                $em->remove($grant);
                $em->flush();
 
                $message = "The grant was removed successfully!";
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to remove the currrent grant - Error: The grant id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to remove the current grant - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Delete("/deleteupdate/{id}.{_format}", name="grant_delete_update", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="grant update was successfully removed"
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="An error was occurred trying to remove the grant update"
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The Grant Update ID"
     * )
     *
     * @SWG\Tag(name="Grant")
     */
    public function deleteUpdateAction(Request $request, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        try {
            $code = 200;
            $error = false;
            $grantupdate = $em->getRepository("App:GrantUpdate")->find($id);
 
            if (!is_null($$grantupdate)) {
                $em->remove($$grantupdate);
                $em->flush();
 
                $message = "The grant update was removed successfully!";
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to remove the currrent grant - Error: The grant update id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to remove the current grant update - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

 
}