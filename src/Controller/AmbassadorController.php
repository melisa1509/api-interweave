<?php
/**
 * AmbassadorController.php
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
 
use App\Entity\User;
use App\Entity\Task;
use App\Form\AmbassadorType;
use App\Form\AmbassadorEditType;
use App\Form\UserPasswordEditType;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Nelmio\AmbassadorDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use JMS\Serializer\SerializationContext;
 
/**
 * Class AmbassadorController
 *
 * @Route("/ambassador")
 */
class AmbassadorController extends FOSRestController
{
    
 
    /**
     * @Rest\Get("/", name="ambassadors_index", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets all ambassadors."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to get all ambassador ambassadors."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="Ambassador ID"
     * )
     *
     *
     * @SWG\Tag(name="Ambassador")
     */
    public function indexAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $ambassadors = [];
        $message = "";
 
        try {
            $code = 200;
            $error = false;
 
            //$ambassadorId = $this->getAmbassador()->getId();
            $ambassadors = $em->getRepository("App:User")->getAmbassadors();
 
            if (is_null($ambassadors)) {
                $ambassadors = [];
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get all Ambassadors - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $ambassadors : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Post("/new", name="ambassador_new")
     *
     * @SWG\Response(
     *     response=201,
     *     description="User was successfully registered"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="User was not successfully registered"
     * )
     *
     * @SWG\Parameter(
     *     name="firtName",
     *     in="body",
     *     type="string",
     *     description="The first Name",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="lasName",
     *     in="body",
     *     type="string",
     *     description="The last name",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="username",
     *     in="body",
     *     type="string",
     *     description="The username",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="password",
     *     in="query",
     *     type="string",
     *     description="The password"
     * )
     * 
     * @SWG\Parameter(
     *     name="language",
     *     in="query",
     *     type="string",
     *     description="The language"
     * )
     * 
     * @SWG\Parameter(
     *     name="country",
     *     in="query",
     *     type="string",
     *     description="The country code"
     * )
     * 
     * @SWG\Parameter(
     *     name="city",
     *     in="query",
     *     type="string",
     *     description="The city"
     * )
     * 
     * @SWG\Parameter(
     *     name="whatsapp",
     *     in="query",
     *     type="integer",
     *     description="The whatsapp"
     * )
     * 
     * @SWG\Parameter(
     *     name="code",
     *     in="query",
     *     type="string",
     *     description="The code"
     * )
     *
     * @SWG\Tag(name="Ambassador")
     */ 
    public function newAction(Request $request, UserPasswordEncoderInterface $encoder) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        $message = "";

        $ambassador=new User();
        //Create a form
        $form=$this->createForm(AmbassadorType::class, $ambassador);
        $form->submit($request->request->all());
        $user = $em->getRepository('App:User')->findOneBy(array('username' => $ambassador->getUsername()));
 
        try {
            $code = 200;
            $error = false;

            if(! is_null($user)){
                $code = 500;
                $error = true;
                $message = "The user already exist";
            }
            else{

                $password = ucfirst($ambassador->getLastName())."123";

    
                $ambassador->setPlainPassword($password);
                $ambassador->setPassword($encoder->encodePassword($ambassador, $password));
                $ambassador->setRoles(['ROLE_EMBASSADOR']);
    
                $em->persist($ambassador);
                $em->flush();
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to register the ambassador - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $ambassador : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    

    /**
     * @Rest\Put("/edit/{id}.{_format}", name="ambassador_edit", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="The ambassador was edited successfully."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to edit the ambassador."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The ambassador ID"
     * )
     * 
     * @SWG\Parameter(
     *     name="firtName",
     *     in="body",
     *     type="string",
     *     description="The first Name",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="lasName",
     *     in="body",
     *     type="string",
     *     description="The last name",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="username",
     *     in="body",
     *     type="string",
     *     description="The username",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="password",
     *     in="query",
     *     type="string",
     *     description="The password"
     * )
     * 
     * @SWG\Parameter(
     *     name="language",
     *     in="query",
     *     type="string",
     *     description="The language"
     * )
     * 
     * @SWG\Parameter(
     *     name="country",
     *     in="query",
     *     type="string",
     *     description="The country code"
     * )
     * 
     * @SWG\Parameter(
     *     name="city",
     *     in="query",
     *     type="string",
     *     description="The city"
     * )
     * 
     * @SWG\Parameter(
     *     name="whatsapp",
     *     in="query",
     *     type="integer",
     *     description="The whatsapp"
     * )
     * 
     * @SWG\Parameter(
     *     name="code",
     *     in="query",
     *     type="string",
     *     description="The code" 
     * )
     *
     *
     *
     * @SWG\Tag(name="Ambassador")
     */
    public function editAction(Request $request, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $ambassador = [];
        $message = "";

        
 
        try {
            $code = 200;
            $error = false;
            $ambassador = $em->getRepository("App:User")->find($id);
 
            if (!is_null($ambassador)) {
                $form = $this->createForm(AmbassadorEditType::class, $ambassador);
                $form->submit($request->request->all());
                //$ambassador->setName($name);
 
                $em->persist($ambassador);
                $em->flush();
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to get a Ambassador - Error: You must to provide fields ambassador or the ambassador id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to edit the ambassador - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $ambassador : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

     /**
     * @Rest\Delete("/delete/{id}.{_format}", name="ambassador_delete", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Ambassador was successfully removed"
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="An error was occurred trying to remove the ambassador"
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The ambassador ID"
     * )
     *
     * @SWG\Tag(name="Ambassador")
     */
    public function deleteAction(Request $request, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        try {
            $code = 200;
            $error = false;
            $ambassador = $em->getRepository("App:User")->find($id);
 
            if (!is_null($ambassador)) {
                $em->remove($ambassador);
                $em->flush();
 
                $message = "The ambassador was removed successfully!";
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to remove the currrent ambassador - Error: The ambassador id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to remove the current ambassador - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

     /**
     * @Rest\Get("/search/{key}.{_format}", name="ambassador_show", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets ambassador info based on passed key parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The ambassador with the passed key parameter was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="key",
     *     in="path",
     *     type="string",
     *     description="The ambassador key"
     * )
     *
     *
     * @SWG\Tag(name="Ambassador")
     */
    public function searchAction(Request $request, $key ) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $ambassador = [];
        $message = "";
        
        try {
            $code = 200;
            $error = false;
 
            
            $ambassador = $em->getRepository("App:User")->ambassadorSearch($key);
 
            if (is_null($ambassador)) {
                $code = 500;
                $error = true;
                $message = "The ambassador does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get the current Ambassador - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $ambassador : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Post("/active_ambassador", name="active_new")
     *
     * @SWG\Response(
     *     response=201,
     *     description="Ambassador was successfully registered"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="Ambassador was not successfully registered"
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
     * @SWG\Tag(name="Ambassador")
     */ 

    public function activeambassadorAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        try {
            $code = 200;
            $error = false;
 
            $ambassador = $this->getAmbassador();
            $em = $this->getDoctrine()->getManager();
            $ambassador = $em->getRepository("App:User")->find($ambassador->getId());
            
            
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to register the ambassador - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $ambassador : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Get("/show/{id}.{_format}", name="ambassador_show", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets ambassador info based on passed ID parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The ambassador with the passed ID parameter was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The Ambassador ID"
     * )
     *
     * @SWG\Tag(name="Ambassador")
     */
    public function showAction(Request $request, $id ) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $ambassador = [];
        $message = "";
        
        try {
            $code = 200;
            $error = false;
 
            $ambassador_id = $id;
            $ambassador = $em->getRepository("App:User")->find($ambassador_id);
 
            if (is_null($ambassador)) {
                $code = 500;
                $error = true;
                $message = "The Ambassador does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get the current Ambassador- Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $ambassador : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Put("/editpassword/{id}.{_format}", name="ambassador_editpassword", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="The ambassador was edited successfully."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to edit the ambassador."
     * )
     *
     * @SWG\Parameter(
     *     name="password",
     *     in="path",
     *     type="string",
     *     description="The ambassador password"
     * )
     *
     *
     *
     * @SWG\Tag(name="Ambassador")
     */
    public function editPasswordAction(Request $request, UserPasswordEncoderInterface $encoder, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $ambassador = [];
        $message = "";

        
 
        try {
            $code = 200;
            $error = false;
            $ambassador = $em->getRepository("App:User")->find($id);
 
            if (!is_null($ambassador)) {
                $form = $this->createForm(UserPasswordEditType::class, $ambassador);
                $form->submit($request->request->all());
                $password = $ambassador->getPassword();
 
                $ambassador->setPlainPassword($password);
                $ambassador->setPassword($encoder->encodePassword($ambassador, $password));
    
                $em->persist($ambassador);
                $em->flush();
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to get a Ambassador - Error: You must to provide fields ambassador or the ambassador id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to edit the ambassador - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $ambassador : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Get("/country/{id}.{_format}", name="ambassador_country", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets list ambassadors based on passed ID Country parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The ambassadors with the passed ID parameter was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The country ID"
     * )
     *
     *
     * @SWG\Tag(name="Ambassador")
     */
    public function countryAction(Request $request, $id ) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $ambassadors = [];
        $message = "";
        
        try {
            $code = 200;
            $error = false;
  
            $country_id = $id;
            
            $ambassadors = $em->getRepository('App:User')->userByRoleCountry('ROLE_EMBASSADOR', $country_id);
  
            if (is_null($ambassadors)) {
                $code = 500;
                $error = true;
                $message = "The country ambassadors does not exist";
            }
  
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get the ambassadors country - Error: {$ex->getMessage()}";
        }
  
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $ambassadors : $message,
        ];
  
        return new Response($serializer->serialize($response, "json", SerializationContext::create()->setGroups(array('future_ambassador'))));
    }

   
 
}