<?php
// src/Controller/TestController.php
namespace App\Controller;

use App\Form\TestType;
use App\Entity\Test;
use App\Repository\TestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\RenderService;

class TestController extends AbstractController {

    /**
     * @Route("/project/{id_project}/tests/new", name="createTest")
     */
    public function viewCreationTest(Request $request, ProjectRepository $projectRepository,
                                      EntityManagerInterface $entityManager, $id_project) : Response
    {
        $project = $projectRepository->find( $id_project);
        $form = $this->createForm(TestType::class, [], [
            TestType::PROJECT => $project
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $error = null; 
            $success = null; 
            try {
                $data = $form->getData();
                $name = $data['name'];
                $description= $data['description'];
                $state=$data['state'];
                $test = new Test($project, $name, $description, $state);
                $success =  "Test {$test->getName()} créée avec succés."; 
                $entityManager->persist($test);
                $entityManager->flush();
            } catch(\Exception $e) {
                $error = $e->getMessage();
            }
            
            return $this->renderTest($error, $success , $project);
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
    public function viewTests(Request $request, ProjectRepository $projectRepository, $id_project) {
        return $this->renderTest(null, null, $projectRepository->find($id_project));
    }

    /**
     * @Route("/project/{id_project}/tests/{id_test}/edit", name="editTest")
     */
    public function editTest(Request $request, EntityManagerInterface $entityManager,
                              ProjectRepository $projectRepository, TestRepository $testRepository,
                              $id_test, $id_project): Response
    {
        $test = $testRepository->find($id_test);
        $project = $projectRepository->find( $id_project);
        $form = $this->createForm(TestType::class, $test, [
            TestType::PROJECT => $project
        ]);
        $form->handleRequest($request);
        $error = null;

        if ($form->isSubmitted() && $form->isValid()) {     
            try {
                $entityManager->persist($test);
                $entityManager->flush();
            } catch(\Exception $e) {
                $error = $e->getMessage();
            }
            return $this->renderTest($error, "Test {$test->getName()} éditée avec succés.", $project);
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
    public function deleteTest(Request $request, TestRepository $test_Repository,
                                EntityManagerInterface $entityManager, ProjectRepository $projectRepository, $id_project, $id_test)
    {
        $test = $test_Repository->find($id_test);
        $error = null;
        $success = null;
        if (!$test) {
            $error ="Aucune test n'existe avec l'id {$id_test}";
        } else {
            try {
                $success = "Test {$test->getName()} supprimée avec succés.";
                $entityManager->remove($test);
                $entityManager->flush();
            } catch(\Exception $e) {
                $error = $e->getMessage();
            }
        }
        return $this->renderTest($error, $success, $projectRepository->find($id_project));
    }

    private function renderTest($error, $success, $project) {
        
        $tests = $project->getTests();

        return $this->render('test/test_list.html.twig', [
            'error' => $error,
            'success' => $success,
            'project'=> $project,
            'tests' => $tests,
            'user' => $this->getUser()
        ]);
    }
}
