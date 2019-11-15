<?php
// src/Controller/IssueController.php
namespace App\Controller;

use App\Form\IssueType;
use App\Entity\Issue;
use App\Entity\Project;
use App\Repository\IssueRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
class IssueController extends AbstractController {
    /**
     * @Route("/project/{id_project}/new_issue", name = "createIssue")
     */
    public function viewCreationIssue(Request $request, ProjectRepository $projectRepository, $id_project) : Response
    {
        $form = $this->createForm(IssueType::class);
        $form->handleRequest($request);

        $error = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $name = $data['name'];
            $description= $data['description'];
            $difficulty=$data['difficulty'];
            $priority=$data['priority'];
            $status=$data['status'];
            $myProject = $projectRepository->findOneBy([
                'id' => $id_project
            ]);

            $issue = new Issue($name,$description,$difficulty,$priority,$status,$myProject);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($issue);
            $entityManager->flush();

            if($error == null){
                return $this->redirectToRoute('dashboard');
            }
        }

        return $this->render('issue/issue_form.html.twig', ['error'=> $error,
                                                            "form"=> $form->createView(),
                                                            'user' => $this->getUser()] );

    }

    /**
     * @Route("/project/{id_project}/issues", name = "issuesList", methods = {"GET"})
     */
    public function viewIssues(Request $request, ProjectRepository $projectRepository, $id_project) {
        $member = $this->getUser();
    
        $myProject = $projectRepository->findOneBy([
            'id' => $id_project
        ]);

        $myIssues = $myProject->getIssues();

        return $this->render('issue/issue_list.html.twig', ["myProject"=> $myProject, 
                                                            "myIssues"=> $myIssues,
                                                            'user' => $this->getUser()]);
    }





    /**
     * @Route("/project/{myProject_id}/issue/{issue_id}/edit", name="editIssue")
     */
    public function editIssue(Request $request, EntityManagerInterface $entityManager,ProjectRepository $projectRepository,IssueRepository $issueRepository, $issue_id,$myProject_id): Response
    {
        $issue=$issueRepository->find($issue_id);
        $form = $this->createForm(IssueType::class);
        $form->handleRequest($request);

        $error = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $name = $data['name'];
            $description = $data['description'];
            $difficulty = $data['difficulty'];
            $priority = $data['priority'];
            $status = $data['status'];
            $project = $projectRepository->find($myProject_id);
            $issue->setName($name);
            $issue->setDescription($description);
            $issue->setDifficulty($difficulty);
            $issue->setPriority($priority);
            $issue->setStatus($status);
            $issue->setProject($project);
            $entityManager->persist($issue);
            $entityManager->flush();
            if ($error == null) {
                return $this->redirectToRoute('dashboard');
            }
        }
        return $this->render('issue/edit.html.twig', [
            'error' => $error,
            'form' => $form->createView(),
            'user' => $this->getUser()
        ]);
    }
/**
 * @Route("/project/{myProject_id}/issue/{issue_id}/delete", name="deleteIssue")
 */
public function deleteIssue(Request $request, IssueRepository $issue_Repository,EntityManagerInterface $entityManager,$issue_id)
{
    $issue = $issue_Repository->find($issue_id);
    if (!$issue) {
        throw $this->createNotFoundException('aucun issue existe avec cet id ' . $issue_id);
    }
    $entityManager->remove($issue);
    $entityManager->flush();
    return $this->redirectToRoute('dashboard');
}}