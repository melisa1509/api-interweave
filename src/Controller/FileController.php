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
        $file = $request->files->get('file');

        try {
            $code = 200;
            $error = false;

            if (!$file->getError()) {

             
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $cvDir = $this->container->getparameter('kernel.project_dir').'/web/file'.'/' . $fileName;

                move_uploaded_file($_FILES['file']['tmp_name'],	$cvDir);
              
 
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
   
 
}