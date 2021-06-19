<?php
/**
 * AdminController.php
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
use App\Form\AdminType;
use App\Form\AdminEditType;
use App\Form\UserPasswordEditType;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Nelmio\AdminDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use JMS\Serializer\SerializationContext;
 
/**
 * Class AdminController
 *
 * @Route("/admin")
 */
class AdminController extends FOSRestController
{
    
 
    /**
     * @Rest\Get("/", name="admin_index", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets all admins."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to get all admin admins."
     * )
     *    
     *
     *
     * @SWG\Tag(name="Admin")
     */
    public function indexAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $admins = [];
        $message = "";
 
        try {
            $code = 200;
            $error = false;
 
            //$adminId = $this->getAdmin()->getId();
            $admins = $em->getRepository("App:User")->getAdmins();
 
            if (is_null($admins)) {
                $admins = [];
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get all Admins - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $admins : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Get("/language", name="admin_language", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets all language admins."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to get all language admins."
     * )
     *     
     *
     *
     * @SWG\Tag(name="Admin")
     */
    public function languageAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $admins = [];
        $message = "";
 
        try {
            $code = 200;
            $error = false;
 
            //$adminId = $this->getAdmin()->getId();
            $admins = $em->getRepository("App:User")->getLanguageAdmins();
 
            if (is_null($admins)) {
                $admins = [];
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get all Admins - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $admins : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Post("/new", name="admin_new")
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
     *     name="first_name",
     *     in="body",
     *     type="string",
     *     description="The first Name",
     *     schema={}
     * )
     *
     * @SWG\Parameter(
     *     name="last_name",
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
     * 
     * @SWG\Parameter(
     *     name="language",
     *     in="query",
     *     type="string",
     *     description="The dashboard language"
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
     *     name="roles",
     *     in="query",
     *     type="string",
     *     description="The role"
     * )
     * 
     * @SWG\Parameter(
     *     name="language_grader",
     *     in="query",
     *     type="string",
     *     description="The languages grader"
     * )
     *      
     *
     * @SWG\Tag(name="Admin")
     */ 
    public function newAction(Request $request, UserPasswordEncoderInterface $encoder) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        $message = "";

        $admin=new User();
        //Create a form
        $form=$this->createForm(AdminType::class, $admin);
        $form->submit($request->request->all());
        $language_grader = explode(',', $request->request->get('language_grader'));
        $message = explode(',', $request->request->get('message'));
        $user = $em->getRepository('App:User')->findOneBy(array('username' => $admin->getUsername()));
 
        try {
            $code = 200;
            $error = false;

            if(! is_null($user)){
                $code = 500;
                $error = true;
                $message = "The user already exist";
            }
            else{
                $password = ucfirst($admin->getLastName())."123";

                $admin->setPlainPassword($password);
                $admin->setPassword($encoder->encodePassword($admin, $password));
                $admin->setRoles(array($admin->getRoles()));
                $admin->setLanguageGrader($language_grader);
                $admin->setMessage($message);
    
                $em->persist($admin);
                $em->flush();
            }

            
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to register the admin - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $admin : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    

    /**
     * @Rest\Put("/edit/{id}.{_format}", name="admin_edit", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="The admin was edited successfully."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to edit the admin."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The admin ID"
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
     * 
     * @SWG\Parameter(
     *     name="language",
     *     in="query",
     *     type="string",
     *     description="The dashboard language"
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
     *     name="roles",
     *     in="query",
     *     type="string",
     *     description="The role"
     * )
     * 
     * @SWG\Parameter(
     *     name="language_grader",
     *     in="query",
     *     type="string",
     *     description="The languages grader"
     * )
     *
     *
     *
     * @SWG\Tag(name="Admin")
     */
    public function editAction(Request $request, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $admin = [];
        $message = "";

        
 
        try {
            $code = 200;
            $error = false;
            $admin = $em->getRepository("App:User")->find($id);
            $language_grader = explode(',', $request->request->get('language_grader'));
            $message = explode(',', $request->request->get('message'));
 
            if (!is_null($admin)) {
                $form = $this->createForm(AdminType::class, $admin);
                $form->submit($request->request->all());
                $admin->setRoles(array($admin->getRoles()));
                $admin->setLanguageGrader($language_grader);
                $admin->setMessage($message);
 
                $em->persist($admin);
                $em->flush();
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to get a Admin - Error: You must to provide fields admin or the admin id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to edit the admin - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $admin : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

     /**
     * @Rest\Delete("/delete/{id}.{_format}", name="admin_delete", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Admin was successfully removed"
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="An error was occurred trying to remove the admin"
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The admin ID"
     * )
     *
     * @SWG\Tag(name="Admin")
     */
    public function deleteAction(Request $request, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        try {
            $code = 200;
            $error = false;
            $admin = $em->getRepository("App:User")->find($id);
 
            if (!is_null($admin)) {
                $em->remove($admin);
                $em->flush();
 
                $message = "The admin was removed successfully!";
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to remove the currrent admin - Error: The admin id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to remove the current admin - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

     /**
     * @Rest\Get("/search/{key}.{_format}", name="admin_show", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets admin info based on passed key parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The admin with the passed key parameter was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="key",
     *     in="path",
     *     type="string",
     *     description="The admin key"
     * )
     *
     *
     * @SWG\Tag(name="Admin")
     */
    public function searchAction(Request $request, $key ) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $admin = [];
        $message = "";
        
        try {
            $code = 200;
            $error = false;
 
            
            $admin = $em->getRepository("App:User")->adminSearch($key);
 
            if (is_null($admin)) {
                $code = 500;
                $error = true;
                $message = "The admin does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get the current Admin - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $admin : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Post("/active_admin", name="active_new")
     *
     * @SWG\Response(
     *     response=201,
     *     description="Admin was successfully registered"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="Admin was not successfully registered"
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
     * @SWG\Tag(name="Admin")
     */ 

    public function activeadminAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        try {
            $code = 200;
            $error = false;
 
            $admin = $this->getAdmin();
            $em = $this->getDoctrine()->getManager();
            $admin = $em->getRepository("App:User")->find($admin->getId());
            
            
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to register the admin - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $admin : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Get("/show/{id}.{_format}", name="admin_show", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets admin info based on passed ID parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The admin with the passed ID parameter was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The Admin ID"
     * )
     *
     * @SWG\Tag(name="Admin")
     */
    public function showAction(Request $request, $id ) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $admin = [];
        $message = "";
        
        try {
            $code = 200;
            $error = false;
 
            $admin_id = $id;
            $admin = $em->getRepository("App:User")->find($admin_id);
 
            if (is_null($admin)) {
                $code = 500;
                $error = true;
                $message = "The Admin does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get the current Admin- Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $admin : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Put("/editpassword/{id}.{_format}", name="admin_editpassword", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="The admin was edited successfully."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to edit the admin."
     * )
     *
     * @SWG\Parameter(
     *     name="password",
     *     in="path",
     *     type="string",
     *     description="The admin password"
     * )
     *
     *
     *
     * @SWG\Tag(name="Admin")
     */
    public function editPasswordAction(Request $request, UserPasswordEncoderInterface $encoder, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $admin = [];
        $message = "";

        
 
        try {
            $code = 200;
            $error = false;
            $admin = $em->getRepository("App:User")->find($id);
 
            if (!is_null($admin)) {
                $form = $this->createForm(UserPasswordEditType::class, $admin);
                $form->submit($request->request->all());
                $password = $admin->getPassword();
 
                $admin->setPlainPassword($password);
                $admin->setPassword($encoder->encodePassword($admin, $password));
    
                $em->persist($admin);
                $em->flush();
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to get a Admin - Error: You must to provide fields admin or the admin id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to edit the admin - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $admin : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Get("/country/{id}.{_format}", name="admin_country", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets list admins based on passed ID Country parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The admins with the passed ID parameter was not found or doesn't exist."
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
     * @SWG\Tag(name="Admin")
     */
    public function countryAction(Request $request, $id ) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $admins = [];
        $message = "";
        
        try {
            $code = 200;
            $error = false;
  
            $country_id = $id;
            
            $admins = $em->getRepository('App:User')->userByRoleCountry('ROLE_EMBASSADOR', $country_id);
  
            if (is_null($admins)) {
                $code = 500;
                $error = true;
                $message = "The country admins does not exist";
            }
  
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get the admins country - Error: {$ex->getMessage()}";
        }
  
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $admins : $message,
        ];
  
        return new Response($serializer->serialize($response, "json", SerializationContext::create()->setGroups(array('future_admin'))));
    }

   
 
}