<?php
/**
 * EvaluationController.php
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
 
use App\Entity\Evaluation;
use App\Form\EvaluationPreType;
use App\Form\EvaluationPostType;
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
 
/**
 * Class EvaluationController
 *
 * @Route("/evaluation")
 */
class EvaluationController extends FOSRestController
{
    
    /**
     * @Rest\Post("/", name="evaluations_index")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets all evaluations."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to get all evaluations."
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
     * @SWG\Tag(name="Evaluation")
     */
    public function indexAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $evaluations = [];
        $message = "";
 
        try {
            $code = 200;
            $error = false;
 
            $role = $request->request->get('role');
            $id = $request->request->get('id_ambassador');
            if($role == 'ROLE_ADMIN' or $role == 'ROLE_LANGUAGE_ADMIN'){
                $evaluations = $em->getRepository("App:Evaluation")->findAll();
            }
            else{
                $evaluations = $em->getRepository("App:Evaluation")->findBy(array('embassador' => $id));
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get all Evaluation - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $evaluations : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

     /**
     * @Rest\Post("/pre", name="evaluation_pre")
     *
     * @SWG\Response(
     *     response=201,
     *     description="Evaluation was successfully registered"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="Evaluation was not successfully registered"
     * )
     *
     * @SWG\Parameter(
     *     name="id_student",
     *     in="body",
     *     type="string",
     *     description="Id Student",
     *     schema={}
     * )
     *      
     *     
     * @SWG\Parameter(
     *     name="question1",
     *     in="body",
     *     type="string",
     *     description="The value of question 1",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="question2",
     *     in="body",
     *     type="string",
     *     description="The value of question 2",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="question3",
     *     in="body",
     *     type="string",
     *     description="The value of question 3",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="question4",
     *     in="body",
     *     type="string",
     *     description="The value of question 4",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="question5",
     *     in="body",
     *     type="string",
     *     description="The value of question 5",
     *     schema={}
     * )
     *      
     * @SWG\Parameter(
     *     name="question6",
     *     in="body",
     *     type="string",
     *     description="The value of question 6",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="question7",
     *     in="body",
     *     type="string",
     *     description="The value of question 7",
     *     schema={}
     * )
     * 
     * 
     *    
     * @SWG\Tag(name="Evaluation")
     */ 

    public function preAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        $message = "";
        $id_student = $request->request->get('id_student');

        $evaluation = $student = $em->getRepository("App:Evaluation")->findOneBy(array('student' => $id_student));
        if (is_null($evaluation)) {
            $evaluation=new Evaluation();
        }
        
        //Create a form
        $form=$this->createForm(EvaluationPreType::class, $evaluation);
        $form->submit($request->request->all());
        
        
        try {
            $code = 200;
            $error = false;

            $student = $em->getRepository("App:User")->find($id_student);
 
            if (is_null($student)) {
                $code = 500;
                $error = true;
                $message = "The Student does not exist";
            }

            $evaluation->setStudent($student);

           

            $em->persist($evaluation);
            $em->flush();
  
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to register the Evaluation - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $evaluation : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Post("/post", name="evaluation_post")
     *
     * @SWG\Response(
     *     response=201,
     *     description="Evaluation was successfully registered"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="Evaluation was not successfully registered"
     * )
     *
     * @SWG\Parameter(
     *     name="id_student",
     *     in="body",
     *     type="string",
     *     description="Id Student",
     *     schema={}
     * )
     *     
     *     
     * @SWG\Parameter(
     *     name="postquestion1",
     *     in="body",
     *     type="string",
     *     description="The value of postquestion 1",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="postquestion2",
     *     in="body",
     *     type="string",
     *     description="The value of postquestion 2",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="postquestion3",
     *     in="body",
     *     type="string",
     *     description="The value of postquestion 3",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="postquestion4",
     *     in="body",
     *     type="string",
     *     description="The value of postquestion 4",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="postquestion5",
     *     in="body",
     *     type="string",
     *     description="The value of postquestion 5",
     *     schema={}
     * )
     *      
     * @SWG\Parameter(
     *     name="postquestion6",
     *     in="body",
     *     type="string",
     *     description="The value of postquestion 6",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="postquestion7",
     *     in="body",
     *     type="string",
     *     description="The value of postquestion 7",
     *     schema={}
     * )
     * 
     * 
     * @SWG\Parameter(
     *     name="postquestion8",
     *     in="body",
     *     type="string",
     *     description="The value of postquestion 8",
     *     schema={}
     * )
     * 
     * 
     * @SWG\Parameter(
     *     name="postquestion9",
     *     in="body",
     *     type="string",
     *     description="The value of postquestion 9",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="postquestion10",
     *     in="body",
     *     type="string",
     *     description="The value of postquestion 10",
     *     schema={}
     * )
     *    
     * @SWG\Tag(name="Evaluation")
     */ 

    public function postAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        $message = "";
        $id_student = $request->request->get('id_student');

        $evaluation = $student = $em->getRepository("App:Evaluation")->findOneBy(array('student' => $id_student));
        if (is_null($evaluation)) {
            $evaluation=new Evaluation();
        }
        
        //Create a form
        $form=$this->createForm(EvaluationPostType::class, $evaluation);
        $form->submit($request->request->all());
        
        
        try {
            $code = 200;
            $error = false;

            $student = $em->getRepository("App:User")->find($id_student);
 
            if (is_null($student)) {
                $code = 500;
                $error = true;
                $message = "The Student does not exist";
            }

            $evaluation->setStudent($student);

           

            $em->persist($evaluation);
            $em->flush();
  
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to register the Evaluation - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $evaluation : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Get("/show/{id}.{_format}", name="evaluation_show", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets evaluation info based on passed ID parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The evaluation with the passed ID parameter was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The Student ID"
     * )
     *
     * @SWG\Tag(name="Evaluation")
     */
    public function showAction(Request $request, $id ) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $evaluation = [];
        $message = "";
        
        try {
            $code = 200;
            $error = false;
 
            $student_id = $id;
            $student = $em->getRepository("App:User")->find($student_id);
            $evaluation = $em->getRepository("App:Evaluation")->findOneBy(array('student' => $student_id));
            $evaluation->setStudent($student);
 
            if (is_null($evaluation)) {
                $code = 500;
                $error = true;
                $message = "The Evaluation does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get the current Evaluation- Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $evaluation : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Put("/edit/{id}.{_format}", name="evaluation_edit", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="The evaluation was edited successfully."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to edit the evaluation."
     * )
     *
       * @SWG\Parameter(
     *     name="name",
     *     in="body",
     *     type="string",
     *     description="The name of Evaluation",
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
     * @SWG\Tag(name="Evaluation")
     */
    public function editAction(Request $request, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $evaluation = [];
        $message = "";

        try {
            $code = 200;
            $error = false;
            $evaluation = $em->getRepository("App:Evaluation")->find($id);
 
            if (!is_null($evaluation)) {
                $form = $this->createForm(EvaluationType::class, $evaluation);
                $form->submit($request->request->all());

                $requestFinalDate = strtotime($request->request->get('finalDate'));
                $formatFinalDate = date('Y-m-d', $requestFinalDate);
                $evaluation->setFinalDate(new \DateTime($formatFinalDate, (new \DateTimeZone('America/New_York'))));

                $requestStartDate = strtotime($request->request->get('startDate'));
                $formatStartDate = date('Y-m-d', $requestStartDate);
                $evaluation->setStartDate(new \DateTime($formatFinalDate, (new \DateTimeZone('America/New_York'))));

                $requestGraduationDate = strtotime($request->request->get('graduationDate'));
                $formatGraduationDate = date('Y-m-d', $requestGraduationDate);
                $evaluation->setGraduationDate(new \DateTime($formatGraduationDate, (new \DateTimeZone('America/New_York'))));

                $evaluation->setNumberStudents(0);
 
                $em->persist($evaluation);
                $em->flush();
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to get a evaluation - Error: You must to provide fields user or the evaluation id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to edit the evaluation - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $evaluation : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

     /**
     * @Rest\Delete("/delete/{id}.{_format}", name="evaluation_delete", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="evaluation was successfully removed"
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="An error was occurred trying to remove the evaluation"
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The Evaluation ID"
     * )
     *
     * @SWG\Tag(name="Evaluation")
     */
    public function deleteAction(Request $request, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        try {
            $code = 200;
            $error = false;
            $evaluation = $em->getRepository("App:Evaluation")->find($id);
 
            if (!is_null($evaluation)) {
                $em->remove($evaluation);
                $em->flush();
 
                $message = "The evaluation was removed successfully!";
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to remove the currrent evaluation - Error: The evaluation id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to remove the current evaluation - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    

   
 
}