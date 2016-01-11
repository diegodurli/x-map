<?php namespace MapBundle\Controller;

use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use FOS\RestBundle\Controller\FOSRestController;
use MapBundle\Document\Team;
use MapBundle\Form\Type\TeamType;
use Symfony\Component\HttpFoundation\Request;

class TeamsController extends FOSRestController
{
    protected $dm;

    protected $repository;

    public function __construct(ManagerRegistry $dm)
    {
        $this->dm = $dm;
        $this->repository = $dm->getRepository('MapBundle:Team');
    }

    public function getTeamsAction()
    {
        $this->denyAccessUnlessGranted('view', new Team);

        $teams = $this->repository->findAll();
        $view = $this->view($teams);

        return $this->handleView($view);
    }

    public function postTeamAction(Request $request)
    {
        $team = new Team;

        $form = $this->createForm(new TeamType, $team);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->denyAccessUnlessGranted('create', $team);
            $this->dm->persist($team);
            $this->dm->flush();
            $view = $this->view($team, 201);

        } else {
            $view = $this->view($form, 400);
        }

        return $this->handleView($view);
    }

    public function putTeamAction(Request $request, $team)
    {
        $team = $this->repository->find($team);

        $this->denyAccessUnlessGranted('edit', $team);

        if (!$team) {
            $view = $this->view(null, 404);
        } else {
            $form = $this->createForm(new TeamType, $team, array('method' => 'PUT'));
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->dm->flush();
                $view = $this->view($team);
            } else {
                $view = $this->view($form, 400);
            }

        }

        return $this->handleView($view);
    }

    public function getTeamAction($team)
    {
        $team = $this->repository->find($team);
        $this->denyAccessUnlessGranted('view', $team);

        $view = $team ? $this->view($team) : $this->view(null, 404);

        return $this->handleView($view);
    }

    public function putTeamUserAction($team, $user) {
        $userRepository = $this->dm->getRepository('MapBundle:User');

        $team = $this->repository->find($team);
        $user = $userRepository->find($user);

        if (false && (!$team || !$user)) {
            $view = $this->view(null, 404);
        } else {
            $this->denyAccessUnlessGranted('join', $team);

            $teams = (array)$user->getTeams();
            if(!in_array($team->getId(), $teams)) {
                $teams[] = $team->getId();
                $user->setTeams($teams);
            }

            $users = (array)$team->getUsers();
            if(!in_array($user->getId(), $users)) {
                $users[] = $user->getId();
                $team->setUsers($users);
            }

            $this->dm->flush();

            $view = $this->view();
        }

        return $this->handleView($view);
    }

    public function deleteTeamUserAction($team, $user) {
        $userRepository = $this->dm->getRepository('MapBundle:User');

        $team = $this->repository->find($team);
        $user = $userRepository->find($user);

        if (!$team || !$user) {
            $view = $this->view(null, 404);
        } else {
            $this->denyAccessUnlessGranted('join', $team);

            $teams = (array)$user->getTeams();
            if(in_array($team->getId(), $teams)) {
                $user->setTeams(array_diff($teams, [$team->getId()]));
            }

            $users = (array)$team->getUsers();
            if(in_array($user->getId(), $users)) {
                $team->setUsers(array_diff($users, [$user->getId()]));
            }

            $this->dm->flush();

            $view = $this->view();
        }

        return $this->handleView($view);
    }
}