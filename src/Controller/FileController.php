<?php
/**
 * FileController.php
 *
 * API Controller
 *
 * @category   Controller
 *
 */
 
namespace App\Controller;
 
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Enfiler\UserPasswordEnfilerInterface;
use Nelmio\UserDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

// Import required classes
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\File\MimeType\FileinfoMimeTypeGuesser;
use ZipArchive;

 
/**
 * Class FileController
 *
 * @Route("/file")
 */
class FileController extends FOSRestController
{
    
  /**
     * @Rest\Post("/upload", name="file_upload")
     *
     * @SWG\Response(
     *     response=201,
     *     description="File was successfully uploaded"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="File was not successfully uploaded"
     * )
     *    
     * 
     * @SWG\Parameter(
     *     name="file",
     *     in="body",
     *     type="file",
     *     description="The file",
     *     schema={}
     * )
     * 
     *    
     * @SWG\Tag(name="File")
     */ 

    public function uploadFileAction(Request $request) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
 
        $message = "";
        $files = $request->files->get('file');
        //$allfiles = $request->files;

        try {
            $code = 200;
            $error = false;

            if (true) {
                $zip = new ZipArchive();
                $fileName = md5(uniqid()).'.zip';
                $createfile = $zip->open($this->container->getparameter('kernel.project_dir').'/web/file/'.$fileName, ZipArchive::CREATE);
                if(count($files) == 1){
                    $fileName = md5(uniqid()).'.'.$files[0]->guessExtension();
                    $cvDir = $this->container->getparameter('kernel.project_dir').'/web/file';
                    $files[0]->move($cvDir, $fileName);
                }
                else{
                    foreach ( $files as $key => $uploadedFile) {
                        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                            $fn = md5(uniqid()).'.'.$uploadedFile->guessExtension();
                            $ext = $uploadedFile->guessExtension();
                            $cvDir = $this->container->getparameter('kernel.project_dir').'/web/file';
                            $uploadedFile->move($cvDir, $fn);

                            if ( $createfile === true){
                                $zip->addFile($this->container->getparameter('kernel.project_dir').'/web/file/'.$fn, "file".$key.".".$ext);
                            }
                        }
                    }
                    $zip->close();
                }
                
                   
             
                //$fileName = md5(uniqid()).'.'.$file->guessExtension();
                //$cvDir = $this->container->getparameter('kernel.project_dir').'/web/file'.'/' . $fileName;

                //move_uploaded_file($file->getRealPath(),	$cvDir);
              
 
                $message = "The file was uploaded successfully!";
 
            } else {
                $code = 500;
                $error = true;
                $message = "An error has occurred trying to upload file - Error: ". $file->getError();
            }

 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to register the Group - Error: {$ex->getMessage()}";
        }
 
        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $fileName : $message,
        ];
 
        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Get("/download", name="file_download")
     *
     * @SWG\Response(
     *     response=201,
     *     description="File was successfully uploaded"
     * )
     *
     * @SWG\Response(
     *     response=500,
     *     description="File was not successfully uploaded"
     * )
     *    
     * 
     * @SWG\Parameter(
     *     name="file",
     *     in="body",
     *     type="file",
     *     description="The file",
     *     schema={}
     * )
     * 
     *    
     * @SWG\Tag(name="File")
     */ 

    public function fileDownloadAction(Request $request) {


        $publicResourcesFolderPath = $this->container->getparameter('kernel.project_dir').'/web/file/';
        $filename = $request->files->get('filename');

        // This should return the file to the browser as response
        $response = new BinaryFileResponse($publicResourcesFolderPath.$filename);

        // To generate a file download, you need the mimetype of the file
        $mimeTypeGuesser = new FileinfoMimeTypeGuesser();

        // Set the mimetype with the guesser or manually
        if($mimeTypeGuesser->isSupported()){
            // Guess the mimetype of the file according to the extension of the file
            $response->headers->set('Content-Type', $mimeTypeGuesser->guess($publicResourcesFolderPath.$filename));
        }else{
            // Set the mimetype of the file manually, in this case for a text file is text/plain
            $response->headers->set('Content-Type', 'text/plain');
        }

        // Set content disposition inline of the file
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );

        return $response;
    }

    /**
     * @Rest\Get("/grantapplication/{id}.{_format}", name="grant_file", defaults={"_format":"json"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Gets grant info based on passed ID parameter."
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="The grant with the passed ID parameter was not found or doesn't exist."
     * )
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="string",
     *     description="The Grant ID"
     * )
     *
     * @SWG\Tag(name="File")
     */
    public function grantApplicationAction(Request $request, $id ) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();
        $grant = [];
        $message = "";
        
        try {
            $code = 200;
            $error = false;
 
            $ga = $em->getRepository('App:GrantAmbassador')->find($id);
 
           

            $pdf = $this->get('white_october.tcpdf.public')->create('vertical', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
      
            $pdf->SetAuthor('Interweave Solutions');
            $pdf->SetTitle(('Grant Application'));
            $pdf->SetSubject('Grant Application');
            $pdf->setFontSubsetting(true);
            
            $pdf->SetFont("snellb", '', 8);
            $pdf->setPrintHeader(false);
      
            $pdf->AddPage();
    
            // get the current page break margin
            $bMargin = $pdf->getBreakMargin();
            // get current auto-page-break mode
            $auto_page_break = $pdf->getAutoPageBreak();
            // disable auto-page-break
            $pdf->SetAutoPageBreak(false, 0);
            
            $img_file = $this->container->getparameter('kernel.project_dir').'/web/img/interweavelogo.png';
            $pdf->Image($img_file, 10, 5, 35, 10, '', '', '', false, 600, '', false, false, 0);
                
            // restore auto-page-break status
            $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
            // set the starting point for the page content
            $pdf->setPageMark();
      
            // remove default header/footer
            $pdf->setPrintHeader(false);

            // set default font subsetting mode
            $pdf->setFontSubsetting(true);

            // Set font
            // dejavusans is a UTF-8 Unicode font, if you only need to
            // print standard ASCII chars, you can use core fonts like
            // helvetica or times to reduce file size.
            $pdf->SetFont('dejavusans', '', 14, '', true);

            // set text shadow effect
            $pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

            // Set some content to print
            $html ='
            <br/><br/>
            <span style="text-align:center;"><h2>Grant Application<h2/><h5>'.$ga->getGrant()->getTitle().'<h5/></span>
            <span style="text-align:center;"><h5><h5/></span>
           
            <table style="border-collapse:collapse;border-color:#ccc;border-spacing:0" class="tg">
            <thead>
            <tr style="line-height: 34px;">
            <th style="background-color:#f0f0f0;border-color:#ccc;border-style:solid;border-width:1px;color:#333;font-family:Arial, sans-serif;font-size:10px;font-weight:bold;overflow:hidden;padding:9px 19px;text-align:left;vertical-align:middle;word-break:normal">
            Administrator
            </th>
            <th style="background-color:#f0f0f0;border-color:#ccc;border-style:solid;border-width:1px;color:#333;font-family:Arial, sans-serif;font-size:10px;font-weight:normal;overflow:hidden;padding:9px 19px;text-align:left;vertical-align:middle;word-break:normal">
            '.$ga->getGrant()->getAdministrator()->getFirstName().' '.$ga->getGrant()->getAdministrator()->getLastName().'
            </th>
            </tr>
            </thead>
            <tbody>
            <tr style="line-height: 34px;">
            <td style="background-color:#fff;border-color:#c0c0c0;border-style:solid;border-width:1px;color:#333;font-family:Arial, sans-serif;font-size:10px;font-weight:bold;overflow:hidden;padding:9px 19px;text-align:left;vertical-align:middle;word-break:normal">
            Dealine for Applications
            </td>
            <td style="background-color:#fff;border-color:#ccc;border-style:solid;border-width:1px;color:#333;font-family:Arial, sans-serif;font-size:10px;font-weight:normal;overflow:hidden;padding:9px 19px;text-align:left;vertical-align:middle;word-break:normal">
            '.$ga->getCreatedAt()->format('t-m-Y').'
            </td>
            </tr>
            <tr style="line-height: 34px;">
            <td style="background-color:#f0f0f0;border-color:#c0c0c0;border-style:solid;border-width:1px;color:#333;font-family:Arial, sans-serif;font-size:10px;font-weight:bold;overflow:hidden;padding:9px 19px;text-align:left;vertical-align:middle;word-break:normal">
            Language
            </td>
            <td style="background-color:#f0f0f0;border-color:#ccc;border-style:solid;border-width:1px;color:#333;font-family:Arial, sans-serif;font-size:10px;font-weight:normal;overflow:hidden;padding:9px 19px;text-align:left;vertical-align:middle;word-break:normal">
            '.$ga->getGrant()->getLanguage().'
            </td>
            </tr>
            <tr style="line-height: 34px;">
            <td style="background-color:#fff;border-color:#c0c0c0;border-style:solid;border-width:1px;color:#333;font-family:Arial, sans-serif;font-size:10px;font-weight:bold;overflow:hidden;padding:9px 19px;text-align:left;vertical-align:middle;word-break:normal">
            Ambassador
            </td>
            <td style="background-color:#fff;border-color:#ccc;border-style:solid;border-width:1px;color:#333;font-family:Arial, sans-serif;font-size:10px;font-weight:normal;overflow:hidden;padding:9px 19px;text-align:left;vertical-align:middle;word-break:normal">
            '.$ga->getAmbassador()->getFirstName().' '.$ga->getAmbassador()->getLastName().'
            </td>
            </tr>
            <tr style="line-height: 34px;">
            <td style="background-color:#f0f0f0;border-color:#c0c0c0;border-style:solid;border-width:1px;color:#333;font-family:Arial, sans-serif;font-size:10px;font-weight:bold;overflow:hidden;padding:9px 19px;text-align:left;vertical-align:middle;word-break:normal">
            Success Ambassador Certificate Code
            </td>
            <td style="background-color:#f0f0f0;border-color:#ccc;border-style:solid;border-width:1px;color:#333;font-family:Arial, sans-serif;font-size:10px;font-weight:normal;overflow:hidden;padding:9px 19px;text-align:left;vertical-align:middle;word-break:normal">
            '.$ga->getCode().'
            </td>
            </tr>
            <tr style="line-height: 34px;">
            <td style="background-color:#fff;border-color:#ccc;border-style:solid;border-width:1px;color:#333;font-family:Arial, sans-serif;font-size:10px;font-weight:bold;overflow:hidden;padding:9px 19px;text-align:left;vertical-align:middle;word-break:normal">
            No. Participants Trained
            </td>
            <td style="background-color:#fff;border-color:#ccc;border-style:solid;border-width:1px;color:#333;font-family:Arial, sans-serif;font-size:10px;font-weight:normal;overflow:hidden;padding:9px 19px;text-align:left;vertical-align:middle;word-break:normal">
            '.$ga->getNumber().'
            </td>
            </tr>
            <tr style="line-height: 34px;">
            <td style="background-color:#f0f0f0;border-color:#ccc;border-style:solid;border-width:1px;color:#333;font-family:Arial, sans-serif;font-size:10px;font-weight:bold;overflow:hidden;padding:9px 19px;text-align:left;vertical-align:middle;word-break:normal">
            Total Amount Sought from Interweave
            </td>
            <td style="background-color:#f0f0f0;border-color:#ccc;border-style:solid;border-width:1px;color:#333;font-family:Arial, sans-serif;font-size:10px;font-weight:normal;overflow:hidden;padding:9px 19px;text-align:left;vertical-align:middle;word-break:normal">
            '.$ga->getQuestion6().'
            </td>
            </tr>
            </tbody>
            </table>
            ';

            // Print text using writeHTMLCell()
            $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

            // Set some content to print
            $html2 ='
            <table style="border-collapse:collapse;border-color:#ccc;border-spacing:0" class="tg">

            <tbody>
            <tr style="line-height: 20px;">
            <td style="background-color:#fff;border-color:#c0c0c0;border-style:solid;border-width:0px;color:#333;font-family:Arial, sans-serif;font-size:10px;font-weight:bold;overflow:hidden;padding:9px 19px;text-align:left;vertical-align:middle;word-break:normal">
            </td>
            </tr>
            <tr style="line-height: 20px;">
            <td style="background-color:#fff;border-color:#fff;border-style:solid;border-width:0px;color:#333;font-family:Arial, sans-serif;font-size:10px;font-weight:bold;overflow:hidden;padding:9px 19px;text-align:left;vertical-align:middle;word-break:normal">
            In the space below, please give a brief history of your time as a Success Ambassador (tell us about the MBS groups you have facilitated so far including the number of participants and graduates, previous grants received from Interweave, etc.) :
            </td>
            </tr>
            <tr style="line-height: 34px;">
            <td style="background-color:#fff;border-color:#fff;border-style:solid;border-width:0px;color:#333;font-family:Arial, sans-serif;font-size:10px;font-weight:normal;overflow:hidden;padding:9px 19px;text-align:left;vertical-align:middle;word-break:normal">
            '.$ga->getQuestion1().'
            </td>
            </tr>
            <tr style="line-height: 20px;">
            <td style="background-color:#f0f0f0;border-color:#f0f0fa;border-style:solid;border-width:0px;color:#333;font-family:Arial, sans-serif;font-size:10px;font-weight:bold;overflow:hidden;padding:9px 19px;text-align:left;vertical-align:middle;word-break:normal">
            </td>
            </tr>
            <tr style="line-height: 20px;">
            <td style="background-color:#f0f0f0;border-color:#f0f0f0;border-style:solid;border-width:0px;color:#333;font-family:Arial, sans-serif;font-size:10px;font-weight:bold;overflow:hidden;padding:9px 19px;text-align:left;vertical-align:middle;word-break:normal">
            In the space below, please explain your need for Interweave’s Scholarship Grant, (including a summary of your efforts to find funding from the sources listed in the introductory letter):
            </td>
            </tr>
            <tr style="line-height: 34px;">
            <td style="background-color:#f0f0f0;border-color:#fff;border-style:solid;border-width:0px;color:#333;font-family:Arial, sans-serif;font-size:10px;font-weight:normal;overflow:hidden;padding:9px 19px;text-align:left;vertical-align:middle;word-break:normal">
            '.$ga->getQuestion2().'
            </td>
            </tr>
            <tr style="line-height: 20px;">
            <td style="background-color:#fff;border-color:#c0c0c0;border-style:solid;border-width:0px;color:#333;font-family:Arial, sans-serif;font-size:10px;font-weight:bold;overflow:hidden;padding:9px 19px;text-align:left;vertical-align:middle;word-break:normal">
            </td>
            </tr>
            <tr style="line-height: 20px;">
            <td style="background-color:#fff;border-color:#fff;border-style:solid;border-width:0px;color:#333;font-family:Arial, sans-serif;font-size:10px;font-weight:bold;overflow:hidden;padding:9px 19px;text-align:left;vertical-align:middle;word-break:normal">
            How much will participants contribute for their own participation? :
            </td>
            </tr>
            <tr style="line-height: 34px;">
            <td style="background-color:#fff;border-color:#fff;border-style:solid;border-width:0px;color:#333;font-family:Arial, sans-serif;font-size:10px;font-weight:normal;overflow:hidden;padding:9px 19px;text-align:left;vertical-align:middle;word-break:normal">
            '.$ga->getQuestion4().'
            </td>
            </tr>
            <tr style="line-height: 20px;">
            <td style="background-color:#f0f0f0;border-color:#f0f0fa;border-style:solid;border-width:0px;color:#333;font-family:Arial, sans-serif;font-size:10px;font-weight:bold;overflow:hidden;padding:9px 19px;text-align:left;vertical-align:middle;word-break:normal">
            </td>
            </tr>
            <tr style="line-height: 20px;">
            <td style="background-color:#f0f0f0;border-color:#f0f0f0;border-style:solid;border-width:0px;color:#333;font-family:Arial, sans-serif;font-size:10px;font-weight:bold;overflow:hidden;padding:9px 19px;text-align:left;vertical-align:middle;word-break:normal">
            What other sources of funding do you have and how much will they contribute? (Partnerships or contracts):
            </td>
            </tr>
            <tr style="line-height: 34px;">
            <td style="background-color:#f0f0f0;border-color:#fff;border-style:solid;border-width:0px;color:#333;font-family:Arial, sans-serif;font-size:10px;font-weight:normal;overflow:hidden;padding:9px 19px;text-align:left;vertical-align:middle;word-break:normal">
            '.$ga->getQuestion5().'
            </td>
            </tr>
            </tbody>
            </table>
            <br>
            ';

            // Print text using writeHTMLCell()
            $pdf->writeHTMLCell(0, 0, '', '', $html2, 0, 1, 0, true, '', true);

            $html3 = '';

            if($ga->getFile() != "undefined"){
                $html3 = $html3. '<p><a  target="_blank" href="https://api.interweavesolutions.org/web/file/'.$ga->getFile().'" style="text-decoration:none; color:black; font-size:10px;" >Download Video 1</a></p>';
            }

            if($ga->getFile2() != "undefined"){
                $html3 = $html3. '<p><a target="_blank" href="https://api.interweavesolutions.org/web/file/'.$ga->getFile2().'" style="text-decoration:none; color:black; font-size:10px;" >Download Video 2</a></p>';
            }

            // Print text using writeHTMLCell()
            $pdf->writeHTMLCell(0, 0, '', '', $html3, 0, 1, 0, true, '', true);
      
      
            //Close and output PDF document
            $pdf->Output('grant_application_'.$ga->getAmbassador()->getFirstName().' '.$ga->getAmbassador()->getLastName().'.pdf', 'I');
      
 
        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to get the current Grant- Error: {$ex->getMessage()}";
        }
 
      
    }
   
 
}