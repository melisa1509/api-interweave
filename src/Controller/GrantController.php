<?php

 
namespace App\Controller;
 
use App\Entity\Grant;
use App\Form\GrantType;
use App\Entity\GrantUpdate;
use App\Form\GrantUpdateType;
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
                $grants = $em->getRepository("App:Grant")->findAll();
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
     *    
     * @SWG\Tag(name="Grant")
     */ 

    public function newAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        $message = "";
        $id_ambassador = $request->request->get('id_ambassador');
        $grant=new Grant();
        //Create a form
        $form=$this->createForm(GrantType::class, $grant);
        $form->submit($request->request->all());
        
        
        try {
            $code = 200;
            $error = false;

            $ambassador = $em->getRepository("App:User")->find($id_ambassador);
 
            if (is_null($ambassador)) {
                $code = 500;
                $error = true;
                $message = "The Ambassador does not exist";
            }

            $grant->setEmbassador($ambassador);

            $requestDate = strtotime($request->request->get('date'));
            $formatDate = date('Y-m-d', $requestDate);
            $grant->setDate(new \DateTime($formatDate, (new \DateTimeZone('America/New_York'))));
           

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
        $id_grant = $request->request->get('id_grant');
        $grantupdate=new GrantUpdate();
        //Create a form
        $form=$this->createForm(GrantUpdateType::class, $grantupdate);
        $form->submit($request->request->all());
        
        
        try {
            $code = 200;
            $error = false;

            $grant = $em->getRepository("App:Grant")->find($id_grant);
 
            if (is_null($grant)) {
                $code = 500;
                $error = true;
                $message = "The Grant does not exist";
            }

            $grantupdate->setGrant($grant);                      

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
 
            $grant_id = $id;
            $grant = $em->getRepository("App:Grant")->find($grant_id);
            $grantupdates = $em->getRepository("App:GrantUpdate")->findBy(array("grant" => $grant_id));
 
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