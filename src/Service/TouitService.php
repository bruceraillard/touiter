<?php

namespace App\Service;

use App\Entity\Touit;
use App\Repository\TouitRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TouitService
{
    public function __construct(
        private TouitRepository    $repo,
        private ValidatorInterface $validator
    )
    {
    }

    /** @return Touit[] */
    public function list(): array
    {
        return $this->repo->createQueryBuilder('t')
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()->getResult();
    }

    public function get(int $id): Touit
    {
        $t = $this->repo->find($id);
        if (!$t) throw new NotFoundHttpException('Touit not found');
        return $t;
    }

    public function create(Touit $t): Touit
    {
        $errors = $this->validator->validate($t);
        if (\count($errors) > 0) {
            throw new BadRequestHttpException((string)$errors);
        }
        $this->repo->getEntityManager()->persist($t);
        $this->repo->getEntityManager()->flush();
        return $t;
    }

    public function delete(int $id): void
    {
        $t = $this->get($id);
        $this->repo->getEntityManager()->remove($t);
        $this->repo->getEntityManager()->flush();
    }
}
