<?php

namespace AppBundle\DataFixtures\ORM;

use BD\UserBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadData implements FixtureInterface, ContainerAwareInterface
{
	/**
	 * @var ContainerInterface
	 */
	private $container;

	public function setContainer(ContainerInterface $container = null)
	{
		$this->container = $container;
	}

	public function load(ObjectManager $manager)
	{
		$user1 = new User();
		$user1->setEmail('admin@pp.pp');
		$user1->setUsername('admin');
		$user1->addRole('ROLE_ADMIN');
		$token = $this->generateUniqId();
		$user1->setToken($token);
		$encoder = $this->container->get('security.password_encoder');
		$password = $encoder->encodePassword($user1, 'ppppp');
		$user1->setPassword($password);
		$createdAt = new \DateTime('now');
		$user1->setCreatedAt($createdAt);
		$manager->persist($user1);

		$user2 = new User();
		$user2->setEmail('user@pp.pp');
		$user2->setUsername('user');
		$user2->addRole('ROLE_USER');
		$token = $this->generateUniqId();
		$user2->setToken($token);
		$encoder = $this->container->get('security.password_encoder');
		$password = $encoder->encodePassword($user2, 'ppppp');
		$user2->setPassword($password);
		$createdAt = new \DateTime('now');
		$user2->setCreatedAt($createdAt);
		$manager->persist($user2);

		$manager->flush();
	}

	private function generateUniqId() {
		$result = bin2hex(openssl_random_pseudo_bytes(16));
		return $result;
	}
}