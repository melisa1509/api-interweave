<?php
/**
 * GroupController.php
 *
 * API Controller
 *
 * @category   Controller
 * @package    MyKanban
 * @author     Francisco Ugalde
 * @copyright  2018 www.franciscougalde.com
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 */
 
namespace App\Controller;
 
use App\Entity\Groupe;
use App\Form\GroupType;
use App\Form\GroupEditType;
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
 * Class GroupController
 *
 * @Route("/group")
 */
class GroupController extends FOSRestController
{
    
    /**
     * @Rest\Post("/", name="groups_index")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets all groups."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to get all groups."
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
     * @SWG\Tag(name="Group")
     */
    public function indexAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $groups = [];
        $message = "";
 
        try {
            $code = 200;
            $error = false;
 
            $role = $request->request->get('role');
            $id = $request->request->get('id_ambassador');
            if($role == 'ROLE_ADMIN' or $role == 'ROLE_LANGUAGE_ADMIN'){
                $groups = $em->getRepository("App:Groupe")->findBy(array(), array('startDate' => 'DESC'));
            }
            else if($role == 'ROLE_EMBASSADOR' or $role == 'ROLE_STUDENT_EMBASSADOR'){
                $groups = $em->getRepository("App:Groupe")->findBy(array('embassador' => $id), array('startDate' => 'DESC'));
            }
            else{
                $groups = $em->getRepository("App:Groupe")->findBy(array(), array('startDate' => 'DESC'));
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get all Group - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $groups : $message,
        ];
 
        return new Response($serializer->serialize($response, "json", SerializationContext::create()->setGroups(array('group_list'))));
    }


     /**
     * @Rest\Post("/new", name="Group_new")
     *
     * @SWG\Response(
     *     response=201,
     *     description="Group was successfully registered"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="Group was not successfully registered"
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
     *     name="name",
     *     in="body",
     *     type="string",
     *     description="The name of Group",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="startDate",
     *     in="body",
     *     type="string",
     *     description="The start date",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="finalDate",
     *     in="body",
     *     type="string",
     *     description="The final date",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="graduationDate",
     *     in="body",
     *     type="string",
     *     description="The graduation date",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="modality",
     *     in="body",
     *     type="string",
     *     description="modality",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="program",
     *     in="body",
     *     type="string",
     *     description="type of program",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="interweaveLocal",
     *     in="body",
     *     type="string",
     *     description="Local Organization",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="authorizationCode",
     *     in="body",
     *     type="string",
     *     description="authorization code",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="name_image",
     *     in="body",
     *     type="string",
     *     description="the name of file",
     *     schema={}
     * )
     * 
     *    
     * @SWG\Tag(name="Group")
     */ 

    public function newAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        $message = "";
        $id_ambassador = $request->request->get('id_ambassador');
        $group=new Groupe();
        //Create a form
        $form=$this->createForm(GroupType::class, $group);
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

            $group->setEmbassador($ambassador);

            $requestFinalDate = strtotime($request->request->get('finalDate'));
            $formatFinalDate = date('Y-m-d', $requestFinalDate);
            $group->setFinalDate(new \DateTime($formatFinalDate, (new \DateTimeZone('America/New_York'))));

            $requestStartDate = strtotime($request->request->get('startDate'));
            $formatStartDate = date('Y-m-d', $requestStartDate);
            $group->setStartDate(new \DateTime($formatStartDate, (new \DateTimeZone('America/New_York'))));

            $requestGraduationDate = strtotime($request->request->get('graduationDate'));
            $formatGraduationDate = date('Y-m-d', $requestGraduationDate);
            $group->setGraduationDate(new \DateTime($formatGraduationDate, (new \DateTimeZone('America/New_York'))));

            $group->setNumberStudents(0);

            $em->persist($group);
            $em->flush();
  
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to register the Group - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $group : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Get("/show/{id}.{_format}", name="group_show", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets group info based on passed ID parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The group with the passed ID parameter was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The Group ID"
     * )
     *
     * @SWG\Tag(name="Group")
     */
    public function showAction(Request $request, $id ) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $group = [];
        $message = "";
        
        try {
            $code = 200;
            $error = false;
 
            $group_id = $id;
            $group = $em->getRepository("App:Groupe")->find($group_id);
 
            if (is_null($group)) {
                $code = 500;
                $error = true;
                $message = "The Group does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get the current Group- Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $group : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Put("/edit/{id}.{_format}", name="group_edit", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="The group was edited successfully."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to edit the group."
     * )
     *
       * @SWG\Parameter(
     *     name="name",
     *     in="body",
     *     type="string",
     *     description="The name of Group",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="startDate",
     *     in="body",
     *     type="string",
     *     description="The start date",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="finalDate",
     *     in="body",
     *     type="string",
     *     description="The final date",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="graduationDate",
     *     in="body",
     *     type="string",
     *     description="The graduation date",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="modality",
     *     in="body",
     *     type="string",
     *     description="modality",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="program",
     *     in="body",
     *     type="string",
     *     description="type of program",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="interweaveLocal",
     *     in="body",
     *     type="string",
     *     description="Local Organization",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="authorizationCode",
     *     in="body",
     *     type="string",
     *     description="authorization code",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="name_image",
     *     in="body",
     *     type="string",
     *     description="the name of file",
     *     schema={}
     * )
     *
     * @SWG\Tag(name="Group")
     */
    public function editAction(Request $request, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $group = [];
        $message = "";

        try {
            $code = 200;
            $error = false;
            $group = $em->getRepository("App:Groupe")->find($id);
 
            if (!is_null($group)) {
                $form = $this->createForm(GroupType::class, $group);
                $form->submit($request->request->all());

                $requestFinalDate = strtotime($request->request->get('finalDate'));
                $formatFinalDate = date('Y-m-d', $requestFinalDate);
                $group->setFinalDate(new \DateTime($formatFinalDate, (new \DateTimeZone('America/New_York'))));

                $requestStartDate = strtotime($request->request->get('startDate'));
                $formatStartDate = date('Y-m-d', $requestStartDate);
                $group->setStartDate(new \DateTime($formatStartDate, (new \DateTimeZone('America/New_York'))));

                $requestGraduationDate = strtotime($request->request->get('graduationDate'));
                $formatGraduationDate = date('Y-m-d', $requestGraduationDate);
                $group->setGraduationDate(new \DateTime($formatGraduationDate, (new \DateTimeZone('America/New_York'))));

                $group->setNumberStudents(0);
 
                $em->persist($group);
                $em->flush();
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to get a group - Error: You must to provide fields user or the group id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to edit the group - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $group : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

     /**
     * @Rest\Delete("/delete/{id}.{_format}", name="group_delete", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="group was successfully removed"
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="An error was occurred trying to remove the group"
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The Group ID"
     * )
     *
     * @SWG\Tag(name="Group")
     */
    public function deleteAction(Request $request, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        try {
            $code = 200;
            $error = false;
            $group = $em->getRepository("App:Groupe")->find($id);
 
            if (!is_null($group)) {
                $em->remove($group);
                $em->flush();
 
                $message = "The group was removed successfully!";
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to remove the currrent group - Error: The group id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to remove the current group - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Post("/program", name="group_program")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets all groups."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to get all groups."
     * )
     *
     * @SWG\Parameter(
     *     name="program",
     *     in="path",
     *     type="string",
     *     description="The program group"
     * )  
     *
     * @SWG\Tag(name="Group")
     */
    public function programAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $groups = [];
        $message = "";
 
        try {
            $code = 200;
            $error = false;
 
            $program = $request->request->get('program');
            $groups = $em->getRepository("App:Groupe")->findBy(array('program' => $program));
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get all Group - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $groups : $message,
        ];
 
        return new Response($serializer->serialize($response, "json", SerializationContext::create()->setGroups(array('group_list'))));
    }  

   
 
}