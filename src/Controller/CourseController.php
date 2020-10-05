<?php
/**
 * CourseController.php
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
 
use App\Entity\Course;
use App\Form\CourseType;
use App\Form\CourseEditType;
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
 * Class CourseController
 *
 * @Route("/course")
 */
class CourseController extends FOSRestController
{
    
    /**
     * @Rest\Get("/", name="courses_index", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets all courses."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to get all courses."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="Course ID"
     * )
     *
     *
     * @SWG\Tag(name="Course")
     */
    public function indexAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $courses = [];
        $message = "";
 
        try {
            $code = 200;
            $error = false;
 
            //$userId = $this->getUser()->getId();
            $courses = $em->getRepository("App:Course")->findAll();
 
            if (is_null($courses)) {
                $courses = [];
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get all Course - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $courses : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

     /**
     * @Rest\Post("/new", name="Course_new")
     *
     * @SWG\Response(
     *     response=201,
     *     description="Course was successfully registered"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="Course was not successfully registered"
     * )
     *
     * @SWG\Parameter(
     *     name="state",
     *     in="body",
     *     type="string",
     *     description="The state",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="description",
     *     in="body",
     *     type="string",
     *     description="The description",
     *     schema={}
     * )
     * 
     * @SWG\Parameter(
     *     name="language",
     *     in="query",
     *     type="string",
     *     description="The language",
     * )
     * @SWG\Tag(name="Course")
     */ 

    public function newAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        $message = "";
        $user = $this->getUser();
        $course=new Course();
        //Create a form
        $form=$this->createForm(CourseType::class, $course);
        $form->submit($request->request->all());
        $course->setUser($user);
        
        try {
            $code = 200;
            $error = false;

            $em->persist($course);
            $em->flush();
  
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to register the Course - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $course : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Get("/show/{id}.{_format}", name="course_show", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets course info based on passed ID parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The course with the passed ID parameter was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The Course ID"
     * )
     *
     * @SWG\Tag(name="Course")
     */
    public function showAction(Request $request, $id ) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $course = [];
        $message = "";
        
        try {
            $code = 200;
            $error = false;
 
            $course_id = $id;
            $course = $em->getRepository("App:Course")->find($course_id);
 
            if (is_null($course)) {
                $code = 500;
                $error = true;
                $message = "The Course does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get the current Course- Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $course : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Put("/edit/{id}.{_format}", name="course_edit", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="The course was edited successfully."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to edit the course."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The course ID"
     * )
     *
     * @SWG\Tag(name="Course")
     */
    public function editAction(Request $request, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $course = [];
        $message = "";

        try {
            $code = 200;
            $error = false;
            $course = $em->getRepository("App:Course")->find($id);
 
            if (!is_null($course)) {
                $form = $this->createForm(CourseEditType::class, $course);
                $form->submit($request->request->all());
                //$curse->setName($name);
 
                $em->persist($course);
                $em->flush();
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to get a course - Error: You must to provide fields user or the course id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to edit the course - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $course : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

     /**
     * @Rest\Delete("/delete/{id}.{_format}", name="course_delete", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="course was successfully removed"
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="An error was occurred trying to remove the course"
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The Course ID"
     * )
     *
     * @SWG\Tag(name="Course")
     */
    public function deleteAction(Request $request, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        try {
            $code = 200;
            $error = false;
            $course = $em->getRepository("App:Course")->find($id);
 
            if (!is_null($course)) {
                $em->remove($course);
                $em->flush();
 
                $message = "The course was removed successfully!";
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to remove the currrent course - Error: The course id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to remove the current course - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

   
 
}