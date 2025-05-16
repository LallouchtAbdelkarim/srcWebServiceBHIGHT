<?php

namespace App\Controller\Users;

use App\Entity\Message;
use App\Entity\DetailMessage;
use App\Entity\Utilisateurs;
use App\Service\MessageService;
use App\Service\AuthService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\DBAL\Connection;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

use Symfony\Component\Serializer\SerializerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

#[Route('/API') ]

class MessageController extends AbstractController
{
    private $MessageService;
    private $entityManager;
    private $JWTManager;
    private $AuthService;
    private $conn;
    public function __construct(Connection $connection,  
    MessageService $MessageService
    ,JWTEncoderInterface $JWTManager,
    EntityManagerInterface $entityManager,
    AuthService $AuthService,
    )
    {
         $this->JWTManager = $JWTManager;
         $this->MessageService = $MessageService;
         $this->entityManager = $entityManager;
         $this->AuthService = $AuthService;
         $this->connection = $connection;
    }


    #[Route('/sendMessage/', name: 'sendMessage')]
    public function sendMessage(Request $request): Response
    {
        
        $codeStatut = "ERROR";
        try {
           
            //code...
            $this->AuthService->checkAuth($request);
            $senderId = $this->AuthService->returnUserId($request);
            $data = json_decode($request->getContent(), true);
            // Fetch the User entities for sender and recipient
            $sender = $this->entityManager->getRepository(Utilisateurs::class)->find($senderId);
            $recipient =  $this->entityManager->getRepository(Utilisateurs::class)->find($data['recipient']);

            if (!$sender || !$recipient) {
                $codeStatut="ERROR-USER"; // TO ADD
            }
            else
            {

                // Create a new Message entity
                $message = new Message();
                $message->setContenu($data['message']);
                $message->setDateCreation(new \DateTime());
                $message->setCreateur($sender);

                // Create a new DetailMessage entity
                $detailMessage = new DetailMessage();
                $detailMessage->setMessage($message);
                $detailMessage->setExpediteur($sender);
                $detailMessage->setDistinataire($recipient);
                $detailMessage->setDateEnvoi(new \DateTime());
                $detailMessage->setIsRecu(false); // Message is sent but not received
                $detailMessage->setIsLu(false); // Message is sent but not read
                $detailMessage->setIsDeleted(false); // Message is not deleted

                // Persist the entities to the database
                $this->entityManager->persist($message);
                $this->entityManager->persist($detailMessage);
                $this->entityManager->flush();
                $codeStatut="OK";
            }

           

      
        } catch (\Exception $th) {
            //throw $th;
            $codeStatut=$th->getMessage();
        }
        
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);    
    }


    #[Route('/messageRecu/', name: 'messageRecu')]
    public function getAllMessagesAndMarkAsRecu(Request $request): Response
    {

        $codeStatut = "ERROR";
        try {

            $distinataireId = $this->AuthService->returnUserId($request);
    
            // Find all messages for the specified Distinataire that are not marked as read
            $messages = $this->entityManager
                ->getRepository(DetailMessage::class)
                ->findBy(['Distinataire' => 1, 'isRecu' => false]);
    
            // Mark each message as read
            foreach ($messages as $message) {
                $message->setIsRecu(true);
                $message->setDateRecu(new \DateTime());
                $this->entityManager->persist($message);
            }
    
            // Persist the changes to the database
            $this->entityManager->flush();    
            $codeStatut="OK";


        }
        catch (\Exception $th) {
            //throw $th;
            $codeStatut=$th->getMessage();
        }
        
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);    
  
    }


    
    #[Route('/messageLu/', name: 'messageLu')]
    public function markMessageAsLu(Request $request): Response
    {
        $codeStatut = "ERROR"; 
        try {

            $this->AuthService->checkAuth($request);
            $data = json_decode($request->getContent(), true);
            $senderId = $this->AuthService->returnUserId($request);
            // // Fetch the User entities for sender and recipient
            $recipient =  $this->entityManager->getRepository(Utilisateurs::class)->find($data);

    
            $messages = $this->entityManager
                ->getRepository(DetailMessage::class)
                ->findBy(['Distinataire' => 1,'Expediteur' =>$recipient , 'isLu' => false]);
    
            // Mark each message as read
            foreach ($messages as $message) {
                $message->setIsLu(true);
                $message->setDateLu(new \DateTime());
                $this->entityManager->persist($message);
            }
    
            // Persist the changes to the database
            $this->entityManager->flush();    
            $codeStatut="OK";


        }
        catch (\Exception $th) {
            //throw $th;
            $codeStatut=$th->getMessage();
        }
        
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);    
    }


    #[Route('/getMessageNotLu/', name: 'getMessageNotLu')]
    public function getMessageNotLu(Request $request): Response
    {
        $codeStatut = "ERROR"; 
        $messages = [];
        try {

            $distinataireId = $this->AuthService->returnUserId($request);

            $query = $this->entityManager->createQueryBuilder()
            ->select('COUNT(dm.id) as messageCount')
            ->from(DetailMessage::class, 'dm')
            ->where('dm.isLu = :isLu')
            ->andWhere('dm.Distinataire = :destinataireId')
            ->setParameter('isLu', 0)
            ->setParameter('destinataireId', 1) // Replace with the actual variable value
            ->getQuery();
        
            $messages = $query->getOneOrNullResult();
                                    
            $codeStatut="OK";

        }
        catch (\Exception $th) {
            //throw $th;
            $codeStatut=$th->getMessage();
        }

        $respObjects["data"] = $messages;
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);    
    }

    
    

    #[Route('/getConversation/', name: 'getConversation')]
    public function getConversation(Request $request): Response
    {
        $codeStatut = "ERROR"; 
        $detailMessages = [];
        try {

   
            //code...
            $this->AuthService->checkAuth($request);
            $data = json_decode($request->getContent(), true);
            $senderId = $this->AuthService->returnUserId($request);
            // // Fetch the User entities for sender and recipient
            $recipient =  $this->entityManager->getRepository(Utilisateurs::class)->find($data);

            $query = $this->entityManager->createQueryBuilder()
                ->select('dm.id AS id, m.Contenu AS messageContent, dm.DateEnvoi AS date')
                ->addSelect(
                    'CASE WHEN dm.Distinataire = :recipient THEN :you ELSE :user END AS senderType'
                )
                ->from(DetailMessage::class, 'dm')
                ->join('dm.Message', 'm')
                ->where('(dm.Distinataire = :recipient AND dm.Expediteur = :sender) OR (dm.Expediteur = :recipient AND dm.Distinataire = :sender)')
                ->setParameter('recipient', $recipient)
                ->setParameter('sender', $senderId)
                ->setParameter('you', 'you') // Value for 'you'
                ->setParameter('user', 'user') // Value for 'user'
                ->orderBy('dm.id', 'ASC')
                ->getQuery();

            $detailMessages = $query->getResult();

                                                
            $codeStatut="OK";

        }
        catch (\Exception $th) {
            //throw $th;
            $codeStatut=$th->getMessage();
        }

        $respObjects["data"] = $detailMessages ;
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);    
    }


    #[Route('/getLastConversation/', name: 'getLastConversation')]
    public function getLastConversation(Request $request): Response
    {
        $codeStatut = "ERROR"; 
        $lastMessages = [];
        try {

   
            //code...
            $this->AuthService->checkAuth($request);
            $senderId = $this->AuthService->returnUserId($request);
            // Fetch the User entities for sender and recipient


            $sql = "
            SELECT
                dm.id AS id,
                m.Contenu AS messageContent,
                dm.distinataire_id AS recipient,
                dm.expediteur_id AS sender,
                dm.date_envoi AS date_envoi,
                dm.is_recu AS ifRecu,
                dm.is_lu As ifLu,
                CASE WHEN sender_user.id = :recipient THEN 'You' ELSE sender_user.nom END AS sender_name,
                CASE WHEN recipient_user.id = :recipient THEN 'You' ELSE recipient_user.nom END AS recipient_name,
                CASE WHEN dm.expediteur_id = :recipient THEN CONCAT(recipient_user.nom, ' ', recipient_user.prenom) ELSE CONCAT(sender_user.nom, ' ', sender_user.prenom) END AS user_name,
                CASE WHEN sender_user.id = :recipient THEN recipient_user.id ELSE sender_user.id END AS user_id,
                (SELECT COUNT(dm1.id) FROM detail_message dm1 WHERE (dm1.distinataire_id = :recipient AND dm1.expediteur_id = user_id) AND dm1.is_lu = 0) AS CountMessage
            FROM
                detail_message dm
            JOIN
                Message m ON dm.Message_id = m.id
            LEFT JOIN
                utilisateurs sender_user ON dm.expediteur_id = sender_user.id
            LEFT JOIN
                utilisateurs recipient_user ON dm.distinataire_id = recipient_user.id
            GROUP BY
                id, messageContent, recipient, sender, date_envoi, sender_name, recipient_name, user_name, user_id
            ORDER BY
                dm.id DESC;
        
            ";

            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue('recipient', $senderId);
            $stmt = $stmt->executeQuery();

            $messages = $stmt->fetchAll();

            $lastMessages = [];
            $groupedMessages = [];

            foreach ($messages as $message) {
                $otherUserId = ($message['recipient'] == $senderId) ? $message['sender'] : $message['recipient'];
                
                if (!isset($groupedMessages[$otherUserId])) {
                    $groupedMessages[$otherUserId] = $message;
                }
            }

            $lastMessages = array_values($groupedMessages);

                                                
            $codeStatut="OK";

        }
        catch (\Exception $th) {
            //throw $th;
            $codeStatut=$th->getMessage();
        }

        $respObjects["data"] = $lastMessages;
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);    
    }


    #[Route('/getListUsers/', name: 'getListUsers')]
    public function getListUsers(Request $request): Response
    {
        $codeStatut = "ERROR";
        $users = [];

        try {
            $distinataireId = $this->AuthService->returnUserId($request);

            // Use a correct SQL query to select users except the authenticated user
            $sql = "SELECT id,nom,prenom FROM Utilisateurs WHERE id != :currentUserId";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue('currentUserId', 1);
            $stmt = $stmt->executeQuery();

            $users = $stmt->fetchAllAssociative(); // Use fetchAllAssociative to fetch data as an associative array

            $codeStatut = "OK";
        } catch (\Exception $th) {
            $codeStatut = $th->getMessage();
        }

        $respObjects["data"] = $users;
        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);

        return $this->json($respObjects);
    }


    #[Route('/sendMessageAll/', name: 'sendMessageAll')]
    public function sendMessageAll(Request $request): Response
    {
        $codeStatut = "ERROR";
        try {
            // ...
            $this->AuthService->checkAuth($request);
            $senderId = $this->AuthService->returnUserId($request);
            $data = json_decode($request->getContent(), true);

            // Fetch the User entity for the sender
            $sender = $this->entityManager->getRepository(Utilisateurs::class)->find($senderId);

            if (!$sender) {
                $codeStatut = "ERROR-USER"; // TO ADD
            } else {
                // Loop through the list of recipients
                // Create a new Message entity
                $message = new Message();
                $message->setContenu($data['message']);
                $message->setDateCreation(new \DateTime());
                $message->setCreateur($sender);
                
                foreach ($data['recipients'] as $recipientId) {
                    $recipient = $this->entityManager->getRepository(Utilisateurs::class)->find($recipientId);

                    if ($recipient) {

                        // Create a new DetailMessage entity
                        $detailMessage = new DetailMessage();
                        $detailMessage->setMessage($message);
                        $detailMessage->setExpediteur($sender);
                        $detailMessage->setDistinataire($recipient);
                        $detailMessage->setDateEnvoi(new \DateTime());
                        $detailMessage->setIsRecu(false); // Message is sent but not received
                        $detailMessage->setIsLu(false); // Message is sent but not read
                        $detailMessage->setIsDeleted(false); // Message is not deleted

                        $this->entityManager->persist($detailMessage);
                    }
                }

                // Persist the entities to the database
                $this->entityManager->persist($message);    

                // Flush the changes to the database
                $this->entityManager->flush();

                $codeStatut = "OK";
            }
        } catch (\Exception $th) {
            $codeStatut = $th->getLine();
        }

        $respObjects["codeStatut"] = $codeStatut;
        $respObjects["message"] = $this->MessageService->checkMessage($codeStatut);
        return $this->json($respObjects);
    }


    #[Route('/testSendEmail/')]
    public function testSendEmail(MailerInterface $mailer)
    {

        // Create an email
        $email = (new Email())
            ->from('abdelkarimlalloucht@gmail.com')
            ->to('backup.afriqueelevation@gmail.com')
            ->subject('dsds')
            ->text('oweeeeeeeey.');

        // Send the email
        try {
            $mailer->send($email);
            $respObjects = 'Email sent successfully';
        } catch (TransportExceptionInterface $e) {
            $respObjects = 'Email could not be sent: ' . $e->getMessage();
        }
        return $this->json($respObjects);    

    }
}