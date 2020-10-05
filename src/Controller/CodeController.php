<?php
/**
 * CodeController.php
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
 
use App\Entity\Certificate;
use App\Form\CertificateType;
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
 * Class CodeController
 *
 * @Route("/code")
 */
class CodeController extends FOSRestController
{
    
    /**
     * @Rest\Get("/", name="codes_index", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets all Codes."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to get all Codes."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="Code ID"
     * )
     *
     *
     * @SWG\Tag(name="Code")
     */
    public function indexAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $codes = [];
        $message = "";
 
        try {
            $code = 200;
            $error = false;
 
            $codesMBS = $em->getRepository('App:Certificate')->findBy(array('program' => 'MBS'));
            $codesJR = $em->getRepository('App:Certificate')->findBy(array('program' => 'JR'));
            $codesSA = $em->getRepository('App:Certificate')->findBy(array('program' => 'SA'));

            foreach ($codesMBS as $cd) {
                $cd->setName($this->getParameter($this->getParameter($cd->getCountry())));
            }

            foreach ($codesJR as $cd) {
                $cd->setName($this->getParameter($this->getParameter($cd->getCountry())));
            }

            foreach ($codesSA as $cd) {
                $cd->setName($this->getParameter($this->getParameter($cd->getCountry())));
            }
 
            $codes = [
                "codesMbs" => $codesMBS,
                "codesJr"  => $codesJR,
                "codesSa"  => $codesSA
            ];
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get all Code - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $codes : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

     /**
     * @Rest\Post("/new", name="code_new")
     *
     * @SWG\Response(
     *     response=201,
     *     description="Code was successfully registered"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="Code was not successfully registered"
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
     * @SWG\Tag(name="Code")
     */ 

    public function newAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        $message = "";
        $user = $this->getUser();
        $Code=new Code();
        //Create a form
        $form=$this->createForm(CodeType::class, $Code);
        $form->submit($request->request->all());
        $Code->setUser($user);
        
        try {
            $code = 200;
            $error = false;

            $em->persist($Code);
            $em->flush();
  
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to register the Code - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $Code : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Get("/show/{id}.{_format}", name="code_show", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets Code info based on passed ID parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The Code with the passed ID parameter was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The Code ID"
     * )
     *
     * @SWG\Tag(name="Code")
     */
    public function showAction(Request $request, $id ) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $codeCertificate = [];
        $message = "";
        
        try {
            $code = 200;
            $error = false;
 
            $codeCertificate_id = $id;
            $codeCertificate = $em->getRepository("App:Certificate")->find($codeCertificate_id);
 
            if (is_null($codeCertificate)) {
                $code = 500;
                $error = true;
                $message = "The Code does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get the current Code- Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $codeCertificate : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Put("/edit/{id}.{_format}", name="code_edit", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="The Code was edited successfully."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to edit the Code."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The Code ID"
     * )
     * 
     * @SWG\Parameter(
     *     name="number",
     *     in="path",
     *     type="string",
     *     description="The consecutive number code"
     * )
     *
     * @SWG\Tag(name="Code")
     */
    public function editAction(Request $request, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $codeCertificate = [];
        $message = "";

        try {
            $code = 200;
            $error = false;
            $codeCertificate = $em->getRepository("App:Certificate")->find($id);
 
            if (!is_null($codeCertificate)) {
                $number = $request->request->get("number");
                $codeCertificate->setNumber($number);
 
                $em->persist($codeCertificate);
                $em->flush();
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to get a Code - Error: You must to provide fields user or the Code id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to edit the Code - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $codeCertificate : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

     /**
     * @Rest\Delete("/delete/{id}.{_format}", name="code_delete", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Code was successfully removed"
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="An error was occurred trying to remove the Code"
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The Code ID"
     * )
     *
     * @SWG\Tag(name="Code")
     */
    public function deleteAction(Request $request, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        try {
            $code = 200;
            $error = false;
            $codeCertificate = $em->getRepository("App:Certificate")->find($id);
 
            if (!is_null($codeCertificate)) {
                $em->remove($codeCertificate);
                $em->flush();
 
                $message = "The Code was removed successfully!";
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to remove the currrent Code - Error: The Code id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to remove the current Code - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

   
 
}