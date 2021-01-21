<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use App\Form\CategoryFormType;
use App\Form\ProductFormType;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends AbstractController
{

    /** @var EntityManager $em */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Request $request
     * @param Product|null $product
     *
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function productAction(Request $request, ?Product $product) : Response
    {
        $productInstance = $product ?? new Product();

        $form = $this->createForm(ProductFormType::class, $productInstance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message = $product ? 'Votre article à été correctement modifié' : 'Votre article à été correctement crée';
            $this->addFlash('success', $message);

            $this->em->persist($productInstance);
            $this->em->flush();

            return $this->redirectToRoute('product_view', [
                'categorySlug' => $productInstance->getCategory()->getSlug(),
                'productSlug'  => $productInstance->getSlug(),
                'id'           => $productInstance->getId()
            ]);
        }

        return $this->render('admin/product.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param Category|null $category
     * @return Response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function categoryAction(Request $request, ?Category $category): Response
    {
        $categoryInstance = $category ?? new Category();

        $form = $this->createForm(CategoryFormType::class, $categoryInstance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->em->persist($categoryInstance);
            $this->em->flush();

            $this->redirectToRoute('product_list');
        }

        return $this->render('admin/category.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param User|null $user
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function userAction(Request $request, ?User $user, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $userInstance = $user ?? new User();

        $form = $this->createForm(UserFormType::class, $userInstance);

        if ($user) {
            $form->remove('plainPassword');
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            if ($userInstance) {
                $userInstance->setPassword(
                    $passwordEncoder->encodePassword(
                        $userInstance,
                        $form->get('plainPassword')->getData()
                    )
                );
            }

                $message = $user ? 'Votre utilisateur à été correctement modifié' : 'Votre utilisateur à été correctement crée';
            $this->addFlash('success', $message);

            $userInstance->setRoles($form->get('roles')->getData());
            $this->em->persist($userInstance);
            $this->em->flush();

            return $this->redirectToRoute('admin_user_list');
        }

        return $this->render('admin/user.html.twig', [
            'form'      => $form->createView(),
            'isCreated' => $user
        ]);
    }

    public function usersListAction(Request $request): Response
    {
        $userRepo = $this->em->getRepository(User::class);
        $users    = $userRepo->findAll();

        return $this->render('admin/user_list.html.twig', [
            'users' => $users
        ]);
    }
}
