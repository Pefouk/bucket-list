<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="Please type in an username !")
     * @Assert\Length(min="2", minMessage="Username too short !", max="50", maxMessage="Username too long !")
     * @ORM\Column(type="string", length=50, unique=true)
     */
    private $username;

    /**
     * @Assert\NotBlank(message="Please type in an email !")
     * @Assert\Length(max="255", maxMessage="Email too long !")
     * @Assert\Email(message="Invalid email !")
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @Assert\NotBlank(message="Please type in a password !")
     * @Assert\Length(min="6", min="Your password has to be at least 6 characters long !", maxMessage="30", maxMessage="Your password can't be more than 30 characters long !")
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCreated;

    /**
     * @return mixed
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param mixed $dateCreated
     */
    public function setDateCreated($dateCreated): void
    {
        $this->dateCreated = $dateCreated;
    }


    /**
     * @return mixed
     */
    public function getUsername()
    {
        return (string) $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username): void
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        return (['ROLE_USER']);
    }
}
