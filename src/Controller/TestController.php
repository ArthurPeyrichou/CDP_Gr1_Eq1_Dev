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
                $test = new Test($project, $data['name'], $data['description'], $data['state'], $data['issue']);
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
        
        $statusStat = $this->testRepository->getProportionStatus($project);

        $todos= array();
        $faileds= array();
        $succeededs= array();
        foreach ($project->getTests() as $test) {
            switch($test->getState()) {
                case 'todo':
                    $todos[]=$test;
                    break;
                case 'failed':
                    $faileds[]=$test;
                    break;
                case 'succeeded':
                    $succeededs[]=$test;
                    break;
            }
        }

        return $this->render('test/test_list.html.twig', [
            'project'=> $project,
            'statusStat' => $statusStat,'todos' => $todos,
            'faileds'=> $faileds,
            'succeededs'=>$succeededs,
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

    /**
     * Handles the edition of a test's state.
     * @Route("/project/{id_project}/test/{id_test}/{state}", name="changeTestState", requirements={
     *     "status"="^todo|failed|succeeded$"
     * })
     */
    public function changeTaskStatus($id_project,$id_test, $state)
    {
        $test = $this->testRepository->find($id_test);

        try {
            $test->setState($state);
            $this->entityManager->flush();
        }
        catch (\Exception $e) {
            $this->notifications->addError($e->getMessage());
        }

        return $this->redirectToRoute('testsList', [
            'id_project' => $id_project
        ]);
    }
}
