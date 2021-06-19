<?php
namespace App\Controller;
 
use App\Entity\User;
use App\Entity\Task;
use App\Form\UserType;
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
 * Class ReportController
 *
 * @Route("/report")
 */
class ReportController extends FOSRestController
{
    
   /**
     * @Rest\Post("/", name="report_index")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets all report."
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to get all ambassador report."
     * )
     *
     * @SWG\Parameter(
     *     name="role",
     *     in="path",
     *     type="string",
     *     description="The Role of User"
     * )
     * 
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The id of Ambassador"
     * )
     *
     *
     * @SWG\Tag(name="Report")
     */
    public function indexAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $reports = [];
        $message = "";
 
        try {
            $code = 200;
            $error = false;
 
            $role = $request->request->get("role");
            $id_ambassador = $request->request->get("id");

            if($role == "ROLE_EMBASSADOR"){
                
                $evaluations = $this->evaluationReportXAmbassadorFull($id_ambassador);               

                $reports = [                  
                    'evaluations'         => $evaluations,                  
                ];
            }

            if($role == "ROLE_ADMIN" or $role == "ROLE_LANGUAGE_ADMIN"){

                $evaluations = $this->evaluationReportByCountry(null);                

                $reports = [                    
                    'evaluations'         => $evaluations,                   
                ];

            }
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get all report - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $reports : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

     /**
     * @Rest\Get("/globalmap", name="global_map")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets all report."
     * )
     *     
     *
     *
     * @SWG\Tag(name="Report")
     */
    public function globalMapAction(Request $request) {
      $serializer = $this->get('jms_serializer');
      $em = $this->getDoctrine()->getManager();
      $reports = [];
      $message = "";

      try {
          $code = 200;
          $error = false;

       
              $mbsNumbers = $em->getRepository('App:Certificate')->findBy(array('program' => 'MBS'), array('number' => 'DESC'));
            

              foreach ($mbsNumbers as $key => $mbs) {
              $sa = $em->getRepository('App:Certificate')->findOneBy(array('program' => 'SA', 'country' => $mbs->getCountry()));
              $jr = $em->getRepository('App:Certificate')->findOneBy(array('program' => 'JR', 'country' => $mbs->getCountry()));

              $jrNumber = 0;
              $saNumber = 0;

              if($sa){
                $saNumber = $sa->getNumber();
              }
              if($jr){
                $jrNumber = $jr->getNumber();
              }                  
              $topNumbers[] = array(
                  'code' => $mbs->getCountry(),
                  'flag' => $this->getParameter($mbs->getCountry()),
                  'country' => $this->getParameter($this->getParameter($mbs->getCountry())),
                  'mbs'=> $mbs->getNumber(),
                  'jr'=> $jrNumber,
                  'sa' => $saNumber
              );

              $vectorMap[] = array(
                'code' => $mbs->getCountry(),
                'flag' => $this->getParameter($mbs->getCountry()),
                'country' => $this->getParameter($this->getParameter($mbs->getCountry())),
                'mbs'=> $mbs->getNumber(),
                'jr'=> $jrNumber,
                'sa' => $saNumber
              );  

              $numMap[$this->getParameter($mbs->getCountry())] = (
                $mbs->getNumber().", ".$jrNumber.", ".$saNumber
              );
            }                           
            
            $reports = [                 
              'vectorMap'   => $topNumbers,
              'topNumbers'  => $vectorMap,
              'numCountries'  => $numMap,
            ];


      } catch (Exception $ex) {
          $code = 500;
          $error = true;
          $message = "An error has occurred trying to get all report - Error: {$ex->getMessage()}";
      }

      $response = [
          'code' => $code,
          'error' => $error,
          'data' => $code == 200 ? $reports : $message,
      ];

      return new Response($serializer->serialize($response, "json"));
  }

  /**
     * @Rest\Post("/group/daterange", name="report_group_date_range")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets data range groups reports"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to get all ambassador report."
     * )
     *
     * @SWG\Parameter(
     *     name="start_date",
     *     in="path",
     *     type="string",
     *     description="The start date"
     * )
     * 
     * @SWG\Parameter(
     *     name="final_date",
     *     in="path",
     *     type="string",
     *     description="The final date"
     * )
     *
     *
     * @SWG\Tag(name="Report")
     */
    public function groupDateRangeAction(Request $request) {
      $serializer = $this->get('jms_serializer');
      $em = $this->getDoctrine()->getManager();
      $reports = [];
      $message = "";

      try {
          $code = 200;
          $error = false;

          $start_date = $request->request->get("start_date");
          $final_date = $request->request->get("final_date");

          $until = new \DateTime();
          $interval = new \DateInterval('P2M');//2 months
          $from = $until->sub($interval);
          $mm =  'from' . $from->format('Y-m-d') . 'until' . $until->format('Y-m-d');
          

      } catch (Exception $ex) {
          $code = 500;
          $error = true;
          $message = "An error has occurred trying to get all report - Error: {$ex->getMessage()}";
      }

      $response = [
          'code' => $code,
          'error' => $error,
          'data' => $code == 200 ? $mm : $message,
      ];

      return new Response($serializer->serialize($response, "json"));
  }


    /**
     * @Rest\Post("/globalnumbers", name="global_numbers")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets all report."
     * )  
     * 
     * @SWG\Parameter(
     *     name="role",
     *     in="path",
     *     type="string",
     *     description="The Role of User"
     * )
     * 
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The id of Ambassador"
     * )   
     *
     * @SWG\Tag(name="Report")
     */
    public function globalnumbersAction(Request $request) {
      $serializer = $this->get('jms_serializer');
      $em = $this->getDoctrine()->getManager();
      $reports = [];
      $message = "";

      try {
          $code = 200;
          $error = false;

          $globalMbs = 0;
          $globalMbsJr = 0;
          $globalSa = 0; 
          $globalParticipants = 0;
          $globalGroups = 0;

          $role = $request->request->get("role");
          $id_ambassador = $request->request->get("id");

          if($role == "ROLE_ADMIN" or $role == "ROLE_LANGUAGE_ADMIN"){

              $mbsNumbers = $em->getRepository('App:Certificate')->findBy(array('program' => 'MBS'));
              $jrNumbers = $em->getRepository('App:Certificate')->findBy(array('program' => 'JR'));
              $saNumbers = count($em->getRepository('App:User')->userByRole("ROLE_EMBASSADOR"));

              foreach ($mbsNumbers as $st) {
              $globalMbs = $globalMbs + (int)$st->getNumber();
              }

              foreach ($jrNumbers as $st) {
              $globalMbsJr = $globalMbsJr + (int)$st->getNumber();
              }

              $globalSa = $saNumbers;

          }
          else if($role == "ROLE_EMBASSADOR" or $role == "ROLE_STUDENT_EMBASSADOR"){
              $studentsMbs = $em->getRepository('App:StudentGroup')->studentsMbsStateByEmbassador($id_ambassador, 'state.approved');
              $studentsSa = $em->getRepository('App:StudentGroup')->studentsAmbassadorStateByEmbassador($id_ambassador, 'state.approved');
              $studentsJr = $em->getRepository('App:StudentGroup')->studentsMbsStateByEmbassadorProgram($id_ambassador, 'state.approved', 'option.program4');
              $participantsMbs = $em->getRepository('App:StudentGroup')->studentsMbsByEmbassador($id_ambassador);
              $groups = $em->getRepository('App:Groupe')->findBy(array("embassador" => $id_ambassador));
              $stories = $em->getRepository('App:StudentGroup')->successStoryByEmbassador($id_ambassador);

              $globalMbs = count($studentsMbs);
              $globalMbsJr = count($studentsJr);
              $globalSa = count($studentsSa);
              $globalStories = count($stories);

              $globalParticipants = count($participantsMbs);
              $globalGroups = count($groups);
          }

          $date1 = new \DateTime("2018-12-15");
          $date2 = new \DateTime('now', (new \DateTimeZone('America/New_York') ) );
          $diff = $date1->diff($date2);

          $reports = [
              'global_mbs'          => $globalMbs - $globalMbsJr,
              'global_mbs_junior'   => $globalMbsJr,
              'global_sa'           => $globalSa,
              'global_participants' => $globalParticipants,
              'global_groups'       => $globalGroups,
              'global_certificates' => $globalMbs,
              'global_stories'      => $globalStories,
              'date_range'           => $this->getFormat($diff),                  
          ];


      } catch (Exception $ex) {
          $code = 500;
          $error = true;
          $message = "An error has occurred trying to get all report - Error: {$ex->getMessage()}";
      }

      $response = [
          'code' => $code,
          'error' => $error,
          'data' => $code == 200 ? $reports : $message,
      ];

      return new Response($serializer->serialize($response, "json"));
  }

  /**
     * @Rest\Get("/global", name="report_global")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets all report."
     * )  
     * 
     * @SWG\Response(
     *     response=500,
     *     description="An error has occurred trying to get all ambassador report."
     * )
     *     
     *
     * @SWG\Tag(name="Report")
     */
    public function globalAction(Request $request) {
      $serializer = $this->get('jms_serializer');
      $em = $this->getDoctrine()->getManager();
      $reports = [];
      $message = "";

      try {
          $code = 200;
          $error = false;

          $globalMbs = 0;
          $globalMbsJr = 0;
          $globalSa = 0; 
          $globalParticipants = 0;
          $globalGroups = 0;

          $role = $request->request->get("role");
          $id_ambassador = $request->request->get("id");


          $mbsNumbers = $em->getRepository('App:Certificate')->findBy(array('program' => 'MBS'));
          $jrNumbers = $em->getRepository('App:Certificate')->findBy(array('program' => 'JR'));
          $saNumbers = count($em->getRepository('App:User')->userByRole("ROLE_EMBASSADOR"));

          foreach ($mbsNumbers as $st) {
          $globalMbs = $globalMbs + (int)$st->getNumber();
          }

          foreach ($jrNumbers as $st) {
          $globalMbsJr = $globalMbsJr + (int)$st->getNumber();
          }

          $globalSa = $saNumbers;

          $globalGroups =  count($em->getRepository("App:Groupe")->findAll());
          $globalCountries = count($mbsNumbers);

          $date1 = new \DateTime("2018-12-15");
          $date2 = new \DateTime('now', (new \DateTimeZone('America/New_York') ) );
          $diff = $date1->diff($date2);

          $reports = [
              'global_mbs'          => $globalMbs + $globalMbsJr,
              'global_mbs_junior'   => $globalMbsJr,
              'global_sa'           => $globalSa,
              'global_groups'       => $globalGroups,
              'countries'           => $globalCountries,
              'date_range'           => $this->getFormat($diff),                  
          ];


      } catch (Exception $ex) {
          $code = 500;
          $error = true;
          $message = "An error has occurred trying to get all report - Error: {$ex->getMessage()}";
      }

      $response = [
          'code' => $code,
          'error' => $error,
          'data' => $code == 200 ? $reports : $message,
      ];

      return new Response($serializer->serialize($response, "json"));
  }

   /**
     * @Rest\Post("/ambassadorstatistics", name="ambassador_statistics")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets all report of Ambassador."
     * )  
     *     
    * @SWG\Parameter(
     *     name="id_ambassador",
     *     in="path",
     *     type="string",
     *     description="The id of Ambassador"
     * )   
     * 
     * @SWG\Tag(name="Report")
     */
    public function ambassadorStatisticsAction(Request $request) {
      $serializer = $this->get('jms_serializer');
      $em = $this->getDoctrine()->getManager();
      $statistics = [];
      $message = "";

      try {
          $code = 200;
          $error = false;

          $id_ambassador = $request->request->get("id_ambassador");

          $ambassador = $em->getRepository('App:User')->find($id_ambassador);

          $participantsMbs = $em->getRepository('App:StudentGroup')->studentsMbsByEmbassador($ambassador->getId());
          $certificates = $em->getRepository('App:StudentGroup')->studentsMbsStateByEmbassador($ambassador->getId(), 'state.approved');
          $groups = $em->getRepository('App:Groupe')->findBy(array("embassador" => $ambassador->getId()));
          $stories = $em->getRepository('App:StudentGroup')->successStoryByEmbassador($ambassador->getId());

          $statistics = [
            'id' => $ambassador->getId(),
            'first_name' => $ambassador->getFirstName(),
            'last_name' => $ambassador->getLastName(),
            'participants' => count($participantsMbs),
            'certificates' => count($certificates),
            'groups'=> count($groups),
            'stories'=> count($stories)
          ];

         


      } catch (Exception $ex) {
          $code = 500;
          $error = true;
          $message = "An error has occurred trying to get all report - Error: {$ex->getMessage()}";
      }

      $response = [
          'code' => $code,
          'error' => $error,
          'data' => $code == 200 ? $statistics : $message,
      ];

      return new Response($serializer->serialize($response, "json"));
  }

  /**
     * @Rest\Get("/listambassadorstatistics", name="list_ambassador_statistics")
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets all report of Ambassador."
     * )  
     *     
     *
     * @SWG\Tag(name="Report")
     */
    public function listAmbassadorStatisticsAction(Request $request) {
      $serializer = $this->get('jms_serializer');
      $em = $this->getDoctrine()->getManager();
      $reports = [];
      $message = "";

      try {
          $code = 200;
          $error = false;

          $globalMbs = 0;
          $globalMbsJr = 0;
          $globalSa = 0; 
          $globalParticipants = 0;
          $globalGroups = 0;

          $ambassadors = $em->getRepository('App:User')->userByRole("ROLE_EMBASSADOR");

          foreach ($ambassadors as $key => $ambassador) {
              $participantsMbs = $em->getRepository('App:StudentGroup')->studentsMbsByEmbassador($ambassador->getId());
              $certificates = $em->getRepository('App:StudentGroup')->studentsMbsStateByEmbassador($ambassador->getId(), 'state.approved');
              $groups = $em->getRepository('App:Groupe')->findBy(array("embassador" => $ambassador->getId()));
              $stories = $em->getRepository('App:StudentGroup')->successStoryByEmbassador($ambassador->getId());

              $statistics[] = array(
                'id' => $ambassador->getId(),
                'first_name' => $ambassador->getFirstName(),
                'last_name' => $ambassador->getLastName(),
                'participants' => count($participantsMbs),
                'certificates' => count($certificates),
                'groups'=> count($groups),
                'stories'=> count($stories)
              );
          }

         


      } catch (Exception $ex) {
          $code = 500;
          $error = true;
          $message = "An error has occurred trying to get all report - Error: {$ex->getMessage()}";
      }

      $response = [
          'code' => $code,
          'error' => $error,
          'data' => $code == 200 ? $statistics : $message,
      ];

      return new Response($serializer->serialize($response, "json"));
  }

    /**
     * @Rest\Get("/ambassador/{id}.{_format}", name="report_ambassador", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets statistics info based on passed ID Ambassador parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The statistics with the passed ID parameter was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The ambassador ID"
     * )
     *
     *
     * @SWG\Tag(name="Report")
     */
    public function ambassadorAction(Request $request, $id ) {
      $serializer = $this->get('jms_serializer');
      $em = $this->getDoctrine()->getManager();
      $statistics = [];
      $message = "";
      
      try {
          $code = 200;
          $error = false;

          $ambassador_id = $id;
          $evaluations = $this->evaluationReportXAmbassador($ambassador_id);
          $statisticsMbs = $this->showStatistics($evaluations['mbs'], "MBS");
          $statisticsJr = $this->showStatistics($evaluations['jr'], "JR");

          $statistics = array(
            'MBS' => $statisticsMbs,
            'JR'  => $statisticsJr
          );

          if (is_null($statistics)) {
              $code = 500;
              $error = true;
              $message = "The ambassador statistics does not exist";
          }

      } catch (Exception $ex) {
          $code = 500;
          $error = true;
          $message = "An error has occurred trying to get the statistics ambassador - Error: {$ex->getMessage()}";
      }

      $response = [
          'code' => $code,
          'error' => $error,
          'data' => $code == 200 ? $statistics : $message,
      ];

      return new Response($serializer->serialize($response, "json"));
  }

  /**
     * @Rest\Get("/country/{id}.{_format}", name="report_country", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets statistics info based on passed ID Country parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The statistics with the passed ID parameter was not found or doesn't exist."
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
     * @SWG\Tag(name="Report")
     */
    public function countryAction(Request $request, $id ) {
      $serializer = $this->get('jms_serializer');
      $em = $this->getDoctrine()->getManager();
      $statistics = [];
      $message = "";
      
      try {
          $code = 200;
          $error = false;

          $country_id = $id;
          
          $evaluations = $this->evaluationReportByCountry($country_id);
          $statistics = $this->showStatistics($evaluations, "MBS");

          if (is_null($statistics)) {
              $code = 500;
              $error = true;
              $message = "The country statistics does not exist";
          }

      } catch (Exception $ex) {
          $code = 500;
          $error = true;
          $message = "An error has occurred trying to get the statistics country - Error: {$ex->getMessage()}";
      }

      $response = [
          'code' => $code,
          'error' => $error,
          'data' => $code == 200 ? $statistics : $message,
      ];

      return new Response($serializer->serialize($response, "json"));
  }

  /**
     * @Rest\Get("/evaluations/{id}.{_format}", name="report_evaluations", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets statistics info based on passed ID Country parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The statistics with the passed ID parameter was not found or doesn't exist."
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
     * @SWG\Tag(name="Report")
     */
    public function evaluationsAction(Request $request, $id ) {
      $serializer = $this->get('jms_serializer');
      $em = $this->getDoctrine()->getManager();
      $statistics = [];
      $message = "";
      
      try {
          $code = 200;
          $error = false;

          $country_id = $id;
          
          $evaluations = $this->evaluationReportByCountryProgram($country_id);
          $statisticsJr = $this->showStatistics($evaluations['jr'], "JR");
          $statisticsMbs = $this->showStatistics($evaluations['mbs'], "MBS");

          $statistics = array(
            'MBS' => $statisticsMbs,
            'JR'  => $statisticsJr
          );

          if (is_null($statistics)) {
              $code = 500;
              $error = true;
              $message = "The country statistics does not exist";
          }

      } catch (Exception $ex) {
          $code = 500;
          $error = true;
          $message = "An error has occurred trying to get the statistics country - Error: {$ex->getMessage()}";
      }

      $response = [
          'code' => $code,
          'error' => $error,
          'data' => $code == 200 ? $statistics : $message,
      ];

      return new Response($serializer->serialize($response, "json"));
  }

  


    private function evaluationReportByCountry($country){
        $em = $this->getDoctrine()->getManager();
  
        if($country and $country != "ALL"){
            $studentsEvaluations = $em->getRepository('App:Evaluation')->evaluationByCountry($country);
        }
        else{
            $studentsEvaluations = $em->getRepository('App:Evaluation')->findAll();
        }
  
  
        $evaluations = array();
        foreach ($studentsEvaluations as $s) {
          $student = $s->getStudent();
            if($s->getPostquestion1()){
               $evaluations[] = array(
                 'name'             => $student->getFirstName()." ".$student->getLastName(),
                 'question1'        => $this->improvementMeasure($s->getQuestion1(), $s->getPostquestion1(), 'question1'),
                 'question2'        => $this->improvementMeasure($s->getQuestion2(), $s->getPostquestion2(), 'question2'),
                 'question3'        => $this->improvementMeasure($s->getQuestion3(), $s->getPostquestion3(), 'question3'),
                 'question4'        => $this->improvementMeasure($s->getQuestion4(), $s->getPostquestion4(), 'question4'),
                 'question5'        => $this->improvementMeasure($s->getQuestion5(), $s->getPostquestion5(), 'question5'),
                 'question6'        => $this->improvementMeasure($s->getQuestion6(), $s->getPostquestion6(), 'question6'),
                 'question7'        => $this->improvementMeasure($s->getQuestion7(), $s->getPostquestion7(), 'question7'),
                 'question8'        => $this->improvementMeasure('option1', $s->getPostquestion8(), 'question8'),
                 'question9'        => $this->improvementMeasure('option1', $s->getPostquestion9(), 'question9'),
                 'questionInitial1' => $this->initialState($s->getQuestion1(), 'question1'),
                 'questionInitial2' => $this->initialState($s->getQuestion2(), 'question2'),
                 'questionInitial3' => $this->initialState($s->getQuestion3(), 'question3'),
                 'questionInitial4' => $this->initialState($s->getQuestion4(), 'question4'),
                 'questionInitial5' => $this->initialState($s->getQuestion5(), 'question5'),
                 'questionInitial6' => $this->initialState($s->getQuestion6(), 'question6'),
                 'questionInitial7' => $this->initialState($s->getQuestion7(), 'question7'),
                 'questionFinal1'   => $this->potentialImprovement($s->getQuestion1(), $s->getPostquestion1(), 'question1'),
                 'questionFinal2'   => $this->potentialImprovement($s->getQuestion2(), $s->getPostquestion2(), 'question2'),
                 'questionFinal3'   => $this->potentialImprovement($s->getQuestion3(), $s->getPostquestion3(), 'question3'),
                 'questionFinal4'   => $this->potentialImprovement($s->getQuestion4(), $s->getPostquestion4(), 'question4'),
                 'questionFinal5'   => $this->potentialImprovement($s->getQuestion5(), $s->getPostquestion5(), 'question5'),
                 'questionFinal6'   => $this->potentialImprovement($s->getQuestion6(), $s->getPostquestion6(), 'question6'),
                 'questionFinal7'   => $this->potentialImprovement($s->getQuestion7(), $s->getPostquestion7(), 'question7'),
                 'questionFinal8'   => $this->potentialImprovement('option1', $s->getPostquestion8(), 'question8'),
                 'questionFinal9'   => $this->potentialImprovement('option1', $s->getPostquestion9(), 'question9'),
  
                 'group'            => $student->getStudentgroup()->getGroup()->getName(),
                 'ambassador'       => $student->getStudentgroup()->getGroup()->getEmbassador()->getFirstName()." ".$student->getStudentgroup()->getGroup()->getEmbassador()->getLastName()
               );
            }
  
        }
        return $evaluations;
  
      }

      private function evaluationReportByCountryProgram($country){
        $em = $this->getDoctrine()->getManager();
  
        if($country and $country != "ALL"){
            $studentsEvaluations = $em->getRepository('App:Evaluation')->evaluationByCountry($country);
        }
        else{
            $studentsEvaluations = $em->getRepository('App:Evaluation')->findAll();
        }
  
  
        $evaluationsMbs = array();
        $evaluationsJr = array();
        $evaluations = array();
        foreach ($studentsEvaluations as $s) {
          $student = $s->getStudent();
            if($s->getPostquestion1() and $student->getStudentgroup()->getGroup()->getProgram() == 'option.program4'){
               $evaluationsJr[] = array(
                 'name'             => $student->getFirstName()." ".$student->getLastName(),
                 'question1'        => $this->improvementMeasure($s->getQuestion1(), $s->getPostquestion1(), 'question1'),
                 'question2'        => $this->improvementMeasure($s->getQuestion2(), $s->getPostquestion2(), 'question2'),
                 'question3'        => $this->improvementMeasure($s->getQuestion3(), $s->getPostquestion3(), 'question3'),
                 'question4'        => $this->improvementMeasure($s->getQuestion4(), $s->getPostquestion4(), 'question4'),
                 'question5'        => $this->improvementMeasure($s->getQuestion5(), $s->getPostquestion5(), 'question5'),
                 'question6'        => $this->improvementMeasure($s->getQuestion6(), $s->getPostquestion6(), 'question6'),
                 'question7'        => $this->improvementMeasure($s->getQuestion7(), $s->getPostquestion7(), 'question7'),
                 'question8'        => $this->improvementMeasure('option1', $s->getPostquestion8(), 'question8'),
                 'question9'        => $this->improvementMeasure('option1', $s->getPostquestion9(), 'question9'),
                 'questionInitial1' => $this->initialState($s->getQuestion1(), 'question1'),
                 'questionInitial2' => $this->initialState($s->getQuestion2(), 'question2'),
                 'questionInitial3' => $this->initialState($s->getQuestion3(), 'question3'),
                 'questionInitial4' => $this->initialState($s->getQuestion4(), 'question4'),
                 'questionInitial5' => $this->initialState($s->getQuestion5(), 'question5'),
                 'questionInitial6' => $this->initialState($s->getQuestion6(), 'question6'),
                 'questionInitial7' => $this->initialState($s->getQuestion7(), 'question7'),
                 'questionFinal1'   => $this->potentialImprovement($s->getQuestion1(), $s->getPostquestion1(), 'question1'),
                 'questionFinal2'   => $this->potentialImprovement($s->getQuestion2(), $s->getPostquestion2(), 'question2'),
                 'questionFinal3'   => $this->potentialImprovement($s->getQuestion3(), $s->getPostquestion3(), 'question3'),
                 'questionFinal4'   => $this->potentialImprovement($s->getQuestion4(), $s->getPostquestion4(), 'question4'),
                 'questionFinal5'   => $this->potentialImprovement($s->getQuestion5(), $s->getPostquestion5(), 'question5'),
                 'questionFinal6'   => $this->potentialImprovement($s->getQuestion6(), $s->getPostquestion6(), 'question6'),
                 'questionFinal7'   => $this->potentialImprovement($s->getQuestion7(), $s->getPostquestion7(), 'question7'),
                 'questionFinal8'   => $this->potentialImprovement('option1', $s->getPostquestion8(), 'question8'),
                 'questionFinal9'   => $this->potentialImprovement('option1', $s->getPostquestion9(), 'question9'),
  
                 'group'            => $student->getStudentgroup()->getGroup()->getName(),
                 'ambassador'       => $student->getStudentgroup()->getGroup()->getEmbassador()->getFirstName()." ".$student->getStudentgroup()->getGroup()->getEmbassador()->getLastName()
               );
            }
            else if($s->getPostquestion1()){
              $evaluationsMbs[] = array(
                'name'             => $student->getFirstName()." ".$student->getLastName(),
                'question1'        => $this->improvementMeasure($s->getQuestion1(), $s->getPostquestion1(), 'question1'),
                'question2'        => $this->improvementMeasure($s->getQuestion2(), $s->getPostquestion2(), 'question2'),
                'question3'        => $this->improvementMeasure($s->getQuestion3(), $s->getPostquestion3(), 'question3'),
                'question4'        => $this->improvementMeasure($s->getQuestion4(), $s->getPostquestion4(), 'question4'),
                'question5'        => $this->improvementMeasure($s->getQuestion5(), $s->getPostquestion5(), 'question5'),
                'question6'        => $this->improvementMeasure($s->getQuestion6(), $s->getPostquestion6(), 'question6'),
                'question7'        => $this->improvementMeasure($s->getQuestion7(), $s->getPostquestion7(), 'question7'),
                'question8'        => $this->improvementMeasure('option1', $s->getPostquestion8(), 'question8'),
                'question9'        => $this->improvementMeasure('option1', $s->getPostquestion9(), 'question9'),
                'questionInitial1' => $this->initialState($s->getQuestion1(), 'question1'),
                'questionInitial2' => $this->initialState($s->getQuestion2(), 'question2'),
                'questionInitial3' => $this->initialState($s->getQuestion3(), 'question3'),
                'questionInitial4' => $this->initialState($s->getQuestion4(), 'question4'),
                'questionInitial5' => $this->initialState($s->getQuestion5(), 'question5'),
                'questionInitial6' => $this->initialState($s->getQuestion6(), 'question6'),
                'questionInitial7' => $this->initialState($s->getQuestion7(), 'question7'),
                'questionFinal1'   => $this->potentialImprovement($s->getQuestion1(), $s->getPostquestion1(), 'question1'),
                'questionFinal2'   => $this->potentialImprovement($s->getQuestion2(), $s->getPostquestion2(), 'question2'),
                'questionFinal3'   => $this->potentialImprovement($s->getQuestion3(), $s->getPostquestion3(), 'question3'),
                'questionFinal4'   => $this->potentialImprovement($s->getQuestion4(), $s->getPostquestion4(), 'question4'),
                'questionFinal5'   => $this->potentialImprovement($s->getQuestion5(), $s->getPostquestion5(), 'question5'),
                'questionFinal6'   => $this->potentialImprovement($s->getQuestion6(), $s->getPostquestion6(), 'question6'),
                'questionFinal7'   => $this->potentialImprovement($s->getQuestion7(), $s->getPostquestion7(), 'question7'),
                'questionFinal8'   => $this->potentialImprovement('option1', $s->getPostquestion8(), 'question8'),
                'questionFinal9'   => $this->potentialImprovement('option1', $s->getPostquestion9(), 'question9'),
 
                'group'            => $student->getStudentgroup()->getGroup()->getName(),
                'ambassador'       => $student->getStudentgroup()->getGroup()->getEmbassador()->getFirstName()." ".$student->getStudentgroup()->getGroup()->getEmbassador()->getLastName()
              );
            }
  
        }
        $evaluations['mbs'] = $evaluationsMbs;
        $evaluations['jr'] = $evaluationsJr;

        return $evaluations;
  
      }

      private function improvementMeasure($question, $postquestion, $numberQuestion){

        $postQ = (int) str_replace('option', '' , $postquestion);
        $preQ = (int) str_replace('option', '' , $question);
  
        if($numberQuestion == "question1"){
          if($postQ < $preQ){
            return "IMPROVEMENT";
  
          }
          elseif($postQ == $preQ){
            return "IQUAL";
  
          }
          else{
            return "WORSEN";
          }
        }
  
  
        if($postQ > $preQ){
          return "IMPROVEMENT";
        }
        elseif($postQ == $preQ){
          return "IQUAL";
        }
        else{
          return "WORSEN";
        }
  
      }
  
      private function potentialImprovement($question, $postquestion, $numberQuestion){
  
        $postQ = (int) str_replace('option', '' , $postquestion);
        $preQ = (int) str_replace('option', '' , $question);
  
        if($numberQuestion == "question1"){
          if($postQ == 1){
            return "YES";
          }
          else{
            return "NOT";
          }
        }
        elseif($preQ < 3){
          if($postQ > $preQ){
            return "YES";
          }
          else{
            return "NOT";
          }
        }
        else{
          if($postQ > 2){
            return "YES";
          }
          else{
            return "NOT";
          }
  
        }
  
      }

      private function initialState($question, $numberQuestion){

        $preQ = (int) str_replace('option', '' , $question);
  
        if($numberQuestion == "question1"){
          if($preQ == 1){
            return "YES";
  
          }
          elseif($preQ == 2){
            return "NOT";
  
          }
        }
  
  
        if($preQ > 2){
          return "YES";
        }
        else{
          return "NOT";
        }
  
      }
  
      private function showStatistics($evaluations, $program ){
        $statistics = array();
        $studentsPre1 = 0;
        $studentsPre2 = 0;
        $studentsPre3 = 0;
        $studentsPre4 = 0;
        $studentsPre5 = 0;
        $studentsPre6 = 0;
        $studentsPre7 = 0;
        $studentsPre8 = 0;
        $studentsPre9 = 0;
  
        $students1 = 0;
        $students2 = 0;
        $students3 = 0;
        $students4 = 0;
        $students5 = 0;
        $students6 = 0;
        $students7 = 0;
        $students8 = 0;
        $students9 = 0;
  
        $studentsInitial1 = 0;
        $studentsInitial2 = 0;
        $studentsInitial3 = 0;
        $studentsInitial4 = 0;
        $studentsInitial5 = 0;
        $studentsInitial6 = 0;
        $studentsInitial7 = 0;
        $studentsInitial8 = 0;
        $studentsInitial9 = 0;
  
        $studentsFinal1 = 0;
        $studentsFinal2 = 0;
        $studentsFinal3 = 0;
        $studentsFinal4 = 0;
        $studentsFinal5 = 0;
        $studentsFinal6 = 0;
        $studentsFinal7 = 0;
        $studentsFinal8 = 0;
        $studentsFinal9 = 0;
  
  
  
        foreach ($evaluations as $e) {
            if($e['question1']  == 'IMPROVEMENT'){
                $students1++;
            }
            if($e['question2']  == 'IMPROVEMENT'){
                $students2++;
            }
            if($e['question3']  == 'IMPROVEMENT'){
                $students3++;
            }
            if($e['question4']  == 'IMPROVEMENT'){
                $students4++;
            }
            if($e['question5']  == 'IMPROVEMENT'){
                $students5++;
            }
            if($e['question6']  == 'IMPROVEMENT'){
                $students6++;
            }
            if($e['question7']  == 'IMPROVEMENT'){
                $students7++;
            }
            if($e['question8']  == 'IMPROVEMENT'){
                $students8++;
            }
            if($e['question9']  == 'IMPROVEMENT'){
                $students9++;
            }
  
            if($e['questionInitial1']  == 'YES'){
                $studentsInitial1++;
            }
            if($e['questionInitial2']  == 'YES'){
                $studentsInitial2++;
            }
            if($e['questionInitial3']  == 'YES'){
                $studentsInitial3++;
            }
            if($e['questionInitial4']  == 'YES'){
                $studentsInitial4++;
            }
            if($e['questionInitial5']  == 'YES'){
                $studentsInitial5++;
            }
            if($e['questionInitial6']  == 'YES'){
                $studentsInitial6++;
            }
            if($e['questionInitial7']  == 'YES'){
                $studentsInitial7++;
            }
  
            if($e['questionFinal1']  == 'YES'){
                $studentsFinal1++;
            }
            if($e['questionFinal2']  == 'YES'){
                $studentsFinal2++;
            }
            if($e['questionFinal3']  == 'YES'){
                $studentsFinal3++;
            }
            if($e['questionFinal4']  == 'YES'){
                $studentsFinal4++;
            }
            if($e['questionFinal5']  == 'YES'){
                $studentsFinal5++;
            }
            if($e['questionFinal6']  == 'YES'){
                $studentsFinal6++;
            }
            if($e['questionFinal7']  == 'YES'){
                $studentsFinal7++;
            }
            if($e['questionFinal8']  == 'YES'){
                $studentsFinal8++;
            }
            if($e['questionFinal9']  == 'YES'){
                $studentsFinal9++;
            }
  
  
  
  
        }
  
        if(!$evaluations){
           $evaluations = array();
           $evaluations[] = "";
           $nst = 0;
           $nst2 = 1;
        }
        else{
          $nst = count($evaluations);
          $nst2 = $nst;
        }

        if($program === "MBS"){
          $preQuestion = "question_";
        }
        else{
          $preQuestion = "question_jr_";
        }
  
        $statistics[] = array(
          'question'      =>  $preQuestion.'evaluation_question1',
          'studentsPre'   =>  $studentsInitial1."/".$nst,
          'students'      =>  $studentsFinal1."/".$nst,
          'percentagePre' =>  intval((100 *  $studentsInitial1) / $nst2),
          'percentage'    =>  intval((100 *  $studentsFinal1) / $nst2)
        );
  
        $statistics[] = array(
          'question'      =>  $preQuestion.'evaluation_question2',
          'studentsPre'   =>  $studentsInitial2."/".$nst,
          'students'      =>  $studentsFinal2."/".$nst,
          'percentagePre' =>  intval((100 *  $studentsInitial2) / $nst2),
          'percentage'    =>  intval((100 *  $studentsFinal2) / $nst2)
        );
  
        $statistics[] = array(
          'question'      =>  $preQuestion.'evaluation_question3',
          'studentsPre'   =>  $studentsInitial3."/".$nst,
          'students'      =>  $studentsFinal3."/".$nst,
          'percentagePre' =>  intval((100 *  $studentsInitial3) / $nst2),
          'percentage'    =>  intval((100 *  $studentsFinal3) / $nst2)
        );
  
        $statistics[] = array(
          'question'      =>  $preQuestion.'evaluation_question4',
          'studentsPre'   =>  $studentsInitial4."/".$nst,
          'students'      =>  $studentsFinal4."/".$nst,
          'percentagePre' =>  intval((100 *  $studentsInitial4) / $nst2),
          'percentage'    =>  intval((100 *  $studentsFinal4) / $nst2)
        );
  
        $statistics[] = array(
          'question'      =>  $preQuestion.'evaluation_question5',
          'studentsPre'   =>  $studentsInitial5."/".$nst,
          'students'      =>  $studentsFinal5."/".$nst,
          'percentagePre' =>  intval((100 *  $studentsInitial5) / $nst2),
          'percentage'    =>  intval((100 *  $studentsFinal5) / $nst2)
        );
  
        $statistics[] = array(
          'question'      =>  $preQuestion.'evaluation_question6',
          'studentsPre'   =>  $studentsInitial6."/".$nst,
          'students'      =>  $studentsFinal6."/".$nst,
          'percentagePre' =>  intval((100 *  $studentsInitial6) / $nst2),
          'percentage'    =>  intval((100 *  $studentsFinal6) / $nst2)
        );
  
        $statistics[] = array(
          'question'      =>  $preQuestion.'evaluation_question7',
          'studentsPre'   =>  $studentsInitial7."/".$nst,
          'students'      =>  $studentsFinal7."/".$nst,
          'percentagePre' =>  intval((100 *  $studentsInitial7) / $nst2),
          'percentage'    =>  intval((100 *  $studentsFinal7) / $nst2)
        );
  
        $statistics[] = array(
          'question'      =>  $preQuestion.'evaluation_question8',
          'studentsPre'   =>  $studentsInitial8."/".$nst,
          'students'      =>  $studentsFinal8."/".$nst,
          'percentagePre' =>  intval((100 *  $studentsPre8) / $nst2),
          'percentage'    =>  intval((100 *  $studentsFinal8) / $nst2)
  
        );
  
        $statistics[] = array(
          'question'      =>  $preQuestion.'evaluation_question9',
          'studentsPre'   =>  $studentsInitial9."/".$nst,
          'students'      =>  $studentsFinal9."/".$nst,
          'percentagePre' =>  intval((100 *  $studentsPre9) / $nst2),
          'percentage'    =>  intval((100 *  $studentsFinal9) / $nst2)
        );
  
        return $statistics;
  
      }
  
      private function getFormat($df) {
          //$language = $this->getUser()->getLanguage();
          $language = "es";
  
          $years = 'Years';
          $year = 'Year';
          $months = 'Months';
          $month = 'Month';
          $days = 'Days';
          $day = 'Day';
  
          $str = '';
          $str .= ($df->invert == 1) ? ' - ' : '';
          if ($df->y > 0) {
              // years
              $str .= ($df->y > 1) ? $df->y . ' '. $years .' ' : $df->y . ' '. $year .' ';
          } if ($df->m > 0) {
              // month
              $str .= ($df->m > 1) ? $df->m . ' '. $months .' ' : $df->m . ' '. $month .' ';
          } if ($df->d > 0) {
              // days
              $str .= ($df->d > 1) ? $df->d . ' '. $days .' ' : $df->d . ' '. $day .' ';
          } if ($df->h > 0) {
              // hours
              //$str .= ($df->h > 1) ? $df->h . ' Hours ' : $df->h . ' Hour ';
          } if ($df->i > 0) {
              // minutes
              //$str .= ($df->i > 1) ? $df->i . ' Minutes ' : $df->i . ' Minute ';
          } if ($df->s > 0) {
              // seconds
              //$str .= ($df->s > 1) ? $df->s . ' Seconds ' : $df->s . ' Second ';
          }
  
          return $str;
      }

      private function evaluationReportXAmbassador($ambassador_id){
        $em = $this->getDoctrine()->getManager();
  
        if($ambassador_id != 'null'){
            $studentsGroup1 = $em->getRepository('App:StudentGroup')->studentsMbsByEmbassador($ambassador_id);
            $studentsGroup2 = $em->getRepository('App:StudentAmbassadorGroup')->studentsMbsByEmbassador($ambassador_id);
            $studentsGroup = new ArrayCollection(
              array_merge($studentsGroup1, $studentsGroup2)
            );
        }
        else{
            $studentsGroup1 = $em->getRepository('App:StudentGroup')->findAll();
            $studentsGroup2 = $em->getRepository('App:StudentAmbassadorGroup')->findAll();

            $studentsGroup = new ArrayCollection(
              array_merge($studentsGroup1, $studentsGroup2)
            );
        }
  
  
        $evaluations    = array();
        $evaluationsJr  = array();
        $evaluationsMbs = array();
        foreach ($studentsGroup as $s) {
          $student = $s->getStudent();
            if($student->getEvaluation()){
                if($student->getEvaluation()->getPostquestion1() and $student->getStudentgroup()->getGroup()->getProgram() == 'option.program4'){
                  $evaluationsJr[] = array(
                    'name'             => $student->getFirstName()." ".$student->getLastName(),
                    'question1'        => $this->improvementMeasure($student->getEvaluation()->getQuestion1(), $student->getEvaluation()->getPostquestion1(), 'question1'),
                    'question2'        => $this->improvementMeasure($student->getEvaluation()->getQuestion2(), $student->getEvaluation()->getPostquestion2(), 'question2'),
                    'question3'        => $this->improvementMeasure($student->getEvaluation()->getQuestion3(), $student->getEvaluation()->getPostquestion3(), 'question3'),
                    'question4'        => $this->improvementMeasure($student->getEvaluation()->getQuestion4(), $student->getEvaluation()->getPostquestion4(), 'question4'),
                    'question5'        => $this->improvementMeasure($student->getEvaluation()->getQuestion5(), $student->getEvaluation()->getPostquestion5(), 'question5'),
                    'question6'        => $this->improvementMeasure($student->getEvaluation()->getQuestion6(), $student->getEvaluation()->getPostquestion6(), 'question6'),
                    'question7'        => $this->improvementMeasure($student->getEvaluation()->getQuestion7(), $student->getEvaluation()->getPostquestion7(), 'question7'),
                    'question8'        => $this->improvementMeasure('option1', $student->getEvaluation()->getPostquestion8(), 'question8'),
                    'question9'        => $this->improvementMeasure('option1', $student->getEvaluation()->getPostquestion9(), 'question9'),
                    'questionInitial1' => $this->initialState($student->getEvaluation()->getQuestion1(), 'question1'),
                    'questionInitial2' => $this->initialState($student->getEvaluation()->getQuestion2(), 'question2'),
                    'questionInitial3' => $this->initialState($student->getEvaluation()->getQuestion3(), 'question3'),
                    'questionInitial4' => $this->initialState($student->getEvaluation()->getQuestion4(), 'question4'),
                    'questionInitial5' => $this->initialState($student->getEvaluation()->getQuestion5(), 'question5'),
                    'questionInitial6' => $this->initialState($student->getEvaluation()->getQuestion6(), 'question6'),
                    'questionInitial7' => $this->initialState($student->getEvaluation()->getQuestion7(), 'question7'),
                    'questionFinal1'   => $this->potentialImprovement($student->getEvaluation()->getQuestion1(), $student->getEvaluation()->getPostquestion1(), 'question1'),
                    'questionFinal2'   => $this->potentialImprovement($student->getEvaluation()->getQuestion2(), $student->getEvaluation()->getPostquestion2(), 'question2'),
                    'questionFinal3'   => $this->potentialImprovement($student->getEvaluation()->getQuestion3(), $student->getEvaluation()->getPostquestion3(), 'question3'),
                    'questionFinal4'   => $this->potentialImprovement($student->getEvaluation()->getQuestion4(), $student->getEvaluation()->getPostquestion4(), 'question4'),
                    'questionFinal5'   => $this->potentialImprovement($student->getEvaluation()->getQuestion5(), $student->getEvaluation()->getPostquestion5(), 'question5'),
                    'questionFinal6'   => $this->potentialImprovement($student->getEvaluation()->getQuestion6(), $student->getEvaluation()->getPostquestion6(), 'question6'),
                    'questionFinal7'   => $this->potentialImprovement($student->getEvaluation()->getQuestion7(), $student->getEvaluation()->getPostquestion7(), 'question7'),
                    'questionFinal8'   => $this->potentialImprovement('option1', $student->getEvaluation()->getPostquestion8(), 'question8'),
                    'questionFinal9'   => $this->potentialImprovement('option1', $student->getEvaluation()->getPostquestion9(), 'question9'),
      
                    'group'            => $student->getStudentgroup()->getGroup()->getName(),
                    'ambassador'       => $student->getStudentgroup()->getGroup()->getEmbassador()->getFirstName()." ".$student->getStudentgroup()->getGroup()->getEmbassador()->getLastName()
                  );
              }
              else if($student->getEvaluation()->getPostquestion1()){
                $evaluationsMbs[] = array(
                  'name'             => $student->getFirstName()." ".$student->getLastName(),
                  'question1'        => $this->improvementMeasure($student->getEvaluation()->getQuestion1(), $student->getEvaluation()->getPostquestion1(), 'question1'),
                  'question2'        => $this->improvementMeasure($student->getEvaluation()->getQuestion2(), $student->getEvaluation()->getPostquestion2(), 'question2'),
                  'question3'        => $this->improvementMeasure($student->getEvaluation()->getQuestion3(), $student->getEvaluation()->getPostquestion3(), 'question3'),
                  'question4'        => $this->improvementMeasure($student->getEvaluation()->getQuestion4(), $student->getEvaluation()->getPostquestion4(), 'question4'),
                  'question5'        => $this->improvementMeasure($student->getEvaluation()->getQuestion5(), $student->getEvaluation()->getPostquestion5(), 'question5'),
                  'question6'        => $this->improvementMeasure($student->getEvaluation()->getQuestion6(), $student->getEvaluation()->getPostquestion6(), 'question6'),
                  'question7'        => $this->improvementMeasure($student->getEvaluation()->getQuestion7(), $student->getEvaluation()->getPostquestion7(), 'question7'),
                  'question8'        => $this->improvementMeasure('option1', $student->getEvaluation()->getPostquestion8(), 'question8'),
                  'question9'        => $this->improvementMeasure('option1', $student->getEvaluation()->getPostquestion9(), 'question9'),
                  'questionInitial1' => $this->initialState($student->getEvaluation()->getQuestion1(), 'question1'),
                  'questionInitial2' => $this->initialState($student->getEvaluation()->getQuestion2(), 'question2'),
                  'questionInitial3' => $this->initialState($student->getEvaluation()->getQuestion3(), 'question3'),
                  'questionInitial4' => $this->initialState($student->getEvaluation()->getQuestion4(), 'question4'),
                  'questionInitial5' => $this->initialState($student->getEvaluation()->getQuestion5(), 'question5'),
                  'questionInitial6' => $this->initialState($student->getEvaluation()->getQuestion6(), 'question6'),
                  'questionInitial7' => $this->initialState($student->getEvaluation()->getQuestion7(), 'question7'),
                  'questionFinal1'   => $this->potentialImprovement($student->getEvaluation()->getQuestion1(), $student->getEvaluation()->getPostquestion1(), 'question1'),
                  'questionFinal2'   => $this->potentialImprovement($student->getEvaluation()->getQuestion2(), $student->getEvaluation()->getPostquestion2(), 'question2'),
                  'questionFinal3'   => $this->potentialImprovement($student->getEvaluation()->getQuestion3(), $student->getEvaluation()->getPostquestion3(), 'question3'),
                  'questionFinal4'   => $this->potentialImprovement($student->getEvaluation()->getQuestion4(), $student->getEvaluation()->getPostquestion4(), 'question4'),
                  'questionFinal5'   => $this->potentialImprovement($student->getEvaluation()->getQuestion5(), $student->getEvaluation()->getPostquestion5(), 'question5'),
                  'questionFinal6'   => $this->potentialImprovement($student->getEvaluation()->getQuestion6(), $student->getEvaluation()->getPostquestion6(), 'question6'),
                  'questionFinal7'   => $this->potentialImprovement($student->getEvaluation()->getQuestion7(), $student->getEvaluation()->getPostquestion7(), 'question7'),
                  'questionFinal8'   => $this->potentialImprovement('option1', $student->getEvaluation()->getPostquestion8(), 'question8'),
                  'questionFinal9'   => $this->potentialImprovement('option1', $student->getEvaluation()->getPostquestion9(), 'question9'),

                  'group'            => $student->getStudentgroup()->getGroup()->getName(),
                  'ambassador'       => $student->getStudentgroup()->getGroup()->getEmbassador()->getFirstName()." ".$student->getStudentgroup()->getGroup()->getEmbassador()->getLastName()
                );
              }
 
            }
       }
       $evaluations['mbs'] = $evaluationsMbs;
       $evaluations['jr'] = $evaluationsJr;

       return $evaluations;
        
  
      }

      private function evaluationReportXAmbassadorFull($ambassador_id){
        $em = $this->getDoctrine()->getManager();
  
        if($ambassador_id != 'null'){
            $studentsGroup1 = $em->getRepository('App:StudentGroup')->studentsMbsByEmbassador($ambassador_id);
            $studentsGroup2 = $em->getRepository('App:StudentAmbassadorGroup')->studentsMbsByEmbassador($ambassador_id);
            $studentsGroup = new ArrayCollection(
              array_merge($studentsGroup1, $studentsGroup2)
            );
        }
        else{
            $studentsGroup1 = $em->getRepository('App:StudentGroup')->findAll();
            $studentsGroup2 = $em->getRepository('App:StudentAmbassadorGroup')->findAll();

            $studentsGroup = new ArrayCollection(
              array_merge($studentsGroup1, $studentsGroup2)
            );
        }
  
  
        $evaluations    = array();
        foreach ($studentsGroup as $s) {
          $student = $s->getStudent();
            if($student->getEvaluation()){
                if($student->getEvaluation()->getPostquestion1()){
                  $evaluations[] = array(
                    'name'             => $student->getFirstName()." ".$student->getLastName(),
                    'question1'        => $this->improvementMeasure($student->getEvaluation()->getQuestion1(), $student->getEvaluation()->getPostquestion1(), 'question1'),
                    'question2'        => $this->improvementMeasure($student->getEvaluation()->getQuestion2(), $student->getEvaluation()->getPostquestion2(), 'question2'),
                    'question3'        => $this->improvementMeasure($student->getEvaluation()->getQuestion3(), $student->getEvaluation()->getPostquestion3(), 'question3'),
                    'question4'        => $this->improvementMeasure($student->getEvaluation()->getQuestion4(), $student->getEvaluation()->getPostquestion4(), 'question4'),
                    'question5'        => $this->improvementMeasure($student->getEvaluation()->getQuestion5(), $student->getEvaluation()->getPostquestion5(), 'question5'),
                    'question6'        => $this->improvementMeasure($student->getEvaluation()->getQuestion6(), $student->getEvaluation()->getPostquestion6(), 'question6'),
                    'question7'        => $this->improvementMeasure($student->getEvaluation()->getQuestion7(), $student->getEvaluation()->getPostquestion7(), 'question7'),
                    'question8'        => $this->improvementMeasure('option1', $student->getEvaluation()->getPostquestion8(), 'question8'),
                    'question9'        => $this->improvementMeasure('option1', $student->getEvaluation()->getPostquestion9(), 'question9'),
                    'questionInitial1' => $this->initialState($student->getEvaluation()->getQuestion1(), 'question1'),
                    'questionInitial2' => $this->initialState($student->getEvaluation()->getQuestion2(), 'question2'),
                    'questionInitial3' => $this->initialState($student->getEvaluation()->getQuestion3(), 'question3'),
                    'questionInitial4' => $this->initialState($student->getEvaluation()->getQuestion4(), 'question4'),
                    'questionInitial5' => $this->initialState($student->getEvaluation()->getQuestion5(), 'question5'),
                    'questionInitial6' => $this->initialState($student->getEvaluation()->getQuestion6(), 'question6'),
                    'questionInitial7' => $this->initialState($student->getEvaluation()->getQuestion7(), 'question7'),
                    'questionFinal1'   => $this->potentialImprovement($student->getEvaluation()->getQuestion1(), $student->getEvaluation()->getPostquestion1(), 'question1'),
                    'questionFinal2'   => $this->potentialImprovement($student->getEvaluation()->getQuestion2(), $student->getEvaluation()->getPostquestion2(), 'question2'),
                    'questionFinal3'   => $this->potentialImprovement($student->getEvaluation()->getQuestion3(), $student->getEvaluation()->getPostquestion3(), 'question3'),
                    'questionFinal4'   => $this->potentialImprovement($student->getEvaluation()->getQuestion4(), $student->getEvaluation()->getPostquestion4(), 'question4'),
                    'questionFinal5'   => $this->potentialImprovement($student->getEvaluation()->getQuestion5(), $student->getEvaluation()->getPostquestion5(), 'question5'),
                    'questionFinal6'   => $this->potentialImprovement($student->getEvaluation()->getQuestion6(), $student->getEvaluation()->getPostquestion6(), 'question6'),
                    'questionFinal7'   => $this->potentialImprovement($student->getEvaluation()->getQuestion7(), $student->getEvaluation()->getPostquestion7(), 'question7'),
                    'questionFinal8'   => $this->potentialImprovement('option1', $student->getEvaluation()->getPostquestion8(), 'question8'),
                    'questionFinal9'   => $this->potentialImprovement('option1', $student->getEvaluation()->getPostquestion9(), 'question9'),
      
                    'group'            => $student->getStudentgroup()->getGroup()->getName(),
                    'ambassador'       => $student->getStudentgroup()->getGroup()->getEmbassador()->getFirstName()." ".$student->getStudentgroup()->getGroup()->getEmbassador()->getLastName()
                  );
              }             
 
            }
       }
     

       return $evaluations;
        
  
      }

}