<?php
/**
 * UnitController.php
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
 
use App\Entity\Unit;
use App\Form\UnitType;
use App\Form\UnitEditType;
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
 * Class UnitController
 *
 * @Route("/unit")
 */
class UnitController extends FOSRestController
{
    
    /**
     * @Rest\Get("/", name="unit_index", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets all units."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to get all units."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="Unit ID"
     * )
     *
     *
     * @SWG\Tag(name="Unit")
     */
    public function indexAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $units = [];
        $message = "";
 
        try {
            $code = 200;
            $error = false;
 
            $units = $em->getRepository("App:Unit")->findAll();
 
            if (is_null($units)) {
                $units = [];
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get all Unit - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $units : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

     /**
     * @Rest\Post("/new", name="Unit_new")
     *
     * @SWG\Response(
     *     response=201,
     *     description="Unit was successfully registered"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="Unit was not successfully registered"
     * )
     *
     * @SWG\Parameter(
     *     name="name",
     *     in="body",
     *     type="string",
     *     description="The name",
     *     schema={}
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
     *     name="course_id",
     *     in="body",
     *     type="string",
     *     description="The Course id of the new unit",
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
     *     name="content",
     *     in="body",
     *     type="string",
     *     description="The content",
     *     schema={}
     * )
     * 
     * @SWG\Tag(name="Unit")
     */ 

    public function newAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        $message = "";
        $unit=new Unit();
        //Create a form
        $form=$this->createForm(UnitType::class, $unit);
        $form->submit($request->request->all());
        $courseId= $request->request->get("course_id", null);
        $course = $em->getRepository("App:Course")->find($courseId);
        $unit->setCourse($course);

        try {
            $code = 200;
            $error = false;

            $em->persist($unit);
            $em->flush();
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to register the Unit - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $unit : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Get("/show/{id}.{_format}", name="Unit_show", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets Unit info based on passed ID parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The Unit with the passed ID parameter was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The Unit ID"
     * )
     *
     * @SWG\Tag(name="Unit")
     */
    public function showAction(Request $request, $id, $name ) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $unit = [];
        $message = "";
        
        try {
            $code = 200;
            $error = false;
 
            $unit_id = $id;
            $unit = $em->getRepository("App:Unit")->find($unit_id);
 
            if (is_null($unit)) {
                $code = 500;
                $error = true;
                $message = "The Unit does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get the current Unit- Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $unit : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Put("/edit/{id}.{_format}", name="Unit_edit", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="The Unit was edited successfully."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to edit the Unit."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The Unit ID"
     * )
     *
     * @SWG\Tag(name="Unit")
     */
    public function editAction(Request $request, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $unit = [];
        $message = "";

        try {
            $code = 200;
            $error = false;
            $unit = $em->getRepository("App:Unit")->find($id);
 
            if (!is_null($Unit)) {
                $form = $this->createForm(UnitEditType::class, $unit);
                $form->submit($request->request->all());
                //$curse->setName($name);
 
                $em->persist($unit);
                $em->flush();
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to get a Unit - Error: You must to provide fields user or the Unit id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to edit the Unit - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $unit : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

     /**
     * @Rest\Delete("/delete/{id}.{_format}", name="Unit_delete", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Unit was successfully removed"
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="An error was occurred trying to remove the Unit"
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The Unit ID"
     * )
     *
     * @SWG\Tag(name="Unit")
     */
    public function deleteAction(Request $request, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        try {
            $code = 200;
            $error = false;
            $unit = $em->getRepository("App:Unit")->find($id);
 
            if (!is_null($unit)) {
                $em->remove($unit);
                $em->flush();
 
                $message = "The Unit was removed successfully!";
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to remove the currrent Unit - Error: The Unit id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to remove the current Unit - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }
 
}