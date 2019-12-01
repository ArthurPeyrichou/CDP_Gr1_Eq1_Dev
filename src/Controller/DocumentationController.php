<?php
// src/Controller/DocumentationController.php
namespace App\Controller;


use App\Entity\Documentation;
use App\Entity\Project;
use App\Form\DocumentationType;
use App\Repository\DocumentationRepository;
use App\Service\NotificationService;
use Doctrine\Common\Collections\ArrayCollection;
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

    public function __construct(NotificationService $notifications, ProjectRepository $projectRepository,
                                DocumentationRepository $documentationRepository)
    {
        $this->notifications = $notifications;
        $this->projectRepository = $projectRepository;
        $this->documentationRepository=$documentationRepository;
    }

    /**
     * @Route("/project/{id_project}/documentation/new ", name = "createRessourceDoc")
     */
    public function viewCreationDocumentation(Request $request, EntityManagerInterface $entityManager, $id_project): Response
    {
        $project = $this->projectRepository->find($id_project);

        $form = $this->createForm(DocumentationType::class, [], [
            DocumentationType::PROJECT => $project
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $data = $form->getData();
                $name = $data['name'];
                $description= $data['description'];
                $link=$data['link'];
                $documentation=new Documentation($name,$description,$link,$project);
                $entityManager->persist($documentation);
                $entityManager->flush();
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
    public function editRelssourceDoc(Request $request, EntityManagerInterface $entityManager,$id_documentation, $id_project): Response
    {
        $project = $this->projectRepository->find($id_project);
        $documentation = $this->documentationRepository->find($id_documentation);
        $form = $this->createForm(DocumentationType::class, $documentation, [
            DocumentationType::PROJECT => $project
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($documentation);
                $entityManager->flush();
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
    public function deleteRessourceDoc(Request $request, EntityManagerInterface $entityManager, $id_project, $id_documentation)
    {
        $documentation = $this->documentationRepository->find($id_documentation);
        if (!$documentation) {
            $this->notifications->addError("Aucune ressource de documentation n'existe avec l'id {$id_documentation}");
        } else {
            try {
                $entityManager->remove($documentation);
                $entityManager->flush();
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