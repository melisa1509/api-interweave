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
use App\Entity\StudentGroup;
use App\Entity\StudentAmbassadorGroup;
use App\Form\UserType;
use App\Form\StudentType;
use App\Form\UserEditType;
use Doctrine\Common\Collections\ArrayCollection;
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
 * Class StudentController
 *
 * @Route("/student")
 */
class StudentController extends FOSRestController
{
    
 
    /**
     * @Rest\Get("/", name="students_index", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets all students."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to get all students."
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
     * @SWG\Tag(name="Student")
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
            $users = $em->getRepository("App:User")->getStudents();
 
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
            'data' => $code == 200 ? $users : $message,
        ];
 
        return new Response($serializer->serialize($response, "json", SerializationContext::create()->setGroups(array('student_list'))));
    }

    /**
     * @Rest\Get("/mbs", name="students_mbs", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets all students."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to get all students."
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
     * @SWG\Tag(name="Student")
     */
    public function mbsAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $users = [];
        $message = "";
 
        try {
            $code = 200;
            $error = false;
 
            //$userId = $this->getUser()->getId();
            $users = $em->getRepository("App:User")->getMbsStudents();
 
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
            'data' => $code == 200 ? $users : $message,
        ];
 
        return new Response($serializer->serialize($response, "json", SerializationContext::create()->setGroups(array('student_list'))));
    }

    /**
     * @Rest\Post("/new", name="student_new")
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
     * @SWG\Parameter(
     *     name="whatsapp",
     *     in="query",
     *     type="integer",
     *     description="The whatsapp"
     * )
     * 
     * @SWG\Parameter(
     *     name="city",
     *     in="query",
     *     type="string",
     *     description="The city of student"
     * )
     * 
     * @SWG\Parameter(
     *     name="modality",
     *     in="query",
     *     type="string",
     *     description="The method of participation"
     * )
     *
     * @SWG\Tag(name="Student")
     */ 
    public function newAction(Request $request, UserPasswordEncoderInterface $encoder) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        $message = "";

        $user=new User();
        //Create a form
        $form=$this->createForm(StudentType::class, $user);
        $form->submit($request->request->all());

        $group = $em->getRepository('App:Groupe')->find($request->request->get('group'));
        $student = $em->getRepository('App:User')->findOneBy(array('username' => $user->getUsername()));
        $password = $request->request->get('password');
 
        try {
            $code = 200;
            $error = false;

            if (is_null($group)) {
                $code = 500;
                $error = true;
                $message = "The group does not exist";
            }
            else if(! is_null($student)){
                $code = 500;
                $error = true;
                $message = "The student already exist";
            }
            else{

                if ($group->getProgram() == 'option.program2'){
                    $user->setRoles(['ROLE_STUDENT_EMBASSADOR']);
                }
                else{ 
                    $user->setRoles(['ROLE_STUDENT']);
                }

                if(! $password){
                    $password = ucfirst($user->getLastName())."123"; 
                    $user->setPlainPassword($password);
                }
                
                $user->setPassword($encoder->encodePassword($user, $password));
                $em->persist($user);
                $em->flush();
    
    
                
                $sg = new StudentGroup();
                $sg->setStudent($user);
                $sg->setGroup($group);
                $em->persist($sg);
                $em->flush();
    
                //$this->sendEmailStudent("subject.new_student_created", $sg->getId());    
               
            }

            
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
 
        return new Response($serializer->serialize($response, "json", SerializationContext::create()->setGroups(array('student_list'))));
    }

    /**
     * @Rest\Get("/show/{id}.{_format}", name="student_show", defaults={"_format":"json"})
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
     *     description="The user ID"
     * )
     *
     *
     * @SWG\Tag(name="Student")
     */
    public function showAction(Request $request, $id ) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $user = [];
        $message = "";
        
        try {
            $code = 200;
            $error = false;
 
            $student_id = $id;
            $user = $em->getRepository("App:User")->find($student_id);
 
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
     * @Rest\Get("/progress/{id}.{_format}", name="student_progress", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets users progress by group."
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
     *     description="The group ID"
     * )
     *
     *
     * @SWG\Tag(name="Student")
     */
    public function progressAction(Request $request, $id ) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $user = [];
        $message = "";
        
        try {
            $code = 200;
            $error = false;
 
            $group_id = $id;

            $studentgroups = $em->getRepository('App:StudentGroup')->findBy(array('group' => $group_id ) );
            $studentambassadorgroups = $em->getRepository('App:StudentAmbassadorGroup')->findBy(array('group' => $group_id ) );

            $liststudentgroups = new ArrayCollection(
                array_merge($studentgroups, $studentambassadorgroups)
            );
 
            if (is_null($liststudentgroups)) {
                $code = 500;
                $error = true;
                $message = "The group does not exist";
            }
            else{
                $progressMbs = array();
                $progressSa  = array();

                foreach ($liststudentgroups as $st) {
                $programMbs = $em->getRepository('App:ProgramMbs')->findOneBy(array( 'student' => $st->getStudent()->getId() ));
                $plan = 0;
                $product = 0;
                $process = 0;
                $promotion = 0;
                $paperwork = 0;
                $price = 0;
                $service = 0;
                $quality = 0;


                        if($programMbs){
                            if(!$programMbs->getModality()){

                                if( $programMbs->getPlan1() )              { $plan = $plan + 50; }
                                if( $programMbs->getPlan2() )              { $plan = $plan + 50; }
                                if( $programMbs->getProduct1() )           { $product = $product + 14; }
                                if( $programMbs->getProduct2() )           { $product = $product + 14; }
                                if( $programMbs->getProduct3() )           { $product = $product + 14; }
                                if( $programMbs->getProduct4() )           { $product = $product + 14; }
                                if( $programMbs->getProduct5() )           { $product = $product + 14; }
                                if( $programMbs->getProduct6() )           { $product = $product + 14; }
                                if( $programMbs->getProduct7() )           { $product = $product + 16; }
                                if( $programMbs->getProcess1()[0] != "" )           { $process = $process + 25; }
                                if( $programMbs->getProcess2() )           { $process = $process + 25; }
                                if( $programMbs->getProcess3() )           { $process = $process + 25; }
                                if( $programMbs->getProcess4() )           { $process = $process + 25; }
                                if( $programMbs->getPrice1() )             { $price = $price + 25; }
                                if( $programMbs->getPrice2() )             { $price = $price + 25; }
                                if( $programMbs->getPrice3() )             { $price = $price + 25; }
                                if( $programMbs->getPrice4() )             { $price = $price + 25; }
                                if( $programMbs->getPaperwork1() )         { $paperwork = $paperwork + 14; }
                                if( $programMbs->getPaperwork3()[0] != "" )      { $paperwork = $paperwork + 14; }
                                if( $programMbs->getPaperwork4()['p4_balance']  != "" )      { $paperwork = $paperwork + 14; }
                                if( $programMbs->getPaperwork5()['p5_income']   != "" )       { $paperwork = $paperwork + 14; }
                                if( $programMbs->getPaperwork6()['p6_balance']  != "" )      { $paperwork = $paperwork + 14; }
                                if( $programMbs->getPaperwork7()['p7_income'][0]   != "" )       { $paperwork = $paperwork + 14; }
                                if( $programMbs->getPaperwork8()['p8_balance'][0]  != "" )      { $paperwork = $paperwork + 16; }
                                if( $programMbs->getService1() )             { $service = $service + 20; }
                                if( $programMbs->getService2() )             { $service = $service + 20; }
                                if( $programMbs->getService3() )             { $service = $service + 20; }
                                if( $programMbs->getService4() )             { $service = $service + 20; }
                                if( $programMbs->getService5() )             { $service = $service + 20; }
                                if( $programMbs->getPromotion1() )           { $promotion = $promotion + 25; }
                                if( $programMbs->getPromotion2() )           { $promotion = $promotion + 25; }
                                if( $programMbs->getPromotion3() )           { $promotion = $promotion + 25; }
                                if( $programMbs->getPromotion4() )           { $promotion = $promotion + 25; }
                                if( $programMbs->getQualityP1() )            { $quality = $quality + 6; }
                                if( $programMbs->getQualityP2() )            { $quality = $quality + 6; }
                                if( $programMbs->getQualityP3() )            { $quality = $quality + 6; }
                                if( $programMbs->getQualityP4() )            { $quality = $quality + 6; }
                                if( $programMbs->getQualityP5() )            { $quality = $quality + 6; }
                                if( $programMbs->getQualityP6() )            { $quality = $quality + 6; }
                                if( $programMbs->getQualityP7() )            { $quality = $quality + 6; }
                                if( $programMbs->getQualityP8() )            { $quality = $quality + 6; }
                                if( $programMbs->getQualityG1() )            { $quality = $quality + 6; }
                                if( $programMbs->getQualityG2() )            { $quality = $quality + 6; }
                                if( $programMbs->getQualityG3() )            { $quality = $quality + 6; }
                                if( $programMbs->getQualityG4() )            { $quality = $quality + 6; }
                                if( $programMbs->getQualityG5() )            { $quality = $quality + 6; }
                                if( $programMbs->getQualityG6() )            { $quality = $quality + 6; }
                                if( $programMbs->getQualityG7() )            { $quality = $quality + 6; }
                                if( $programMbs->getQualityG8() )            { $quality = $quality + 10; }

                                $progressMbs[] = array(
                                'name'        => $st->getStudent()->getFirstName()." ".$st->getStudent()->getLastName(),
                                'plan'        => $plan."%",
                                'product'     => $product."%",
                                'process'     => $process."%",
                                'price'       => $price."%",
                                'promotion'   => $promotion."%",
                                'paperwork'   => $paperwork."%",
                                'quality'     => $quality."%",
                                'service'     => $service."%"
                                );
                            }
                            else{
                                if($programMbs->getState() == "state.approved"){
                                    $progressMbs      = array(
                                        'id'          => $programMbs->getId(),
                                        'state'       => "edit",
                                        'complete'    =>  true,
                                        'submitted'   => "state.approved",
                                        'plan'        => "100%",
                                        'product'     => "100%",
                                        'process'     => "100%",
                                        'price'       => "100%",
                                        'promotion'   => "100%",
                                        'paperwork'   => "100%",
                                        'quality'     => "100%",
                                        'service'     => "100%",
                                    );
                                }
                                else{
                                    $progressMbs      = array(
                                        'id'          => $programMbs->getId(),
                                        'state'       => "new",
                                        'complete'    => "false",
                                        'submitted'   => "state.not_started",
                                        'plan'        => "0%",
                                        'product'     => "0%",
                                        'process'     => "0%",
                                        'price'       => "0%",
                                        'promotion'   => "0%",
                                        'paperwork'   => "0%",
                                        'quality'     => "0%",
                                        'service'     => "0%",
                                    );
                                }
                            }                              
                        }
                        else{
                            $progressMbs[] = array(
                                'name'        => $st->getStudent()->getFirstName()." ".$st->getStudent()->getLastName(),
                                'plan'        => "0%",
                                'product'     => "0%",
                                'process'     => "0%",
                                'price'       => "0%",
                                'promotion'   => "0%",
                                'paperwork'   => "0%",
                                'quality'     => "0%",
                                'service'     => "0%"
                            );
                        }




                        $programSa = $em->getRepository('App:ProgramSa')->findOneBy(array( 'student' => $st->getStudent()->getId() ));
                        $mision = 0;
                        $generate = 0;
                        $graduate = 0;
                        $facilitate = 0;
                        $graduate = 0;
                        $support = 0;                       


                        if($programSa){

                            if(!$programSa->getModality()){
                                if( $programSa->getMision1() )              { $mision = $mision + 25; }
                                if( $programSa->getMision2() )              { $mision = $mision + 25; }
                                if( $programSa->getMision3() )              { $mision = $mision + 25; }
                                if( $programSa->getMision4() )              { $mision = $mision + 25; }
                                if( $programSa->getgenerateGroups1() )           { $generate = $generate + 14; }
                                if( $programSa->getgenerateGroups2() )           { $generate = $generate + 14; }
                                if( $programSa->getgenerateGroups3() )           { $generate = $generate + 14; }
                                if( $programSa->getgenerateGroups4() )           { $generate = $generate + 14; }
                                if( $programSa->getgenerateGroups5() )           { $generate = $generate + 14; }
                                if( $programSa->getgenerateGroups6() )           { $generate = $generate + 14; }
                                if( $programSa->getgenerateGroups7() )           { $generate = $generate + 16; }
                                if( $programSa->getRule1() )           { $facilitate = $facilitate + 10; }
                                if( $programSa->getRule2() )           { $facilitate = $facilitate + 10; }
                                if( $programSa->getRule3() )           { $facilitate = $facilitate + 10; }
                                if( $programSa->getRule4() )           { $facilitate = $facilitate + 10; }
                                if( $programSa->getRule5() )           { $facilitate = $facilitate + 10; }
                                if( $programSa->getRule6() )           { $facilitate = $facilitate + 10; }
                                if( $programSa->getRule7() )           { $facilitate = $facilitate + 10; }
                                if( $programSa->getRule8() )           { $facilitate = $facilitate + 10; }
                                if( $programSa->getRule9() )           { $facilitate = $facilitate + 10; }
                                if( $programSa->getRule10() )           { $facilitate = $facilitate + 10; }                             
                                if( $programSa->getGraduate1() )             { $graduate = $graduate + 25; }
                                if( $programSa->getGraduate2() )             { $graduate = $graduate + 25; }
                                if( $programSa->getGraduate3() )             { $graduate = $graduate + 25; }
                                if( $programSa->getGraduate4() )             { $graduate = $graduate + 25; }                              
                                if( $programSa->getSupport1() )             { $support = $support + 33; }
                                if( $programSa->getSupport2() )             { $support = $support + 33; }
                                if( $programSa->getSupport3() )             { $support = $support + 34; }
                               

                                $progressSa[] = array(
                                'name'          => $st->getStudent()->getFirstName()." ".$st->getStudent()->getLastName(),
                                'mision'        => $mision."%",
                                'generate'      => $generate."%",
                                'facilitate'    => $facilitate."%",   
                                'graduate'      => $graduate."%",                         
                                'support'       => $support."%"
                                );
                            }                       

                        }
                }
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get the current group - Error: {$ex->getMessage()}";
        }

        $data = [
            "progressMbs" => $progressMbs,
            "progressSa"  => $progressSa
        ];
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $data : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Get("/dashboard/{id}.{_format}", name="student_dashboard", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets users dashboard by group."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The student with the passed ID parameter was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The student ID"
     * )
     *
     *
     * @SWG\Tag(name="Student")
     */
    public function dashboardAction(Request $request, $id ) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $user = [];
        $message = "";
        
        try {
            $code = 200;
            $error = false;
 
            $student_id = $id;
            $student = $em->getRepository('App:User')->find($student_id);

 
            if (is_null($student)) {
                $code = 500;
                $error = true;
                $message = "The group does not exist";
            }
            else{
                $progressMbs = array();
                $progressSa  = array();

                $programMbs = $em->getRepository('App:ProgramMbs')->findOneBy(array( 'student' => $student->getId() ));

             

                
                $plan = 0;
                $product = 0;
                $process = 0;
                $promotion = 0;
                $paperwork = 0;
                $price = 0;
                $service = 0;
                $quality = 0;
                
              
                        if($programMbs){
                            if(!$programMbs->getModality()){

                                if( $programMbs->getPlan1() )              { $plan = $plan + 50; }
                                if( $programMbs->getPlan2() )              { $plan = $plan + 50; }
                                if( $programMbs->getProduct1() )           { $product = $product + 14; }
                                if( $programMbs->getProduct2() )           { $product = $product + 14; }
                                if( $programMbs->getProduct3() )           { $product = $product + 14; }
                                if( $programMbs->getProduct4() )           { $product = $product + 14; }
                                if( $programMbs->getProduct5() )           { $product = $product + 14; }
                                if( $programMbs->getProduct6() )           { $product = $product + 14; }
                                if( $programMbs->getProduct7() )           { $product = $product + 16; }
                                if( $programMbs->getProcess1()[0] != "" )           { $process = $process + 25; }
                                if( $programMbs->getProcess2() )           { $process = $process + 25; }
                                if( $programMbs->getProcess3() )           { $process = $process + 25; }
                                if( $programMbs->getProcess4() )           { $process = $process + 25; }
                                if( $programMbs->getPrice1() )             { $price = $price + 25; }
                                if( $programMbs->getPrice2() )             { $price = $price + 25; }
                                if( $programMbs->getPrice3() )             { $price = $price + 25; }
                                if( $programMbs->getPrice4() )             { $price = $price + 25; }
                                if( $programMbs->getPaperwork1() )         { $paperwork = $paperwork + 14; }
                                if( $programMbs->getPaperwork3()[0] != "" )      { $paperwork = $paperwork + 14; }
                                if( $programMbs->getPaperwork4()['p4_balance']  != "" )      { $paperwork = $paperwork + 14; }
                                if( $programMbs->getPaperwork5()['p5_income']   != "" )       { $paperwork = $paperwork + 14; }
                                if( $programMbs->getPaperwork6()['p6_balance']  != "" )      { $paperwork = $paperwork + 14; }
                                if( $programMbs->getPaperwork7()['p7_income'][0]   != "" )       { $paperwork = $paperwork + 14; }
                                if( $programMbs->getPaperwork8()['p8_balance'][0]  != "" )      { $paperwork = $paperwork + 16; }
                                if( $programMbs->getService1() )             { $service = $service + 20; }
                                if( $programMbs->getService2() )             { $service = $service + 20; }
                                if( $programMbs->getService3() )             { $service = $service + 20; }
                                if( $programMbs->getService4() )             { $service = $service + 20; }
                                if( $programMbs->getService5() )             { $service = $service + 20; }
                                if( $programMbs->getPromotion1() )           { $promotion = $promotion + 25; }
                                if( $programMbs->getPromotion2() )           { $promotion = $promotion + 25; }
                                if( $programMbs->getPromotion3() )           { $promotion = $promotion + 25; }
                                if( $programMbs->getPromotion4() )           { $promotion = $promotion + 25; }
                                if( $programMbs->getQualityP1() )            { $quality = $quality + 6; }
                                if( $programMbs->getQualityP2() )            { $quality = $quality + 6; }
                                if( $programMbs->getQualityP3() )            { $quality = $quality + 6; }
                                if( $programMbs->getQualityP4() )            { $quality = $quality + 6; }
                                if( $programMbs->getQualityP5() )            { $quality = $quality + 6; }
                                if( $programMbs->getQualityP6() )            { $quality = $quality + 6; }
                                if( $programMbs->getQualityP7() )            { $quality = $quality + 6; }
                                if( $programMbs->getQualityP8() )            { $quality = $quality + 6; }
                                if( $programMbs->getQualityG1() )            { $quality = $quality + 6; }
                                if( $programMbs->getQualityG2() )            { $quality = $quality + 6; }
                                if( $programMbs->getQualityG3() )            { $quality = $quality + 6; }
                                if( $programMbs->getQualityG4() )            { $quality = $quality + 6; }
                                if( $programMbs->getQualityG5() )            { $quality = $quality + 6; }
                                if( $programMbs->getQualityG6() )            { $quality = $quality + 6; }
                                if( $programMbs->getQualityG7() )            { $quality = $quality + 6; }
                                if( $programMbs->getQualityG8() )            { $quality = $quality + 10; }

                                $total = $plan + $product + $process + $price + $promotion + $paperwork + $quality + $service;
                                if($total == 800){
                                    $complete = true;
                                }
                                else{
                                    $complete = false;
                                }

                                $progressMbs  = array(
                                'id'          => $programMbs->getId(),
                                'state'       => "edit",
                                'submitted'   => $programMbs->getState(),
                                'complete'    => $complete,
                                'plan'        => $plan."%",
                                'product'     => $product."%",
                                'process'     => $process."%",
                                'price'       => $price."%",
                                'promotion'   => $promotion."%",
                                'paperwork'   => $paperwork."%",
                                'quality'     => $quality."%",
                                'service'     => $service."%"
                                );
                            }
                            else{
                                if($programMbs->getState() == "state.approved"){
                                    $progressMbs      = array(
                                        'id'          => $programMbs->getId(),
                                        'state'       => "edit",
                                        'complete'    =>  true,
                                        'submitted'   => "state.approved",
                                        'plan'        => "100%",
                                        'product'     => "100%",
                                        'process'     => "100%",
                                        'price'       => "100%",
                                        'promotion'   => "100%",
                                        'paperwork'   => "100%",
                                        'quality'     => "100%",
                                        'service'     => "100%",
                                    );
                                }
                                else{
                                    $progressMbs      = array(
                                        'id'          => $programMbs->getId(),
                                        'state'       => "new",
                                        'complete'    => "false",
                                        'submitted'   => "state.not_started",
                                        'plan'        => "0%",
                                        'product'     => "0%",
                                        'process'     => "0%",
                                        'price'       => "0%",
                                        'promotion'   => "0%",
                                        'paperwork'   => "0%",
                                        'quality'     => "0%",
                                        'service'     => "0%",
                                    );
                                }
                            }
                                                        
                        }
                        else{
                            $progressMbs      = array(
                                'state'       => "new",
                                'complete'    => "false",
                                'submitted'   => "state.not_started",
                                'plan'        => "0%",
                                'product'     => "0%",
                                'process'     => "0%",
                                'price'       => "0%",
                                'promotion'   => "0%",
                                'paperwork'   => "0%",
                                'quality'     => "0%",
                                'service'     => "0%",
                            );
                        }




                        $programSa = $em->getRepository('App:ProgramSa')->findOneBy(array( 'student' => $student->getId() ));
                        $mision = 0;
                        $generate = 0;
                        $graduate = 0;
                        $facilitate = 0;
                        $graduate = 0;
                        $support = 0;       
                        $student_ambassador = false;
                        
                        $studentGroup = $em->getRepository('App:StudentGroup')->findOneBy(array( 'student' => $student->getId() ));
                        $studentAmbassadorGroup = $em->getRepository('App:StudentAmbassadorGroup')->findOneBy(array( 'student' => $student->getId() ));

                        if($studentGroup){
                            if($studentGroup->getGroup()->getProgram() == "option.program3" || $studentGroup->getGroup()->getProgram() == "option.program2"){
                                $student_ambassador = true;
                            }
                        }
                        if($studentAmbassadorGroup){
                            if($studentAmbassadorGroup->getGroup()->getProgram() == "option.program3" || $studentAmbassadorGroup->getGroup()->getProgram() == "option.program2"){
                                $student_ambassador = true;
                            }
                        }


                        if($programSa){

                            if(!$programSa->getModality()){
                                if( $programSa->getMision1() )              { $mision = $mision + 25; }
                                if( $programSa->getMision2() )              { $mision = $mision + 25; }
                                if( $programSa->getMision3() )              { $mision = $mision + 25; }
                                if( $programSa->getMision4() )              { $mision = $mision + 25; }
                                if( $programSa->getgenerateGroups1() )           { $generate = $generate + 14; }
                                if( $programSa->getgenerateGroups2() )           { $generate = $generate + 14; }
                                if( $programSa->getgenerateGroups3() )           { $generate = $generate + 14; }
                                if( $programSa->getgenerateGroups4() )           { $generate = $generate + 14; }
                                if( $programSa->getgenerateGroups5() )           { $generate = $generate + 14; }
                                if( $programSa->getgenerateGroups6() )           { $generate = $generate + 14; }
                                if( $programSa->getgenerateGroups7() )           { $generate = $generate + 16; }
                                if( $programSa->getRule1() )           { $facilitate = $facilitate + 10; }
                                if( $programSa->getRule2() )           { $facilitate = $facilitate + 10; }
                                if( $programSa->getRule3() )           { $facilitate = $facilitate + 10; }
                                if( $programSa->getRule4() )           { $facilitate = $facilitate + 10; }
                                if( $programSa->getRule5() )           { $facilitate = $facilitate + 10; }
                                if( $programSa->getRule6() )           { $facilitate = $facilitate + 10; }
                                if( $programSa->getRule7() )           { $facilitate = $facilitate + 10; }
                                if( $programSa->getRule8() )           { $facilitate = $facilitate + 10; }
                                if( $programSa->getRule9() )           { $facilitate = $facilitate + 10; }
                                if( $programSa->getRule10() )           { $facilitate = $facilitate + 10; }                             
                                if( $programSa->getGraduate1() )             { $graduate = $graduate + 25; }
                                if( $programSa->getGraduate2() )             { $graduate = $graduate + 25; }
                                if( $programSa->getGraduate3() )             { $graduate = $graduate + 25; }
                                if( $programSa->getGraduate4() )             { $graduate = $graduate + 25; }                              
                                if( $programSa->getSupport1() )             { $support = $support + 33; }
                                if( $programSa->getSupport2() )             { $support = $support + 33; }
                                if( $programSa->getSupport3() )             { $support = $support + 34; }
                               
                                $total = $mision + $generate + $facilitate + $graduate + $support;
                                if($total == 500){
                                    $complete = true;
                                }
                                else{
                                    $complete =  false;
                                }

                                $progressSa     = array(
                                'id'            => $programSa->getId(),
                                'submitted'     => $programSa->getState(),
                                'state'         => "edit",
                                'complete'      => $complete,
                                'mision'        => $mision."%",
                                'generate'      => $generate."%",
                                'facilitate'    => $facilitate."%",   
                                'graduate'      => $graduate."%",                         
                                'support'       => $support."%",
                                'student_ambassador' => $student_ambassador
                                );
                            }                       

                        }
                        else{
                            $progressSa         = array(
                                'submitted'     => "state.not_started",
                                'state'         => "new",
                                'complete'      => "false",
                                'mision'        => $mision."%",
                                'generate'      => $generate."%",
                                'facilitate'    => $facilitate."%",   
                                'graduate'      => $graduate."%",                         
                                'support'       => $support."%",
                                'student_ambassador' => $student_ambassador
                                );
                        }
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get the current group - Error: {$ex->getMessage()}";
        }

        $data = [
            "progressMbs" => $progressMbs,
            "progressSa"  => $progressSa
        ];
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $data : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Put("/edit/{id}.{_format}", name="student_edit", defaults={"_format":"json"})
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
     * @SWG\Tag(name="Student")
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
     * @Rest\Delete("/delete/{id}.{_format}", name="student_delete", defaults={"_format":"json"})
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
     * @SWG\Tag(name="Student")
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
     * @Rest\Get("/search/{key}.{_format}", name="student_show", defaults={"_format":"json"})
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
     * @SWG\Tag(name="Student")
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
     * @Rest\Post("/mbs", name="student_studentmbs")
     *
    * @SWG\Response(
     *     response=200,
     *     description="Gets all students."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to get all students."
     * )
     * 
     *  * * @SWG\Parameter(
     *     name="state",
     *     in="query",
     *     type="string",
     *     description="The state project"
     * )
     * 
     *  * * @SWG\Parameter(
     *     name="ambassador",
     *     in="query",
     *     type="integer",
     *     description="The id ambassador"
     * )
     * 
     *  * * @SWG\Parameter(
     *     name="language",
     *     in="query",
     *     type="string",
     *     description="The language"
     * )
     * 
     *  * * @SWG\Parameter(
     *     name="role",
     *     in="query",
     *     type="string",
     *     description="The role user"
     * )
     *
     * @SWG\Tag(name="Student")
     */ 
    public function studentMbsAction(Request $request) {
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

            $language = $request->request->get("language");
            $id_ambassador = $request->request->get("ambassador");
            $state = $request->request->get("state");
            $role = $request->request->get("role");
            $studentsMbs = [];

            $language = explode(",", preg_replace('([^A-Za-z0-9,])', '', $language));

            if($role == "ROLE_EMBASSADOR" ){
                $studentsMbs = $em->getRepository('App:StudentGroup')->studentsMbsStateByEmbassador($id_ambassador, $state);
            }
            if($role == "ROLE_ADMIN" ){
                $studentsMbs = $em->getRepository('App:StudentGroup')->studentMbsByState($state);
            }
            if($role == "ROLE_LANGUAGE_ADMIN" ){
                $studentsMbs = $em->getRepository('App:StudentGroup')->studentsMbsStateByLanguage($language, $state);
            }
 
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get student list - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $studentsMbs : $message,
        ];
 
        return new Response($serializer->serialize($response, "json", SerializationContext::create()->setGroups(array('future_ambassador'))));
    }

    /**
     * @Rest\Post("/ambassador", name="student_studentambassador")
     *
    * @SWG\Response(
     *     response=200,
     *     description="Gets all students."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to get all students."
     * )
     * 
     *  * * @SWG\Parameter(
     *     name="state",
     *     in="query",
     *     type="string",
     *     description="The state project"
     * )
     * 
     *  * * @SWG\Parameter(
     *     name="ambassador",
     *     in="query",
     *     type="integer",
     *     description="The id ambassador"
     * )
     * 
     *  * * @SWG\Parameter(
     *     name="language",
     *     in="query",
     *     type="string",
     *     description="The language"
     * )
     * 
     *  * * @SWG\Parameter(
     *     name="role",
     *     in="query",
     *     type="string",
     *     description="The role user"
     * )
     *
     * @SWG\Tag(name="Student")
     */ 
    public function studentAmbassadorAction(Request $request) {
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

            $language = $request->request->get("language");
            $id_ambassador = $request->request->get("ambassador");
            $state = $request->request->get("state");
            $role = $request->request->get("role");
            $studentsAmbassador = [];

            $language = explode(",", preg_replace('([^A-Za-z0-9,])', '', $language));

            if($role == "ROLE_EMBASSADOR" ){ 
                $studentsAmbassador1 = $em->getRepository('App:StudentGroup')->studentsMbsStateByEmbassador($id_ambassador, $state);
                $studentsAmbassador2 = $em->getRepository('App:StudentAmbassadorGroup')->studentsMbsStateByEmbassador($id_ambassador, $state);

                $studentsAmbassador = new ArrayCollection(
                    array_merge($studentsAmbassador1, $studentsAmbassador2)
                );

            
            }
            if($role == "ROLE_ADMIN" ){
                $studentsGroup = $em->getRepository('App:StudentGroup')->studentAmbassadorByState($state);
                $studentsAmbassadorGroup = $em->getRepository('App:StudentAmbassadorGroup')->studentAmbassadorByState($state);

                foreach ($studentsGroup as $key => $st) {
                    $student = $em->getRepository('App:StudentAmbassadorGroup')->findOneBy(array('student' => $st->getStudent()->getId()));
                    if($student){
                        unset($studentsGroup[$key]);
                    }
                }

                $studentsAmbassador = new ArrayCollection(
                    array_merge($studentsGroup, $studentsAmbassadorGroup )
                );

            }
            if($role == "ROLE_LANGUAGE_ADMIN" ){
                $studentsGroup = $em->getRepository('App:StudentGroup')->studentsAmbassadorStateByLanguage($language, $state);
                $studentsAmbassadorGroup = $em->getRepository('App:StudentAmbassadorGroup')->studentsAmbassadorStateByLanguage($language, $state);

                $studentsAmbassador = new ArrayCollection(
                    array_merge($studentsGroup, $studentsAmbassadorGroup )
                );
            }
 
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get student list - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $studentsAmbassador : $message,
        ];
 
        return new Response($serializer->serialize($response, "json", SerializationContext::create()->setGroups(array('future_ambassador'))));
    }

    /**
     * @Rest\Post("/confirmgroup", name="student_confirmgroup")
     *
     * @SWG\Response(
     *     response=200,
     *     description="The new group was successful assigned."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to assign the new group."
     * )
     *
     * @SWG\Parameter(
     *     name="student",
     *     in="path",
     *     type="string",
     *     description="The student ID"
     * )
     *
     * @SWG\Parameter(
     *     name="group",
     *     in="path",
     *     type="string",
     *     description="The Group ID"
     * )
     *
     * @SWG\Tag(name="Student")
     */
    public function confirmGroupAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $studentAmbassadorGroup = new StudentAmbassadorGroup();
        $message = "";

        
 
        try {
            $code = 200;
            $error = false;
            $student = $em->getRepository("App:User")->find($request->request->get('student'));
            $group = $em->getRepository("App:Groupe")->find($request->request->get('group'));
 
            if (!is_null($student) and !is_null($group)) {
                $studentAmbassadorGroup->setStudent($student);
                $studentAmbassadorGroup->setGroup($group);

                $student->setRoles(['ROLE_STUDENT_EMBASSADOR']);
                $em->persist($student);
                $em->flush();
 
                $em->persist($studentAmbassadorGroup);
                $em->flush();
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to assign the new group - Error: You must to provide fields student or the group does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to assign new group - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $studentAmbassadorGroup : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Post("/deletefutureambassador", name="student_deletefutureambassador")
     *
     * @SWG\Response(
     *     response=200,
     *     description="The student was successful delete of future ambassador list."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to delete future ambassador."
     * )
     *
     * @SWG\Parameter(
     *     name="student",
     *     in="path",
     *     type="string",
     *     description="The student ID"
     * )
     *    
     *
     * @SWG\Tag(name="Student")
     */
    public function deleteFutureAmbassadorGroupAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $message = "";

        try {
            $code = 200;
            $error = false;
            $evaluation = $em->getRepository("App:Evaluation")->findOneBy(array('student' => $request->request->get('student')));
 
            if (!is_null($evaluation)) {
                $evaluation->setPostquestion10("option2");
 
                $em->persist($evaluation);
                $em->flush();
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to assign the new group - Error: You must to provide fields student or the group does not exist";
            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to assign new group - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $evaluation : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Post("/ambassador/all", name="student_ambassador")
     *
     * @SWG\Response(
     *     response=200,
     *     description="The list student belong to ambassador."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to get list student."
     * )
     *
     * @SWG\Parameter(
     *     name="id_ambassador",
     *     in="path",
     *     type="string",
     *     description="The ambassador ID"
     * )
     *    
     *
     * @SWG\Tag(name="Student")
     */
    public function ambassadorAllAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $message = "";
        $students = [];

        try {
            $code = 200;
            $error = false;
            $studentsGroup = $em->getRepository("App:User")->studentGroupByAmbassador($request->request->get('id_ambassador'));
            $studentsAmbassadorGroup = $em->getRepository("App:User")->studentAmbassadorGroupByAmbassador($request->request->get('id_ambassador'));  

            $students = new ArrayCollection(
                array_merge($studentsGroup, $studentsAmbassadorGroup)
            );
            
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to assign new group - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $students : $message,
        ];
 
        return new Response($serializer->serialize($response, "json", SerializationContext::create()->setGroups(array('student_list'))));
    }

    /**
     * @Rest\Post("/futureambassador", name="student_futureambassador")
     *
    * @SWG\Response(
     *     response=200,
     *     description="Gets all students."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to get all students."
     * )
     * 
     * @SWG\Parameter(
     *     name="language",
     *     in="path",
     *     type="string",
     *     description="The language Grader"
     * )
     * 
     * @SWG\Parameter(
     *     name="roles",
     *     in="path",
     *     type="string",
     *     description="The role user"
     * )
     *
     * @SWG\Tag(name="Student")
     */ 
    public function futureAmbassadorAction(Request $request) {
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

            $role = $request->request->get("role");
            $lang = $request->request->get("language");

            $lang = explode(",", preg_replace('([^A-Za-z0-9,])', '', $lang));

            $futureAmbassadors = [];
            if($role == "ROLE_LANGUAGE_ADMIN"){
                $futureAmbassadors  = $em->getRepository('App:User')->futureEmbassadorsByLanguage($lang);
            }
            else{
                $futureAmbassadors  = $em->getRepository('App:User')->futureEmbassadors();
            }
            
 
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get student list - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $futureAmbassadors : $message,
        ];
 
        return new Response($serializer->serialize($response, "json", SerializationContext::create()->setGroups(array('future_ambassador'))));
    }

    /**
     * @Rest\Post("/successstorylist", name="success_story_list")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets all available success stories."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to get all success stories."
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
     * @SWG\Tag(name="Student")
     */
    public function SuccessStorylistAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $students = [];
        $message = "";
 
        try {
            $code = 200;
            $error = false;
 
            $role = $request->request->get('role');
            $id = $request->request->get('id_ambassador');

            if($role == 'ROLE_ADMIN' or $role == 'ROLE_LANGUAGE_ADMIN'){
                $students = $em->getRepository('App:StudentGroup')->listSuccessStory();
            }
            else if($role == 'ROLE_EMBASSADOR' or $role == 'ROLE_STUDENT_EMBASSADOR'){
                $students = $em->getRepository('App:StudentGroup')->successStoryByEmbassador($id);
            }
          
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get all SuccessStory - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $students : $message,
        ];
 
        return new Response($serializer->serialize($response, "json", SerializationContext::create()->setGroups(array("student_group"))));
    }

    
}