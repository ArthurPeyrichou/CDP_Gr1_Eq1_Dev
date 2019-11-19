<?php
// src/Controller/IssueController.php
namespace App\Controller;

use App\Form\IssueType;
use App\Entity\Issue;
use App\Repository\IssueRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;

class IssueController extends AbstractController {

    /**
     * @Route("/project/{id_project}/issues/new", name="createIssue")
     */
    public function viewCreationIssue(Request $request, ProjectRepository $projectRepository,
                                      EntityManagerInterface $entityManager, $id_project) : Response
    {
        $form = $this->createForm(IssueType::class);
        $form->handleRequest($request);
        $project = $projectRepository->find( $id_project);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $name = $data['name'];
            $description= $data['description'];
            $difficulty=$data['difficulty'];
            $priority=$data['priority'];
            $status=$data['status'];

            $issue = new Issue($name, $description, $difficulty, $priority, $status, $project);

            $entityManager->persist($issue);
            $entityManager->flush();
            return $this->redirectToRoute('issuesList', [
                'id_project' => $project->getId()
            ]);
        }

        return $this->render('issue/issue_form.html.twig', [
            'form'=> $form->createView(),
            'user' => $this->getUser(),
            'project' => $project
        ]);

    }

    /**
     * @Route("/project/{id_project}/issues", name="issuesList", methods={"GET"})
     */
    public function viewIssues(Request $request, ProjectRepository $projectRepository, $id_project) {
        $member = $this->getUser();

        $project = $projectRepository->findOneBy([
            'id' => $id_project
        ]);

        $issues = $project->getIssues();

        return $this->render('issue/issue_list.html.twig', [
            'project'=> $project,
            'issues' => $issues,
            'user' => $member
        ]);
    }

    /**
     * @Route("/project/{id_project}/issues/{id_issue}/edit", name="editIssue")
     */
    public function editIssue(Request $request, EntityManagerInterface $entityManager,
                              ProjectRepository $projectRepository, IssueRepository $issueRepository,
                              $id_issue, $id_project): Response
    {
        $issue = $issueRepository->find($id_issue);
        $form = $this->createForm(IssueType::class, $issue);
        $form->handleRequest($request);
        $project = $projectRepository->find($id_project);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($issue);
            $entityManager->flush();
            return $this->redirectToRoute('issuesList', [
                'id_project' => $project->getId()
            ]);
        }

        return $this->render('issue/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $this->getUser(),
            'project' => $project
        ]);
    }

    /**
     * @Route("/project/{id_project}/issues/{id_issue}/delete", name="deleteIssue")
     */
    public function deleteIssue(Request $request, IssueRepository $issue_Repository,
                                EntityManagerInterface $entityManager, $issue_id)
    {
        $issue = $issue_Repository->find($issue_id);
        if (!$issue) {
            throw $this->createNotFoundException("Aucune issue n'existe avec l'id {$issue_id}");
        }
        $entityManager->remove($issue);
        $entityManager->flush();
        return $this->redirectToRoute('dashboard');
    }
}
