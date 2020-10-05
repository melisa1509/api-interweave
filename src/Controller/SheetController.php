<?php
/**
 * SheetController.php
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
 
use App\Entity\Sheet;
use App\Form\SheetType;
use App\Form\SheetEditType;
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
 * Class SheetController
 *
 * @Route("/sheet")
 */
class SheetController extends FOSRestController
{
    
    /**
     * @Rest\Get("/", name="sheet_index", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets all Sheets."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to get all Sheets."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="Sheet ID"
     * )
     *
     *
     * @SWG\Tag(name="Sheet")
     */
    public function indexAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $sheets = [];
        $message = "";
 
        try {
            $code = 200;
            $error = false;
 
            $sheets = $em->getRepository("App:Sheet")->findAll();
 
            if (is_null($sheets)) {
                $Sheets = [];
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get all Sheet - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $sheets : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

     /**
     * @Rest\Post("/new", name="sheet_new")
     *
     * @SWG\Response(
     *     response=201,
     *     description="Sheet was successfully registered"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="Sheet was not successfully registered"
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
     *     name="unit_id",
     *     in="body",
     *     type="string",
     *     description="The Unit id of the new Sheet",
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
     * @SWG\Tag(name="Sheet")
     */ 

    public function newAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        $message = "";
        $sheet=new Sheet();
        //Create a form
        $form=$this->createForm(SheetType::class, $sheet);
        $form->submit($request->request->all());
        $unitId= $request->request->get("unit_id", null);
        $unit = $em->getRepository("App:Unit")->find($unitId);
        $sheet->setUnit($unit);

        try {
            $code = 200;
            $error = false;

            $em->persist($sheet);
            $em->flush();
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to register the Sheet - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $sheet : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Get("/show/{id}.{_format}", name="Sheet_show", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets Sheet info based on passed ID parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The Sheet with the passed ID parameter was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The Sheet ID"
     * )
     *
     * @SWG\Tag(name="Sheet")
     */
    public function showAction(Request $request, $id, $name ) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $sheet = [];
        $message = "";
        
        try {
            $code = 200;
            $error = false;
 
            $sheet_id = $id;
            $sheet = $em->getRepository("App:Sheet")->find($sheet_id);
 
            if (is_null($sheet)) {
                $code = 500;
                $error = true;
                $message = "The Sheet does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get the current Sheet- Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $sheet : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Put("/edit/{id}.{_format}", name="Sheet_edit", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="The Sheet was edited successfully."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to edit the Sheet."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The Sheet ID"
     * )
     *
     * @SWG\Tag(name="Sheet")
     */
    public function editAction(Request $request, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $sheet = [];
        $message = "";

        try {
            $code = 200;
            $error = false;
            $sheet = $em->getRepository("App:Sheet")->find($id);
 
            if (!is_null($sheet)) {
                $form = $this->createForm(SheetEditType::class, $sheet);
                $form->submit($request->request->all());
                //$curse->setName($name);
 
                $em->persist($sheet);
                $em->flush();
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to get a Sheet - Error: You must to provide fields user or the Sheet id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to edit the Sheet - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $sheet : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

     /**
     * @Rest\Delete("/delete/{id}.{_format}", name="Sheet_delete", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Sheet was successfully removed"
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="An error was occurred trying to remove the Sheet"
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The Sheet ID"
     * )
     *
     * @SWG\Tag(name="Sheet")
     */
    public function deleteAction(Request $request, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        try {
            $code = 200;
            $error = false;
            $sheet = $em->getRepository("App:Sheet")->find($id);
 
            if (!is_null($sheet)) {
                $em->remove($sheet);
                $em->flush();
 
                $message = "The Sheet was removed successfully!";
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to remove the currrent Sheet - Error: The Sheet id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to remove the current Sheet - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }
 
}