<?php
/**
 * UserController.php
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
use App\Form\UserType;
use App\Form\UserEditType;
use App\Form\UserPasswordEditType;
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
 * Class UserController
 *
 * @Route("/user")
 */
class UserController extends FOSRestController
{
    
 
    /**
     * @Rest\Get("/", name="users_index", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets all users."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to get all user users."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="User ID"
     * )
     *
     *
     * @SWG\Tag(name="User")
     */
    public function indexAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $users = [];
        $message = "";
 
        try {
            $code = 200;
            $error = false;
 
            //$userId = $this->getUser()->getId();
            $users = $em->getRepository("App:User")->findAll();
            $students = $em->getRepository("App:User")->getStudents();
            $array = [];
            $alone = [];

            foreach ($users as $key => $user) {
                $repeatuser = $em->getRepository("App:User")->findBy(array('username' => $user->getUsername()) );
                if(count($repeatuser) > 1){
                    $array[]= $repeatuser;
                }
                
               
            }
            
 
            if (is_null($users)) {
                $users = [];
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get all Users - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $array : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Post("/new", name="user_new")
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
     * * @SWG\Parameter(
     *     name="language",
     *     in="query",
     *     type="string",
     *     description="The language"
     * )
     * 
     *  * * @SWG\Parameter(
     *     name="country",
     *     in="query",
     *     type="string",
     *     description="The country code"
     * )
     * 
     *  * * @SWG\Parameter(
     *     name="whatsapp",
     *     in="query",
     *     type="integer",
     *     description="The whatsapp"
     * )
     * 
     *  * * @SWG\Parameter(
     *     name="code",
     *     in="query",
     *     type="string",
     *     description="The code"
     * )
     *
     * @SWG\Tag(name="User")
     */ 
    public function newAction(Request $request, UserPasswordEncoderInterface $encoder) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        $message = "";

        $user=new User();
        //Create a form
        $form=$this->createForm(UserType::class, $user);
        $form->submit($request->request->all());
 
        try {
            $code = 200;
            $error = false;

            $password = $user->getPassword();
 
            $user->setPlainPassword($password);
            $user->setPassword($encoder->encodePassword($user, $password));
 
            $em->persist($user);
            $em->flush();
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to register the user - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $user : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Post("/reset_password", name="user_reset_password")
     *
     * @SWG\Response(
     *     response=201,
     *     description="User was successfully found"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="User was not successfully found"
     * )
     *
     *
     * @SWG\Parameter(
     *     name="username",
     *     in="body",
     *     type="string",
     *     description="The username",
     *     schema={}
     * )    
     *
     * @SWG\Tag(name="User")
     */ 
    public function resetPasswordAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        $message = "";

        try {
            $user = $em->getRepository("App:User")->findOneBy(array('username' => $request->request->get('username')));
 
            if (!is_null($user)) {
                $code = 200;
                $error = false;
                $this->sendEmail($user);
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to get a User - Error: You must to provide fields user or the user id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get the user - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $user : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    

    /**
     * @Rest\Put("/edit/{id}.{_format}", name="user_edit", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="The user was edited successfully."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to edit the user."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The user ID"
     * )
     *
     *
     *
     * @SWG\Tag(name="User")
     */
    public function editAction(Request $request, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $user = [];
        $message = "";

        
 
        try {
            $code = 200;
            $error = false;
            $user = $em->getRepository("App:User")->find($id);
 
            if (!is_null($user)) {
                $form = $this->createForm(UserEditType::class, $user);
                $form->submit($request->request->all());
                //$user->setName($name);
 
                $em->persist($user);
                $em->flush();
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to get a User - Error: You must to provide fields user or the user id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to edit the user - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $user : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

     /**
     * @Rest\Delete("/delete/{id}.{_format}", name="user_delete", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="User was successfully removed"
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="An error was occurred trying to remove the user"
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The user ID"
     * )
     *
     * @SWG\Tag(name="User")
     */
    public function deleteAction(Request $request, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        try {
            $code = 200;
            $error = false;
            $user = $em->getRepository("App:User")->find($id);
 
            if (!is_null($user)) {
                $em->remove($user);
                $em->flush();
 
                $message = "The user was removed successfully!";
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to remove the currrent user - Error: The user id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to remove the current user - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

     /**
     * @Rest\Get("/search/{key}.{_format}", name="user_show", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets user info based on passed key parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The user with the passed key parameter was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="key",
     *     in="path",
     *     type="string",
     *     description="The user key"
     * )
     *
     *
     * @SWG\Tag(name="User")
     */
    public function searchAction(Request $request, $key ) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $user = [];
        $message = "";
        
        try {
            $code = 200;
            $error = false;
 
            
            $user = $em->getRepository("App:User")->userSearch($key);
 
            if (is_null($user)) {
                $code = 500;
                $error = true;
                $message = "The user does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get the current User - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $user : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Post("/active_user", name="active_new")
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

    public function activeuserAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        try {
            $code = 200;
            $error = false;
 
            $user = $this->getUser();
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository("App:User")->find($user->getId());
            
            
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to login the user - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $user : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Get("/show/{id}.{_format}", name="user_show", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets user info based on passed ID parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The user with the passed ID parameter was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The User ID"
     * )
     *
     * @SWG\Tag(name="User")
     */
    public function showAction(Request $request, $id ) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $user = [];
        $message = "";
        
        try {
            $code = 200;
            $error = false;
 
            $user_id = $id;
            $user = $em->getRepository("App:User")->find($user_id);
 
            if (is_null($user)) {
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
            'data' => $code == 200 ? $user : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Put("/editpassword/{id}.{_format}", name="user_editpassword", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="The user was edited successfully."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to edit the user."
     * )
     *
     * @SWG\Parameter(
     *     name="password",
     *     in="path",
     *     type="string",
     *     description="The user password"
     * )
     *
     *
     *
     * @SWG\Tag(name="User")
     */
    public function editPasswordAction(Request $request, UserPasswordEncoderInterface $encoder, $id) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $user = [];
        $message = "";

        
 
        try {
            $code = 200;
            $error = false;
            $user = $em->getRepository("App:User")->find($id);
 
            if (!is_null($user)) {
                $form = $this->createForm(UserPasswordEditType::class, $user);
                $form->submit($request->request->all());
                $password = $user->getPassword();
 
                $user->setPlainPassword($password);
                $user->setPassword($encoder->encodePassword($user, $password));
    
                $em->persist($user);
                $em->flush();
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to get a User - Error: You must to provide fields user or the user id does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to edit the user - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $user : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    public function sendEmail($user){

        $subject = $this->getParameter("subject.change_password_".$user->getLanguage());
        $subjectEmail = $subject;
        $bodyEmail = "https://academy.interweavesolutions.org/user/editpassword/".$user->getId();
        //$bodyEmail = "http://localhost:3000/user/newpassword/".$user->getId();

        $message = (new \Swift_Message($subjectEmail))
         ->setFrom('myplatform@interweavesolutions.com')
         ->setTo($user->getUsername())
         ->setBody($bodyEmail);

         //$mailer->send($message);
         $this->get('mailer')->send($message);

    }

   
 
}