<?php
// src/Controller/ProjectController.php
namespace App\Controller;

use App\Entity\Invitation;
use App\Entity\Member;
use App\Entity\Project;
use App\Entity\PlanningPoker;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use App\Repository\InvitationRepository;
use App\Repository\MemberRepository;
use App\Repository\PlanningPokerRepository;
use App\Repository\IssueRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController {

    private $notifications;

    public function __construct(NotificationService $notifications)
    {
        $this->notifications = $notifications;
    }

    /**
     * @Route("/project/new", name="createProject")
     */
    public function createProject(Request $request, EntityManagerInterface $entityManager) : Response
    {
        $form = $this->createForm(ProjectType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $owner = $this->getUser();
            $name = $data['name'];
            $description= $data['description'];
            $date= new \DateTime('now');
            $project = new Project($owner, $name, $description, $date);

            try {
                $entityManager->persist($project);
                $entityManager->flush();
                $this->notifications->addSuccess("Création du projet {$project->getName()} réussie");
                return $this->redirectToRoute('projectDetails', [
                    'id' => $project->getId()
                ]);
            }catch (\Exception $e) {
                $this->notifications->addError($e->getMessage());
            }
        }

        return $this->render('project/creation.html.twig', [
            'form' => $form->createView(),
            'user' => $this->getUser()
        ]);
    }

    /**
     * @Route("/project/{id}", name="projectDetails", methods={"GET"})
     */
    public function viewProject(ProjectRepository $projectRepository, PlanningPokerRepository $planPokerRepository,
                                InvitationRepository $invitationRepository, EntityManagerInterface $entityManager, $id)
    : Response
    {
        $project = $projectRepository->findOneBy([
            'id' => intval($id)
        ]);
        /**@var $user Member*/
        $user = $this->getUser();
        $status = null;

        $invitation = $invitationRepository->findOneBy([
            'project' => $project,
            'member' => $user
        ]);
        $status = $this->getMemberStatus($user, $project, $invitation);

        $today = new \DateTime();
        foreach($project->getIssues() as $issue){
            foreach($planPokerRepository->getPlanningPokerNotDoneByIssue($issue) as $planningPoker){
                if(date_diff($planningPoker->getCreationDate(), $today)->format('%d') > PlanningPoker::TIME) {
                    $entityManager->remove($planningPoker);
                }
            }
            if($planPokerRepository->isPlanningPokerDoneByIssue($issue) ) {
                $cpt = 0;
                $amount = 0;
                foreach($planPokerRepository->getPlanningPokerByIssue($issue) as $planningPokerDone) {
                    ++$cpt;
                    $amount += $planningPokerDone->getValue();
                    $entityManager->remove($planningPokerDone);
                }
                $issue->setDifficulty($amount / $cpt);
                $this->notifications->addSuccess("Fin du planning poker pour l'issue {$issue->getNumber()}.");
            }
        }
        $entityManager->flush();


        return $this->render('project/project_details.html.twig', [
                'status' => $status,
                'myInvitation' => $invitation,
                'project' => $project,
                'owner' => $project->getOwner(),
                'members' => $project->getMembers(),
                'user' => $user
            ]);
    }

    private function getMemberStatus(Member $member, Project $project, ?Invitation $invitation)
    : string
    {
        $status = '';
        if ($project->getOwner()->getId() == $member->getId()){
            $status = 'owner';
        }
        else if ($project->getMembers()->contains($member) ) {
            $status = 'member';
        }
        else if ($invitation) {
            $status = 'invited';
        }
        return $status;
    }

    /**
     * @Route("/project/{id}/edit", name="editProject")
     */
    public function editProject(Request $request, EntityManagerInterface $entityManager,ProjectRepository $projectRepository, $id): Response
    {
        $project =  $projectRepository->find($id);

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($project);
                $entityManager->flush();
                $this->notifications->addSuccess("Edition du projet {$project->getName()} réussie");
                return $this->redirectToRoute('projectDetails', [
                    'id' => $id
                ]);
            } catch (\Exception $e) {
                $this->notifications->addError($e->getMessage());
            }
        }

        return $this->render('project/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $this->getUser(),
            'project' => $project
        ]);
    }

    /**
     * @Route("/project/{id}/delete", name="deleteProject")
     */
    public function deleteProject(Request $request, ProjectRepository $projectRepository, EntityManagerInterface $entityManager, $id)
    {
        $project = $projectRepository->find($id);
        if (!$project) {
            $this->notifications->addError("Aucun projet n'existe avec l'id {$id}");
        }
        try {
            $entityManager->remove($project);
            $entityManager->flush();
            $this->notifications->addSuccess("Suppression du projet {$project->getName()} réussie");
            return $this->redirectToRoute('dashboard');
        } catch (\Exception $e) {
            $this->notifications->addError($e->getMessage());
        }

        return $this->redirectToRoute('projectDetails', [
            'id' => $id
        ]);
    }

    /**
     * @Route("/project/{projectId}/deleteMember/{memberId}", name="deleteMember")
     */
    public function deleteMember(ProjectRepository $projectRepository, MemberRepository $memberRepository, EntityManagerInterface $entityManager, $projectId, $memberId): Response
    {
        $user = $this->getUser();
        $member = $memberRepository->find($memberId);
        $project = $projectRepository->find($projectId);

        if (!$member) {
            $this->notifications->addError("Aucun membre n'existe avec l'id {$memberId}");
        }
        else if (!$project) {
            $this->notifications->addError("Aucun projet n'existe avec l'id {$projectId}");
        }
        else if($project->getOwner() != $user && $user->getId() != $memberId) {
            $this->notifications->addError('Vous ne pouvez pas supprimer un collaborateur d\'un projet dont vous n\'êtes pas propriétaire');
        }
        else {
            try {
                $project->removeMember($member);
                $entityManager->flush();
                if($project->getOwner() != $user) {
                    $this->notifications->addSuccess("Vous venez de quitter le projet {$project->getName()}");
                    return $this->redirectToRoute('dashboard');
                } else {
                    $this->notifications->addSuccess("{$member->getName()} a été retiré du projet avec succès");
                }
            } catch (\Exception $e) {
                $this->notifications->addError($e->getMessage());
            }
        }

        return $this->redirectToRoute('projectDetails', [
            'id' => $projectId
        ]);
    }

}
