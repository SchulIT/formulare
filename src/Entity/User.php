<?php

namespace App\Entity;

use Serializable;
use Stringable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[UniqueEntity(fields: ['username'])]
class User implements UserInterface, Serializable, Stringable
{
    use IdTrait;
    #[ORM\Column(type: 'uuid')]
    private ?UuidInterface $idpId = null;
    #[ORM\Column(type: 'string', unique: true)]
    private ?string $username = null;
    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    private ?string $firstname = null;
    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    private ?string $lastname = null;
    /**
     * @var string[]
     */
    #[ORM\Column(type: 'json')]
    private array $roles = ['ROLE_USER'];
    /**
     * @var string[]
     */
    #[ORM\Column(type: 'json')]
    private array $data = [ ];
    /**
     * @return UuidInterface|null
     */
    public function getIdpId(): ?UuidInterface {
        return $this->idpId;
    }
    /**
     * @return User
     */
    public function setIdpId(UuidInterface $uuid): User {
        $this->idpId = $uuid;
        return $this;
    }
    /**
     * @return int|null
     */
    public function getId(): ?int {
        return $this->id;
    }
    /**
     * @return string|null
     */
    public function getFirstname(): ?string {
        return $this->firstname;
    }
    /**
     * @param string|null $firstname
     * @return User
     */
    public function setFirstname(?string $firstname): User {
        $this->firstname = $firstname;
        return $this;
    }
    /**
     * @return string|null
     */
    public function getLastname(): ?string {
        return $this->lastname;
    }
    /**
     * @param string|null $lastname
     * @return User
     */
    public function setLastname(?string $lastname): User {
        $this->lastname = $lastname;
        return $this;
    }
    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles) {
        $this->roles = $roles;
    }
    /**
     * @return string[]
     */
    public function getRoles(): array {
        return $this->roles;
    }
    /**
     * @return User
     */
    public function setUsername(string $username): User {
        $this->username = $username;
        return $this;
    }
    /**
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }
    public function getData(string $key, $default = null) {
        return $this->data[$key] ?? $default;
    }
    public function setData(string $key, $data): void {
        $this->data[$key] = $data;
    }
    /**
     * @inheritDoc
     */
    public function getPassword() {
        return '';
    }
    /**
     * @inheritDoc
     */
    public function getSalt() {
        return null;
    }
    /**
     * @inheritDoc
     */
    public function eraseCredentials() { }
    public function getUserIdentifier(): string {
        return $this->getUsername();
    }
    /**
     * @inheritDoc
     */
    public function serialize() {
        return serialize([
            $this->getId(),
            $this->getUsername()
        ]);
    }
    /**
     * @inheritDoc
     */
    public function unserialize($serialized) {
        [$this->id, $this->username] = unserialize($serialized);
    }
    public function __toString(): string {
        return sprintf('%s, %s (%s)', $this->getLastname(), $this->getFirstname(), $this->getUsername());
    }
}