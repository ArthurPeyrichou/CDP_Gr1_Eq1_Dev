<?php
// src/Controller/ProjectController.php
namespace App\Controller;

use App\Entity\Invitation;
use App\Entity\Member;
use App\Entity\Project;
use App\Entity\PlanningPoker;
use App\Entity\Notification;
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
    private $entityManager;
    private $projectRepository;

    public function __construct(NotificationService $notifications, EntityManagerInterface $entityManager, ProjectRepository $projectRepository)
    {
        $this->notifications = $notifications;
        $this->entityManager = $entityManager;
        $this->projectRepository = $projectRepository;
    }

    /**
     * Displays and handles the project creation form.
     * @Route("/project/new", name="createProject")
     */
    public function createProject(Request $request) : Response
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
                $this->entityManager->persist($project);
                $this->entityManager->flush();
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
     * Displays the details of a specific project.
     * @Route("/project/{id}", name="projectDetails", methods={"GET"})
     */
    public function viewProject(PlanningPokerRepository $planPokerRepository, InvitationRepository $invitationRepository, $id)
    : Response
    {
        $project = $this->projectRepository->findOneBy([
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
            $cptPP = 0;
            foreach($planPokerRepository->getPlanningPokerNotDoneByIssue($issue) as $planningPoker){
                if(date_diff($planningPoker->getCreationDate(), $today)->format('%d') > PlanningPoker::TIME) {
                    $this->entityManager->remove($planningPoker);
                    $this->entityManager->flush();
                    $cptPP++;
                }
            }
            if($cptPP > 0 && $planPokerRepository->isPlanningPokerDoneByIssue($issue) ) {
                $cpt = 1;
                $amount = $issue->getDifficulty();
                foreach($planPokerRepository->getPlanningPokerByIssue($issue) as $planningPokerDone) {
                    ++$cpt;
                    $amount += $planningPokerDone->getValue();
                    $this->entityManager->remove($planningPokerDone);
                    $this->entityManager->flush();
                }
                $issue->setDifficulty($amount / $cpt);
                $entityManager->persist($issue);
                $entityManager->flush();
                $message = "Fin du planning poker pour l'issue {$issue->getNumber()}.";
                foreach($project->getMembers() as $member) {
                    if($member->getId() == $user->getId() ){
                        $this->notifications->addInfo($message);
                    } else {
                        $notif = new Notification($message);
                        $member->addNotification($notif);
                        $entityManager->persist($notif);
                        $entityManager->flush();
                    }
                }

                if($project->getOwner()->getId() == $user->getId() ){
                    $this->notifications->addInfo($message);
                } else {
                    $notif = new Notification($message);
                    $project->getOwner()->addNotification($notif);
                    $this->entityManager->persist($notif);
                    $this->entityManager->flush();
                }

            }
        }
        foreach($project->getSprints() as $sprint) {
            $sprint->setBurnDownChart();
        }
        $this->entityManager->flush();


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
     * Displays and handles the project edit form.
     * @Route("/project/{id}/edit", name="editProject")
     */
    public function editProject(Request $request, $id): Response
    {
        $project =  $this->projectRepository->find($id);

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->entityManager->persist($project);
                $this->entityManager->flush();
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
     * Handles the deletion of a project.
     * @Route("/project/{id}/delete", name="deleteProject")
     */
    public function deleteProject(Request $request, $id)
    {
        $project = $this->projectRepository->find($id);
        if (!$project) {
            $this->notifications->addError("Aucun projet n'existe avec l'id {$id}");
        }
        try {
            $this->entityManager->remove($project);
            $this->entityManager->flush();
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
     * Handles the deletion of a member from a project.
     * @Route("/project/{projectId}/deleteMember/{memberId}", name="deleteMember")
     */
    public function deleteMember(MemberRepository $memberRepository, $projectId, $memberId): Response
    {
        $user = $this->getUser();
        $member = $memberRepository->find($memberId);
        $project = $this->projectRepository->find($projectId);

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
                $this->entityManager->flush();
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
