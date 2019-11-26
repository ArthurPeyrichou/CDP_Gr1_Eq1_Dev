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

    public function __construct( TestRepository $testRepository, NotificationService $notifications, ProjectRepository $projectRepository)
    {
        $this->testRepository = $testRepository;
        $this->notifications = $notifications;
        $this->projectRepository = $projectRepository;
    }

    /**
     * @Route("/project/{id_project}/tests/new", name="createTest")
     */
    public function viewCreationTest(Request $request, EntityManagerInterface $entityManager, $id_project) : Response
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
                $test = new Test($project, $name, $description, $state);
                $entityManager->persist($test);
                $entityManager->flush();
                $this->notifications->addSuccess("Test {$test->getName()} créée avec succés.");
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
     * @Route("/project/{id_project}/tests", name="testsList", methods={"GET"})
     */
    public function viewTests(Request $request, $id_project) {
        $project = $this->projectRepository->find($id_project);
        $tests = $project->getTests();

        $statTests = $this->testRepository->getProportionStatus($project);

        return $this->render('test/test_list.html.twig', [
            'project'=> $project,
            'statistic' => $statTests,
            'tests' => $tests,
            'user' => $this->getUser()
        ]);
    }

    /**
     * @Route("/project/{id_project}/tests/{id_test}/edit", name="editTest")
     */
    public function editTest(Request $request, EntityManagerInterface $entityManager, $id_test, $id_project): Response
    {
        $test = $this->testRepository->find($id_test);
        $project = $this->projectRepository->find( $id_project);
        $form = $this->createForm(TestType::class, $test, [
            TestType::PROJECT => $project
        ]);
        $form->handleRequest($request);
        $error = null;

        if ($form->isSubmitted() && $form->isValid()) {     
            try {
                $entityManager->persist($test);
                $entityManager->flush();
                $this->notifications->addSuccess("Test {$test->getName()} éditée avec succés.");
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
     * @Route("/project/{id_project}/tests/{id_test}/delete", name="deleteTest")
     */
    public function deleteTest(Request $request, EntityManagerInterface $entityManager, $id_project, $id_test)
    {
        $test = $this->testRepository->find($id_test);
        if (!$test) {
            $this->notifications->addError("Aucune test n'existe avec l'id {$id_test}");
        } else {
            try {
                $entityManager->remove($test);
                $entityManager->flush();
                $this->notifications->addSuccess("Test {$test->getName()} supprimée avec succés.");
            } catch(\Exception $e) {
                $this->notifications->addError($e->getMessage());
            }
        }
        return $this->redirectToRoute('testsList', [
            'id_project' => $id_project
        ]);
    }
}
