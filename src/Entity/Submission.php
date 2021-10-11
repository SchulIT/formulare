<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Submission {
    use IdTrait;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    private $date;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $form;

    /**
     * @ORM\Column(type="json")
     * @Assert\NotNull()
     * @var mixed
     */
    private $data;

    /**
     * @return string
     */
    public function getForm(): string {
        return $this->form;
    }

    /**
     * @param string $form
     * @return Submission
     */
    public function setForm(string $form): Submission {
        $this->form = $form;
        return $this;
    }

    public function getData() {
        return $this->data;
    }

    public function setData($data): Submission {
        $this->data = $data;
        return $this;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function updateDate(): void {
        $this->date = new \DateTime();
    }

    public function getDate(): \DateTime {
        return $this->date;
    }
}