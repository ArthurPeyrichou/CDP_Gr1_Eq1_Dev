<?php
// src/Controller/DocumentationController.php
namespace App\Controller;


use App\Entity\Documentation;
use App\Form\DocumentationType;
use App\Repository\DocumentationRepository;
use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;

class DocumentationController extends AbstractController
{
    private $notifications;
    private $projectRepository;
    private $documentationRepository;
    private $entityManager;

    public function __construct(NotificationService $notifications, ProjectRepository $projectRepository,
                                DocumentationRepository $documentationRepository, EntityManagerInterface $entityManager)
    {
        $this->notifications = $notifications;
        $this->projectRepository = $projectRepository;
        $this->documentationRepository=$documentationRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/project/{id_project}/documentation/new ", name = "createRessourceDoc")
     */
    public function viewCreationDocumentation(Request $request, $id_project): Response
    {
        $project = $this->projectRepository->find($id_project);

        $form = $this->createForm(DocumentationType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $data = $form->getData();
                $name = $data['name'];
                $description= $data['description'];
                $link=$data['link'];
                $documentation=new Documentation($name,$description,$link,$project);
                $this->entityManager->persist($documentation);
                $this->entityManager->flush();
                $this->notifications->addSuccess("La documentation a été créer avec succés.");
            } catch(\Exception $e) {
                $this->notifications->addError($e->getMessage());
            }
            return $this->redirectToRoute('ressourcesDocList', [
                'id_project' => $id_project
            ]);
        }
        return $this->render('documentation/documentation_form.html.twig', [
            'form'=> $form->createView(),
            'user' => $this->getUser(),
            'project' => $project
        ]);
    }
    /**
     * @Route("/project/{id_project}/documentation ", name="ressourcesDocList", methods={"GET"})
     */
    public function viewDocumentations(Request $request, $id_project) {
        $project = $this->projectRepository->find($id_project);
        $ressourcesDoc = $project->getRessourcesDoc();
        return $this->render('documentation/documentation_list.html.twig', [
            'project'=> $project,
            'ressourcesDoc' => $ressourcesDoc,
            'user' => $this->getUser()
        ]);
    }



    /**
     * @Route("/project/{id_project}/documentation/{id_documentation}/edit", name="editRessourceDoc")
     */
    public function editRelssourceDoc(Request $request, $id_documentation, $id_project): Response
    {
        $project = $this->projectRepository->find($id_project);
        $documentation = $this->documentationRepository->find($id_documentation);
        $form = $this->createForm(DocumentationType::class, $documentation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->entityManager->persist($documentation);
                $this->entityManager->flush();
                $this->notifications->addSuccess("Ressource de documentation  {$documentation->getName()} éditée avec succés.");
            } catch(\Exception $e) {
                $this->notifications->addError($e->getMessage());
            }
            return $this->redirectToRoute('ressourcesDocList', [
                'id_project' => $id_project
            ]);
        }

        return $this->render('documentation/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $this->getUser(),
            'project' => $project
        ]);
    }


    /**
     * @Route("/project/{id_project}/documentation/{id_documentation}/delete", name="deleteRessourceDoc")
     */
    public function deleteRessourceDoc(Request $request, $id_project, $id_documentation)
    {
        $documentation = $this->documentationRepository->find($id_documentation);
        if (!$documentation) {
            $this->notifications->addError("Aucune ressource de documentation n'existe avec l'id {$id_documentation}");
        } else {
            try {
                $this->entityManager->remove($documentation);
                $this->entityManager->flush();
                $this->notifications->addSuccess("ressource de documentation {$documentation->getName()} est supprimée avec succés.");
            } catch(\Exception $e) {
                $this->notifications->addError($e->getMessage());
            }
        }
        return $this->redirectToRoute('ressourcesDocList', [
            'id_project' => $id_project
        ]);
    }


}
