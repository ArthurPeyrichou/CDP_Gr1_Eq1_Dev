<?php
// src/Controller/TestController.php
namespace App\Controller;

use App\Form\TestType;
use App\Entity\Test;
use App\Repository\TestRepository;
use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\RenderService;

class TestController extends AbstractController {

    private $testRepository;
    private $notifications;
    private $projectRepository;
    private $entityManager;

    public function __construct( TestRepository $testRepository, NotificationService $notifications, ProjectRepository $projectRepository, EntityManagerInterface $entityManager)
    {
        $this->testRepository = $testRepository;
        $this->notifications = $notifications;
        $this->projectRepository = $projectRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Displays and handles the test creation form.
     * @Route("/project/{id_project}/tests/new", name="createTest")
     */
    public function viewCreationTest(Request $request, $id_project) : Response
    {
        $project = $this->projectRepository->find( $id_project);
        $form = $this->createForm(TestType::class, [], [
            TestType::PROJECT => $project
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $data = $form->getData();
                $name = $data['name'];
                $description= $data['description'];
                $state=$data['state'];
                $issue=$data['issue'];
                $test = new Test($project, $name, $description, $state,$issue);
                $this->entityManager->persist($test);
                $this->entityManager->flush();
                $this->notifications->addSuccess("Test {$test->getName()} créé avec succès.");
            } catch(\Exception $e) {
                $this->notifications->addError($e->getMessage());
            }

            return $this->redirectToRoute('testsList', [
                'id_project' => $id_project
            ]);
        }

        return $this->render('test/test_form.html.twig', [
            'form'=> $form->createView(),
            'user' => $this->getUser(),
            'project' => $project
        ]);

    }

    /**
     * Displays the test list page.
     * @Route("/project/{id_project}/tests", name="testsList", methods={"GET"})
     */
    public function viewTests(Request $request, $id_project) {
        $project = $this->projectRepository->find($id_project);
        $tests = $project->getTests();

        $statusStat = $this->testRepository->getProportionStatus($project);

        return $this->render('test/test_list.html.twig', [
            'project'=> $project,
            'statusStat' => $statusStat,
            'tests' => $tests,
            'user' => $this->getUser()
        ]);
    }

    /**
     * Displays and handles the test edit form.
     * @Route("/project/{id_project}/tests/{id_test}/edit", name="editTest")
     */
    public function editTest(Request $request, $id_test, $id_project): Response
    {
        $test = $this->testRepository->find($id_test);
        $project = $this->projectRepository->find( $id_project);
        $form = $this->createForm(TestType::class, $test, [
            TestType::PROJECT => $project
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->entityManager->persist($test);
                $this->entityManager->flush();
                $this->notifications->addSuccess("Test {$test->getName()} édité avec succès.");
            } catch(\Exception $e) {
                $this->notifications->addError($e->getMessage());
            }
            return $this->redirectToRoute('testsList', [
                'id_project' => $id_project
            ]);
        }

        return $this->render('test/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $this->getUser(),
            'project' => $project
        ]);
    }

    /**
     * Handles the deletion of a test.
     * @Route("/project/{id_project}/tests/{id_test}/delete", name="deleteTest")
     */
    public function deleteTest(Request $request, $id_project, $id_test)
    {
        $test = $this->testRepository->find($id_test);
        if (!$test) {
            $this->notifications->addError("Aucun test n'existe avec l'id {$id_test}");
        } else {
            try {
                $this->entityManager->remove($test);
                $this->entityManager->flush();
                $this->notifications->addSuccess("Test {$test->getName()} supprimé avec succès.");
            } catch(\Exception $e) {
                $this->notifications->addError($e->getMessage());
            }
        }
        return $this->redirectToRoute('testsList', [
            'id_project' => $id_project
        ]);
    }
}
